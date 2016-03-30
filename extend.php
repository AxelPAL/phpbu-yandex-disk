<?php
require 'src/Backup/Sync/YandexDisk.php';
try {
    phpbu\App\Factory::register('sync', 'yandex.disk', '\\AxelPAL\\Backup\\Sync\\YandexDisk');
} catch (Exception $e) {
    die($e->getMessage());
}