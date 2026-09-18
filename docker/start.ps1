# Start SafeG VMS via Podman Compose (Windows)
# Usage: powershell -File docker/start.ps1
#
# App config: docker/.env (real CI4 .env)
# Ports:     docker/compose.env (APP_PORT — local often 8081; server uses 80)

$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot\..

$image = 'localhost/vms:v1.0.1'
if (-not (podman images -q $image)) {
    if (podman images -q 'localhost/vms:v.1.0.1') {
        $image = 'localhost/vms:v.1.0.1'
    } else {
        Write-Host 'Image missing — running docker/build.ps1 ...'
        & "$PSScriptRoot\build.ps1"
        $image = 'localhost/vms:v1.0.1'
    }
}

if (-not (Test-Path 'docker\.env')) {
    if (Test-Path '.env') {
        Write-Host 'Creating docker/.env from project .env ...'
        Copy-Item '.env' 'docker\.env'
        $c = Get-Content 'docker\.env'
        $c = $c | ForEach-Object {
            if ($_ -match '^\s*database\.default\.hostname\s*=') { 'database.default.hostname = db' }
            elseif ($_ -match '^\s*database\.default\.username\s*=') { 'database.default.username = vms' }
            elseif ($_ -match '^\s*database\.default\.password\s*=') { 'database.default.password = vms' }
            elseif ($_ -match '^\s*#?\s*CI_ENVIRONMENT\s*=') { 'CI_ENVIRONMENT = production' }
            elseif ($_ -match '^\s*#?\s*app\.baseURL\s*=') { "app.baseURL = 'http://127.0.0.1:8081/'" }
            else { $_ }
        }
        $c | Set-Content 'docker\.env'
        Write-Host 'Edit docker/.env if needed (baseURL, MAIL_*, LLM_*, encryption.key).'
    } else {
        throw 'docker/.env missing. Copy your CI4 .env to docker/.env (see docker/.env.example).'
    }
}

$envFile = 'docker/compose.env'
if (-not (Test-Path $envFile)) {
    Copy-Item 'docker/compose.env.example' $envFile
    # Windows: avoid privileged/busy port 80 — use 8081 by default locally
    (Get-Content $envFile) -replace 'APP_PORT=80', 'APP_PORT=8081' | Set-Content $envFile
    Write-Host "Created $envFile (APP_PORT=8081 for Windows)."
}

podman compose --env-file $envFile up -d

$ip = $null
for ($i = 1; $i -le 30; $i++) {
    $ip = (podman inspect vms-app --format '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' 2>$null)
    if ($ip) { $ip = $ip.Trim(); if ($ip) { break } }
    Start-Sleep -Seconds 1
}
if (-not $ip) {
    throw 'vms-app has no IP yet — wait a few seconds and re-run.'
}

$key = Join-Path $env:USERPROFILE '.local\share\containers\podman\machine\machine'
$appPort = 8081
$line = Get-Content $envFile | Where-Object { $_ -match '^\s*APP_PORT\s*=' } | Select-Object -First 1
if ($line -match '=\s*(\d+)') { $appPort = [int]$Matches[1] }

$healthy = $false
try {
    curl.exe -sI --max-time 3 "http://127.0.0.1:$appPort/" 1>$null 2>$null
    if ($LASTEXITCODE -eq 0) { $healthy = $true }
} catch {}

if (-not $healthy) {
    Get-NetTCPConnection -LocalPort $appPort -State Listen -ErrorAction SilentlyContinue |
        Select-Object -ExpandProperty OwningProcess -Unique |
        ForEach-Object {
            Write-Host "Stopping stale listener PID $_ on :$appPort"
            Stop-Process -Id $_ -Force -ErrorAction SilentlyContinue
        }
    Start-Sleep -Seconds 1

    Write-Host "Opening SSH tunnel 127.0.0.1:$appPort -> ${ip}:80 ..."
    Start-Process -FilePath ssh -ArgumentList @(
        '-N',
        '-o', 'StrictHostKeyChecking=no',
        '-o', 'UserKnownHostsFile=NUL',
        '-i', $key,
        '-p', '35457',
        "-L", "${appPort}:${ip}:80",
        'user@127.0.0.1'
    ) -WindowStyle Hidden
    Start-Sleep -Seconds 2
}

Write-Host "App:        http://127.0.0.1:$appPort"
Write-Host 'phpMyAdmin: http://127.0.0.1:8082'
Write-Host 'Env:        docker/.env (CI4 original format)'
Write-Host 'DB:         localhost:3307 (user/pass/db = vms)'
