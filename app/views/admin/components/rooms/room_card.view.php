<div class="bg-white border rounded-3xl shadow-lg p-6 w-full max-w-[350px] h-[400px] flex flex-col items-center">

<div class="w-full h-[150px] bg-gray-200 rounded-lg mb-4">
<img src="<?= $room['image'] ?>" class="w-full h-full object-cover rounded-lg">
</div>

<div class="text-center">
<h3 class="text-lg font-bold text-gray-800 mb-2">
<?= $room['type'] ?> (<?= $room['occupancy'] ?>)
</h3>

<h2 class="text-lg font-bold text-green-600 mb-2">
₱ <?= number_format($room['price']) ?>
</h2>

<p class="text-gray-600 mb-4"><?= $room['description'] ?></p>
</div>

<div class="flex gap-3">
<button class="edit-btn bg-blue-500 text-white px-4 py-2 rounded-3xl">Edit</button>
<button class="delete-btn bg-red-500 text-white px-4 py-2 rounded-3xl">Delete</button>

<form action="/rooms/delete" method="POST">

<input type="hidden" name="id" value="<?= $room['id'] ?>">

<button type="submit" class="delete-btn">
Delete
</button>

</form>

</div>

</div>