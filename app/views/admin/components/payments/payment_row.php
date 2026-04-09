<tr>

<td class="px-6 py-4"><?= $payment['guest_name'] ?></td>

<td class="px-6 py-4"><?= $payment['room_number'] ?></td>

<td class="px-6 py-4">
<?= $payment['check_in'] ?> - <?= $payment['check_out'] ?>
</td>

<td class="px-6 py-4">

<span class="bg-green-500 text-white px-2 py-1 rounded">
<?= $payment['status'] ?>
</span>

</td>

<td class="px-6 py-4 flex gap-2">

<button class="view-btn bg-blue-500 text-white px-3 py-1 rounded"
data-id="<?= $payment['id'] ?>">
View
</button>

<form method="POST" action="/payments/refund">

<input type="hidden" name="id" value="<?= $payment['id'] ?>">

<button class="bg-red-500 text-white px-3 py-1 rounded">
Refund
</button>

</form>

</td>

</tr>