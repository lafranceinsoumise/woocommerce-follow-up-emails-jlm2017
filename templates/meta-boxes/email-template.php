<?php
/* @var FUE_Email $email */
$templates = fue_get_installed_templates();
?>
<select id="template" name="template" class="select2" data-placeholder="<?php _e('Please select a template', 'follow_up_emails'); ?>" style="width: 100%;">
    <option value=""></option>
    <?php
    foreach ( $templates as $template ):
        $template = fue_template_basename( $template );
        $tpl = new FUE_Email_Template( $template );
        $name = $tpl->name;

        if (! $name ) {
            $name = $template;
        }
    ?>
    <option value="<?php echo $template; ?>" <?php selected( basename( $email->template ), $template ); ?>><?php echo wp_kses( $name, array() ); ?></option>
    <?php endforeach; ?>
</select>