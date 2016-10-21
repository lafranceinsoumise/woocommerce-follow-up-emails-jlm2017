<?php
$count  = count($campaigns);
$i      = 0;
?>
<div class="subsubsub_section">
    <ul class="subsubsub">
        <li><?php _e('Filter by Campaign:', 'follow_up_emails'); ?></li>
        <li>
            <a href="admin.php?page=followup-emails" class="<?php if (empty($_GET['campaign'])) echo 'current'; ?>"><?php _e('All', 'follow_up_emails'); ?></a>
            <?php if ( $count > 0 ) echo '|'; ?>
        </li>
        <?php
        foreach ( $campaigns as $campaign ):
            $i++;
        ?>
        <li>
            <a href="admin.php?page=followup-emails&campaign=<?php echo $campaign->slug; ?>" class="<?php if (!empty($_GET['campaign']) && $_GET['campaign'] == $campaign->slug) echo 'current'; ?>"><?php echo $campaign->name .' ('. $campaign->count .')'; ?></a>
            <?php if ($i < $count) echo '|'; ?>
        </li>
        <?php endforeach; ?>
    </ul>
    <br class="clear">
</div>