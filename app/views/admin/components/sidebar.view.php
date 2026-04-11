<nav class="fixed h-screen w-64 bg-gray-900 text-white flex flex-col z-100">
  <div class="p-4.5 text-2xl font-bold border-b border-gray-700"> Admin</div>
  <nav class="flex-1 p-4 text-l flex flex-col gap-3">
    <?php
    /* ============================================================
   In sidebar.view.php, add this at the very top of the file:
   ============================================================ */
    $_role = $_SESSION['role'] ?? '';
    ?>

    <?php /* Then wrap each nav link like this: */ ?>

    <?php /* Dashboard — admin/manager only */ ?>
    <?php if (in_array($_role, ['admin', 'manager'])): ?>
      <a href="/admin/dashboard" class="px-4 py-2 rounded hover:bg-gray-700" onclick="show('statistics')">Dashboard</a>
    <?php endif; ?>

    <?php /* Reservations — all roles */ ?>
    <a href="/admin/reservations" class=" px-4 py-2 rounded hover:bg-gray-700"
      onclick="show('reservations')">Reservations</a>

    <?php /* Calendar — all roles */ ?>
    <a href="/admin/calendar" class=" px-4 py-2 rounded hover:bg-gray-700" onclick="show('calendar')">Calendar</a>

    <?php /* Payments — admin/manager only */ ?>
    <?php if (in_array($_role, ['admin', 'manager'])): ?>
      <a href="/admin/payments" class=" px-4 py-2 rounded hover:bg-gray-700" onclick="show('payments')">Payments</a>
    <?php endif; ?>

    <?php /* Rooms — admin/manager only */ ?>
    <?php if (in_array($_role, ['admin', 'manager'])): ?>
      <a href="/admin/rooms" class=" px-4 py-2 rounded hover:bg-gray-700" onclick="show('rooms')">Rooms</a>
    <?php endif; ?>

    <?php /* Logs — admin only */ ?>
    <?php if ($_role === 'admin'): ?>
      <a href="/admin/logs" class=" px-4 py-2 rounded hover:bg-gray-700" onclick="show('logs')">Activity Logs</a>
    <?php endif; ?>

    <?php /* Backup — admin only */ ?>
    <?php if ($_role === 'admin'): ?>
      <a href="/admin/backup" class=" px-4 py-2 rounded hover:bg-gray-700" onclick="show('backups')">Backups</a>
    <?php endif; ?>
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