<?php
require_once '../app/models/Room.php';
require_once '../app/models/RoomType.php';
class SearchController
{
    public function search()
    {
        $logged_in = $this->getAuthState();

        $roomModel = new Room($GLOBALS['conn']);
        $roomTypeModel = new RoomType($GLOBALS['conn']);

        $rooms = [];
        // $room_type = $_POST['room_type'] ?? '';
        $roomTypes = $roomTypeModel->getAllRoomTypes();

        $filters = $filters ?? [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $checkin = $_POST['checkin'] ?? null;
            $checkout = null;

            if (!empty($checkin)) {
                $dates = explode(" to ", $checkin);
                if (count($dates) === 2) {
                    $checkinObj = DateTime::createFromFormat('d/m/Y', trim($dates[0]));
                    $checkoutObj = DateTime::createFromFormat('d/m/Y', trim($dates[1]));
                    if ($checkinObj && $checkoutObj) {
                        $checkin = $checkinObj->format('Y-m-d');
                        $checkout = $checkoutObj->format('Y-m-d');
                    } else {
                        $checkin = $checkout = null; // fallback
                    }
                }
            }

            $filters = [
                'checkin' => $checkin,
                'checkout' => $checkout,
                'adults' => !empty($_POST['adults']) ? $_POST['adults'] : null,
                'children' => !empty($_POST['children']) ? $_POST['children'] : null,
                'room' => !empty($_POST['room']) ? $_POST['room'] : null,
                'room_type' => !empty($_POST['room_type']) ? $_POST['room_type'] : null,
                'beds' => !empty($_POST['beds']) ? $_POST['beds'] : null,
            ];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['auto'])) {
            $typeMap = [
                'standard' => 1,
                'deluxe' => 2,
                'suite' => 3
            ];
            $roomTypeId = $typeMap[$_GET['room_type']] ?? null;

            $today = new DateTime();
            $tomorrow = (new DateTime())->modify('+1 day');

            $checkin = $today->format('Y-m-d');
            $checkout = $tomorrow->format('Y-m-d');

            $filters = [
                'checkin' => $checkin,
                'checkout' => $checkout,
                'adults' => null,
                'children' => null,
                'room' => null,
                'room_type' => $roomTypeId,
                'beds' => null,
            ];

            // Pre-fill form display format (important)
            $_POST['checkin'] = $today->format('d/m/Y') . ' to ' . $tomorrow->format('d/m/Y');
            $_POST['room_type'] = $roomTypeId;
        }

        $rooms = $roomModel->searchAvailable($filters);

        require_once __DIR__ . '/../views/rooms/search.view.php';
    }

    public function getAuthState()
    {
        return isset($_SESSION['logged_in_user_id']);
    }
    function convertDate($range, $isCheckout = false)
    {
        $dates = explode(" to ", $range);
        if (count($dates) !== 2)
            return null;
        $dateObj = DateTime::createFromFormat('d/m/Y', trim($dates[$isCheckout ? 1 : 0]));
        return $dateObj ? $dateObj->format('Y-m-d') : null;
    }
}
?>