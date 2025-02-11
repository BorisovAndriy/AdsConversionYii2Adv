<?php

namespace common\services\conversions;

use Admitad\Api\Api as AdmitadApi;
use google\Client as GoogleClient;
use Google\Service\Sheets as GoogleSheetsService;
use Google\Service\Sheets\ValueRange as GoogleSheetsValueRange;

class Conversions
{
    // Визначення констант класу
    public const GOOGLE_ACCOUNT_KEY_FILE_PATH = '/var/www/AdsConversion.loc/common/services/conversions/configs/evident-alloy-377514-91abcd0f9793_ac_ba2.json';

    private $admitadConfig;

    public function __construct($configFile)
    {

        $this->loadConfig($configFile);

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

        $apiAdmitad = new AdmitadApi($access_token);

        /**
         * Завантаження данних по Апі з Адмітаду та підготовка данних
         */
        $startDate = date('d.m.Y', strtotime('-2 days')); // Дата три дні тому

        $body = $apiAdmitad->get('/statistics/actions/', array(
            'date_start'=>$startDate,
            'limit' => 500
        ))->getBody();

        //$body = $data->getBody();

        // Декодування JSON у масив
        $responseArray = json_decode($body, true);
/*
        echo '<pre>';
        var_dump($responseArray["results"] );
        die();
*/
        foreach ($responseArray["results"] as $key => $subres){
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
        $range = 'conversion-import-template!A4:E'.$rowcount; // Очищуємо починаючи з 4-го рядка
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
                $product['con_value'],
                $product['currency'],
            );
        }

        //todo fix  Дата в далекому майбутньому.



        /**
         * Запис результатів у Гугл таблицю
         *
         * https://developers.google.com/sheets/api/reference/rest/v4/ValueInputOption
         */
        $row_add = 'conversion-import-template!A4';
        $body    = new GoogleSheetsValueRange( [ 'values' => $values ] );
        $options = array( 'valueInputOption' => 'USER_ENTERED' );
        $service->spreadsheets_values->update( $this->admitadConfig['spreadsheet_id'], $row_add, $body, $options );

        echo '<a href="https://docs.google.com/spreadsheets/d/'.$this->admitadConfig['spreadsheet_id'].'">Open Spreadsheet for '.$this->admitadConfig['spreadsheet_id'].'</a><br>';

        die('die');


    }

    private function loadConfig ($configFile)
    {

        $filePath = '/var/www/AdsConversion.loc/common/services/conversions/configs/config_adm_'.$configFile.'.json';

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
        return true;
    }
}
