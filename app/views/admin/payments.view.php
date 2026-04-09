<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lance Hotel Admin</title>
  <link href="/css/output.css" rel="stylesheet">
  <link href="/css/admin/admin.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-stone-200 font-roboto flex flex-col min-h-screen">
  <!-- Sidebar -->
  <?php include_once __DIR__ . '/components/sidebar.view.php'; ?>

  <!-- Top Bar -->
  <?php include_once __DIR__ . '/components/header.view.php'; ?>

  <!-- Main Content -->
  <main class="flex-1 ml-64 mt-24 p-6 space-y-10">

<h2 class="text-4xl font-bold mb-6 flex items-center gap-3">
<ion-icon name="pricetag-outline"></ion-icon>
Payments
</h2>

<div class="overflow-x-auto">

<table class="min-w-full bg-white rounded-2xl shadow-lg">

<thead class="bg-gray-100">

<tr>

<th class="px-6 py-3">Guest Name</th>
<th class="px-6 py-3">Room Number</th>
<th class="px-6 py-3">Check-in / Check-out</th>
<th class="px-6 py-3">Status</th>
<th class="px-6 py-3">Actions</th>

</tr>

</thead>

<tbody>

<?php foreach($payments as $payment): ?>

<?php include "../app/views/components/payment_row.php"; ?>

<?php endforeach; ?>

</tbody>

</table>

</div>

<h2 class="text-4xl font-bold mt-12 flex items-center gap-3">

<ion-icon name="refresh-circle-outline"></ion-icon>
Refunded Payments

</h2>

<div class="overflow-x-auto">

<table class="min-w-full bg-white rounded-2xl shadow-lg">

<thead class="bg-gray-100">

<tr>

<th class="px-6 py-3">Guest Name</th>
<th class="px-6 py-3">Room Number</th>
<th class="px-6 py-3">Check-in / Check-out</th>
<th class="px-6 py-3">Status</th>

</tr>

</thead>

<tbody>

<?php foreach($refunds as $refund): ?>

<?php include "../app/views/components/refund_row.php"; ?>

<?php endforeach; ?>

</tbody>

</table>

</div>

<?php include "../app/views/components/refund_popup.php"; ?>
<?php include "../app/views/components/view_popup.php"; ?>

</main>

  
</body>

</html>