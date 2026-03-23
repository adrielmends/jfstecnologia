@echo off
echo Iniciando servidor PHP local em http://localhost:8000...
echo Pressione Ctrl+C para encerrar o servidor.
"C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" -d extension_dir="C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\ext" -d extension=pdo_sqlite -d extension=sqlite3 -d extension=curl -d extension=openssl -d mbstring.internal_encoding=UTF-8 -S localhost:8000 router.php
pause
