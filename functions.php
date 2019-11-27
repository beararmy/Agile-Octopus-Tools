<?php
require './secrets.php'; # DB Credentials and Spotify API creds

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
        require './secrets.php'; # DB Credentials and Spotify API creds
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
