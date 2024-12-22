<?php
// get_history.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

$servername = "localhost";
$dbname = 'detection_history';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    error_log('Database connection successful');

    $stmt = $pdo->query("
        SELECT * FROM detections 
        ORDER BY processed_at DESC 
        LIMIT 50
    ");

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log('Number of results fetched: ' . count($results));

    echo json_encode(['status' => 'success', 'data' => $results]);
} catch (PDOException $e) {
    http_response_code(500);
    error_log('Database error: ' . $e->getMessage()); // Log the error message
    echo json_encode(['status' => 'error', 'message' => 'Internal Server Error']);
}
?>