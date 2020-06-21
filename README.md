# Agile-Octopus-Tools

Simple set of tools to aid management / user-friendliness for the agile octopus energy supplier. Focussed on Electric only.

If anyone is going to sign up, please use my referral link and we split £100.
https://share.octopus.energy/dusk-wave-115

## Features

The bulk of this code is written as functions which can be called in either an automated fashion (ie to periodically check in and get prices, then drop into a DB) or more point in time data (ie what is the current price).

- [x] Actively Validate mysql and octopus api connections
- [x] Centralised config in json or php
- [x] A way to nicely view data on kindle or ipad or an e-ink pi screen
- [x] Display Current price
- [x] Display most expensive few hours today
- [x] Display all of todays prices
- [x] Display Cheapest few hours today
- [x] Drop prices into mysql db for later analysis
- [x] Drop past consumption into mysql db
- [ ] Make it an Azure Function to update DB rather than curl against a webpage
- [ ] Email alerts for following day 'avoid at x, use lots at y'
- [ ] Make data visible to Home Assistant to allow device scheduling
- [x] Some form of integration with hue, high prices = red light kinda thing.

### Prerequisites

What things you need to install the software and how to install them

```
Working MySQL installation
PHP and your httpd of choice  (Personally, Apache)
```

### Installing

Clone the repo

Configure your DB using DB-setup.sql helper

Update your secrets.php file with your chosen settings as per local comments

Add a cronjob to curl the automated version of the webpage as example in example.cron

visit index2.php for nicely visible data once enough has been pulled in to database.

## Built With

* [PHP 7.3.12](https://www.php.net/releases/7_3_12.php)
* [Apache 2.4.6](https://httpd.apache.org/download.cgi)
* [MariaDB 5.5.60](https://mariadb.com/kb/en/mariadb-5560-release-notes/)

## Function call examples

```php
// Test: function TestOctopusLogin
$call_url = $base . $emeter . $elec_mpan . "/";
$status_octopus = TestOctopusLogin($api_key, $call_url);
```

```php
// Test: function TestMySQLLogin
$status_octopus = TestMySQLLogin();
```

```php
// Test: function GetUsage
print("<pre>" . print_r(GetUsage(), true) . "</pre>");
```

```php
// Test: function GetUpcomingPrices
print("<pre>" . print_r(GetUpcomingPrices($api_key), true) . "</pre>");
```

```php
// Test: function InsertUpcomingPrices
$pricesArray = GetUpcomingPrices($api_key);
InsertUpcomingPrices($pricesArray);
```

```php
// Test: function InsertRecentUsage
$pricesArray = GetUsage($api_key);
InsertRecentUsage($pricesArray);
```

```php
// Test: function GetTotalCost
$start_date = "2020-01-03";
$end_date = "2020-01-05";
print("<pre>" . print_r(GetTotalCost($start_date, $end_date), true) . "</pre>");
```

```php
// Test: function GetCurrentRate
print("<pre>" . print_r(GetCurrentRate(), true) . "</pre>");
echo GetCurrentRate()['current_rate_per_kWh'];
```

```php
// Test: function GetHighestRate
print("<pre>" . print_r(GetHighestRate('10'), true) . "</pre>");
```

```php
// Test: function GetUpcomingPrices
print("<pre>" . print_r(GetUpcomingPrices($api_key)['results'], true) . "</pre>");
```

```php
// Test: function GetDaysRatesFromDB
print("<pre>" . print_r(GetDaysRatesFromDB(), true) . "</pre>");
```

## Contributing

Pull requests and issues welcome to guide development!

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags). 

## Authors

* **Stefan Harrington-Palmer**

See also the list of [contributors](hhttps://github.com/beararmy/Agile-Octopus-Tools/graphs/contributors) who participated in this project.

## Acknowledgments

* [Octopus](https://share.octopus.energy/dusk-wave-115), who's devs have been great at answering questions I have.
