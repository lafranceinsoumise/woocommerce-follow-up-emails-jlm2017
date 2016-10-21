<?php
/* @var FUE_Email $email */
$types = Follow_Up_Emails::get_email_types();

if ( empty( $email->type ) && !empty( $_GET['type'] ) ) {
    $email->type = $_GET['type'];
}
?>
<input type="hidden" id="email_id" value="<?php echo $email->id; ?>" />
<select id="email_type" name="email_type" class="select2" data-placeholder="<?php _e('Please select an email type', 'follow_up_emails'); ?>" style="width: 100%;">
    <option value=""></option>
    <?php foreach ( $types as $type ): ?>
    <option value="<?php echo $type->id; ?>" <?php selected( $email->type, $type->id ); ?>><?php echo $type->singular_label; ?></option>
    <?php endforeach; ?>
</select>

<?php foreach ( $types as $type ): ?>
    <p class="email-type-description" id="<?php echo $type->id; ?>_desc" style="display: none;"><?php echo $type->short_description; ?></p>
<?php endforeach; ?>