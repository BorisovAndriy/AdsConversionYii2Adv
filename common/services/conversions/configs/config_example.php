<?php
    /**
     * Зробити копію як config_???.php
     * Приклад основного файла конфігурації
     * Надалі усі параметри та налаштування вказувати в файлі config.php
    */

    /**
     * var $spreadsheet_id string Хеш Гугл таблиці з результатами експорту
     * 1pnIWftebkdk_...
     */
    $spreadsheet_id = '';

    /**
     * var $client_id string CLIENT_ID з налаштувать ключів Апі в Адмітаді
     * 3370...
     */
    $client_id =  '';

    /**
     * var $client_secret string з налаштувать ключів Апі в Адмітаді
     * 5b20...
     */
    $client_secret =  '';

    /**
     * var $scope string SCOPE не змінювати
     */
    $scope =  'statistics';

    /**
     * var $user_name string USER_NAME логін користувача в Адмітаді
     * SlavaUkraini
     */
    $user_name =  '';

    /**
     * var $user_password string USER_PASSWORD пароль користувача в Адмітаді
     * 12sd413ev178
     */
    $user_password =  '';

    /**
     * var $action_start_id integer Id конверсії з Адмітаду, з якої почати вигрузку
     * 919564704
     */
    $action_start_id = '';

    /**
     * var $action_started_at string Дата конверсії з Адмітаду, з якої почати вигрузку
     * 01.01.2023
     */
    $action_started_at = '01.03.2023';

