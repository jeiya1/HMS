<?php
require_once '../../app/models/User.php';
require_once '../../app/models/Statistics.php';
require_once '../../app/models/Reservation.php';
require_once '../../app/models/Payment.php';

require_once '../../app/models/Room.php';
require_once '../../app/models/Event.php';
require_once '../../app/models/Log.php';
class AdminController
{
    // ====== ADMIN SIDE ======
    public function admin()
    {
        $logged_in = $this->getAuthState();
        if (isset($_SESSION['logged_in_admin_id'])) {
            header("Location: /admin/dashboard");
            exit();
        } else {
            header("Location: /admin/login");
            exit();
        }
    }
    public function adminDashboard()
    {
        if (!$this->getAuthState()) {
            header("Location: /admin/login");
            exit();
        }

        $statisticsModel = new Statistics($GLOBALS['conn']);

        $newBookings = $statisticsModel->getNewBookingsToday();
        $checkIns = $statisticsModel->getCheckInsToday();
        $checkOuts = $statisticsModel->getCheckOutsToday();
        $revenueTotal = $statisticsModel->getTotalRevenue();

        $occupiedRooms = $statisticsModel->getOccupiedRooms();
        $reservedRooms = $statisticsModel->getReservedRooms();
        $availableRooms = $statisticsModel->getAvailableRooms();
        $maintenanceRooms = $statisticsModel->getMaintenanceRooms();

        $reservationsWeek = $statisticsModel->getReservationsLast7Days();
        $reservationsMonth = $statisticsModel->getReservationsThisMonth();
        $revenue6Months = $statisticsModel->getRevenueLast6Months();
        $userAccounts6Months = $statisticsModel->getUserAccountsLast6Months();

        $paymentsByType = $statisticsModel->getPaymentsByType();
        $topRoomTypes = $statisticsModel->getTopRoomTypes();
        require_once '../../app/views/admin/dashboard.view.php';
    }
    public function adminReservations()
    {
        if (!$this->getAuthState()) {
            header("Location: /admin/login");
            exit();
        }
        $reservationModel = new Reservation($GLOBALS['conn']);
        $reservations = $reservationModel->getAllConfirmedReservations();
        require_once '../../app/views/admin/reservations.view.php';
    }

    public function adminRooms()
    {
        if (!$this->getAuthState()) {
            header("Location: /admin/login");
            exit();
        }
        $roomModel = new Room($GLOBALS['conn']);
        $rooms = $roomModel->getAllRooms();
        $roomTypes = $roomModel->getAllRoomTypes();
        $bedTypes = $roomModel->getAllBedTypes();
        $floors = $roomModel->getAllFloors();

        require_once '../../app/views/admin/rooms.view.php';
    }
    public function loginForm()
    {
        require_once '../../app/views/admin/login.view.php';
    }
    public function adminPayments()
    {
        if (!$this->getAuthState()) {
            header("Location: /admin/login");
            exit();
        }

        $paymentModel = new Payment($GLOBALS['conn']);
        $payments = $paymentModel->getAllPaymentsAdmin();

        require_once '../../app/views/admin/payments.view.php';
    }
    public function adminCalendar()
    {
        if (!$this->getAuthState()) {
            header("Location: /admin/login");
            exit();
        }

        $eventModel = new Event($GLOBALS['conn']);
        $events = $eventModel->getCalendarEvents();

        require "../../app/views/admin/calendar.view.php";
    }

    public function adminLogs()
    {
        if (!$this->getAuthState()) {
            header("Location: /admin/login");
            exit();
        }
        require "../../app/views/admin/activityLogs.view.php";
    }

    // Check if user session is active
    public function getAuthState()
    {
        return isset($_SESSION['admin_logged_in']);
    }

    public function login()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            if (empty($email) || empty($password)) {
                echo json_encode([
                    "success" => false,
                    "error" => "Please enter both email and password."
                ]);
                exit;
            }

            try {
                $userModel = new User($GLOBALS['conn']);
                $user = $userModel->getUserByEmail($email);
                if (!$user) {
                    echo json_encode(["success" => false, "error" => "User not found."]);
                    exit;
                }

                // Make sure the user is an admin
                $role = $userModel->getUserRole($user->UserID);

                if ($role->RoleName !== 'admin' && $role->RoleName !== 'receptionist' && $role->RoleName !== 'manager') {
                    echo json_encode(["success" => false, "error" => "Not an admin account."]);
                    exit;
                }

                // Verify password
                if (!password_verify($password, $user->PasswordHash)) {
                    echo json_encode(["success" => false, "error" => "Incorrect password."]);
                    exit;
                }

                // Set session
                $_SESSION['role'] = $role->RoleName;
                $_SESSION['admin_logged_in'] = $user->UserID;

                echo json_encode([
                    "success" => true,
                    "message" => "Login successful!",
                    "redirect" => "/admin/dashboard"
                ]);
                exit;
            } catch (Exception $e) {
                echo json_encode(["success" => false, "error" => $e->getMessage()]);
                exit;
            }
        }

        // fallback for GET request, show login page
        require_once '../../app/views/admin/login.view.php';
    }

    // -------------------------
    // CHANGE USERNAME
    // -------------------------
    public function usernameChange()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['admin_logged_in'])) {
                echo json_encode(['success' => false, 'message' => 'Not logged in']);
                exit;
            }

            $newUsername = trim($_POST['username'] ?? '');
            if (empty($newUsername)) {
                echo json_encode(['success' => false, 'message' => 'Username cannot be empty']);
                exit;
            }

            try {
                $userModel = new User($GLOBALS['conn']);
                $userId = $_SESSION['admin_logged_in'];
                $userModel->updateUsername($userId, $newUsername);

                echo json_encode(['success' => true, 'message' => 'Username updated successfully']);
                exit;
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        }

        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }

    // -------------------------
    // CHANGE PASSWORD
    // -------------------------
    public function passwordChange()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['admin_logged_in'])) {
                echo json_encode(['success' => false, 'message' => 'Not logged in']);
                exit;
            }

            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword)) {
                echo json_encode(['success' => false, 'message' => 'Both password fields are required']);
                exit;
            }

            try {
                $userModel = new User($GLOBALS['conn']);
                $userId = $_SESSION['admin_logged_in'];
                $user = $userModel->getUserById($userId);

                if (!$user || !password_verify($currentPassword, $user->PasswordHash)) {
                    echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
                    exit;
                }

                // updatePasswordAdmin handles hashing internally
                $userModel->updatePasswordAdmin($userId, $newPassword);

                echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
                exit;
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        }

        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }

    // -------------------------
    // LOGOUT
    // -------------------------
    public function logout()
    {
        header('Content-Type: application/json');
        http_response_code(200); // ensure status is OK

        session_unset();
        session_destroy();

        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
        exit;
    }

    // ENDPOINTS
    public function liveReservations()
    {
        header('Content-Type: application/json');

        if (!$this->getAuthState()) {
            echo json_encode([]);
            exit;
        }

        $reservationModel = new Reservation($GLOBALS['conn']);
        $pending = $reservationModel->getPendingReservations();

        echo json_encode($pending);
    }

    public function confirmPayment()
    {
        header('Content-Type: application/json');

        $bookingCode = $_POST['bookingCode'] ?? null;

        if (!$bookingCode) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Missing booking code'
            ]);
            exit;
        }

        try {
            $paymentModel = new Payment($GLOBALS['conn']);

            $result = $paymentModel->confirmPaymentByToken($bookingCode);

            if (!$result) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to confirm payment'
                ]);
                exit;
            }

            echo json_encode([
                'success' => true,
                'message' => 'Payment confirmed successfully'
            ]);
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function refundPayment()
    {
        header('Content-Type: application/json');

        $bookingCode = $_POST['bookingCode'] ?? null;

        if (!$bookingCode) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Missing booking code'
            ]);
            exit;
        }

        try {
            $paymentModel = new Payment($GLOBALS['conn']);
            $paymentModel->refundPaymentByToken($bookingCode);

            echo json_encode([
                'success' => true,
                'message' => 'Payment refunded successfully'
            ]);
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    // Backup directory — all .sql files live here
    private string $backupDir = '/opt/lampp/htdocs/HMS/databaseBackup/';
    // ── Page ──────────────────────────────────────────────────
    public function adminBackup()
    {
        if (!$this->getAuthState()) {
            header('Location: /admin/login');
            exit();
        }

        $dir     = $this->backupDir;
        $backups = [];

        if (is_dir($dir)) {
            $files = glob($dir . '*.sql');
            // Sort newest first
            usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

            foreach ($files as $file) {
                $bytes = filesize($file);
                $backups[] = [
                    'name' => basename($file),
                    'size' => $this->formatBytes($bytes),
                    'date' => date('Y-m-d H:i:s', filemtime($file)),
                ];
            }
        }

        require '../../app/views/admin/backup.view.php';
    }

    // ── Run Backup ────────────────────────────────────────────
    public function runBackup()
    {
        header('Content-Type: application/json');

        $dir = $this->backupDir;

        // Create directory if it doesn't exist
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'HMS_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $dir . $filename;

        $host = 'localhost';
        $user = 'root';
        $db   = 'HMS';

        // Use proc_open so stdout goes directly to file, stderr captured separately
        $cmd = '/opt/lampp/bin/mysqldump --no-tablespaces'
            . ' -h ' . escapeshellarg($host)
            . ' -u ' . escapeshellarg($user)
            . ' '    . escapeshellarg($db);

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['file', $filepath, 'w'],  // stdout → file
            2 => ['pipe', 'w'],             // stderr → captured
        ];

        $proc = proc_open($cmd, $descriptors, $pipes);

        if (!is_resource($proc)) {
            echo json_encode(['success' => false, 'message' => 'Failed to start mysqldump process']);
            return;
        }

        fclose($pipes[0]);
        $stderr     = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $returnCode = proc_close($proc);

        if ($returnCode !== 0 || !file_exists($filepath) || filesize($filepath) === 0) {
            if (file_exists($filepath)) unlink($filepath);
            echo json_encode([
                'success' => false,
                'message' => 'Backup failed: ' . trim($stderr),
            ]);
            return;
        }

        echo json_encode([
            'success'  => true,
            'filename' => $filename,
            'message'  => 'Backup created successfully',
        ]);
    }

    // ── Restore Backup ────────────────────────────────────────
    public function restoreBackup()
    {
        header('Content-Type: application/json');

        $filename = basename($_POST['filename'] ?? '');

        if (!$filename || pathinfo($filename, PATHINFO_EXTENSION) !== 'sql') {
            echo json_encode(['success' => false, 'message' => 'Invalid filename']);
            return;
        }

        $filepath = $this->backupDir . $filename;

        if (!file_exists($filepath)) {
            echo json_encode(['success' => false, 'message' => 'Backup file not found']);
            return;
        }

        $host = 'localhost';
        $user = 'root';
        $db   = 'HMS';

        // Use proc_open so we can feed the file to stdin and capture stderr
        $cmd = '/opt/lampp/bin/mysql'
            . ' -h ' . escapeshellarg($host)
            . ' -u ' . escapeshellarg($user)
            . ' '    . escapeshellarg($db);

        $descriptors = [
            0 => ['file', $filepath, 'r'],  // stdin ← backup file
            1 => ['pipe', 'w'],             // stdout (ignored)
            2 => ['pipe', 'w'],             // stderr → captured
        ];

        $proc = proc_open($cmd, $descriptors, $pipes);

        if (!is_resource($proc)) {
            echo json_encode(['success' => false, 'message' => 'Failed to start mysql process']);
            return;
        }

        $stderr     = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $returnCode = proc_close($proc);

        if ($returnCode !== 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Restore failed: ' . trim($stderr),
            ]);
            return;
        }

        echo json_encode(['success' => true, 'message' => 'Database restored successfully']);
    }

    // ── Delete Backup ─────────────────────────────────────────
    public function deleteBackup()
    {
        header('Content-Type: application/json');

        $filename = basename($_POST['filename'] ?? '');

        if (!$filename || pathinfo($filename, PATHINFO_EXTENSION) !== 'sql') {
            echo json_encode(['success' => false, 'message' => 'Invalid filename']);
            return;
        }

        $filepath = $this->backupDir . $filename;

        if (!file_exists($filepath)) {
            echo json_encode(['success' => false, 'message' => 'File not found']);
            return;
        }

        unlink($filepath);
        echo json_encode(['success' => true]);
    }

    // ── Helper ────────────────────────────────────────────────
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2)    . ' KB';
        return $bytes . ' B';
    }
}
