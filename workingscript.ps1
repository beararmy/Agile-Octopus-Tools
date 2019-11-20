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