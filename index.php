<?php

require './functions.php'; # Load functions
require './secrets.php'; # Load secrets

# Test: function TestOctopusLogin
#$call_url = $base . $emeter . $elec_mpan . "/";
#$status_octopus = TestOctopusLogin($api_key, $call_url);
#echo $status_octopus;

# Test: function TestMySQLLogin
#$status_octopus = TestMySQLLogin();
#echo $status_octopus;

# Test: function GetUsage
#print("<pre>" . print_r(GetUsage($api_key), true) . "</pre>");

# Test: function GetUpcomingPrices
#print("<pre>" . print_r(GetUpcomingPrices($api_key), true) . "</pre>");

# Test: function InsertUpcomingPrices
#$pricesArray = GetUpcomingPrices($api_key);
#InsertUpcomingPrices($pricesArray);

# Test: function InsertRecentUsage
#$pricesArray = GetUsage($api_key);
#InsertRecentUsage($pricesArray);

if (isset($_GET['automated'])) {
    #echo "Doing automated stuff";
    try {
        # Do some checks
        $call_url = $base . $emeter . $elec_mpan . "/";
        $status_octopus = TestOctopusLogin($api_key, $call_url);
        $status_mysql = TestMySQLLogin();

        # Update tomorrow's prices (expected 16:00 UTC/0)
        $pricesArray = GetUpcomingPrices($api_key);
        InsertUpcomingPrices($pricesArray);

        # Update the most recent usage stats
        $pricesArray = GetUsage($api_key);
        InsertRecentUsage($pricesArray);
    } catch (\Throwable $th) {
        throw $th;
    }
} else {
    $call_url = $base . $emeter . $elec_mpan . "/";
    $status_octopus = TestOctopusLogin($api_key, $call_url);
    $status_mysql = TestMySQLLogin();
    echo "Statuses: Octopus is <b>$status_octopus</b>, MySQL is <b>$status_mysql</b>";
}

#http://bike.bear.army/azure-function/functions.php?automated=yes

#print("<pre>" . print_r(GetCurrentRate(), true) . "</pre>");

echo "<h3> The current rate is:</h3>";
echo GetCurrentRate()['current_rate_per_kWh'];



echo "<h3> The highest 5 rates today are:</h3>";
GetHighestRate("3");