<?php
include 'functions.php';
session_start();

//Checks for ID param in URL
if (!isset($_GET['id'])) {
    exit('Unspecified Ticket ID');
}
//Selects tickets by ID
$stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = ?');
$stmt->execute([$_GET['id']]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);
//Checks if ticket exists
if (!$ticket) {
    exit('Invalid Ticket ID.');
}

//Update ticket status
if (isset($_GET['status']) && in_array($_GET['status'], array('open','closed', 'resolved', 'on hold'))) {
    $stmt = $pdo->prepare('UPDATE tickets SET status = ? WHERE id = ?');
    $stmt->execute([$_GET['status'], $_GET['id']]);

    header('Location:viewtickets.php?id=' . $_GET['id']);
    exit;
}

//Comment form submission check, then inserts into ticket_comments table
if (isset($_POST['msg']) && !empty($_POST['msg'])) {
    $stmt = $pdo->prepare('INSERT INTO tickets_comments (ticket_id, msg) VALUES (?, ?)');
    $stmt->execute([$_GET['id'], $_POST['msg']]);
    header('Location:viewtickets.php?id=' . $_GET['id']);
    exit;
}
//Comment listing
$stmt = $pdo->prepare('SELECT * FROM tickets_comments WHERE ticket_id = ? ORDER BY created DESC');
$stmt->execute([ $_GET['id'] ]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?=template_header('Ticket')?>
<div class="content view">
    <h2><?=htmlspecialchars($ticket['title'], ENT_QUOTES)?><class="<?=$ticket['status']?>"</span></h2>
    <div class="ticket">
        <p class="user"><?=nl2br($ticket['username']) ?></p>
        <p class="created"><?=date('F dSm G:ia', strtotime($ticket['created']))?></p>
        <p class="msg"><?=nl2br(htmlspecialchars($ticket['msg'], ENT_QUOTES))?></p>
    </div>

    <div class="comments">
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <p>
                <span><?=nl2br($_SESSION['username'])?></span>
                <span><?=date('F dS, G:ia', strtotime($comment['created']))?></span>
                <?=nl2br(htmlspecialchars($comment['msg'], ENT_QUOTES))?>
                </p>
            </div>
        <?php endforeach; ?>
        <form action="" method="post">
            <textarea name="msg" placeholder="Comment..."></textarea>
            <input type="submit" value="Comment">
        </form>
    </div>
</div>

<?=template_footer()?>