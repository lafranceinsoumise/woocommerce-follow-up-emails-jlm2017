<style>
    .wrap > div.updated {
        display: none;
    }
    .ui-progressbar {
        position: relative;
    }
    .ui-progressbar-value {
        border: 1px solid #fff;
        background: #ededed;
    }
    .progress-label {
        position: absolute;
        left: 10px;
        top: 4px;
        font-weight: bold;
        text-shadow: 1px 1px 0 #fff;
        color: #a9a9a9;
    }
    #log {
        max-height: 300px;
        overflow: auto;
    }
    #log p.success {
        color: green;
    }
    #log p.failure {
        color: #ff0000;
    }
</style>
<?php
if ( isset($_GET['ref']) ) {
    $return = $_GET['ref'];
} else {
    $return = admin_url( 'admin.php?page=followup-emails' );
}
?>
<script>
    var return_url = '<?php echo $return; ?>';
</script>
<div class="wrap">
    <h2>
        <?php _e('Subscriptions Update', 'follow_up_emails'); ?>
    </h2>

    <p id="total-items-label"><?php _e('Loading', 'follow_up_emails'); ?>...</p>
    <div id="progressbar"><div class="progress-label"><?php _e('Loading', 'follow_up_emails'); ?>...</div></div>

    <div id="log">

    </div>
</div>