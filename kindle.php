<?php
echo "<html>
<head>
<title>Electric</title>
</head>
<meta http-equiv=refresh content=30>";

# High Prices, most expensive N today
echo "<h2>High Prices today:</h2>";

# Low Prices, cheapest N today

echo "<h2>Low Prices today:</h2>";
echo "<br><br>";

echo "Today is " . date("h:i:sa");

function TestMySQLLogin()
{
    try {
        require './secrets.php'; # DB Credentials and Spotify API creds
        $conn = new mysqli($db_servername_8459, $db_username_2734, $db_password_1924, $db_name_9781) or die("Unable to                                                                                                                 Connect");
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

