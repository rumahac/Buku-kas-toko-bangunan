<?php
require_once 'auth.php';
require_once 'Security.php';

// Cek CSRF untuk semua request POST (kecuali public endpoints)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['csrf_token'])) {
    if (!Security::validateCSRFToken($_GET['csrf_token'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
        exit;
    }
}

header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    switch ($action) {
        case 'get_transactions':
            getTransactions();
            break;
        case 'get_categories':
            getCategories();
            break;
        case 'save_transaction':
            saveTransaction();
            break;
        case 'delete_transaction':
            deleteTransaction();
            break;
        case 'pay_installment':
            payInstallment();
            break;
        case 'get_payment_history':
            getPaymentHistory();    
            break;
         case 'get_all_users':
        if ($_SESSION['role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            break;
        }
        echo json_encode(getAllUsers());
        break;

    case 'get_user':
        if ($_SESSION['role'] !== 'admin') {
            echo json_encode(['error' => 'Unauthorized']);
            break;
        }
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            echo json_encode(['error' => 'ID user diperlukan']);
            break;
        }
        $user = getUserById($id);
        if ($user) {
            echo json_encode($user);
        } else {
            echo json_encode(['error' => 'User tidak ditemukan']);
        }
        break;

    case 'add_user':
        if ($_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            break;
        }
        $result = addUser(
            $_POST['username'] ?? '',
            $_POST['password'] ?? '',
            $_POST['name'] ?? '',
            $_POST['email'] ?? null,
            $_POST['role'] ?? 'operator',
            $_POST['status'] ?? 'active'
        );
        if ($result === true) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $result]);
        }
        break;

    case 'update_user':
        if ($_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            break;
        }
        $success = updateUser(
            $_POST['id'] ?? 0,
            $_POST['name'] ?? '',
            $_POST['email'] ?? null,
            $_POST['role'] ?? 'operator',
            $_POST['status'] ?? 'active'
        );
        echo json_encode(['success' => $success]);
        break;

    case 'reset_password':
        if ($_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            break;
        }
        $success = resetUserPassword($_POST['id'] ?? 0, $_POST['new_password'] ?? '');
        echo json_encode(['success' => $success]);
        break;

    case 'delete_user':
        if ($_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            break;
        }
        $result = deleteUser($_POST['id'] ?? 0);
        if ($result === true) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $result]);
        }
        break;

        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function getTransactions() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM transactions ORDER BY date DESC");
    $transactions = $stmt->fetchAll();
    // Format data agar kompatibel dengan JavaScript (camelCase)
    $result = array_map(function($t) {
        return [
            'id' => (int)$t['id'],
            'date' => $t['date'],
            'desc' => $t['description'],
            'type' => $t['type'],
            'category' => $t['category'],
            'status' => $t['status'],
            'amount' => (float)$t['amount'],
            'paidAmount' => (float)$t['paid_amount'],
            'dueDate' => $t['due_date'],
            'image' => $t['image_path'],
            'notes' => $t['notes'],
            'created_by' => $t['created_by']
        ];
    }, $transactions);
    echo json_encode($result);
}

function getCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT name, type FROM categories WHERE status = 'active' ORDER BY name");
    $rows = $stmt->fetchAll();
    $cats = ['income' => [], 'expense' => []];
    foreach ($rows as $row) {
        $cats[$row['type']][] = $row['name'];
    }
    echo json_encode($cats);
}

function saveTransaction() {
    global $pdo;

    // Baca semua input
    $id = $_POST['id'] ?? null;
    $date = $_POST['date'] ?? date('Y-m-d');
    $desc = $_POST['desc'] ?? '';
    $type = $_POST['type'] ?? 'expense';
    $category = $_POST['category'] ?? '';
    $status = $_POST['status'] ?? 'lunas';
    $amount = (float) ($_POST['amount'] ?? 0);
    $paidAmount = (float) ($_POST['paidAmount'] ?? 0);
    $dueDate = $_POST['dueDate'] ?? null;
    if ($dueDate === 'null' || $dueDate === '') {
        $dueDate = null;
    }
    $notes = $_POST['notes'] ?? null;
    $userId = $_SESSION['user_id'] ?? 1; // fallback

    // Validasi
    if ($amount <= 0) {
        echo json_encode(['success' => false, 'error' => 'Jumlah harus lebih dari 0']);
        return;
    }

    // Upload gambar
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $destination = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $imagePath = $destination;
        }
    }

    if ($id) {
        // UPDATE
        $sql = "UPDATE transactions SET 
                date = ?, description = ?, type = ?, category = ?, status = ?,
                amount = ?, paid_amount = ?, due_date = ?, notes = ?,
                image_path = COALESCE(?, image_path), updated_at = NOW()
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$date, $desc, $type, $category, $status, $amount, $paidAmount, $dueDate, $notes, $imagePath, $id]);
    } else {
        // INSERT
        $sql = "INSERT INTO transactions 
                (date, description, type, category, status, amount, paid_amount, due_date, notes, image_path, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$date, $desc, $type, $category, $status, $amount, $paidAmount, $dueDate, $notes, $imagePath, $userId]);
        $id = $pdo->lastInsertId();
    }

    // Catat payment history jika ada perubahan paidAmount pada cicilan
    if ($status === 'cicilan' && $paidAmount > 0) {
        // Ambil paid_amount sebelumnya
        $oldPaid = 0;
        if ($id) {
            $stmt = $pdo->prepare("SELECT paid_amount FROM transactions WHERE id = ?");
            $stmt->execute([$id]);
            $old = $stmt->fetch();
            $oldPaid = $old ? (float)$old['paid_amount'] : 0;
        }
        $difference = $paidAmount - $oldPaid;
        if ($difference > 0) {
            $stmt = $pdo->prepare("INSERT INTO payment_history (transaction_id, payment_date, amount, notes, created_by)
                                   VALUES (?, CURDATE(), ?, 'Pembayaran via sistem', ?)");
            $stmt->execute([$id, $difference, $userId]);
        }
    }

    echo json_encode(['success' => true, 'id' => $id]);
}

function deleteTransaction() {
    global $pdo;
    $id = $_POST['id'] ?? 0;
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
}

function payInstallment() {
    global $pdo;
    $id = $_POST['id'] ?? 0;
    $payment = (float)str_replace('.', '', $_POST['payment'] ?? '0');
    $userId = $_SESSION['user_id'];

    // Ambil transaksi
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ?");
    $stmt->execute([$id]);
    $trans = $stmt->fetch();
    if (!$trans) {
        echo json_encode(['error' => 'Transaksi tidak ditemukan']);
        return;
    }

    $newPaid = $trans['paid_amount'] + $payment;
    // Update paid_amount
    $stmt = $pdo->prepare("UPDATE transactions SET paid_amount = ? WHERE id = ?");
    $stmt->execute([$newPaid, $id]);

    // Catat payment history
    $stmt = $pdo->prepare("INSERT INTO payment_history (transaction_id, payment_date, amount, notes, created_by)
                           VALUES (?, CURDATE(), ?, 'Pembayaran cicilan', ?)");
    $stmt->execute([$id, $payment, $userId]);

    echo json_encode(['success' => true, 'newPaid' => $newPaid]);
}

function getPaymentHistory() {
    global $pdo;
    
    $transaction_id = $_GET['transaction_id'] ?? 0;
    if (!$transaction_id) {
        echo json_encode(['error' => 'ID transaksi diperlukan']);
        return;
    }

    $stmt = $pdo->prepare("
        SELECT ph.*, u.name as created_by_name 
        FROM payment_history ph
        LEFT JOIN users u ON ph.created_by = u.id
        WHERE ph.transaction_id = ?
        ORDER BY ph.payment_date DESC, ph.id DESC
    ");
    $stmt->execute([$transaction_id]);
    $history = $stmt->fetchAll();

    echo json_encode($history);
}

?>