<?php

// Connect to PostgreSQL using environment overrides where provided.
$hostname = getenv('PGHOST') ?: 'localhost';
$port = getenv('PGPORT') ?: '5432';
$database = getenv('PGDATABASE') ?: 'quran';
$username = getenv('PGUSER') ?: 'postgres';
$password = getenv('PGPASSWORD') ?: '1234';

$dsn = "pgsql:host={$hostname};port={$port};dbname={$database}";
$conn = new PDO($dsn, $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
?>