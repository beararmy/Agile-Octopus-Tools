<?php

// echo "<h2>You probably want the nice page <a href=index2.php>HERE</a>.<br></h2>";

require './functions.php'; // Load functions
require './secrets.php'; // Load secrets

if (isset($_GET['automated'])) {
    // This is automated, called periodically with cronjob to pull in new data
    try {
        // Do some checks
        $call_url = $base . $emeter . $elec_mpan . "/";
        $status_octopus = TestOctopusLogin($api_key, $call_url);
        $status_mysql = TestMySQLLogin();

        // Update tomorrow's prices (expected 16:00 UTC/0)
        $pricesArray = GetUpcomingPrices();
        InsertUpcomingPrices($pricesArray);

        // Update the most recent usage stats
        $pricesArray = GetUsage();
        InsertRecentUsage($pricesArray);

        // Update the standing charges for the day
        $date = date("Y-m-d");
        $pricesArray = GetStandingCharge();
        InsertStandingCharge($pricesArray, $date);
    } catch (\Throwable $th) {
        throw $th;
    }
} else {
    $call_url = $base . $emeter . $elec_mpan . "/";
    $status_octopus = TestOctopusLogin($api_key, $call_url);
    $status_mysql = TestMySQLLogin();
    echo "Statuses: Octopus is <b>$status_octopus</b>, MySQL is <b>$status_mysql</b>";
}

// Below are many examples of working code and how you may use each function

// Test: function TestOctopusLogin
// $call_url = $base . $emeter . $elec_mpan . "/";
// $status_octopus = TestOctopusLogin($api_key, $call_url);
// echo $status_octopus;

// Test: function TestMySQLLogin
// $status_octopus = TestMySQLLogin();
// echo $status_octopus;

// Test: function GetUsage
// print("<pre>" . print_r(GetUsage(), true) . "</pre>");

// Test: function GetUpcomingPrices
// print("<pre>" . print_r(GetUpcomingPrices($api_key), true) . "</pre>");

// Test: function InsertUpcomingPrices
// $pricesArray = GetUpcomingPrices($api_key);
// InsertUpcomingPrices($pricesArray);

// Test: function InsertRecentUsage
// $pricesArray = GetUsage($api_key);
// InsertRecentUsage($pricesArray);

// Test: function GetTotalCost
// $start_date = "2020-01-03";
// $end_date = "2020-01-05";
// print("<pre>" . print_r(GetTotalCost($start_date, $end_date), true) . "</pre>");

// Test: function GetCurrentRate
// print("<pre>" . print_r(GetCurrentRate(), true) . "</pre>");
// echo GetCurrentRate()['current_rate_per_kWh'];

// Test: function GetHighestRate
// print("<pre>" . print_r(GetHighestRate('10'), true) . "</pre>");

// Test: function GetUpcomingPrices
// print("<pre>" . print_r(GetUpcomingPrices($api_key)['results'], true) . "</pre>");

// Test: function GetDaysRatesFromDB
// print("<pre>" . print_r(GetDaysRatesFromDB(), true) . "</pre>");
