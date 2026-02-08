<?php
    $host = "localhost";
    $password = "786786";
    $username = "root";
    $database = "crud";
    $message = "";
    header("Content-Type: application/json");

    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["error" => "DB connection failed"]);
        exit;
    }
    $input = json_decode(file_get_contents("php://input"), true);

    // Fallback for form-data
    $data = is_array($input) ? $input : $_POST;

    $operation = $data['operation'] ?? null;

        

    if ($_SERVER["REQUEST_METHOD"] === "POST" && $operation === "1") {
        $password = password_hash($data["password"], PASSWORD_BCRYPT);
        $email = trim($data["email"] ?? '');
        $name = trim($data["name"] ?? '');
        $phone = trim($data["phone"] ?? '');
        $role = trim($data["role"] ?? 'user');


        $checkEmail = $conn->prepare("SELECT id FROM crud WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            echo json_encode(["error" => "Email already registered"]);
            exit;
        }

        // $task = $_POST["operation"];
        // if($_POST["operation"] === '1'){
            $stmt = $conn->prepare("INSERT INTO crud (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)" );
            $stmt->bind_param("sssss", $name, $email, $password, $phone, $role);
            if ($stmt->execute()){
                echo json_encode(["message" => "Registration successful"]);
            }else{
                echo json_encode(["error" => "Registration failed"]);
            }

        }else if ($_SERVER["REQUEST_METHOD"] === "GET" && ($_GET["operation"] ?? null) === "2") {

            $id = (int) ($_GET["id"] ?? 0);
        
            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(["error" => "Invalid ID"]);
                exit;
            }
        
            $check = $conn->prepare(
                "SELECT name, email, phone, role FROM crud WHERE id = ?"
            );
            $check->bind_param("i", $id);
            $check->execute();
            $result = $check->get_result();
        
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                http_response_code(404);
                echo json_encode(["error" => "User not found"]);
            }
        
        

    }else if($_SERVER["REQUEST_METHOD"] === "PUT" ){
        // if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            // Read the raw input stream
            $putdata = file_get_contents("php://input");
        
            // Parse the URL-encoded string into an associative array
            parse_str($putdata, $put_vars);
            $operation = $put_vars['operation'] ?? '';
        
            if($operation === "3"){ 
                    // Now you can access the data using the $put_vars array
                    $id = (int)($put_vars['id'] ?? 0);
                    $name  = trim($put_vars['name'] ?? '');
                    $email = trim($put_vars['email'] ?? '');
                    $phone = trim($put_vars['phone'] ?? '');
                    $role  = trim($put_vars['role'] ?? 'user');

                
                if ($id <= 0 || $name === '' || $email === '') {
                    http_response_code(400);
                    echo json_encode(["error" => "Invalid ID"]);
                    exit;
                }
                $check = $conn->prepare(
                    "UPDATE crud
                    SET name = ?, email = ?, phone = ?, role = ?
                    WHERE id = ?;" 
                );
                $check->bind_param("ssssi", $name, $email, $phone, $role, $id);
                // $check->execute();
                // $result= $check->get_result();
                $check->execute();

                if ($check->affected_rows > 0) {
                    echo json_encode(["message" => "Updated successfully"]);
                } else {
                    echo json_encode(["error" => "User not found or no changes made"]);
                }
            }
            
    }else if ($_SERVER["REQUEST_METHOD"] === "POST" && $operation === "4") {

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
            echo json_encode(["message" => "Deleted successfully"]);
        } else {
            echo json_encode(["error" => "User not found"]);
        }
    }
    
    else 
        {
                http_response_code(405);
                echo json_encode(["error" => "Method not allowed"]);
            
        
        }


        $conn->close(); 


?>