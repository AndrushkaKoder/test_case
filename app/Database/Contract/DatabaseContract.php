<?php

namespace App\Database\Contract;

interface DatabaseContract
{
	public function insert(string $table, bool $fromRequest = false, array $data = []): int|false;

	public function update(string $table, int $id, array $conditions = []): void;

	public function select(string $table, array $conditions = []): bool|array;

	public function getAnalytics(array $conditions): bool|string;
}