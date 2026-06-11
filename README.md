# скопировать в systemd
sudo cp /var/www/test.com/test-app/laravel-worker.service /etc/systemd/system/laravel-worker.service

# перечитать конфиги systemd
sudo systemctl daemon-reload

# включить автозапуск при старте
sudo systemctl enable laravel-worker

# запустить
sudo systemctl start laravel-worker

# проверить статус
sudo systemctl status laravel-worker
