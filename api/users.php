<?php
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        // READ = SELECT
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch() ?: ['error' => 'Not found']);
        } else {
            $stmt = $pdo->query('SELECT * FROM users ORDER BY user_id DESC');
            echo json_encode($stmt->fetchAll());
        }
        break;

    case 'POST':
        // CREATE = INSERT
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['name']) || empty($data['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'name and email are required']);
            break;
        }
        try {
            $stmt = $pdo->prepare('INSERT INTO users (name, email) VALUES (?, ?)');
            $stmt->execute([$data['name'], $data['email']]);
            echo json_encode(['id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already exists']); // UNIQUE constraint violation
        }
        break;

    case 'PUT':
        // UPDATE
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'id is required']);
            break;
        }
        $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ? WHERE user_id = ?');
        $stmt->execute([$data['name'], $data['email'], $data['id']]);
        echo json_encode(['updated' => $stmt->rowCount()]);
        break;

    case 'DELETE':
        // DELETE
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'id is required']);
            break;
        }
        $stmt = $pdo->prepare('DELETE FROM users WHERE user_id = ?');
        $stmt->execute([$data['id']]);
        echo json_encode(['deleted' => $stmt->rowCount()]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
