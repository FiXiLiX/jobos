-- Grant all privileges to laravel user from any host
GRANT ALL PRIVILEGES ON laravel.* TO 'laravel'@'%' IDENTIFIED BY 'secret';
FLUSH PRIVILEGES;
