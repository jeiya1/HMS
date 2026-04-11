<?php
/**
 * SearchAvailabilityController.php
 * 
 * Lightweight AJAX-only controller.
 * Returns JSON: which rooms are still available for the given dates/filters.
 * 
 * Place this file at: app/controllers/SearchAvailabilityController.php
 */

require_once '../app/models/Room.php';

class SearchAvailabilityController
{
    /**
     * GET /search-available
     * 
     * Query params (same as /search GET):
     *   checkin      – "YYYY-MM-DD" or "YYYY/MM/DD to YYYY/MM/DD"
     *   checkout     – "YYYY-MM-DD"  (optional if checkin has range)
     *   adults       – int (optional)
     *   children     – int (optional)
     *   room         – "single"|"double" (optional)
     *   room_type    – "Standard"|"Deluxe"|"Suite" (optional)
     *   cartID       – int (optional, excludes rooms already in cart)
     *   rooms[]      – array of RoomNumbers currently shown on the page
     *                  (used to narrow the diff — only check these rooms)
     */
    public function available(): void
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        header('Content-Type: application/json');
        header('Cache-Control: no-store');

        // ── Parse dates ──────────────────────────────────────────────────
        $checkinStr = $_GET['checkin'] ?? null;
        $checkoutStr = $_GET['checkout'] ?? null;

        [$checkin, $checkout] = $this->parseCheckinCheckout($checkinStr, $checkoutStr);

        if (!$checkin || !$checkout) {
            echo json_encode(['error' => 'Invalid or missing dates', 'available' => []]);
            return;
        }

        // ── Other filters ────────────────────────────────────────────────
        $adults = isset($_GET['adults']) ? max(0, (int) $_GET['adults']) : null;
        $children = isset($_GET['children']) ? max(0, (int) $_GET['children']) : null;

        if ($adults !== null && $children !== null && ($adults + $children) === 0) {
            $adults = $children = null;
        }

        $cartID = isset($_GET['cartID']) ? (int) $_GET['cartID'] : ($_SESSION['cart_id'] ?? null);
        $room = $_GET['room'] ?? null;
        $roomType = $_GET['room_type'] ?? null;

        // ── Rooms currently displayed (sent by JS) ───────────────────────
        $shownRooms = $_GET['rooms'] ?? [];   // array of RoomNumber strings
        if (!is_array($shownRooms)) {
            $shownRooms = [$shownRooms];
        }

        // ── Run the same search the full page uses ───────────────────────
        $roomModel = new Room($GLOBALS['conn']);

        $filters = [
            'checkin' => $checkin,
            'checkout' => $checkout,
            'adults' => $adults,
            'children' => $children,
            'room' => $room,
            'room_type' => $roomType,
            'cartID' => $cartID,
        ];

        $availableRooms = $roomModel->searchAvailable($filters);

        // Index by RoomNumber for quick lookup
        $availableMap = [];
        foreach ($availableRooms as $r) {
            $availableMap[$r['RoomNumber']] = $r;
        }

        // ── Build response ───────────────────────────────────────────────
        // For each room the page is showing, tell JS whether it's still available
        $result = [];

        if (!empty($shownRooms)) {
            foreach ($shownRooms as $rn) {
                $result[$rn] = isset($availableMap[$rn]);   // true = still available
            }
        } else {
            // Fallback: just return all currently available room numbers
            foreach ($availableRooms as $r) {
                $result[$r['RoomNumber']] = true;
            }
        }

        echo json_encode([
            'available' => $result,           // { "101": true, "102": false, … }
            'checkin' => $checkin,
            'checkout' => $checkout,
            'polled_at' => date('c'),
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function parseCheckinCheckout(?string $checkinStr, ?string $checkoutStr = null): array
    {
        if (!$checkinStr)
            return [null, null];

        // Range format: "YYYY/MM/DD to YYYY/MM/DD"  or  "YYYY-MM-DD to YYYY-MM-DD"
        if (str_contains($checkinStr, ' to ')) {
            $parts = explode(' to ', $checkinStr);
            return [
                $this->normalizeDate(trim($parts[0])),
                $this->normalizeDate(trim($parts[1])),
            ];
        }

        return [
            $this->normalizeDate($checkinStr),
            $this->normalizeDate($checkoutStr ?? ''),
        ];
    }

    private function normalizeDate(string $raw): ?string
    {
        if (!$raw)
            return null;
        foreach (['Y/m/d', 'Y-m-d', 'd/m/Y'] as $fmt) {
            $d = DateTime::createFromFormat($fmt, $raw);
            if ($d)
                return $d->format('Y-m-d');
        }
        return null;
    }

    // Check if user session is active
    public function getAuthState()
    {
        return isset($_SESSION['logged_in_user_id']);
    }

    // get current cart count
    private function getCartCount()
    {
        $cartModel = new Cart($GLOBALS['conn']);
        return $cartModel->getCartAmount();
    }
}
