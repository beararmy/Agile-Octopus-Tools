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