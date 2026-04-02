<?php
    $host = "localhost";
    $password = "786786";
    $username = "root";
    $database = "crud";

    // CORS Headers to allow the frontend to communicate with the API
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    header("Content-Type: application/json");

    // Handle Preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["error" => "DB connection failed"]);
        exit;
    }

    $input = json_decode(file_get_contents("php://input"), true);
    $data = is_array($input) ? $input : $_POST;
    $operation = $data['operation'] ?? ($_GET['operation'] ?? null);

    // 1. CREATE / REGISTER
    if ($_SERVER["REQUEST_METHOD"] === "POST" && $operation === "1") {
        $password = password_hash($data["password"] ?? '', PASSWORD_BCRYPT);
        $email = trim($data["email"] ?? '');
        $name = trim($data["name"] ?? '');
        $phone = trim($data["phone"] ?? '');
        $role = trim($data["role"] ?? 'user');

        if (empty($email) || empty($name)) {
            echo json_encode(["error" => "Missing required fields"]);
            exit;
        }

        $checkEmail = $conn->prepare("SELECT id FROM crud WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            echo json_encode(["error" => "Email already registered"]);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO crud (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $password, $phone, $role);

        if ($stmt->execute()){
            echo json_encode(["message" => "User registered successfully"]);
        } else {
            echo json_encode(["error" => "Registration failed"]);
        }

    // 2. READ / RETRIEVE
    } else if ($_SERVER["REQUEST_METHOD"] === "GET" && $operation === "2") {
        $id = (int) ($_GET["id"] ?? 0);

        if ($id > 0) {
            $stmt = $conn->prepare("SELECT id, name, email, phone, role FROM crud WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                http_response_code(404);
                echo json_encode(["error" => "User not found"]);
            }
        } else {
            // List all users
            $result = $conn->query("SELECT id, name, email, phone, role FROM crud ORDER BY id DESC");
            $users = [];
            while($row = $result->fetch_assoc()) $users[] = $row;
            echo json_encode($users);
        }

    // 3. UPDATE
    } else if ($_SERVER["REQUEST_METHOD"] === "PUT") {
        parse_str(file_get_contents("php://input"), $put_vars);
        $operation = $put_vars['operation'] ?? '';

        if ($operation === "3") {
            $id = (int)($put_vars['id'] ?? 0);
            $name  = trim($put_vars['name'] ?? '');
            $email = trim($put_vars['email'] ?? '');
            $phone = trim($put_vars['phone'] ?? '');
            $role  = trim($put_vars['role'] ?? 'user');

            if ($id <= 0 || empty($name) || empty($email)) {
                http_response_code(400);
                echo json_encode(["error" => "Invalid input data"]);
                exit;
            }

            $stmt = $conn->prepare("UPDATE crud SET name = ?, email = ?, phone = ?, role = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $email, $phone, $role, $id);
            $stmt->execute();

            if ($stmt->affected_rows >= 0) {
                echo json_encode(["message" => "Updated successfully"]);
            } else {
                echo json_encode(["error" => "Update failed"]);
            }
        }

    // 4. DELETE
    } else if ($_SERVER["REQUEST_METHOD"] === "POST" && $operation === "4") {
        $id = (int) ($data["id"] ?? 0);
    
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid ID"]);
            exit;
        }
    
        $stmt = $conn->prepare("DELETE FROM crud WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        if ($stmt->affected_rows > 0) {
            echo json_encode(["message" => "User deleted successfully"]);
        } else {
            echo json_encode(["error" => "User not found or already deleted"]);
        }
    } else {
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
    }

    $conn->close();
?>