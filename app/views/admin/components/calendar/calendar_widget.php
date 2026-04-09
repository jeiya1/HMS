<?php include "../app/views/layouts/header.php"; ?>
<?php include "../app/views/layouts/sidebar.php"; ?>

<main class="flex-1 ml-64 mt-10 p-6 space-y-10">

<div class="flex flex-col gap-6 md:flex-row">

<?php include "../app/views/components/calendar_widget.php"; ?>

<?php include "../app/views/components/event_panel.php"; ?>

</div>

</main>

<script>

const events = <?= json_encode($events); ?>;

</script>

<script src="/public/js/calendar.js"></script>
<script src="/public/js/settings.js"></script>