@echo off
set SCRIPT_PATH=%~dp0run_cron.ps1

echo Creating Windows Scheduled Task to run every 5 minutes...
SCHTASKS /Create /SC MINUTE /MO 5 /TN "GitHubEmailCron" /TR "powershell.exe -ExecutionPolicy Bypass -File \"%SCRIPT_PATH%\"" /F

echo Task Scheduled. It will run every 5 minutes.
pause
