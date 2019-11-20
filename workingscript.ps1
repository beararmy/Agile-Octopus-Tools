# API documentation here:
# https://octopus.energy/dashboard/developer/

$ErrorActionPreference = "Stop"
$VerbosePreference = "Continue"
$configlocation = ".\config.json"

# Gotta git me some config
$config = Get-Content -Path $configlocation -Raw | ConvertFrom-Json

# Build authentication header because not curl.
$bytes = [System.Text.Encoding]::ASCII.GetBytes($config.user_specific.api_key)
$base64 = [System.Convert]::ToBase64String($bytes)
$basicAuthValue = "Basic $base64"
$headers = @{ Authorization = $basicAuthValue }
function Test-OctopusLogin {
    # Do a small query to test that we get a non-error http code back.
    param (
        $headers
    )

    $config = Get-Content -Path $configlocation -Raw | ConvertFrom-Json
    $query = $config.api_endpoints.base + $config.api_endpoints.emeter + $config.user_specific.elec_mpan + "/"

    try {
        $result = ( Invoke-WebRequest -Uri $query -Headers $headers )
    }
    catch {
        Write-Error "HTTP CODE $($result.StatusCode)"
        return $false
    }
    return $true
}
function Get-OctopusUpcomingRates {
    param (
        [bool]$todayonly,
        [bool]$tomorrowonly
    )
    #  Clear-Variable result
    $config = Get-Content -Path $configlocation -Raw | ConvertFrom-Json

    # Validate key works
    try {
        Test-OctopusLogin
    }
    catch {
        Write-Host "API Key appears invalid"
        return $false
    }

    # Build URI
    $uri = $config.api_endpoints.base + $config.api_endpoints.upcomingagilerate

    # Do the stuff
    try {
        $result = ( Invoke-RestMethod -Uri $uri -Headers $headers )
        $result = $result.results
    }
    catch {
        Write-Error "Something failed getting rates"
        return $false
    }

    if ( $todayonly ) {
        # Get today's date in UTC.
        $dt = ( Get-Date ).ToUniversalTIme()
        $today = Get-Date -Format "yyyy-MM-dd" -Date $dt
        $result = ( $result | Where-Object -Property valid_from -Like "*$today*" )
    }

    if ( $tomorrowonly ) {
        if ( $config.octopus_configs.agile_reset_time_utc0 -gt ( Get-Date -Format "HH:mm" ) ) {
            Write-Error "Tomorrow values are available as of $($config.octopus_configs.agile_reset_time_utc0), try back later"
            return $false
        }

        # Get tomorrow's date in UTC.
        $dt = ( Get-Date ).AddDays(1).ToUniversalTIme()
        $tomorrow = Get-Date -Format "yyyy-MM-dd" -Date $dt
        $result = ( $result | Where-Object -Property valid_from -Like "*$tomorrow*" )
    }

    if ( !$result ) {
        write-host "No data returned"
    }

    #change the order and tidy up.

    return $result
}