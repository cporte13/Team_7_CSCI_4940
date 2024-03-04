<?php
include 'functions.php';

session_start();
if (!isset ($_SESSION["loggedin"])) {
    header('Location: login.php');
    exit;
}

$pdo = pdo_connect_mysql();
$stmt = $pdo->prepare('SELECT * FROM tickets ORDER BY created DESC');
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?=template_header('Tickets')?>
    <div class="content home">
        <h2>Tickets</h2>
        <a href="createticket.php" class="create_btn">Create Ticket</a>

        <div class="tickets-list">
            <table>
                <tr>
                    <td>ID</td>
                    <td>Username</td>
                    <td>Title</td>
                    <td>Message</td>
                    <td>Date</td>
                    <td>Status</td>
                </tr>
                <?php foreach ($tickets as $ticket): ?>
                <a href="viewtickets.php?id=<?=$ticket['id']?>" class="ticket">
                <tr>
                    <td><?=htmlspecialchars($ticket['id'], ENT_QUOTES)?></td>
                    <td><?=htmlspecialchars($ticket['username'], ENT_QUOTES)?></td>
                    <td><?=htmlspecialchars($ticket['title'], ENT_QUOTES)?></td>
                    <td><?=htmlspecialchars($ticket['msg'], ENT_QUOTES)?></td>
                    <td><?=date('F dS, G:ia', strtotime($ticket['created']))?></td>
                    <td><?=htmlspecialchars($ticket['status'], ENT_QUOTES)?></td>
                </tr>
                </php endforeach; ?>
            </table>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

<?=template_footer()?>