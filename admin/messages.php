<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db_config.php';

// Check if admin is logged in
if (!isset($_SESSION['cafe_admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Handle status update
if (isset($_GET['mark']) && is_numeric($_GET['mark'])) {
    $msg_id = intval($_GET['mark']);
    $status = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : 'Read';
    mysqli_query($con, "UPDATE contact_messages SET status = '$status' WHERE id = $msg_id");
    header("Location: messages.php");
    exit();
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $msg_id = intval($_GET['delete']);
    mysqli_query($con, "DELETE FROM contact_messages WHERE id = $msg_id");
    header("Location: messages.php");
    exit();
}

// Get messages
$messages = mysqli_query($con, "SELECT * FROM contact_messages ORDER BY created_at DESC");

$title = "Messages - Cloud 9 Cafe";
$active_sidebar = 'messages';
ob_start();
?>

<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
    }

    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-new { background: #d4edda; color: #155724; }
    .status-read { background: #e2e3e5; color: #6c757d; }
    .status-replied { background: #cce5ff; color: #004085; }
    .status-archived { background: #f8d7da; color: #721c24; }

    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.3s ease;
    }

    .action-btn:hover { transform: translateY(-2px); }
    .action-btn.read { background: rgba(102, 126, 234, 0.1); color: #667eea; }
    .action-btn.archive { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
    .action-btn.delete { background: rgba(220, 53, 69, 0.1); color: #dc3545; }

    .message-row {
        cursor: pointer;
        transition: background 0.2s;
    }

    .message-row:hover {
        background: #f8f9fa;
    }

    .message-preview {
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .new-indicator {
        width: 10px;
        height: 10px;
        background: #28a745;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold mb-2"><i class="fas fa-envelope me-2"></i>Contact Messages</h3>
            <p class="mb-0 opacity-75">View and manage customer messages</p>
        </div>
    </div>
</div>

<!-- Messages Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">From</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($messages) === 0): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No messages found</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php while ($msg = mysqli_fetch_assoc($messages)): 
                        $status_class = '';
                        switch ($msg['status']) {
                            case 'New': $status_class = 'status-new'; break;
                            case 'Read': $status_class = 'status-read'; break;
                            case 'Replied': $status_class = 'status-replied'; break;
                            case 'Archived': $status_class = 'status-archived'; break;
                        }
                    ?>
                    <tr class="message-row" data-bs-toggle="modal" data-bs-target="#msgModal<?php echo $msg['id']; ?>">
                        <td class="ps-4">
                            <?php if ($msg['status'] == 'New'): ?>
                            <span class="new-indicator"></span>
                            <?php endif; ?>
                            <div>
                                <div class="fw-medium"><?php echo htmlspecialchars($msg['name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($msg['email']); ?></small>
                            </div>
                        </td>
                        <td>
                            <span class="fw-medium"><?php echo htmlspecialchars($msg['subject'] ?? 'No Subject'); ?></span>
                        </td>
                        <td>
                            <span class="message-preview text-muted"><?php echo htmlspecialchars($msg['message']); ?></span>
                        </td>
                        <td>
                            <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></small>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo $msg['status']; ?>
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="?mark=<?php echo $msg['id']; ?>&status=Read" class="action-btn read" title="Mark Read" onclick="event.stopPropagation();">
                                <i class="fas fa-envelope-open"></i>
                            </a>
                            <a href="?mark=<?php echo $msg['id']; ?>&status=Archived" class="action-btn archive ms-1" title="Archive" onclick="event.stopPropagation();">
                                <i class="fas fa-archive"></i>
                            </a>
                            <a href="?delete=<?php echo $msg['id']; ?>" class="action-btn delete ms-1" title="Delete" onclick="event.stopPropagation(); return confirm('Delete this message?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Message Modal -->
                    <div class="modal fade" id="msgModal<?php echo $msg['id']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold"><?php echo htmlspecialchars($msg['subject'] ?? 'No Subject'); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <strong>From:</strong> <?php echo htmlspecialchars($msg['name']); ?> (<?php echo htmlspecialchars($msg['email']); ?>)
                                    </div>
                                    <div class="mb-3">
                                        <strong>Date:</strong> <?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Status:</strong> <span class="status-badge <?php echo $status_class; ?>"><?php echo $msg['status']; ?></span>
                                    </div>
                                    <hr>
                                    <div class="bg-light p-3 rounded">
                                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="mailto:<?php echo $msg['email']; ?>?subject=Re: <?php echo urlencode($msg['subject'] ?? ''); ?>" class="btn btn-primary">
                                        <i class="fas fa-reply me-2"></i>Reply
                                    </a>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$dashboard_content = ob_get_clean();
include 'admin_layout.php';
?>
