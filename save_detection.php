<?php
// save_detection.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$dbname = 'detection_history';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents('php://input'), true);

    // Log the incoming data
    error_log('Incoming data: ' . print_r($data, true));

    if ($data['confidence'] >= 47) {
        $stmt = $pdo->prepare("
            INSERT INTO detections (detected_item, recommendations, confidence)
            VALUES (:detected_item, :recommendations, :confidence)
        ");

        $stmt->execute([
            'detected_item' => $data['class'],
            'recommendations' => $data['insights'],
            'confidence' => $data['confidence']
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Detection saved']);
    } else {
        echo json_encode(['status' => 'skipped', 'message' => 'Low confidence detection not saved']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    error_log('Database error: ' . $e->getMessage()); // Log the error message
    echo json_encode(['status' => 'error', 'message' => 'Internal Server Error']);
}
?>