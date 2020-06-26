<?php

$cookie_end_date = 2147471999; // one second before 32bit epoch dies ;.;

if (!isset($_COOKIE[$cookie_USERNAME])) {
    $cookie_USERNAME = "Stef";
    setcookie("EL-USERNAME", $cookie_USERNAME, $cookie_end_date, "/");
}

if (!isset($_COOKIE[$cookie_DAYSTOSHOW])) {
    $cookie_DAYSTOSHOW = 3; // Number of days of recent data to show
    setcookie("EL-DAYSTOSHOW", $cookie_DAYSTOSHOW, $cookie_end_date, "/");
}

if (!isset($_COOKIE[$cookie_TIMEZONE])) {
    $cookie_TIMEZONE = "Europe/London"; // TZ
    setcookie("EL-TIMEZONE", $cookie_TIMEZONE, $cookie_end_date, "/");
}

if (!isset($_COOKIE[$cookie_NUMBEROFWINDOWS])) {
    $cookie_NUMBEROFWINDOWS = 3; // Number of 3hr windows to show
    setcookie("EL-DAYSTOSHOW", $cookie_NUMBEROFWINDOWS, $cookie_end_date, "/");
}
