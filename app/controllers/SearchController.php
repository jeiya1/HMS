<?php
require_once '../app/models/Room.php';
require_once '../app/models/RoomType.php';
require_once '../app/models/Cart.php';
class SearchController
{
    public function search()
    {
        $logged_in = $this->getAuthState();

        $roomModel = new Room($GLOBALS['conn']);
        $roomTypeModel = new RoomType($GLOBALS['conn']);
        $roomTypes = $roomTypeModel->getAllRoomTypes();

        $filters = [];

        // Handle POST: redirect to GET with query params
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $checkinStr = $_POST['checkin'] ?? null; // <-- use POST here
            list($checkin, $checkout) = $this->parseCheckinCheckout($checkinStr);

            $filters = [
                'checkin' => $checkin,
                'checkout' => $checkout,
                'adults' => !empty($_POST['adults']) ? (int) $_POST['adults'] : null,
                'children' => !empty($_POST['children']) ? (int) $_POST['children'] : null,
                'room' => $_POST['room'] ?? null,            // single/double
                'room_type' => $_POST['room_type'] ?? null,      // string: Standard/Deluxe/Suite
            ];

            // Redirect to GET with query parameters
            $query = http_build_query($filters);
            header("Location: /search?$query");
            exit;
        }

        // Handle GET (from redirect or direct URL)
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['auto'])) {
                $typeMap = [
                    'standard' => 1,
                    'deluxe' => 2,
                    'suite' => 3
                ];
                $roomTypeId = $typeMap[$_GET['room_type']] ?? null;

                $today = new DateTime();
                $tomorrow = (new DateTime())->modify('+1 day');

                $filters = [
                    'checkin' => $today->format('Y-m-d'),
                    'checkout' => $tomorrow->format('Y-m-d'),
                    'adults' => '',
                    'children' => '',
                    'room' => '',
                    'room_type' => $roomTypeId,
                ];

                // For form prefill
                $_GET['checkin'] = $today->format('d/m/Y') . ' to ' . $tomorrow->format('d/m/Y');
                $_GET['room_type'] = $roomTypeId;
            } else {
                $checkinStr = $_GET['checkin'] ?? null;
                list($checkin, $checkout) = $this->parseCheckinCheckout($checkinStr);

                // GET
                $filters = [
                    'checkin' => $checkin,
                    'checkout' => $checkout,
                    'adults' => !empty($_GET['adults']) ? (int) $_GET['adults'] : null,
                    'children' => !empty($_GET['children']) ? (int) $_GET['children'] : null,
                    'room' => $_GET['room'] ?? null,
                    'room_type' => $_GET['room_type'] ?? null,        // string
                ];
            }
        }

        // Fetch rooms
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
        $cartModel = new Cart($GLOBALS['conn']);
        return $cartModel->getCartAmount();
    }

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
        if (!$range)
            return [null, null];
        $dates = explode(" to ", $range);
        if (count($dates) !== 2)
            return [null, null];
        $checkinObj = DateTime::createFromFormat('d/m/Y', trim($dates[0]));
        $checkoutObj = DateTime::createFromFormat('d/m/Y', trim($dates[1]));
        if (!$checkinObj || !$checkoutObj)
            return [null, null];
        return [
            $checkinObj->format('Y-m-d'),
            $checkoutObj->format('Y-m-d')
        ];
    }
}
?>