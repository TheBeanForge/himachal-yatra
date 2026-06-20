<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['admin_user'])) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'Unauthorised']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit; }

require_once __DIR__ . '/../includes/vars.php';
require_admin_csrf();

$id   = (int)($_POST['id'] ?? 0);
$slot = trim($_POST['slot'] ?? '');           // e.g. home_hero | manali_hero | manali_about
$val  = (int)(bool)($_POST['value'] ?? 0);    // 1 = assign, 0 = remove

if (!$id || !photo_slot_valid($slot)) {
    http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Invalid input']); exit;
}

// Photo must exist
$stmt = $conn->prepare('SELECT id FROM photos WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$exists = (bool)$stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$exists) { echo json_encode(['ok'=>false,'error'=>'Photo not found']); exit; }

$cap = photo_slot_cap($slot);

$conn->begin_transaction();
try {
    if ($val === 1) {
        // Already assigned? Treat as success (idempotent).
        $c = $conn->prepare('SELECT 1 FROM photo_assignments WHERE photo_id = ? AND slot = ?');
        $c->bind_param('is', $id, $slot);
        $c->execute();
        $already = (bool)$c->get_result()->fetch_assoc();
        $c->close();

        if (!$already) {
            // Count current occupants of this slot
            $c = $conn->prepare('SELECT COUNT(*) AS n FROM photo_assignments WHERE slot = ?');
            $c->bind_param('s', $slot);
            $c->execute();
            $n = (int)($c->get_result()->fetch_assoc()['n'] ?? 0);
            $c->close();

            if ($cap === 1) {
                // Single-photo slot: clear any existing occupant first
                $d = $conn->prepare('DELETE FROM photo_assignments WHERE slot = ?');
                $d->bind_param('s', $slot);
                $d->execute(); $d->close();
            } elseif ($n >= $cap) {
                $conn->rollback();
                echo json_encode(['ok'=>false,'error'=>"This slot is full (max {$cap}). Remove one first."]);
                exit;
            }

            $u = $conn->prepare('INSERT INTO photo_assignments (photo_id, slot, position) VALUES (?, ?, 0)');
            $u->bind_param('is', $id, $slot);
            $u->execute(); $u->close();
        }
    } else {
        $d = $conn->prepare('DELETE FROM photo_assignments WHERE photo_id = ? AND slot = ?');
        $d->bind_param('is', $id, $slot);
        $d->execute(); $d->close();
    }
    $conn->commit();
} catch (Throwable) {
    $conn->rollback();
    echo json_encode(['ok'=>false,'error'=>'Could not update']); exit;
}

// Report the slot's new count so the UI can update its counter.
$c = $conn->prepare('SELECT COUNT(*) AS n FROM photo_assignments WHERE slot = ?');
$c->bind_param('s', $slot);
$c->execute();
$count = (int)($c->get_result()->fetch_assoc()['n'] ?? 0);
$c->close();

audit_log('photo_slot', "Photo #{$id} slot {$slot}=" . ($val ? 'on' : 'off'));
echo json_encode(['ok'=>true, 'slot'=>$slot, 'count'=>$count, 'cap'=>$cap]);
