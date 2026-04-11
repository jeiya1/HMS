<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Backup</title>
    <link rel="icon" type="image/x-icon" href="/admin/assets/icons/favicon.svg">
    <link href="/admin/css/output.css" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body class="bg-stone-200 font-roboto flex flex-col min-h-screen">
    <?php include_once __DIR__ . '/components/toast.view.php'; ?>
    <?php include_once __DIR__ . '/components/sidebar.view.php'; ?>
    <?php include_once __DIR__ . '/components/header.view.php'; ?>

    <main class="ml-64 mt-15.5 p-4 flex flex-col gap-4">

        <!-- Create Backup -->
        <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm">
            <h3 class="text-lg font-bold mb-1">Create Backup</h3>
            <p class="text-sm text-gray-500 mb-4">
                Saves a full dump of the HMS database to
                <span class="font-mono text-xs bg-gray-100 px-1 py-0.5 rounded">/opt/lampp/htdocs/HMS/database.sql.bak/</span>
            </p>
            <button id="backupBtn"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded cursor-pointer text-sm">
                Run Backup Now
            </button>
        </div>

        <!-- Restore from Backup -->
        <div class="bg-white p-6 border border-gray-300 rounded-lg shadow-sm">
            <h3 class="text-lg font-bold mb-1">Restore from Backup</h3>
            <p class="text-sm text-gray-500 mb-4">
                Select a saved backup file to restore the database. <span class="text-red-500 font-medium">This will overwrite all current data.</span>
            </p>

            <?php if (empty($backups)): ?>
                <p class="text-sm text-gray-400">No backup files found.</p>
            <?php else: ?>
                <table class="w-full border border-gray-300 text-sm">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 p-2 text-left">Filename</th>
                            <th class="border border-gray-300 p-2 text-center">Size</th>
                            <th class="border border-gray-300 p-2 text-center">Created</th>
                            <th class="border border-gray-300 p-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($backups as $b): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2 font-mono text-xs"><?= htmlspecialchars($b['name']) ?></td>
                                <td class="p-2 text-center"><?= $b['size'] ?></td>
                                <td class="p-2 text-center"><?= $b['date'] ?></td>
                                <td class="p-2 text-center space-x-1">
                                    <button class="restore-btn bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded cursor-pointer"
                                        data-file="<?= htmlspecialchars($b['name']) ?>">
                                        Restore
                                    </button>
                                    <button class="delete-backup-btn bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded cursor-pointer"
                                        data-file="<?= htmlspecialchars($b['name']) ?>">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </main>

    <!-- Confirm Modal -->
    <div id="confirmModal" class="fixed inset-0 hidden items-center justify-center z-50">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="bg-white w-96 p-6 rounded-lg shadow-lg relative z-10">
            <h2 id="confirmTitle" class="text-lg font-semibold mb-3">Confirm Action</h2>
            <p id="confirmMessage" class="text-sm text-gray-600 mb-5">Are you sure?</p>
            <div class="flex justify-end space-x-2">
                <button id="cancelConfirm" class="px-3 py-1 rounded bg-gray-300 hover:bg-gray-400 cursor-pointer">Cancel</button>
                <button id="okConfirm" class="px-3 py-1 rounded bg-green-500 text-white hover:bg-green-600 cursor-pointer">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        // ── Confirm Modal ─────────────────────────────────────────
        let pendingAction = null;

        function openConfirmModal(title, message, action) {
            pendingAction = action;
            $('#confirmTitle').text(title);
            $('#confirmMessage').text(message);
            $('#confirmModal').removeClass('hidden').addClass('flex');
        }

        function closeConfirmModal() {
            $('#confirmModal').addClass('hidden').removeClass('flex');
            pendingAction = null;
        }

        $('#cancelConfirm').on('click', closeConfirmModal);
        $('#okConfirm').on('click', function() {
            if (typeof pendingAction === 'function') pendingAction();
            closeConfirmModal();
        });

        // ── Create Backup ─────────────────────────────────────────
        $('#backupBtn').on('click', function() {
            openConfirmModal('Create Backup', 'Run a full database backup now?', function() {
                $('#backupBtn').text('Running...').prop('disabled', true);

                $.post('/admin/runBackup', function(res) {
                    if (res.success) {
                        showToast('Backup created: ' + res.filename, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(res.message || 'Backup failed', 'error');
                        $('#backupBtn').text('Run Backup Now').prop('disabled', false);
                    }
                }, 'json').fail(function(xhr) {
                    console.log(xhr.status, xhr.responseText); // ADD THIS
                    showToast('Backup request failed', 'error');
                    $('#backupBtn').text('Run Backup Now').prop('disabled', false);
                });
            });
        });

        // ── Restore ───────────────────────────────────────────────
        $(document).on('click', '.restore-btn', function() {
            const file = $(this).data('file');
            openConfirmModal(
                'Restore Database',
                `Restore from "${file}"? All current data will be overwritten. This cannot be undone.`,
                function() {
                    $.post('/admin/restoreBackup', {
                        filename: file
                    }, function(res) {
                        if (res.success) {
                            showToast('Database restored successfully', 'success');
                        } else {
                            showToast(res.message || 'Restore failed', 'error');
                        }
                    }, 'json');
                }
            );
        });

        // ── Delete Backup ─────────────────────────────────────────
        $(document).on('click', '.delete-backup-btn', function() {
            const file = $(this).data('file');
            openConfirmModal(
                'Delete Backup',
                `Delete "${file}"? This cannot be undone.`,
                function() {
                    $.post('/admin/deleteBackup', {
                        filename: file
                    }, function(res) {
                        if (res.success) {
                            showToast('Backup deleted', 'success');
                            setTimeout(() => location.reload(), 800);
                        } else {
                            showToast(res.message || 'Delete failed', 'error');
                        }
                    }, 'json');
                }
            );
        });
    </script>
</body>

</html>