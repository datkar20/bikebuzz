$phpExe = 'D:\php\php.exe'

if (-not (Test-Path -LiteralPath $phpExe)) {
    Write-Host "Khong tim thay PHP tai $phpExe"
    Write-Host 'Hay cai PHP hoac sua bien $phpExe trong file start-bikebuzz.ps1.'
    exit 1
}

& $phpExe -d extension_dir=D:\php\ext -d extension=pdo_sqlite -d extension=sqlite3 -S localhost:8000
