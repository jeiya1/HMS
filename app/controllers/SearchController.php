<?php
require_once '../app/models/Room.php';
require_once '../app/models/RoomType.php';
require_once '../app/models/Cart.php';
class SearchController
{
    public function search()
    {
        // Check authentication and get room data
        $logged_in = $this->getAuthState();

        $roomModel = new Room($GLOBALS['conn']);
        $roomTypeModel = new RoomType($GLOBALS['conn']);
        $roomTypes = $roomTypeModel->getAllRoomTypes();

        $cartID = $_SESSION['cart_id'];
        $filters['cartID'] = $cartID;

        $filters = [];

        // =========================
        // POST → REDIRECT TO GET
        // =========================
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $checkinStr = $_POST['checkin'] ?? null;
            list($checkin, $checkout) = $this->parseCheckinCheckout($checkinStr);

            // Normalize negative guest values 
            $adults = isset($_POST['adults']) ? (int) $_POST['adults'] : 0;
            $children = isset($_POST['children']) ? (int) $_POST['children'] : 0;

            if ($adults < 0)
                $adults = 0;
            if ($children < 0)
                $children = 0;

            // Clear guest counts if total is zero
            if (($adults + $children) === 0) {
                $adults = null;
                $children = null;
            }

            // Filter array for search
            $filters = [
                'checkin' => $checkin,
                'checkout' => $checkout,
                'adults' => $adults,
                'children' => $children,
                'room' => $_POST['room'] ?? null,
                'room_type' => $_POST['room_type'] ?? null,
                'cartID' => $cartID,
            ];

            // Build query string and redirect
            $query = http_build_query(array_filter([
                'checkin' => $checkin,
                'checkout' => $checkout,
                'adults' => $adults,
                'children' => $children,
                'room' => $filters['room'],
                'room_type' => $filters['room_type'],
                'cartID' => $cartID
            ], fn($v) => $v !== null));

            header("Location: /search?$query");
            exit;
        }

        // =========================
        // GET
        // =========================
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            // Normalize negative guest values
            $adults = isset($_GET['adults']) ? (int) $_GET['adults'] : 0;
            $children = isset($_GET['children']) ? (int) $_GET['children'] : 0;

            if ($adults < 0)
                $adults = 0;
            if ($children < 0)
                $children = 0;

            // Clear guest counts if total is zero
            if (($adults + $children) === 0) {
                $adults = null;
                $children = null;
            }

            // Auto-date
            if (isset($_GET['auto'])) {

                $today = new DateTime();
                $tomorrow = (new DateTime())->modify('+1 day');

                // Filter array for search (with auto-date)
                $filters = [
                    'checkin' => $today->format('Y-m-d'),
                    'checkout' => $tomorrow->format('Y-m-d'),
                    'adults' => $adults,
                    'children' => $children,
                    'room' => null,
                    'room_type' => $_GET['room_type'] ?? null,
                    'cartID' => $cartID,
                ];

                $_GET['checkin'] = $today->format('Y/m/d') . ' to ' . $tomorrow->format('Y/m/d');

            } else {
                // Parse check-in and check-out from GET parameters
                $checkinStr = $_GET['checkin'] ?? null;
                $checkoutStr = $_GET['checkout'] ?? null;

                if ($checkinStr && strpos($checkinStr, ' to ') !== false) {
                    list($checkin, $checkout) = $this->parseCheckinCheckout($checkinStr);
                } else {
                    $checkin = $checkinStr ?: null;
                    $checkout = $checkoutStr ?: null;
                }

                // Filter array for search
                $filters = [
                    'checkin' => $checkin,
                    'checkout' => $checkout,
                    'adults' => $adults,
                    'children' => $children,
                    'room' => $_GET['room'] ?? null,
                    'room_type' => $_GET['room_type'] ?? null,
                    'cartID' => $cartID,
                ];
            }
        }

        // Search for available rooms with filters
        $rooms = $roomModel->searchAvailable($filters);
        $cartCount = $this->getCartCount();

        require_once __DIR__ . '/../views/rooms/search.view.php';
    }

    public function getAuthState()
    {
        return isset($_SESSION['logged_in_user_id']);
    }

    private function getCartCount()
    {
        // Get cart amount
        $cartModel = new Cart($GLOBALS['conn']);
        return $cartModel->getCartAmount();
    }

    // Convert date to string range
    function convertDate($range, $isCheckout = false)
    {
        $dates = explode(" to ", $range);
        if (count($dates) !== 2)
            return null;
        $dateObj = DateTime::createFromFormat('d/m/Y', trim($dates[$isCheckout ? 1 : 0]));
        return $dateObj ? $dateObj->format('Y-m-d') : null;
    }

    // Helper function (can reuse convertDate)
    function parseCheckinCheckout($range)
    {
        // Parse check-in/check-out dates from range string
        if (!$range)
            return [null, null];

        $dates = explode(" to ", $range);
        if (count($dates) !== 2)
            return [null, null];

        $formats = ['Y/m/d', 'Y-m-d'];
        $checkinObj = $checkoutObj = false;

        foreach ($formats as $f) {
            if (!$checkinObj)
                $checkinObj = DateTime::createFromFormat($f, trim($dates[0]));
            if (!$checkoutObj)
                $checkoutObj = DateTime::createFromFormat($f, trim($dates[1]));
        }

        if (!$checkinObj || !$checkoutObj)
            return [null, null];

        // Return formatted dates
        return [
            $checkinObj->format('Y-m-d'),
            $checkoutObj->format('Y-m-d')
        ];
    }
}
?>