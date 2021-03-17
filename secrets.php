<?php

if ($_ENV['PLATFORM'] != "azure") {
    require __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createMutable(__DIR__);
    $dotenv->load();
}

// DB (mysql/mariadb) config
$db_servername_8459 = $_ENV['DB_SERVERNAME_8459'];
$db_username_2734   = $_ENV['DB_USERNAME_2734'];
$db_password_1924   = $_ENV['DB_PASSWORD_1924'];
$db_name_9781       = $_ENV['DB_NAME_9781'];
$db_tablename_9834  = "ElectricPrices";
$db_tablename_9833  = "ElectricConsumption";
$db_tablename_7439  = "StandingCharges";

// Octopus config
$short_tariff_code  = "AGILE-18-02-21";
$tariff_code        = $_ENV['DNO_GSP_ID'];
$api_key            = $_ENV['OCOTPUS_API_KEY'];
$elec_mpan          = $_ENV['ELEC_MPAN'];
$elec_serial        = $_ENV['ELEC_SERIAL'];
$agile_reset_time_utc0 = "16:00";

// Octopus API endpoints
$base = "https://api.octopus.energy/v1/";
$products = "products/";
$emeter = "electricity-meter-points/";
$upcomingagilerate = "products/" .$short_tariff_code. "/electricity-tariffs/E-1R-AGILE-18-02-21-" .$tariff_code. "/standard-unit-rates/";
#TODO: this string gets built to use in one function, seems a waste

// Site config
$number_qry_results = $_ENV['NUMBER_QRY_RESULTS'];
