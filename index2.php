<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="index2.css">
</head>

<body>
    <?php
    require './functions.php'; # Load functions
    require './secrets.php'; # Load secrets

    # North West (Current prices)
    echo "<div id=NW><h3>Current Price</h3><font size=5>" . round(GetCurrentRate()['current_rate_per_kWh'], 1) . "</font> GBp per kWh</div>";

    # North East corner! (Most Expensive)
    echo "<div id=NE><h3>Today's most expensive times</h3>";
    $highestrates = GetHighestRate('8');
    foreach ($highestrates as $segmentTimeStart => $rate) {
        $segmentTimeStart = strtotime($segmentTimeStart);
        $segmentTimeStart = date('H:i', $segmentTimeStart);
        $segmentTimeEnd = date("H:i", strtotime($segmentTimeStart) + 1800);
        $rate = round($rate, 2);
        echo "<br>$segmentTimeStart - $segmentTimeEnd is <b>$rate</b> GBp per kWh";
    }
    echo "</div>";

    # South West corner! (Last n days costs.)
    echo "<div id=SW><h3>Recent daily Totals</h3>";
    $numberofDaysToShow = 50;
    $start_date = date("Y-m-d", time() - ($numberofDaysToShow * 86400));
    $end_date = date("Y-m-d", time() - 86400);
    $recentPrices = GetTotalCost($start_date, $end_date);
    foreach ($recentPrices as $date => $values) {
        setlocale(LC_MONETARY, 'en_GB.utf8');
        $number = $values['total_cost_in_GBP'];
        $value = money_format('%.2n', $number) . "\n";
        echo "<br>$date - $value using $values[kWh_total_consumed] kWh";
    }
    echo "</div>";

    # South East corner! (Today's prices)
    echo "<div id=SE><h3>Today's prices</h3>";
    $todaysPrices = GetTodaysRatesFromDB();
    foreach ($todaysPrices as $segmentTimeStart => $rate) {
        $segmentTimeStart = strtotime($segmentTimeStart);
        $segmentTimeStart = date('H:i', $segmentTimeStart);
        $segmentTimeEnd = date("H:i", strtotime($segmentTimeStart) + 1800);
        $rate = round($rate, 2);
        echo "<br>$segmentTimeStart - $segmentTimeEnd is <b>$rate</b> GBp per kWh";
    }
    echo "</div>";
