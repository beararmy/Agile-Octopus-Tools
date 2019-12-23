<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
            $status_octopus = "true";
        } else {
            $status_octopus = "false";
            throw new Exception("MySQL Connection Failed");
        }
        $conn->close();
    } catch (\Throwable $th) {
        $status_octopus = "false";
        throw new Exception("MySQL Connection Failed");
    } finally {
        return $status_octopus;
    }
}
function GetUsage($api_key)
{
    $api_key = $api_key . ":";
    $url = "https://api.octopus.energy/v1/electricity-meter-points/1012953229244/meters/18P0906658/consumption/";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERPWD, "$api_key");
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    $json = json_decode($result, true);
    return $json;
}
function GetUpcomingPrices($api_key)
{
    $api_key = $api_key . ":";
    $url = "https://api.octopus.energy/v1/products/AGILE-18-02-21/electricity-tariffs/E-1R-AGILE-18-02-21-A/standard-unit-rates/";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERPWD, $api_key);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    $json = json_decode($result, true);
    return $json;
}
function GetStandingCharge($tariff_code)
{
    $url = "https://api.octopus.energy/v1/products/AGILE-18-02-21/electricity-tariffs/$tariff_code/standing-charges/";
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
        $sql = "INSERT INTO $db_tablename_9834 (valid_from, valid_to, value_exc_vat, value_inc_vat ) SELECT '$innerArray[valid_from]', '$innerArray[valid_to]','$innerArray[value_exc_vat]','$innerArray[value_inc_vat]' FROM DUAL WHERE NOT EXISTS (SELECT * FROM $db_tablename_9834 WHERE valid_from='$innerArray[valid_from]' AND valid_to='$innerArray[valid_to]' LIMIT 1);";
        $result = $conn->query($sql);
    }
    $conn->close();
}
function InsertRecentUsage($pricesArray)
{
    require './secrets.php';
    $conn = new mysqli($db_servername_8459, $db_username_2734, $db_password_1924, $db_name_9781) or die("Unable to Connect");
    foreach ($pricesArray['results'] as $row => $innerArray) {
        $sql = "INSERT INTO $db_tablename_9833 (interval_start, interval_end, consumption) SELECT '$innerArray[interval_start]', '$innerArray[interval_end]','$innerArray[consumption]' FROM DUAL WHERE NOT EXISTS (SELECT * FROM $db_tablename_9833 WHERE interval_start='$innerArray[interval_start]' AND interval_end='$innerArray[interval_end]' LIMIT 1);";
        $result = $conn->query($sql);
    }
    $conn->close();
}
function GetCurrentRate()
{
    require './secrets.php'; # DB Credentials
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
function GetHighestRate($howmany)
{
    $i = 0;
    if (!isset($howmany)) {
        $howmany = 5;
    }
    require './secrets.php'; # DB Credentials
    date_default_timezone_set('UTC');
    $start_date = date("Y-m-d");
    $end_date = new DateTime('+1 day');
    $end_date = $end_date->format('Y-m-d');
    $conn = new mysqli($db_servername_8459, $db_username_2734, $db_password_1924, $db_name_9781) or die("Unable to Connect");
    $sql = "SELECT valid_from, valid_to, value_inc_vat FROM $db_tablename_9834 WHERE valid_from > '$start_date' AND valid_to < '$end_date' ORDER BY value_inc_vat DESC LIMIT $howmany;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        print("<pre>" . print_r($result->   fetch_assoc(), true) . "</pre>");

        while ($row = $result->fetch_assoc()) {

            echo "<br>" . $row["value_inc_vat"];
            echo "<br>" . $row["valid_from"];
            echo "<br>" . $row["valid_to"];
            $i++;
            
            print("<pre>" . print_r($row, true) . "</pre>");

        }
}
    $conn->close();
   # return array('current_rate_per_kWh' => $value_inc_vat);

   }
   