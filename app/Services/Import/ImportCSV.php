<?php

namespace App\Services\Import;

class ImportCSV
{
	private string $directory = '/log';
	private string $filename = 'file';

	public function createCSV(array $data): string
	{
		$filename = $this->filename . '.csv';
		$saveDir = APP . $this->directory;
		if (!is_dir($saveDir)) mkdir($saveDir, 775, true);
		$path = "{$saveDir}/{$filename}";

		$fp = fopen($path, 'w');
		fputcsv($fp, array_keys($data[0]));
		foreach ($data as $i => $dataArray) {
			fputcsv($fp, array_values($dataArray));
		}
		fclose($fp);

		if (file_exists($path)) return $path;

		return false;
	}

	public function setDirectory(string $directory): self
	{
		$this->directory = preg_replace('/\/+$/', '', $directory);
		return $this;
	}

	public function setFilename(string $filename): self
	{
		$this->filename = trim($filename);
		return $this;
	}
}