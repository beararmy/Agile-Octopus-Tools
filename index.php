    <?php
    require './functions.php'; // Load functions
    require './secrets.php';   // Load secrets
    // require './cookies.php';   // Process cookies

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
        // This is the human-viewable site
        echo '<!DOCTYPE html>
        <html>
        <head>
            <link rel="stylesheet" href="index.css">
        </head>
        <body>
            <script>
                document.onload = () => {
                    location.href = "#now"
                };
            </script>';

        // Set global config things
        setlocale(LC_MONETARY, 'en_GB.utf8');
        $GBP_format = "%.2n";
        $GBp_format = "%.2n";
        $negative_GBp_format = "%.4n";

        echo "<div class=row><div class=column>";

        // Connection Statuses
        $call_url = $base . $emeter . $elec_mpan . "/";
        $status_octopus = TestOctopusLogin($api_key, $call_url);
        $status_mysql = TestMySQLLogin();
        if ($status_mysql == true && $status_octopus == true) {
            echo "<div class='insidebox statuses-healthy'>";
        } else {
            echo "<div class='insidebox statuses-unhealthy'>";
        }
        echo "<h4>Connection Statuses</h4>";
        echo "<p>Statuses: Octopus is <b>$status_octopus</b>, MySQL is <b>$status_mysql</b></p>";
        echo "</div>";

        // Configuration
        $username = cookieChecker("ELUSERNAME");
        $userlocaltimezone = cookieChecker("ELTIMEZONE");
        $days_to_show = cookieChecker("ELDAYSTOSHOW");
        $qty_3hr_windows = cookieChecker("ELNUMBEROFWINDOWS");
        $show_future_only = cookieChecker("SHOWALLFUTUREPRICES");

        echo "<div class='configuration insidebox'>";
        echo "<h4>Configurtion Options <a href=cookies.php><small>Change cookied settings</small></a></h4>";
        echo "<p>Active User: $username <br>
        Current Timezone: $userlocaltimezone <br>
        Number of recent days: $days_to_show <br>
        Number of 3hr Windows: $qty_3hr_windows <br>
        </p>";
        echo "</div>";

        // Current prices
        echo "<div id=NW class=insidebox><h3>Misc info</h3>";
        echo "<h4>Current rate (right now!)</h4>";
        $currentrate = GetCurrentRate()['current_rate_per_kWh'] / 100;
        $currentrate = money_format($GBp_format, $currentrate);
        echo "<p>$currentrate per kWh</p>";

        echo "<h4>Current month</h4>";
        $start_date = date('Y-m-d', strtotime('first day of this month'));
        $end_date = date('Y-m-d', strtotime('last day of this month'));
        $recentPrices = GetTotalCostAsSummary($start_date, $end_date);
        foreach ($recentPrices as $date => $values) {
            $date = date('M', $date);
            $number = $values['total_cost_in_GBP'];
            $value = money_format($GBP_format, $number);
            echo "<p>$date - $value using $values[kWh_total_consumed] kWh</p>";
        }

        echo "<s>";
        echo "<h4>Current Year</h4>";
        // $start_date = date('Y-m-d', strtotime('Jan 1'));
        // $end_date = date('Y-m-d', strtotime('Dec 31'));
        // $recentPrices = GetTotalCostAsSummary($start_date, $end_date);
        // foreach ($recentPrices as $date => $values) {
        //     $number = $values['total_cost_in_GBP'];
        //     $value = money_format($GBP_format, $number);
        //     echo "<p>$date - $value using $values[kWh_total_consumed] kWh</p>";
        // }
        echo "</s>";

        echo "<h4>Cheapest 3 Hour Windows</h4>";
        $x = 0;
        $cheapestWindows = CalculateCheapestWindow();
        foreach ($cheapestWindows as $segmentTimeEnd => $rate) {
            if ($rate && $x < $qty_3hr_windows) {
                $rate = $rate / 100;
                if ($rate <= 0) {
                    $rate = money_format($negative_GBp_format, $rate);
                } else {
                    $rate = money_format($GBp_format, $rate);
                }
                $friendlyStart = date("H:i", strtotime($segmentTimeEnd));
                $friendlyEnd = date("H:i", strtotime($segmentTimeEnd) + 10800);
                if (date("d-m", strtotime($segmentTimeEnd)) == date("d-m")) {
                    $friendlyDay = "today";
                } else {
                    $friendlyDay = "tomorrow";
                }
                echo "<p>Starting $friendlyStart, ending $friendlyDay at $friendlyEnd, AvgRate $rate<br>";
                $x++;
            }
            echo "</p>";
        }
        echo "</div>";

        // Last n days costs
        echo "<div class='insidebox recentdailytotals'><h3>Recent daily Totals</h3>";
        echo "<h4>Recent Days</h4><p>";
        $start_date = date("Y-m-d", time() - (($days_to_show + 1) * 86400));
        $end_date = date("Y-m-d", time() - 86400);
        $recentPrices = GetTotalCost($start_date, $end_date);
        $recentPrices = array_reverse($recentPrices);
        foreach ($recentPrices as $date => $values) {
            $number = $values['total_cost_in_GBP'];
            $value = money_format($GBP_format, $number);
            echo "$date - $value using $values[kWh_total_consumed] kWh<br />";
        }
        echo "</p></div>";

        echo "</div><div class=column>";

        // Most Expensive
        echo "<div class='insidebox mostexpensive'><h3>Today's most expensive times</h3><p>";
        $highestrates = GetHighestRate('10');
        foreach ($highestrates as $segmentTimeStart => $rate) {
            $segmentTimeStart = strtotime($segmentTimeStart);
            $segmentTimeStart = date('H:i', $segmentTimeStart);
            $segmentTimeEnd = date("H:i", strtotime($segmentTimeStart) + 1800);
            $rate = $rate / 100;
            $rate = money_format($GBp_format, $rate);
            $lineText = "$segmentTimeStart - $segmentTimeEnd is <b>$rate</b> GBp per kWh<br />";
            if ((date('H:i')) >= $segmentTimeStart && (date('H:i')) <= $segmentTimeEnd) {
                echo "<span class=currentrate>" . $lineText . "</span>";
            } else {
                echo $lineText;
            }
        }
        echo "</p></div>";

        // Today's prices
        echo $show_future_only;
        echo gettype($show_future_only), "\n";
        if ($show_future_only) {
            echo "<div id=SE class=insidebox><h3>Upcoming prices</h3><p>";
        } else {
            echo "<div id=SE class=insidebox><h3>All current prices</h3><p>";
        }
        $todaysPrices = GetTodaysRatesFromDB($show_future_only);
        $currentAlreadyHighlighted = false;
        foreach ($todaysPrices as $segmentTimeStart => $rate) {
            $segmentTimeStart = strtotime($segmentTimeStart);
            $segmentTimeStart = date('H:i', $segmentTimeStart);
            $segmentTimeEnd = date("H:i", strtotime($segmentTimeStart) + 1800);
            $rate = $rate / 100;
            if ($rate <= 0) {
                $rate = money_format($negative_GBp_format, $rate);
            } else {
                $rate = money_format($GBp_format, $rate);
            }
            $lineText = "$segmentTimeStart - $segmentTimeEnd is <b>$rate</b> GBp per kWh<br />";
            if ((date('H:i')) > $segmentTimeStart && (date('H:i')) < $segmentTimeEnd && ($currentAlreadyHighlighted == false)) {
                echo  "<span id='now' class=currentrate>" . $lineText . "</span>";
                $currentAlreadyHighlighted = true;
            } else {
                echo $lineText;
            }
        }
        echo "</p></div>";
        echo "</div></div></body></html>";
    }
