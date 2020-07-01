<?php

if ($_GET['submit'] == "yes") {
    $cookie_end_date = 2147471999; // one second before 32bit epoch dies ;.;
    setcookie("ELUSERNAME", $_POST['username'], $cookie_end_date, "/");
    setcookie("ELDAYSTOSHOW", $_POST['daystoshow'], $cookie_end_date, "/");
    setcookie("ELTIMEZONE", $_POST['timezone'], $cookie_end_date, "/");
    setcookie("ELNUMBEROFWINDOWS", $_POST['numberofwindows'], $cookie_end_date, "/");
}


if (!isset($_COOKIE['ELUSERNAME'])) {
    $cookie_ELUSERNAME = "CHANGEME";
} else {
    $cookie_ELUSERNAME = $_COOKIE['ELUSERNAME'];
}

if (!isset($_COOKIE['ELDAYSTOSHOW'])) {
    $cookie_ELDAYSTOSHOW = "3";
} else {
    $cookie_ELDAYSTOSHOW = $_COOKIE['ELDAYSTOSHOW'];
}

if (!isset($_COOKIE['ELTIMEZONE'])) {
    $cookie_ELTIMEZONE = "Europe/London";
} else {
    $cookie_ELTIMEZONE = $_COOKIE['ELTIMEZONE'];
}

if (!isset($_COOKIE['ELNUMBEROFWINDOWS'])) {
    $cookie_ELNUMBEROFWINDOWS = "3";
} else {
    $cookie_ELNUMBEROFWINDOWS = $_COOKIE['ELNUMBEROFWINDOWS'];
}

echo "<form action='cookies.php?submit=yes' method=post>";

echo "<label for='username'>Username:</label><br>
<input type='text' id='username' name='username' value=$cookie_ELUSERNAME><br>
<label for='daystoshow'>Days to show:</label><br>
<input type='text' id='daystoshow' name='daystoshow' value=$cookie_ELDAYSTOSHOW><br>
<label for='timezone'>Timezone:</label><br>
<input type='text' id='timezone' name='timezone' value=$cookie_ELTIMEZONE><br>
<label for='numberofwindows'>Number of Windows:</label><br>
<input type='text' id='numberofwindows' name='numberofwindows' value=$cookie_ELNUMBEROFWINDOWS><br>";

echo "<input type='submit' value='Submit'></form>";

// $cookie_end_date = 2147471999; // one second before 32bit epoch dies ;.;

// if (!isset($_COOKIE[$cookie_USERNAME])) {
//     $cookie_USERNAME = "Stef";
//     // setcookie("EL-USERNAME", $cookie_USERNAME, $cookie_end_date, "/");
// }

// if (!isset($_COOKIE[$cookie_DAYSTOSHOW])) {
//     $cookie_DAYSTOSHOW = 3; // Number of days of recent data to show
//     // setcookie("EL-DAYSTOSHOW", $cookie_DAYSTOSHOW, $cookie_end_date, "/");
// }

// if (!isset($_COOKIE[$cookie_TIMEZONE])) {
//     $cookie_TIMEZONE = "Europe/London"; // TZ
//     // setcookie("EL-TIMEZONE", $cookie_TIMEZONE, $cookie_end_date, "/");
// }

// if (!isset($_COOKIE[$cookie_NUMBEROFWINDOWS])) {
//     $cookie_NUMBEROFWINDOWS = 3; // Number of 3hr windows to show
//     // setcookie("EL-NUMBEROFWINDOWS", $cookie_NUMBEROFWINDOWS, $cookie_end_date, "/");
// }
