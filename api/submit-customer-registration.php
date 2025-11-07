<?php
/**
 * Customer Registration Form Submission Handler
 * Handles form data and file uploads for customer registration
 */

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Allow CORS if needed (remove in production if not required)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Validate required fields
    $errors = [];
    
    if (empty($_POST['first_name'])) {
        $errors[] = 'First name is required';
    }
    
    if (empty($_POST['last_name'])) {
        $errors[] = 'Last name is required';
    }
    
    if (empty($_POST['phone'])) {
        $errors[] = 'Phone number is required';
    } elseif (!preg_match('/^\d{10}$/', $_POST['phone'])) {
        $errors[] = 'Phone must be exactly 10 digits';
    }
    
    if (empty($_POST['email'])) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($_POST['address'])) {
        $errors[] = 'Address is required';
    }
    
    if (empty($_POST['nic'])) {
        $errors[] = 'NIC is required';
    } elseif (!preg_match('/^(?:\d{12}|\d{9}[VX])$/i', $_POST['nic'])) {
        $errors[] = 'NIC must be 12 digits or 9 digits + V/X';
    }
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }
    
    require_once __DIR__ . '/../admin/config/db.php';
    
    // Check if mobile number already exists
    $mobileCheckStmt = $pdo->prepare("SELECT id, customer_code, full_name FROM customers WHERE mobile = ?");
    $mobileCheckStmt->execute([$_POST['phone']]);
    $existingCustomer = $mobileCheckStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingCustomer) {
        echo json_encode([
            'success' => false, 
            'error' => 'A customer with this mobile number already exists',
            'existing_customer' => $existingCustomer
        ]);
        exit;
    }
    
    // Check if NIC already exists
    $nicCheckStmt = $pdo->prepare("SELECT id, customer_code, full_name FROM customers WHERE nic = ?");
    $nicCheckStmt->execute([strtoupper($_POST['nic'])]);
    $existingNIC = $nicCheckStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingNIC) {
        echo json_encode([
            'success' => false, 
            'error' => 'A customer with this NIC already exists',
            'existing_customer' => $existingNIC
        ]);
        exit;
    }
    
    // Generate next customer code
    $stmt = $pdo->query("SELECT customer_code FROM customers ORDER BY id DESC LIMIT 1");
    $lastCustomer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($lastCustomer) {
        $lastNumber = intval(substr($lastCustomer['customer_code'], 1));
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }
    
    $customerCode = 'C' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    
    // Handle file uploads
    $uploadDir = __DIR__ . '/../uploads/customers/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $nicFrontPath = null;
    $nicBackPath = null;
    $customerPhotoPath = null;
    
    // Process NIC Front
    if (isset($_FILES['nic_front']) && $_FILES['nic_front']['error'] === UPLOAD_ERR_OK) {
        $fileInfo = pathinfo($_FILES['nic_front']['name']);
        $extension = strtolower($fileInfo['extension']);
        
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $fileName = $customerCode . '_nic_front_' . time() . '.' . $extension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['nic_front']['tmp_name'], $targetPath)) {
                $nicFrontPath = 'uploads/customers/' . $fileName;
            }
        }
    }
    
    // Process NIC Back
    if (isset($_FILES['nic_back']) && $_FILES['nic_back']['error'] === UPLOAD_ERR_OK) {
        $fileInfo = pathinfo($_FILES['nic_back']['name']);
        $extension = strtolower($fileInfo['extension']);
        
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $fileName = $customerCode . '_nic_back_' . time() . '.' . $extension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['nic_back']['tmp_name'], $targetPath)) {
                $nicBackPath = 'uploads/customers/' . $fileName;
            }
        }
    }
    
    // Process Customer Photo
    if (isset($_FILES['customer_photo']) && $_FILES['customer_photo']['error'] === UPLOAD_ERR_OK) {
        $fileInfo = pathinfo($_FILES['customer_photo']['name']);
        $extension = strtolower($fileInfo['extension']);
        
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $fileName = $customerCode . '_photo_' . time() . '.' . $extension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['customer_photo']['tmp_name'], $targetPath)) {
                $customerPhotoPath = 'uploads/customers/' . $fileName;
            }
        }
    }
    
    // Construct full name
    $fullName = trim($_POST['first_name']) . ' ' . trim($_POST['last_name']);
    
    // Prepare insert statement
    $sql = "INSERT INTO customers (
                customer_code,
                first_name,
                last_name,
                full_name,
                full_name_with_surname,
                email,
                mobile,
                address,
                nic,
                gnd,
                lgi,
                police_station,
                occupation,
                residence_period,
                nic_front_path,
                nic_back_path,
                customer_photo_path,
                status,
                total_purchased,
                total_paid
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', 0.00, 0.00)";
    
    $params = [
        $customerCode,
        trim($_POST['first_name']),
        trim($_POST['last_name']),
        $fullName,
        !empty($_POST['full_name']) ? trim($_POST['full_name']) : null,
        trim($_POST['email']),
        trim($_POST['phone']),
        trim($_POST['address']),
        strtoupper(trim($_POST['nic'])),
        !empty($_POST['gnd']) ? trim($_POST['gnd']) : null,
        !empty($_POST['lgi']) ? trim($_POST['lgi']) : null,
        !empty($_POST['police_station']) ? trim($_POST['police_station']) : null,
        !empty($_POST['occupation']) ? trim($_POST['occupation']) : null,
        !empty($_POST['residence_period']) ? trim($_POST['residence_period']) : null,
        $nicFrontPath,
        $nicBackPath,
        $customerPhotoPath
    ];
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    $customerId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Customer registration completed successfully!',
        'customer_id' => $customerId,
        'customer_code' => $customerCode,
        'customer_name' => $fullName
    ]);
    
} catch (PDOException $e) {
    error_log('Customer registration error: ' . $e->getMessage());
    
    // Check for duplicate entry
    if ($e->getCode() == 23000) {
        echo json_encode([
            'success' => false,
            'error' => 'A customer with this information already exists'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ]);
    }
} catch (Exception $e) {
    error_log('Customer registration error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to register customer: ' . $e->getMessage()
    ]);
}
?>





