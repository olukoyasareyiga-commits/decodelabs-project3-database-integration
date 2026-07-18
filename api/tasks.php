<?php
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        // READ = SELECT, optionally filtered by user_id (1:many lookup)
        if (isset($_GET['user_id'])) {
            $stmt = $pdo->prepare('SELECT * FROM tasks WHERE user_id = ? ORDER BY task_id DESC');
            $stmt->execute([$_GET['user_id']]);
        } else {
            $stmt = $pdo->query('SELECT * FROM tasks ORDER BY task_id DESC');
        }
        echo json_encode($stmt->fetchAll());
        break;

    case 'POST':
        // CREATE = INSERT
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['user_id']) || empty($data['title'])) {
            http_response_code(400);
            echo json_encode(['error' => 'user_id and title are required']);
            break;
        }
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO tasks (user_id, title, status) VALUES (?, ?, ?)'
            );
            $stmt->execute([
                $data['user_id'],
                $data['title'],
                $data['status'] ?? 'pending',
            ]);
            echo json_encode(['id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid user_id or status']); // FK / CHECK violation
        }
        break;

    case 'PUT':
        // UPDATE = HTTP PUT = SQL UPDATE
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'id is required']);
            break;
        }
        $stmt = $pdo->prepare('UPDATE tasks SET title = ?, status = ? WHERE task_id = ?');
        $stmt->execute([$data['title'], $data['status'], $data['id']]);
        echo json_encode(['updated' => $stmt->rowCount()]);
        break;

    case 'DELETE':
        // DELETE = HTTP DELETE = SQL DELETE
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'id is required']);
            break;
        }
        $stmt = $pdo->prepare('DELETE FROM tasks WHERE task_id = ?');
        $stmt->execute([$data['id']]);
        echo json_encode(['deleted' => $stmt->rowCount()]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
