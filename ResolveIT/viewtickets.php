<?php
session_start();

include 'functions.php';

$currentID = $_GET['id'];
$file = 'currentID.txt';
file_put_contents($file, $currentID);


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

    //header('Location: viewtickets.php?id=' . $_GET['id']);
    header('Location: tickets.php');
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

<?=template_header('Comment')?>
<div class="content view">
    <h2>Title: <?=htmlspecialchars($ticket['title'], ENT_QUOTES)?> <span class="<?=$ticket['status']?>">(<?=$ticket['status']?>)</span></h2>
    <div class="ticket">
        <p class="user">Created By: <?=nl2br($ticket['username']) ?></p>
        <p class="created">Date Created: <?=date('F dSm G:ia', strtotime($ticket['created']))?></p>
        <p class="msg">Message: <?=nl2br(htmlspecialchars($ticket['msg'], ENT_QUOTES))?></p>
    </div>

    <?php
    $query = $pdo->prepare("SELECT role FROM users WHERE username = '{$_SESSION['username']}'");
    $query->execute();
    $role = $query->fetch();

    if ($role['role'] == "user") {
        echo '<div class="btns">';
            echo '<a href="viewtickets.php?id=' . $_GET['id'] . '?>&status=resolved' . '"class="status_btn_resolved">' . 'Resolve' . '</a>';
            echo '<a href="viewtickets.php?id=' . $_GET['id'] . '?>&status=closed' . '"class="status_btn_closed">' . 'Close' . '</a>';
        echo '</div>';
    } else if ($role['role'] == "admin") {
        echo '<div class="btns">';
            echo '<a href="viewtickets.php?id=' . $_GET['id'] . '?>&status=resolved' . '"class="status_btn_resolved">' . 'Resolve' . '</a>';
            echo '<a href="viewtickets.php?id=' . $_GET['id'] . '?>&status=closed' . '"class="status_btn_closed">' . 'Close' . '</a>';
            echo '<a href="viewtickets.php?id=' . $_GET['id'] . '?>&status=open' . '"class="status_btn_open">' . 'Open' . '</a>';
            echo '<a href="viewtickets.php?id=' . $_GET['id'] . '?>&status=on hold' . '"class="status_btn_onhold">' . 'On Hold' . '</a>';
        echo '</div>';
    }

    ?>

    <div class="comments">
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <p>
                <span>Commenter: <?=nl2br($_SESSION['username'])?></span>
                <span>Comment Date: <?=date('F dS, G:ia', strtotime($comment['created']))?></span>
                Comment: <?=nl2br(htmlspecialchars($comment['msg'], ENT_QUOTES))?>
                </p>
            </div>
        <?php endforeach; ?>
        <form action="" method="post">
            <textarea name="msg" placeholder="Comment..."></textarea>
            <input type="submit" value="Comment">
        </form>
        <form action="">
            <input id="chatty" onclick="myFunction()" type="button" value="Ask Chatty">
            <script>
                
            </script>
        </form>
    </div>
</div>

<?=template_footer()?>