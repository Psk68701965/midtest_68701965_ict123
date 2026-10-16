<?php
$conn = new PDO(
    'mysql:host=127.0.0.1;dbname=student_db;charset=utf8mb4',
    'root',
    ''
);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$conn->exec("CREATE TABLE IF NOT EXISTS contacts (
    `student_id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
)");

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $body = file_get_contents("php://input");
    $payload = $body !== '' ? json_decode($body, true) : [];
    $action = $payload['action'] ?? null;

    if ($method === 'GET') {
        $action = $_GET['action'] ?? 'read';
    }

    if ($method === 'GET' || $action === 'read') {
        $stmt = $conn->prepare("SELECT * FROM students ORDER BY student_id DESC");
        $stmt->execute();
        echo json_encode([
            "success" => true,
            "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
        exit;
    }

    if ($method === 'POST' || $action === 'create') {
        $data = $payload;

        if (
            empty($data["student_id"]) ||
            empty($data["first_name"]) ||
            empty($data["last_name"]) ||
            empty($data["phone"]) ||
            empty($data["email"])
        ) {
            echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบ"]);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO students (student_id,first_name, last_name,phone, email)
                                VALUES (:student_id, :first_name, :last_name, :phone, :email)");

        $stmt->bindParam(":student_id", $data["student_id"]);
        $stmt->bindParam(":first_name", $data["first_name"]);
        $stmt->bindParam(":last_name", $data["last_name"]);
        $stmt->bindParam(":phone", $data["phone"]);
        $stmt->bindParam(":email", $data["email"]);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "เพิ่มข้อมูลนักศึกษาเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถเพิ่มข้อมูลนักศึกษาได้"]);
        }
        exit;
    }

    if ($method === 'PUT' || $action === 'update') {
        $data = $payload;

        if (!isset($data["student_id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบค่า student_id"]);
            exit;
        }

        $student_id = intval($data["student_id"]);

        $sql = "UPDATE students
                SET student_id = :student_id,
                    first_name = :first_name,
                    last_name = :last_name,
                    phone = :phone,
                    email = :email
                WHERE student_id = :current_student_id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":first_name", $data["first_name"]);
        $stmt->bindParam(":last_name", $data["last_name"]);
        $stmt->bindParam(":phone", $data["phone"]);
        $stmt->bindParam(":email", $data["email"]);
        $stmt->bindParam(":student_id", $student_id, PDO::PARAM_INT);
        $current_student_id = intval($data["current_student_id"] ?? $data["student_id"]);
        $stmt->bindParam(":current_student_id", $current_student_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "แก้ไขข้อมูลนักศึกษาเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถแก้ไขข้อมูลนักศึกษาได้"]);
        }
        exit;
    }

    if ($method === 'DELETE' || $action === 'delete') {
        $data = $payload;

        if (!isset($data["student_id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบค่า student_id"]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM students WHERE student_id = :student_id");
        $stmt->bindParam(":student_id", $data["student_id"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "ลบข้อมูลนักศึกษาเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถลบข้อมูลนักศึกษาได้"]);
        }
        exit;
    }

    echo json_encode(["success" => false, "message" => "Action ไม่ถูกต้อง"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
