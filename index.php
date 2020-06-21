<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <script>
        document.onload = () => {
            location.href = "#now"
        };
    </script>

    <?php
    require './functions.php'; // Load functions
    require './secrets.php';   // Load secrets

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

        // Set global config things
        setlocale(LC_MONETARY, 'en_GB.utf8');
        $GBP_format = "%.2n";
        $GBp_format = "%.2n";
        $negative_GBp_format = "%.4n";


        echo "<div class=row><div class=column>";

        // Connection Statuses
        echo "<div id=statuses>";
        $call_url = $base . $emeter . $elec_mpan . "/";
        $status_octopus = TestOctopusLogin($api_key, $call_url);
        $status_mysql = TestMySQLLogin();
        echo "<p>Statuses: Octopus is <b>$status_octopus</b>, MySQL is <b>$status_mysql</b></p>";
        echo "</div>";

        // North West (Current prices)
        echo "<div id=NW><h3>Misc info</h3>";
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

        echo "<h4>Current Year</h4>";
        $start_date = date('Y-m-d', strtotime('Jan 1'));
        $end_date = date('Y-m-d', strtotime('Dec 31'));
        $recentPrices = GetTotalCostAsSummary($start_date, $end_date);
        foreach ($recentPrices as $date => $values) {
            $number = $values['total_cost_in_GBP'];
            $value = money_format($GBP_format, $number);
            echo "<p>$date - $value using $values[kWh_total_consumed] kWh</p>";
        }

        echo "<h4>Cheapest 3 Hour Windows</h4>";
        $numberofWindowsToShow = 3;
        $x = 0;
        $cheapestWindows = CalculateCheapestWindow();
        foreach ($cheapestWindows as $segmentTimeEnd => $rate) {
            if ($rate && $x < $numberofWindowsToShow) {
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

        // North East corner (Most Expensive)
        echo "<div id=NE><h3>Today's most expensive times</h3><p>";
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

        echo "</div><div class=column>";

        // South West corner (Last n days costs.)
        echo "<div id=SW><h3>Recent daily Totals</h3>";
        echo "<h4>Recent Days</h4><p>";
        $numberofDaysToShow = 1;
        $start_date = date("Y-m-d", time() - ($numberofDaysToShow * 86400));
        $end_date = date("Y-m-d", time() - 86400);
        $recentPrices = GetTotalCost($start_date, $end_date);
        $recentPrices = array_reverse($recentPrices);
        foreach ($recentPrices as $date => $values) {
            $number = $values['total_cost_in_GBP'];
            $value = money_format($GBP_format, $number);
            echo "$date - $value using $values[kWh_total_consumed] kWh<br />";
        }
        echo "</p></div>";

        // South East corner (Today's prices)
        $allfuture = True;
        if ($allfuture) {
            echo "<div id=SE><h3>Upcoming prices <small>(Tomorrow as of 1600 GMT)</small></h3><p>";
        } else {
            echo "<div id=SE><h3>Upcoming prices</h3><p>";
        }
        $todaysPrices = GetTodaysRatesFromDB($allfuture);
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
    }

    echo "</div></div></body></html>";
