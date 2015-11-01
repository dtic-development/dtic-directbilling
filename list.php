<?php
require_once('directbilling.php');
@session_start();
define('DTIC_API_KEY', '966d1e3b3f10b0f4c515e833fd3ea966');
$billing = new DirectBilling(DTIC_API_KEY);
$subscriptions = $billing->listSubscriptions();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug System Traces</title>

    <style>
        /*
        *{
            font-family: 'Courier New', Courier, 'Lucida Sans Typewriter', 'Lucida Typewriter', monospace;
            font-size: 12px;
        }
        */
        table, th, td {
            border: 1px solid black;
            /*width: 100%;*/
            border-collapse: collapse;
            border-spacing: 2px;
        }
        tr:nth-child(even) {
            background-color: #f3f3f3;
        }
    </style>

</head>
<body>

<h1>Subscriptions</h1>
<?php if(!empty($subscriptions)): ?>

    <table>
        <tr>
            <th>consumer</th>
            <th>operator</th>
            <th>created</th>
            <th>finished</th>
            <th>Actions</th>
        </tr>

        <?php foreach($subscriptions as $s):

            ?>
            <tr>


                <td><?php echo $s['consumerId'] ?></td>
                <td><?php echo $s['operator'] ?></td>
                <td><?php echo $s['created']['date'] ?></td>
                <td><?php echo $s['finished']['date']; ?></td>
                <td><?php
                    if(empty($s['finished']['date'])):
                        ?>
                        <a href="terminate.php?subscriptionId=<?php echo $s['id']?>">Finalizar</a>
                        <?php
                    endif;
                    ?></td>

            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>


</body>

</html>
