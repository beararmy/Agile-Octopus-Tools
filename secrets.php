<?php

// DB (mysql/mariadb) config
$db_servername_8459 = getenv("DB_SERVERNAME_8459");
$db_username_2734   = getenv("DB_USERNAME_2734");
$db_password_1924   = getenv("DB_PASSWORD_1924");
$db_name_9781       = getenv("DB_NAME_9781");
$db_tablename_9834  = "ElectricPrices";
$db_tablename_9833  = "ElectricConsumption";
$db_tablename_7439  = "StandingCharges";

// Octopus config
$short_tariff_code  = "AGILE-18-02-21";
$tariff_code        = "E-1R-AGILE-18-02-21-A";
$api_key            = getenv("api_key");
$elec_mpan          = getenv("ELEC_MPAN");
$elec_serial        = getenv("ELEC_SERIAL");
$agile_reset_time_utc0 = "16:00";

// Octopus API endpoints
$base = "https://api.octopus.energy/v1/";
$products = "products/";
$emeter = "electricity-meter-points/";
$upcomingagilerate = "products/AGILE-18-02-21/electricity-tariffs/E-1R-AGILE-18-02-21-A/standard-unit-rates/";

// Site config
$number_results_consumption = getenv("number_results_consumption");
