<?php

namespace App\Database;

use App\Database\Contract\DatabaseContract;
use App\Services\Import\ImportCSV;

class Database implements DatabaseContract
{
	private \PDO $pdo;

	public function __construct()
	{
		$this->connect();
	}

	private function connect(): void
	{
		try {
			$config = $this->config();
			$this->pdo = new \PDO(
				"mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']}",
				$config['db_user'],
				$config['db_password']
			);
		} catch (\Exception $exception) {
			setLog($exception->getMessage());
			die('Ошибка подключения к БД! ' . $exception->getMessage());
		}
	}

	private function config(): array
	{
		$configPath = 'config/database.php';
		try {
			if (file_exists($configPath)) {
				return include $configPath;
			} else {
				throw new \Exception('Файл конфигрурации БД не получен!');
			}
		} catch (\Exception $exception) {
			setLog($exception->getMessage());
			die($exception->getMessage());
		}
	}

	public function insert(string $table, bool $fromRequest = false, array $data = []): int|false
	{
		if ($fromRequest && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$dataArray = array_map(function ($field) {
				return htmlspecialchars($field);
			}, $_POST);
		} else {
			$dataArray = $data;
		}

		if (!$dataArray) return false;
		$fields = array_keys($dataArray);
		$columns = implode(', ', $fields);
		if (!$columns) return false;

		$bindValues = implode(', ', array_map(fn($item) => ":{$item}", array_values($fields)));
		$query = "INSERT INTO {$table} ($columns) VALUES ($bindValues)";

		try {
			$sql = $this->pdo->prepare($query);
			$sql->execute($dataArray);
		} catch (\Exception $exception) {
			setLog($exception->getMessage());
			die('Пользователь с такими данными уже существует! ' . $exception->getMessage());
		}

		return $this->pdo->lastInsertId();
	}

	public function update(string $table, int $id, array $conditions = []): void
	{
		$set = '';
		if ($conditions) {
			$set = "SET " . implode(' AND ', array_map(function ($item) {
					return "$item = :$item";
				}, array_keys($conditions)));
		}

		$query = "UPDATE {$table} {$set} WHERE id = {$id}";

		$statement = $this->pdo->prepare($query);
		$statement->execute($conditions);

	}

	public function select(string $table, array $conditions = []): bool|array
	{
		$dbFields = '*';
		$where = '';

		if ($conditions) $where = "WHERE " . implode(' AND ', array_map(function ($item) {
				return "$item = :$item";
			}, array_keys($conditions)));

		$query = "SELECT {$dbFields} FROM {$table} {$where}";
		$statement = $this->pdo->prepare($query);
		$statement->execute($conditions);

		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getAnalytics(array $conditions = []): string|bool
	{
		$data = $this->select('users', $conditions);

		if ($data) {
			$excelImport = new ImportCSV();
			return $excelImport
				->setDirectory('/log')
				->setFilename('import')
				->createCSV($data);
		}
		return false;
	}
}