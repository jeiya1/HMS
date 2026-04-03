<!-- Live Reservation Feed -->
<div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm w-1/2 h-96 flex flex-col">
    <h3 class="text-lg font-bold mb-4">Live Reservation Feed</h3>
    <div id="liveFeed"
        class="flex-1 space-y-2 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100">
        <!-- Live reservations will be injected here -->
    </div>
</div>

<style>
    #liveFeed::-webkit-scrollbar {
        width: 6px;
    }

    #liveFeed::-webkit-scrollbar-track {
        background: #f3f4f6;
        /* Tailwind gray-100 */
    }

    #liveFeed::-webkit-scrollbar-thumb {
        background-color: #9ca3af;
        /* Tailwind gray-400 */
        border-radius: 3px;
    }

    #liveFeed::-webkit-scrollbar-thumb:hover {
        background-color: #6b7280;
        /* Tailwind gray-500 */
    }
</style>