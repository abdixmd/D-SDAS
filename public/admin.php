<?php
// public/admin.php
// require_once '../bootstrap.php';
// require_once '../src/Auth.php';
// require_once '../src/Admin.php';

// Force Admin Access
// Auth::checkAccess('admin');

// $admin = new Admin();
$message = [];

// Handle POST actions (Create, Status Change, Finalize)
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     if (isset($_POST['action'])) {
//         try {
//             if ($_POST['action'] === 'create_round') {
//                 $result = $admin->createRound($_POST['name'], $_POST['start'], $_POST['end']);
//                 $message = ['type' => 'success', 'text' => 'Round scheduled successfully.'];
//             } elseif ($_POST['action'] === 'update_status') {
//                 $admin->updateRoundStatus($_POST['round_id'], $_POST['status']);
//                 $message = ['type' => 'success', 'text' => 'Round status updated.'];
//             } elseif ($_POST['action'] === 'finalize') {
//                 $result = $admin->finalizeAllocations($_POST['round_id']);
//                 $message = $result['success'] ? ['type' => 'success', 'text' => $result['msg']] : ['type' => 'error', 'text' => $result['msg']];
//             }
//         } catch (Exception $e) {
//             $message = ['type' => 'error', 'text' => 'Operation failed: ' . $e->getMessage()];
//         }
//     }
// }

// Fetch all data for display
// $rounds = $admin->getAllRounds();
// $summary = $admin->getAllocationSummary();

// MOCK DATA FOR UI DEMO
$rounds = [
    ['round_id' => 1, 'round_name' => 'Round 1 - Merit', 'start_time' => '2025-12-15 08:00:00', 'end_time' => '2025-12-18 17:00:00', 'status' => 'active'],
    ['round_id' => 2, 'round_name' => 'Round 2 - Choice', 'start_time' => '2025-12-20 08:00:00', 'end_time' => '2025-12-22 17:00:00', 'status' => 'scheduled']
];
$summary = [
    ['dept_name' => 'Computer Science', 'base_capacity' => 200, 'current_locked' => 150, 'historical_cutoff_rank' => 450],
    ['dept_name' => 'Electrical Engineering', 'base_capacity' => 180, 'current_locked' => 40, 'historical_cutoff_rank' => 600],
    ['dept_name' => 'Mechanical Engineering', 'base_capacity' => 150, 'current_locked' => 20, 'historical_cutoff_rank' => 800],
    ['dept_name' => 'Medicine', 'base_capacity' => 100, 'current_locked' => 95, 'historical_cutoff_rank' => 150]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>D-SDAS Admin Control</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header class="ms-header">...</header>
    <div class="layout-container">
        <aside class="ms-sidebar">...</aside>
        <main class="ms-content">
            <h1>Allocation System Management</h1>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message['type']; ?>"><?php echo $message['text']; ?></div>
            <?php endif; ?>

            <div class="tile large-tile">
                <div class="tile-header">
                    <h3>Round Lifecycle Control</h3>
                </div>
                <div class="tile-body">
                    <h4>Schedule New Round</h4>
                    <form method="POST" class="round-form">
                        <input type="hidden" name="action" value="create_round">
                        <input type="text" name="name" placeholder="Round Name (e.g., R-2026-1)" required>
                        <input type="datetime-local" name="start" required>
                        <input type="datetime-local" name="end" required>
                        <button type="submit" class="ms-btn primary">Schedule</button>
                    </form>

                    <h4>Current Rounds</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Start/End</th>
                                <th>Status</th>
                                <th>Control</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rounds as $r): ?>
                                <tr>
                                    <td><?php echo $r['round_id']; ?></td>
                                    <td><?php echo $r['round_name']; ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($r['start_time'])); ?></td>
                                    <td><span
                                            class="status-tag status-<?php echo $r['status']; ?>"><?php echo ucfirst($r['status']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($r['status'] === 'scheduled'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="round_id" value="<?php echo $r['round_id']; ?>">
                                                <input type="hidden" name="status" value="active">
                                                <button type="submit" class="ms-btn">Activate</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($r['status'] === 'active'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="round_id" value="<?php echo $r['round_id']; ?>">
                                                <input type="hidden" name="status" value="sealed">
                                                <button type="submit" class="ms-btn">Seal Round</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($r['status'] === 'sealed'): ?>
                                            <form method="POST" style="display:inline;"
                                                onsubmit="return confirm('FINAL ALLOCATION. Are you sure?');">
                                                <input type="hidden" name="action" value="finalize">
                                                <input type="hidden" name="round_id" value="<?php echo $r['round_id']; ?>">
                                                <button type="submit" class="ms-btn primary">Finalize Allocations</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tile large-tile">
                <div class="tile-header">
                    <h3>Live Allocation Monitoring</h3>
                </div>
                <div class="tile-body">
                    <table>
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Capacity</th>
                                <th>Locked</th>
                                <th>Remaining</th>
                                <th>Cutoff Rank</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($summary as $s): ?>
                                <tr>
                                    <td><?php echo $s['dept_name']; ?></td>
                                    <td><?php echo $s['base_capacity']; ?></td>
                                    <td><?php echo $s['current_locked']; ?></td>
                                    <td
                                        class="stat-<?php echo ($s['base_capacity'] - $s['current_locked'] < 20) ? 'danger' : 'safe'; ?>">
                                        <?php echo $s['base_capacity'] - $s['current_locked']; ?>
                                    </td>
                                    <td>#<?php echo $s['historical_cutoff_rank']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</body>

</html>