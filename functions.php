<?php

require './secrets.php';

function TestOctopusLogin($api_key, $call_url)
{
    try {
        $handle = curl_init($call_url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if ($httpCode != 200) {
            throw new Exception("Got a non-200 response");
        }
        curl_close($handle);
        $status_octopus = "true";
    } catch (\Throwable $th) {
        $status_octopus = "false";
        throw new Exception("API Connection Failed");
    } finally {
        return $status_octopus;
    }
}
function TestMySQLLogin()
{
    try {
        require './secrets.php';
        $conn = new mysqli($db_servername_8459, $db_username_2734, $db_password_1924, $db_name_9781) or die("Unable to Connect");
        $sql = "SELECT * from $db_tablename_9834 LIMIT 1;";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $status_MySQL = "true";
        } else {
            $status_MySQL = "false";
            throw new Exception("MySQL Connection Failed");
        }
        $conn->close();
    } catch (\Throwable $th) {
        $status_MySQL = "false";
        throw new Exception("MySQL Connection Failed");
    } finally {
        return $status_MySQL;
    }
}
function GetUsage()
{
    require './secrets.php';
    $api_key = $api_key . ":";
    $url = $base . $emeter . $elec_mpan . "/meters/" . $elec_serial . "/consumption/?page_size=" . $number_results_consumption;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERPWD, "$api_key");
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    $json = json_decode($result, true);
    return $json;
}
function GetUpcomingPrices()
{
    require './secrets.php';
    $api_key = $api_key . ":";
    $url = $base . $upcomingagilerate;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERPWD, $api_key);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    $json = json_decode($result, true);
    return $json;
}
function GetStandingCharge()
{
    require './secrets.php';
    $url = $base . $products . $short_tariff_code . "/electricity-tariffs/" . $tariff_code . "/standing-charges/";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    $json = json_decode($result, true);
    return $json['results'][0];
}
function InsertUpcomingPrices($pricesArray)
{
    require './secrets.php';
    $conn = new mysqli($db_servername_8459, $db_username_2734, $db_password_1924, $db_name_9781) or die("Unable to Connect");
    foreach ($pricesArray['results'] as $row => $innerArray) {
        $from_time_php = date('Y-m-d H:i:s', strtotime($innerArray['valid_from']));
        $to_time_php = date('Y-m-d H:i:s', strtotime($innerArray['valid_to']));
        $sql = "INSERT INTO $db_tablename_9834 (valid_from, valid_to, value_exc_vat, value_inc_vat ) SELECT '$from_time_php', '$to_time_php','$innerArray[value_exc_vat]','$innerArray[value_inc_vat]' FROM DUAL WHERE NOT EXISTS (SELECT * FROM $db_tablename_9834 WHERE valid_from='$from_time_php' AND valid_to='$to_time_php' LIMIT 1);";
        $result = $conn->query($sql);
    }
    $conn->close();
}
function InsertRecentUsage($pricesArray)
{
    require './secrets.php';
    $conn = new mysqli($db_servername_8459, $db_username_2734, $db_password_1924, $db_name_9781) or die("Unable to Connect");
    foreach ($pricesArray['results'] as $row => $innerArray) {
        $interval_start_php = date('Y-m-d H:i:s', strtotime($innerArray['interval_start']));
        $interval_end_php = date('Y-m-d H:i:s', strtotime($innerArray['interval_end']));
        #$innerArray["interval_start"] = gmdate('c', strtotime($innerArray["interval_start"])); # Roll TZ modifier into datetime as if Zulu0
        $sql = "INSERT INTO $db_tablename_9833 (interval_start, interval_end, consumption) SELECT '$interval_start_php', '$interval_end_php','$innerArray[consumption]' FROM DUAL WHERE NOT EXISTS (SELECT * FROM $db_tablename_9833 WHERE interval_start='$interval_start_php' AND interval_end='$interval_end_php' LIMIT 1);";
        $result = $conn->query($sql);
    }
    $conn->close();
}
function InsertStandingCharge($pricesArray, $date)
{
    require './secrets.php';
    $conn = new mysqli($db_servername_8459, $db_username_2734, $db_password_1924, $db_name_9781) or die("Unable to Connect");
    $sql = "INSERT INTO $db_tablename_7439 (date, value_exc_vat, value_inc_vat) SELECT '$date', '$pricesArray[value_exc_vat]', '$pricesArray[value_inc_vat]' FROM DUAL WHERE NOT EXISTS (SELECT * FROM $db_tablename_7439 WHERE date='$date' AND value_inc_vat='$pricesArray[value_inc_vat]' LIMIT 1);";
    $result = $conn->query($sql);
    $conn->close();
}
function GetCurrentRate()
{
    require './secrets.php';
    date_default_timezone_set('UTC');
    $datetime = date("Y-m-d H:m:s");
    $conn = new mysqli($db_servername_8459, $db_username_2734, $db_password_1924, $db_name_9781) or die("Unable to Connect");
    $sql = "SELECT value_inc_vat FROM $db_tablename_9834 WHERE valid_from <= '$datetime' AND valid_to >= '$datetime' LIMIT 1;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $value_inc_vat = $row["value_inc_vat"];
        }
    }
    $conn->close();
    return array('current_rate_per_kWh' => $value_inc_vat);
}
function GetTotalCost($start_date, $end_date)
{
    require './secrets.php';
    date_default_timezone_set('UTC');
    $conn = new mysqli($db_servername_8459, $db_username_2734, $db_password_1924, $db_name_9781) or die("Unable to Connect");
    $sql = "SELECT DATE_FORMAT(interval_start, '%Y-%m-%d') date, SUM(c.consumption * p.value_inc_vat) kWh_cost_GBP, SUM(c.consumption) total_kWh, (SELECT value_inc_vat FROM StandingCharges WHERE date = '$start_date' LIMIT 1) standing_charge FROM ElectricConsumption c RIGHT JOIN ElectricPrices p on p.valid_from = c.interval_start WHERE interval_start >= '$start_date 00:00:00' AND interval_start < '$end_date 23:45:00' GROUP BY date;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $kWh_cost_GBP = $row["kWh_cost_GBP"] / 100;
            $kWh_cost_GBP = round($kWh_cost_GBP, 2);
            $total_cost_GBp = $row["kWh_cost_GBP"] + $row["standing_charge"];
            $total_cost_GBP = round(($total_cost_GBp / 100), 2);
            $data[$row["date"]]["date"] = $row["date"];
            $data[$row["date"]]["kWh_total_consumed"] = $row["total_kWh"];
            $data[$row["date"]]["kWh_cost_in_GBp"] = $row["kWh_cost_GBP"];
            $data[$row["date"]]["kWh_cost_in_GBP"] = $kWh_cost_GBP;
            $data[$row["date"]]["standing_charge_GBp"] = $row["standing_charge"];
            $data[$row["date"]]["total_cost_in_GBp"] = $total_cost_GBp;
            $data[$row["date"]]["total_cost_in_GBP"] = $total_cost_GBP;
        }
    }
    $conn->close();
    return $data;
}
function GetTotalCostAsSummary($start_date, $end_date)
{
    require './secrets.php';
    date_default_timezone_set('UTC');
    $conn = new mysqli($db_servername_8459, $db_username_2734, $db_password_1924, $db_name_9781) or die("Unable to Connect");
    $sql = "SELECT DATE_FORMAT(interval_start, '%Y') date, SUM(c.consumption * p.value_inc_vat) kWh_cost_GBP, SUM(c.consumption) total_kWh, (SELECT SUM(value_inc_vat) FROM StandingCharges WHERE date >= '$start_date' AND date <= '$end_date') standing_charge FROM ElectricConsumption c RIGHT JOIN ElectricPrices p on p.valid_from = c.interval_start WHERE interval_start >= '$start_date 00:00:00' AND interval_start < '$end_date' group by date;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $kWh_cost_GBP = $row["kWh_cost_GBP"] / 100;
            $kWh_cost_GBP = round($kWh_cost_GBP, 2);
            $total_cost_GBp = $row["kWh_cost_GBP"] + $row["standing_charge"];
            $total_cost_GBP = round(($total_cost_GBp / 100), 2);
            $data[$row["date"]]["date"] = $row["date"];
            $data[$row["date"]]["kWh_total_consumed"] = $row["total_kWh"];
            $data[$row["date"]]["kWh_cost_in_GBp"] = $row["kWh_cost_GBP"];
            $data[$row["date"]]["kWh_cost_in_GBP"] = $kWh_cost_GBP;
            $data[$row["date"]]["standing_charge_GBp"] = $row["standing_charge"];
            $data[$row["date"]]["total_cost_in_GBp"] = $total_cost_GBp;
            $data[$row["date"]]["total_cost_in_GBP"] = $total_cost_GBP;
        }
    }
    $conn->close();
    return $data;
}
function GetHighestRate($howmany)
{
    require './secrets.php';
    date_default_timezone_set('UTC');
    if (!isset($howmany) || ($howmany == "0")) {
        $howmany = 5;
    }
    $start_date = date("Y-m-d");
    $end_date = new DateTime('+1 day');
    $end_date = $end_date->format('Y-m-d');
    $conn = new mysqli($db_servername_8459, $db_username_2734, $db_password_1924, $db_name_9781) or die("Unable to Connect");
    $sql = "SELECT valid_from, valid_to, value_inc_vat FROM $db_tablename_9834 WHERE valid_from > '$start_date' AND valid_to < '$end_date' ORDER BY value_inc_vat DESC LIMIT $howmany;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $valid_from = $row["valid_from"];
            $data["results"][$valid_from] = $row["value_inc_vat"];
        }
    }
    $conn->close();
    return $data['results'];
}
function GetTodaysRatesFromDB($show_future_only)
{
    require './secrets.php';
    date_default_timezone_set('UTC');
    $conn = new mysqli($db_servername_8459, $db_username_2734, $db_password_1924, $db_name_9781) or die("Unable to Connect");
    if ($show_future_only) {
        $date = date("Y-m-d H:i:s");
        $sql = "SELECT * FROM $db_tablename_9834 WHERE valid_from >= '$date' ORDER BY valid_from;";
    } else {
        $date = date("Y-m-d");
        $sql = "SELECT * FROM $db_tablename_9834 WHERE valid_from >= '$date 00:00:00' AND valid_from <= '$date 23:59:00' ORDER BY valid_from;";
    }
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $valid_from = $row["valid_from"];
            $data["results"][$valid_from] = $row["value_inc_vat"];
        }
    }
    $conn->close();
    return $data['results'];
}
function CalculateCheapestWindow()
{
    require './secrets.php';
    date_default_timezone_set('UTC');
    $conn = new mysqli($db_servername_8459, $db_username_2734, $db_password_1924, $db_name_9781) or die("Unable to Connect");
    $sql = "SELECT ElectricPrices.entry_id, valid_to, AVG(value_inc_vat) OVER(ORDER BY valid_to ROWS BETWEEN CURRENT ROW AND 5 FOLLOWING) AS 4hR_Hour_wndw FROM ElectricPrices LEFT JOIN ( SELECT entry_id FROM ElectricPrices ORDER BY valid_to DESC LIMIT 5) AS Last_5 ON Last_5.entry_id = ElectricPrices.entry_id WHERE valid_to > NOW() AND Last_5.entry_id IS NULL ORDER BY 4hR_Hour_wndw;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $valid_to = $row["valid_to"];
            $data["results"][$valid_to] = $row["4hR_Hour_wndw"];
        }
    }
    $conn->close();
    return $data['results'];
}
function cookieChecker($cookiename)
{
    if (!isset($_COOKIE[$cookiename])) {
        $output = "NOT SET YET";
    } else {
        $output = $_COOKIE[$cookiename];
    }
    if ($output == "True") {
        (bool)$output = True;
    } elseif ($output == "False") {
        (bool)$output = False;
    }
    return $output;
}
