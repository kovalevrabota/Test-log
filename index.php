<?php
use Test\Log\Log;

require_once __DIR__ .'/autoload.php';

try {
    $log = new Log(__DIR__ . '/test_task.log');

    $search = $log->search($key = 'phone', $values = array('+79997779977', '+79998889988'));

    echo '<pre>';
    foreach ($search as $record) {
        echo $record;
    }
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}";
}