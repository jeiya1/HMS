<div id="add-room-popup"
class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[800px] bg-white border rounded-3xl shadow-2xl p-6 hidden">

<h2 class="text-2xl font-bold mb-6">Create Room</h2>

<form method="POST" action="/rooms/store" enctype="multipart/form-data" class="flex flex-col gap-4">

<input name="type" type="text" placeholder="Room Type"
class="border rounded-lg px-4 py-2">

<input name="price" type="number" placeholder="Room Price"
class="border rounded-lg px-4 py-2">

<textarea name="description"
placeholder="Room Description"
class="border rounded-lg px-4 py-2"></textarea>

<input name="image" type="file">

<button class="bg-blue-600 text-white px-6 py-2 rounded-lg">
Create Room
</button>

</form>

</div>