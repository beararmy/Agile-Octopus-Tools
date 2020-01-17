<?php

// DB (mysql/mariadb) config
$db_servername_8459 = "foo.bar.tld";    // Server hostname
$db_username_2734   = "xyz";            // Username (only needs SELECT,INSERT)
$db_password_1924   = "xyz";            // DB Password
$db_name_9781       = "xyz";            // DB Name
$db_tablename_9834  = "xyz";            // Table for segment prices
$db_tablename_9833  = "xyz";            // Table for segment prices 
$db_tablename_7439  = "xyz";            // Table for daily standing charge (changes infrequently)

// Octopus config
$tariff_code = "E-1R-AGILE-18-02-21-A";         // Note, the suffix letter changes based on region, https://en.wikipedia.org/wiki/Distribution_network_operator is your friend.
$api_key = "sk_live_xyz";                       // As defined on https://octopus.energy/dashboard/developer/
$elec_mpan = "0123456789";                      // As defined on https://octopus.energy/dashboard/developer/
$elec_serial = "9876543210";                    // As defined on https://octopus.energy/dashboard/developer/
$agile_reset_time_utc0 = "16:00";               // Time at which previous usage and future prices are released

// Octopus API endpoints
$base = "https://api.octopus.energy/v1/";
$products = "products/";
$emeter = "electricity-meter-points/";
$upcomingagilerate = "products/AGILE-18-02-21/electricity-tariffs/E-1R-AGILE-18-02-21-A/standard-unit-rates/"; # As defined on https://octopus.energy/dashboard/developer/ under 'Unit Rates' example.
