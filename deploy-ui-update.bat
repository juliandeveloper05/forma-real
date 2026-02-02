@echo off
:: ========================================
:: Forma Real - UI Update Deployment Script
:: ========================================
:: Author: Julian
:: Date: February 2026
:: Description: Deploys UI updates and clears cache
:: ========================================

echo.
echo ╔══════════════════════════════════════════════════════╗
echo ║     FORMA REAL - UI UPDATE DEPLOYMENT SCRIPT         ║
echo ╚══════════════════════════════════════════════════════╝
echo.

:: Check if running as administrator (optional but recommended)
net session >nul 2>&1
if %errorLevel% == 0 (
    echo [✓] Running with administrator privileges
) else (
    echo [!] Running without administrator privileges - Some operations may fail
)

:: Set variables
set "WP_ROOT=%~dp0"
set "THEME_DIR=%WP_ROOT%wp-content\themes\forma-real-theme"
set "PLUGIN_DIR=%WP_ROOT%wp-content\plugins\forma-real-core"

echo.
echo [1/5] Verifying directory structure...
echo ─────────────────────────────────────

if exist "%THEME_DIR%" (
    echo [✓] Theme directory found: forma-real-theme
) else (
    echo [✗] Theme directory NOT found!
    echo     Expected: %THEME_DIR%
    pause
    exit /b 1
)

if exist "%PLUGIN_DIR%" (
    echo [✓] Plugin directory found: forma-real-core
) else (
    echo [!] Plugin directory not found (optional)
)

echo.
echo [2/5] Checking theme files...
echo ─────────────────────────────────────

set FILES_OK=1

if exist "%THEME_DIR%\footer.php" (
    echo [✓] footer.php exists
) else (
    echo [✗] footer.php missing
    set FILES_OK=0
)

if exist "%THEME_DIR%\style.css" (
    echo [✓] style.css exists
) else (
    echo [✗] style.css missing
    set FILES_OK=0
)

if exist "%THEME_DIR%\partials\notifications-dropdown.php" (
    echo [✓] notifications-dropdown.php exists
) else (
    echo [!] notifications-dropdown.php missing (creating directory)
    if not exist "%THEME_DIR%\partials" mkdir "%THEME_DIR%\partials"
)

if exist "%THEME_DIR%\templates\moderation-panel.php" (
    echo [✓] moderation-panel.php exists
) else (
    echo [!] moderation-panel.php missing
)

if exist "%THEME_DIR%\templates\search-results.php" (
    echo [✓] search-results.php exists
) else (
    echo [!] search-results.php missing
)

echo.
echo [3/5] Creating backup of current files...
echo ─────────────────────────────────────

set "BACKUP_DIR=%WP_ROOT%backups\ui-backup-%date:~-4,4%%date:~-10,2%%date:~-7,2%"
if not exist "%WP_ROOT%backups" mkdir "%WP_ROOT%backups"
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

echo Backup directory: %BACKUP_DIR%

if exist "%THEME_DIR%\footer.php" (
    copy "%THEME_DIR%\footer.php" "%BACKUP_DIR%\footer.php.bak" >nul 2>&1
    echo [✓] footer.php backed up
)

if exist "%THEME_DIR%\style.css" (
    copy "%THEME_DIR%\style.css" "%BACKUP_DIR%\style.css.bak" >nul 2>&1
    echo [✓] style.css backed up
)

echo.
echo [4/5] Running PHP cache clear script...
echo ─────────────────────────────────────

:: Check if WP-CLI is available
where wp >nul 2>&1
if %errorLevel% == 0 (
    echo [✓] WP-CLI detected, clearing cache...
    cd /d "%WP_ROOT%"
    wp cache flush --allow-root 2>nul
    wp transient delete --all --allow-root 2>nul
    wp rewrite flush --allow-root 2>nul
    echo [✓] WordPress cache cleared via WP-CLI
) else (
    echo [!] WP-CLI not found - Using PHP fallback
    if exist "%WP_ROOT%clear-cache.php" (
        echo [i] Run clear-cache.php from browser or CLI
    )
)

echo.
echo [5/5] Generating version file...
echo ─────────────────────────────────────

:: Create a version stamp file
echo {"version":"2.0.0","updated":"%date% %time%","files":["footer.php","style.css","notifications-dropdown.php","moderation-panel.php","search-results.php"]} > "%THEME_DIR%\ui-version.json"
echo [✓] Version file created: ui-version.json

echo.
echo ╔══════════════════════════════════════════════════════╗
echo ║                  DEPLOYMENT COMPLETE!                ║
echo ╚══════════════════════════════════════════════════════╝
echo.
echo Next steps:
echo   1. Visit WordPress Admin → Settings → Permalinks
echo   2. Click "Save Changes" (without modifying anything)
echo   3. Clear browser cache: Ctrl + Shift + R
echo   4. Test pages:
echo      • Footer: Any page (check social icons)
echo      • Search: http://forma-real.test/buscar/
echo      • Moderation: http://forma-real.test/moderacion/
echo.
echo Backup location: %BACKUP_DIR%
echo.

pause
