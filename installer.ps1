# Set variables
$repoUrl = "https://github.com/KENnotcode/Voting-System.git"
$targetFolder = "$env:USERPROFILE\Voting-System"

Write-Host "📥 Cloning Voting System repository..." -ForegroundColor Cyan

# Check if Git is installed
if (-not (Get-Command git -ErrorAction SilentlyContinue)) {
    Write-Host "❌ Git is not installed. Please install Git first." -ForegroundColor Red
    Start-Process "https://git-scm.com/downloads"
    exit
}

# Remove existing folder if it exists
if (Test-Path $targetFolder) {
    Write-Host "⚠️ Folder already exists. Removing..." -ForegroundColor Yellow
    Remove-Item $targetFolder -Recurse -Force
}

# Clone the repo
git clone $repoUrl $targetFolder

# Confirm success
if (Test-Path $targetFolder) {
    Write-Host "✅ Repository cloned to $targetFolder" -ForegroundColor Green

    # Optional: Open the folder
    Start-Process $targetFolder

    # Optional: Run a setup script inside the repo (e.g., setup.ps1)
    $setupScript = Join-Path $targetFolder "setup.ps1"
    if (Test-Path $setupScript) {
        Write-Host "🚀 Running setup.ps1..." -ForegroundColor Cyan
        & $setupScript
    } else {
        Write-Host "ℹ️ No setup.ps1 found. Manual configuration may be required." -ForegroundColor Yellow
    }
} else {
    Write-Host "❌ Failed to clone repository." -ForegroundColor Red
}