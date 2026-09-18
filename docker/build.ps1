# Build SafeG VMS Podman image (Windows)
#
#   powershell -File docker/build.ps1
#   $env:VMS_TAG='v1.0.2'; powershell -File docker/build.ps1
#
# Push to Artifactory example:
#   podman tag localhost/vms:v1.0.2 bytespace.jfrog.io/vms/vms:v1.0.2
#   podman push bytespace.jfrog.io/vms/vms:v1.0.2
# Server:
#   VMS_IMAGE=bytespace.jfrog.io/vms/vms:v1.0.2 ./docker/upgrade-app.sh

$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot\..

if (-not (Test-Path 'vendor\autoload.php')) {
    Write-Host 'Running composer install --no-dev...'
    composer install --no-dev --optimize-autoloader --no-interaction
}

# Prefer Artifactory-style tags: v1.0.1 (also tag v.1.0.1 for older local refs)
$tag = if ($env:VMS_TAG) { $env:VMS_TAG } else { 'v1.0.1' }

Write-Host "Building localhost/vms:$tag ..."
podman build --network=host --format=docker `
  -t "localhost/vms:$tag" `
  -t "localhost/vms:v.$($tag.TrimStart('v'))" `
  -t localhost/vms:latest `
  -f Containerfile .

Write-Host "Done: localhost/vms:$tag (+ latest)"
Write-Host 'Start: powershell -File docker/start.ps1'
Write-Host "Push:  podman tag localhost/vms:$tag bytespace.jfrog.io/vms/vms:$tag"
Write-Host "       podman push bytespace.jfrog.io/vms/vms:$tag"
