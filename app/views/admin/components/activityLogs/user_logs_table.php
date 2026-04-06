<div>

<h2 class="text-4xl font-extrabold mb-10 flex items-center gap-3">
<ion-icon name="person-circle-outline"></ion-icon>
User Logs
</h2>

<div class="bg-white shadow-md rounded-3xl border-2 p-6">

<h3 class="text-2xl font-bold mb-6 flex items-center gap-2">
<ion-icon name="archive-outline"></ion-icon>
Archived Logs
</h3>

<table class="w-full text-sm border-collapse">

<thead>

<tr class="border-b">
<th class="py-2 px-3">Date</th>
<th class="py-2 px-3">Action</th>
</tr>

</thead>

<tbody>

<?php foreach($userLogs as $log): ?>

<tr class="border-b">

<td class="py-2 px-3"><?= $log['date'] ?></td>
<td class="py-2 px-3"><?= $log['action'] ?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>