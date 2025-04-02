<?php

$dbHost = 'localhost';
$dbUser = 'project';
$dbPass = 'project';
$dbName = 'svcet_educnt';

// Create a connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Path to mysqldump
$mysqldumpPath = 'E:\\xampp\\mysql\\bin\\mysqldump.exe'; // Update path if necessary

// Create a temporary file to store the SQL
$tempFile = tempnam(sys_get_temp_dir(), 'mysql_dump_');

// Build the mysqldump command
$command = "\"$mysqldumpPath\" -u " . escapeshellarg($dbUser) . " -p" . escapeshellarg($dbPass) . " " . escapeshellarg($dbName) . " > " . escapeshellarg($tempFile);

// Execute the command
$output = shell_exec($command);

// Check for errors
if (!file_exists($tempFile) || filesize($tempFile) === 0) {
    echo "Error during mysqldump: " . $output;
} else {
    // Download the SQL file
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename="database_backup.sql"');
    header('Content-Type: application/sql');
    header('Content-Length: ' . filesize($tempFile));
    readfile($tempFile);

    // Clean up the temporary file
    unlink($tempFile);
}

$conn->close();
