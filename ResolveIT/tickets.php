<?php
include 'functions.php';

session_start();
if (!isset ($_SESSION["loggedin"])) {
    header('Location: login.php');
    exit;
}
?>

<?=template_header('Tickets')?>
    <div class="content home">
        <h2>Tickets</h2>
        <a href="createticket.php" class="create_btn">Create Ticket</a>

        <div class="tickets-list">
            <table class="tickets_table">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
                <?php
                $pdo = pdo_connect_mysql();
                $stmt = $pdo->prepare('SELECT * FROM tickets ORDER BY created DESC');
                $stmt->execute();
                $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($tickets as $ticket) {
                    echo '<tr>';
                    echo '<td>' . $ticket['id'] . '</td>';
                    echo '<td>' . $ticket['username'] . '</td>';
                    echo '<td>' . $ticket['title'] . '</td>';
                    echo '<td><a href="viewtickets.php?id='.$ticket['id'].'">' . $ticket['msg'] . '</a>' . '</td>';
                    echo '<td>' . $ticket['created'] . '</td>';
                    echo '<td>' . $ticket['status'] . '</td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </div>
    </div>

<?=template_footer()?>