# Build SafeG VMS Podman image (Windows)
#
#   powershell -File docker/build.ps1
#   $env:VMS_TAG='v.1.0.2'; powershell -File docker/build.ps1
#
# Push to Artifactory (Tailscale):
#   podman login bytespaceartifactory.tail61f14e.ts.net:8443
#   podman tag localhost/vms:v.1.0.2 bytespaceartifactory.tail61f14e.ts.net:8443/vms/vms:v.1.0.2
#   podman push bytespaceartifactory.tail61f14e.ts.net:8443/vms/vms:v.1.0.2
# Server:
#   VMS_IMAGE=bytespaceartifactory.tail61f14e.ts.net:8443/vms/vms:v.1.0.2 ./docker/upgrade-app.sh

$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot\..

if (-not (Test-Path 'vendor\autoload.php')) {
    Write-Host 'Running composer install --no-dev...'
    composer install --no-dev --optimize-autoloader --no-interaction
}

$tag = if ($env:VMS_TAG) { $env:VMS_TAG } else { 'v.1.0.2' }
$registry = if ($env:VMS_REGISTRY) { $env:VMS_REGISTRY } else { 'bytespaceartifactory.tail61f14e.ts.net:8443/vms/vms' }

Write-Host "Building localhost/vms:$tag ..."
podman build --network=host --format=docker `
  -t "localhost/vms:$tag" `
  -t localhost/vms:latest `
  -f Containerfile .

Write-Host "Done: localhost/vms:$tag (+ latest)"
Write-Host "Push:"
Write-Host "  podman login bytespaceartifactory.tail61f14e.ts.net:8443"
Write-Host "  podman tag localhost/vms:$tag ${registry}:$tag"
Write-Host "  podman push ${registry}:$tag"
