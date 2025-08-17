# Set variables
$repoUrl = "https://github.com/KENnotcode/Voting-System.git"
$targetFolder = "C:\xampp\htdocs\Voting-System"
$xamppPath = "C:\xampp\htdocs"

Write-Host "📥 Preparing to clone Voting System repository..." -ForegroundColor Cyan

# Check if Git is installed
if (-not (Get-Command git -ErrorAction SilentlyContinue)) {
    Write-Host "❌ Git is not installed. Please install Git first." -ForegroundColor Red
    Start-Process "https://git-scm.com/downloads"
    exit
}

# Check if XAMPP is installed
if (-not (Test-Path $xamppPath)) {
    Write-Host "`n❌ You need to install XAMPP to clone this repository." -ForegroundColor Red
    Write-Host "Download XAMPP now?" -ForegroundColor Yellow
    $downloadPrompt = Read-Host "Type 'yes' to continue or 'no' to cancel"

    if ($downloadPrompt -eq "yes") {
        Write-Host "`nChoose your platform:" -ForegroundColor Cyan
        Write-Host "1. Windows"
        Write-Host "2. Linux"
        Write-Host "3. OS X"
        $platformChoice = Read-Host "Enter the number of your platform"

        switch ($platformChoice) {
            "1" {
                Write-Host "🔗 Opening XAMPP installer for Windows..." -ForegroundColor Green
                Start-Process "https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.12/xampp-windows-x64-8.2.12-0-VS16-installer.exe"
            }
            "2" {
                Write-Host "🔗 Opening XAMPP installer for Linux..." -ForegroundColor Green
                Start-Process "https://sourceforge.net/projects/xampp/files/XAMPP%20Linux/8.2.12/xampp-linux-x64-8.2.12-0-installer.run"
            }
            "3" {
                Write-Host "🔗 Opening XAMPP installer for OS X..." -ForegroundColor Green
                Start-Process "https://sourceforge.net/projects/xampp/files/XAMPP%20Mac%20OS%20X/8.2.4/xampp-osx-8.2.4-0-installer.dmg"
            }
            default {
                Write-Host "❌ Invalid selection. Exiting..." -ForegroundColor Red
            }
        }
    } else {
        Write-Host "❌ Installation cancelled. XAMPP is required to proceed." -ForegroundColor Red
    }
    exit
}

# Remove existing folder if it exists
if (Test-Path $targetFolder) {
    Write-Host "⚠️ Voting-System folder already exists. Removing..." -ForegroundColor Yellow
    Remove-Item $targetFolder -Recurse -Force
}

# Clone the repo
git clone $repoUrl $targetFolder

# Confirm success
if (Test-Path $targetFolder) {
    Write-Host "✅ Repository cloned to $targetFolder" -ForegroundColor Green

    # Optional: Open in File Explorer
    Start-Process $targetFolder

    # Optional: Launch in browser
    $url = "http://localhost/Voting-System"
    Write-Host "🌐 Opening $url in browser..." -ForegroundColor Cyan
    Start-Process $url
} else {
    Write-Host "❌ Failed to clone repository." -ForegroundColor Red
}