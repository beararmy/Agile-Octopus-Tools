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