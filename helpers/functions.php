<?php

function setLog(string $message, string $logFile = 'log.txt'): void
{
	$date = date('Y-m-d-m-Y-H-i-s');
	$logDirectory = APP . '/log';
	if(!is_dir($logDirectory)) mkdir($logDirectory, 775, true);
	$logMessage = "{$date} | {$message}; \n";
	file_put_contents("$logDirectory/$logFile", $logMessage, FILE_APPEND);
}
