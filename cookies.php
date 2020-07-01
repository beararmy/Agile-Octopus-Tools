<?php

if ($_GET['submit'] == "yes") {
    $cookie_end_date = 2147471999; // one second before 32bit epoch dies ;.;
    setcookie("ELUSERNAME", $_POST['username'], $cookie_end_date, "/");
    setcookie("ELDAYSTOSHOW", $_POST['daystoshow'], $cookie_end_date, "/");
    setcookie("ELTIMEZONE", $_POST['timezone'], $cookie_end_date, "/");
    setcookie("ELNUMBEROFWINDOWS", $_POST['numberofwindows'], $cookie_end_date, "/");
    setcookie("SHOWALLFUTUREPRICES", $_POST['showalltimes'], $cookie_end_date, "/");
    header('Location: index.php');
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

if (!isset($_COOKIE['SHOWALLFUTUREPRICES'])) {
    $cookie_SHOWALLFUTUREPRICES = "False";
} else {
    $cookie_SHOWALLFUTUREPRICES = $_COOKIE['SHOWALLFUTUREPRICES'];
}

echo "<form action='cookies.php?submit=yes' method=post>";
echo "<label for='username'>Username:</label><br>
<input type='text' id='username' name='username' value=$cookie_ELUSERNAME><br>
<label for='showalltimes'>Show even past times for today (True/False)</label><br>
<input type='text' id='showalltimes' name='showalltimes' value=$cookie_SHOWALLFUTUREPRICES><br>
<label for='daystoshow'>Days to show:</label><br>
<input type='text' id='daystoshow' name='daystoshow' value=$cookie_ELDAYSTOSHOW><br>
<label for='timezone'>Timezone:</label><br>
<input type='text' id='timezone' name='timezone' value=$cookie_ELTIMEZONE><br>
<label for='numberofwindows'>Number of Windows:</label><br>
<input type='text' id='numberofwindows' name='numberofwindows' value=$cookie_ELNUMBEROFWINDOWS><br>";
echo "<input type='submit' value='Submit'></form>";
