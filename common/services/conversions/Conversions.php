<?php

namespace common\services\conversions;

use Admitad\Api\Api as AdmitadApi;
use google\Client as GoogleClient;
use Google\Service\Sheets as GoogleSheetsService;
use Google\Service\Sheets\ValueRange as GoogleSheetsValueRange;

class Conversions
{
    // Визначення констант класу
    public const GOOGLE_ACCOUNT_KEY_FILE_PATH = '/var/www/fra.loc/common/config/configs/evident-alloy-377514-91abcd0f9793_ac_ba2.json';
    public const SPREADSHEET_ID = '1Lsjq5Srj6UHtx6J8OGZ9tsFIrmwgpj6Ckf6QDhINqN0';
    public const CLIENT_ID = 'dc6f259d348413b1490795006518fe';
    public const CLIENT_SECRET = 'f92044288ab57da24b0e0a0caf1f03';
    public const SCOPE = 'statistics';

    private $userName;
    private $userPassword;
    private $actionStartId;
    private $actionStartedAt;

    private $configFile = false;

    private $admitadConfig;


    public function __construct($configFile)
    {

        //$this->$configFile = $configFile;
        $this->loadConfig($configFile);


        /****



         */


        $apiAdmitad = new AdmitadApi();






        $admitad_response = $apiAdmitad->authorizeByPassword(
            $this->admitadConfig['client_id'],
            $this->admitadConfig['client_secret'],
            $this->admitadConfig['scope'],
            $this->admitadConfig['user_name'],
            $this->admitadConfig['user_password']
        );

        // Перетворення відповіді в масив
        $response_data = $apiAdmitad->getArrayResultFromResponse($admitad_response);

        // Отримання access_token
        $access_token = $response_data['access_token'] ?? null;


        //die();
        $apiAdmitad = new AdmitadApi($access_token);



        /**
         * Завантаження данних по Апі з Адмітаду та підготовка данних
         */
        $data = $apiAdmitad->get('/statistics/actions/', array(
            //'date_start'=>'01.01.2023',
            'action_id_start' =>$this->admitadConfig['action_start_id'],
            //'offset' => 0,
            'limit' => 1
        ));

        $body = $data->getBody();

        // Декодування JSON у масив
        $responseArray = json_decode($body, true);




        // Перевірка наявності ключів 'count' та 'id'
        if (isset($responseArray['_meta']['count']) && isset($responseArray['results'][0]['id'])) {
            $total_action_count = $responseArray['_meta']['count'];
            $start_action_id = $responseArray['results'][0]['id'];
        } else {
            // Обробка випадку, коли ключі відсутні
            $total_action_count = null;
            $start_action_id = null;
        }

        /**
         * Завантаження данних по Апі з Адмітаду та підготовка типізованого масиву данних
         */
        $new_data = array();
        for( $i = 0; $i < $total_action_count; $i+=500)
        {
            $data = $apiAdmitad->get('/statistics/actions/', array(
                'date_start'=> $this->admitadConfig['action_started_at'],
                //'action_id_start' =>ACTION_START_ID,
                'offset' => $i,
                'limit' =>500
            ));



            $dataArray = $data->getBody();

            // Декодування JSON у масив
            $responseDataArray = json_decode($dataArray, true);


            foreach ($responseDataArray["results"] as $key => $subres){
                if(!empty($subres['subid4'])){

                    if (empty($new_data[$subres['subid4']]['con_value']))
                        $new_data[$subres['subid4']]['con_value'] = 0;

                    $new_data[$subres['subid4']]['google_click_id'] =$subres['subid4'];
                    $new_data[$subres['subid4']]['con_time'] =$subres['action_date'];
                    $new_data[$subres['subid4']]['con_value'] +=$subres["positions"][0]['payment'];
                    $new_data[$subres['subid4']]['currency'] =$subres['currency'];
                    $new_data[$subres['subid4']]['click_country_code'] =$subres['click_country_code'];
                    $new_data[$subres['subid4']]['advcampaign_name'] =$subres['advcampaign_name'];

                }
            }
        }




        /**
         * Підготовка, очистка та запис данних у Гугл таблицю
         * Документація
         * https://developers.google.com/sheets/api/
         * https://developers.google.com/identity/protocols/googlescopes
         */


        putenv( 'GOOGLE_APPLICATION_CREDENTIALS=' . $this::GOOGLE_ACCOUNT_KEY_FILE_PATH );

        $client = new GoogleClient();
        $client->useApplicationDefaultCredentials();

        $client->addScope( 'https://www.googleapis.com/auth/spreadsheets' );
        $service = new GoogleSheetsService( $client );

        $range = 'conversion-import-template';
        $response = $service->spreadsheets_values->get($this->admitadConfig['spreadsheet_id'], $range);
        //var_dump($new_data);

        /**
         * Очистка попередніх результатів у Гугл таблиці
         */
        $rowcount = count($response->values) + 1;
        $range = 'conversion-import-template!A8:E'.$rowcount; // the range to clear, the 23th and 24th lines
        $clear = new GoogleSheetsService\ClearValuesRequest();
        $service->spreadsheets_values->clear($this->admitadConfig['spreadsheet_id'], $range, $clear);

        /**
         * Підготовка результуючого масиву
         */
        $values = array();
        foreach ($new_data as $product) {
            $values[] = array(
                $product['google_click_id'],
                'sale',
                $product['con_time'],
                '1',
                'UAH'
            );
        }

        /**
         * Запис результатів у Гугл таблицю
         *
         * https://developers.google.com/sheets/api/reference/rest/v4/ValueInputOption
         */
        $row_add = 'conversion-import-template!A8';
        $body    = new GoogleSheetsValueRange( [ 'values' => $values ] );
        $options = array( 'valueInputOption' => 'USER_ENTERED' );
        $service->spreadsheets_values->update( $this->admitadConfig['spreadsheet_id'], $row_add, $body, $options );

        echo '<a href="https://docs.google.com/spreadsheets/d/'.$this->admitadConfig['spreadsheet_id'].'">Open Spreadsheet for '.$this->admitadConfig['spreadsheet_id'].'</a><br>';

        die('die');


    }

    private function loadConfig ($configFile)
    {

        ///var/www/fra.loc/common/services/conversions/configs
        //$filePath = __DIR__ . '/common/services/conversions/configs/config_'.$configFile.'.php';

        //$filePath = __DIR__ . '/configs/config_'.$configFile.'.php';

        $filePath = '/var/www/fra.loc/common/services/conversions/configs/config_adm_ba.json';

       // var_dump($filePath);
       // die();

        if (!file_exists($filePath)) {
            throw new \Exception('Config file '.$filePath.' does not exist');
        }

        $jsonContent = file_get_contents($filePath);
        $this->admitadConfig = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error parsing JSON config file: " . json_last_error_msg());
        }

        return false;
    }

    public function processConversions()
    {
        /*
        // Підготовка, очистка та запис даних у Google таблицю
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . self::GOOGLE_ACCOUNT_KEY_FILE_PATH);

        $client = new GoogleClient();
        $client->useApplicationDefaultCredentials();
        $client->addScope('https://www.googleapis.com/auth/spreadsheets');
        $service = new GoogleSheetsService($client);

        $range = 'conversion-import-template';
        $response = $service->spreadsheets_values->get(self::SPREADSHEET_ID, $range);

        // Логіка авторизації та отримання даних з Admitad API
        // ...

        // Підготовка, очистка та запис даних у Google таблицю
        // ...

        return 'Processing complete';
        */
        return true;
    }
}
