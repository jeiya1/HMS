<nav class="fixed h-screen w-64 bg-gray-900 text-white flex flex-col z-100">
    <div class="p-4.5 text-2xl font-bold border-b border-gray-700"> Admin</div>
    <nav class="flex-1 p-4 text-l flex flex-col gap-3">
        <a href="/admin/dashboard" class="px-4 py-2 rounded hover:bg-gray-700" onclick="show('statistics')">Dashboard</a>
        <a href="/admin/reservations" class=" px-4 py-2 rounded hover:bg-gray-700"
            onclick="show('reservations')">Reservations</a>
        <a href="/admin/rooms" class=" px-4 py-2 rounded hover:bg-gray-700" onclick="show('rooms')">Rooms</a>
        <a href="/admin/financials" class=" px-4 py-2 rounded hover:bg-gray-700" onclick="show('payments')">Financials</a>
        <a href="/admin/calendar" class=" px-4 py-2 rounded hover:bg-gray-700" onclick="show('calendar')">Calendar</a>
        <a href="/admin/logs" class=" px-4 py-2 rounded hover:bg-gray-700" onclick="show('logs')">Activity Logs</a>
    </nav>
    <div class="p-4 border-t border-gray-700 text-xs text-gray-400">Hotel Rivera HMS v1.0.5</div>
</nav>
<script>
  const path = window.location.pathname;

  const links = document.querySelectorAll('nav a');

  links.forEach(link => {
    if (link.getAttribute('href') === path) {
      link.classList.add('bg-gray-700', 'text-gray-100');
    } else {
      link.classList.remove('bg-gray-700', 'text-gray-100');
      link.classList.add('text-gray-400'); // optional: make inactive links gray
    }
  });
</script>