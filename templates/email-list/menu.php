<ul class="subsubsub types">
    <li><?php _e('View by Email Type:', 'follow_up_emails'); ?></li>
    <?php
    $num    = count($types);
    $i      = 0;

    foreach ( $types as $key => $type ):
        $i++;
        $cls = ($i == 1) ? 'current' : '';
        echo '<li><a href="#'. $type->id .'_mails" class="'. $cls .'">'. $type->label .'</a>';

        if ($i < $num) echo '|';
        echo '</li>';

    endforeach;
    ?>
    <?php do_action( 'fue_email_types_sub' ); ?>
</ul>
<br class="clear">