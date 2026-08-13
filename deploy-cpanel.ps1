# ==========================================
# Laravel cPanel Zip Generator (Windows)
# ==========================================

$ProjectRoot = Get-Location
$ZipName = "laravel-cpanel-deploy.zip"
$ZipPath = Join-Path $ProjectRoot $ZipName
$TempDir = Join-Path $env:TEMP "laravel_build_tmp"

Write-Host "🚀 Starting Laravel Production Deployment Build..." -ForegroundColor Green

# 1. Clear previous zip and temp directory if they exist
if (Test-Path $ZipPath) { Remove-Item $ZipPath -Force }
if (Test-Path $TempDir) { Remove-Item $TempDir -Recurse -Force }

# 2. Compile Assets & Optimize Composer locally
Write-Host "📦 Compiling Frontend Assets & Optimizing Composer..." -ForegroundColor Yellow
npm run build
composer install --no-dev --optimize-autoloader --quiet

# 3. Clear Framework Caches
Write-Host "🧹 Clearing Laravel Framework Cache..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 4. Copy project files to a temporary build directory
Write-Host "📁 Copying files to temporary build directory..." -ForegroundColor Yellow
New-Item -ItemType Directory -Path $TempDir | Out-Null

$ExcludeItems = @(
    "node_modules",
    ".git",
    ".github",
    "tests",
    ".env",
    "$ZipName",
    "deploy-cpanel.ps1",
    "storage\logs\*.log",
    "storage\framework\cache\data\*"
)

Get-ChildItem -Path $ProjectRoot -Exclude $ExcludeItems | ForEach-Object {
    Copy-Item -Path $_.FullName -Destination $TempDir -Recurse -Force
}

# 5. INODE REDUCTION: Strip unnecessary documentation, tests, and markdown files from vendor/
Write-Host "🪓 Stripping vendor files to minimize cPanel Inodes..." -ForegroundColor Cyan

# Remove documentation/test folders inside vendor
$VendorFolderExclusions = @("tests", "test", "docs", "doc", "Documentation", "examples")
Get-ChildItem -Path "$TempDir\vendor" -Recurse -Directory | Where-Object { 
    $VendorFolderExclusions -contains $_.Name 
} | Remove-Item -Recurse -Force -ErrorAction SilentlyContinue

# Remove non-essential text & dev files inside vendor
$VendorFileExtensions = @("*.md", "*.txt", "LICENSE*", ".gitignore", ".gitattributes", "phpunit.xml*", "phpstan.neon*")
Get-ChildItem -Path "$TempDir\vendor" -Recurse -Include $VendorFileExtensions | Remove-Item -Force -ErrorAction SilentlyContinue

# 6. Compress clean project into ZIP
Write-Host "🗜️ Creating deployment ZIP file ($ZipName)..." -ForegroundColor Green
Compress-Archive -Path "$TempDir\*" -DestinationPath $ZipPath -CompressionLevel Optimal

# 7. Cleanup temp directory
Remove-Item $TempDir -Recurse -Force

Write-Host "`n=======================================================" -ForegroundColor Green
Write-Host " SUCCESS! Deployment package generated successfully:" -ForegroundColor Green
Write-Host " File: $ZipPath" -ForegroundColor White
Write-Host "=======================================================" -ForegroundColor Green