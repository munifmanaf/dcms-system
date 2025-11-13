# OAI-PMH Test Script for DCMS - dcms-dev.test (PowerShell Version)

$BASE_URL = "http://dcms-dev.test/oai"

Write-Host "🧪 OAI-PMH Protocol Test Suite - dcms-dev.test" -ForegroundColor Green
Write-Host "Testing URL: $BASE_URL"
Write-Host ""

# Test 1: Identify
Write-Host "1. Testing Identify verb:" -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "$BASE_URL?verb=Identify" -UseBasicParsing
    Write-Host "SUCCESS: HTTP $($response.StatusCode)" -ForegroundColor Green
    Write-Host "Response:"
    $response.Content
} catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
}
