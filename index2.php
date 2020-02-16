<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="index2.css">
</head>

<body>
    <script document.onload=()=>
        {
            location.href = "#now"
        };
    </script>

    <?php
    require './functions.php'; # Load functions
    require './secrets.php'; # Load secrets

    # Set global config things
    setlocale(LC_MONETARY, 'en_GB.utf8');
    $GBP_format = "%.2n";
    $GBp_format = "%.2n";

    # North West (Current prices)
    echo "<div id=NW><h3>Current Prices</h3>";
    echo "<h4>Current rate (right now!)</h4>";
    $currentrate = GetCurrentRate()['current_rate_per_kWh'] / 100;
    $currentrate = money_format($GBp_format, $currentrate);
    echo "$currentrate per kWh";


    echo "<h4>Current month</h4>";
    $start_date = date('Y-m-d', strtotime('first day of this month'));
    $end_date = date('Y-m-d', strtotime('last day of this month'));
    $recentPrices = GetTotalCostAsSummary($start_date, $end_date);
    foreach ($recentPrices as $date => $values) {
        $date = date('M', $date);
        $number = $values['total_cost_in_GBP'];
        $value = money_format($GBP_format, $number);
        echo "$date - $value using $values[kWh_total_consumed] kWh<br />";
    }

    echo "<h4>Current Year</h4>";
    $start_date = date('Y-m-d', strtotime('Jan 1'));
    $end_date = date('Y-m-d', strtotime('Dec 31'));
    $recentPrices = GetTotalCostAsSummary($start_date, $end_date);
    foreach ($recentPrices as $date => $values) {
        $number = $values['total_cost_in_GBP'];
        $value = money_format($GBP_format, $number);
        echo "$date - $value using $values[kWh_total_consumed] kWh<br />";
    }
    echo "</div>";

    # North East corner! (Most Expensive)
    echo "<div id=NE><h3>Today's most expensive times</h3><p>";
    $highestrates = GetHighestRate('10');
    foreach ($highestrates as $segmentTimeStart => $rate) {
        $segmentTimeStart = strtotime($segmentTimeStart);
        $segmentTimeStart = date('H:i', $segmentTimeStart);
        $segmentTimeEnd = date("H:i", strtotime($segmentTimeStart) + 1800);
        $rate = $rate / 100;
        $rate = money_format($GBp_format, $rate);
        if ((date('H:i')) >= $segmentTimeStart && (date('H:i')) <= $segmentTimeEnd) {
            echo "<font class=currentrate>";
        }
        echo "$segmentTimeStart - $segmentTimeEnd is <b>$rate</b> GBp per kWh<br /></font>";
    }
    echo "</p></div>";

    # South West corner! (Last n days costs.)
    echo "<div id=SW><h3>Recent daily Totals</h3>";
    echo "<h4>Recent Days</h4>";
    $numberofDaysToShow = 5;
    $start_date = date("Y-m-d", time() - ($numberofDaysToShow * 86400));
    $end_date = date("Y-m-d", time() - 86400);
    $recentPrices = GetTotalCost($start_date, $end_date);
    $recentPrices = array_reverse($recentPrices);
    foreach ($recentPrices as $date => $values) {
        $number = $values['total_cost_in_GBP'];
        $value = money_format($GBP_format, $number);
        echo "$date - $value using $values[kWh_total_consumed] kWh<br />";
    }
    echo "</div>";

    # South East corner! (Today's prices)
    echo "<div id=SE><h3>Today's prices</h3>";
    $todaysPrices = GetTodaysRatesFromDB();
    foreach ($todaysPrices as $segmentTimeStart => $rate) {
        $segmentTimeStart = strtotime($segmentTimeStart);
        $segmentTimeStart = date('H:i', $segmentTimeStart);
        $segmentTimeEnd = date("H:i", strtotime($segmentTimeStart) + 1800);
        $rate = $rate / 100;
        $rate = money_format($GBp_format, $rate);
        if ((date('H:i')) > $segmentTimeStart && (date('H:i')) < $segmentTimeEnd) {
            echo "<font id='now' class=currentrate>";
        }
        echo "$segmentTimeStart - $segmentTimeEnd is <b>$rate</b> GBp per kWh<br /></font>";
    }
    echo "</div>";
