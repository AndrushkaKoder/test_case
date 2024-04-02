<?php
ini_set('error_reporting', 1);
define('APP', dirname(__DIR__ . '/app'));

require_once "vendor/autoload.php";
require_once "helpers/functions.php";

use App\Database\Database;

$db = new Database();

$data = [
	'name' => 'Admin',
	'login' => 'admin_admin',
	'password' => md5('password'),
	'birthday' => date('Y-m-d'),
];

#Добавить пользователя
$id = $db->insert('users', false, $data);

#Обновить данные по id
$db->update('users', 1, [
	'name' => 'new_name'
]);

#Выбрать записи по условиям
$res = $db->select('users', [
	'name' => 'new_name'
]);

#собрать аналитику по условиям. В переменную возвращется путь до файла.
# По дефолту csv файл сохранится в /log/analytics.csv
$analytics = $db->getAnalytics(['active' => 1]);

