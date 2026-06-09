@echo off
set PHP_EXE=D:\php\php.exe

if not exist "%PHP_EXE%" (
  echo Khong tim thay PHP tai %PHP_EXE%.
  echo Hay cai PHP hoac sua bien PHP_EXE trong file start-bikebuzz.bat.
  pause
  exit /b 1
)

"%PHP_EXE%" -d extension_dir=D:\php\ext -d extension=pdo_sqlite -d extension=sqlite3 -S localhost:8000
