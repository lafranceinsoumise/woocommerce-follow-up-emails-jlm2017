<div class="wrap fue-addons-wrap">
    <h2>
        <?php _e('Templates', 'follow_up_emails'); ?>
        <a class="add-new-h2" href="admin.php?page=followup-emails-templates&action=new"> <?php _e('Create New Follow-Up', 'follow_up_emails'); ?> </a>
    </h2>

    <?php

    include FUE_TEMPLATES_DIR .'/add-ons/notifications.php';

    if ( isset( $_POST['action'] ) ) {
        if ( $_POST['action'] == 'template_create' ) {
            include FUE_TEMPLATES_DIR . '/add-ons/template-create.php';
        } elseif ( $_POST['action'] == 'template_upload' ) {
            if ( isset($_FILES['template']['tmp_name'] ) && is_uploaded_file( $_FILES['template']['tmp_name'] ) ) {
                include FUE_TEMPLATES_DIR . '/add-ons/template-upload.php';
            } else {
                show_message( __('No file selected or file is too large', 'follow_up_emails') );
            }
        }
    } else {
        $action = (empty($_GET['action'])) ? 'list' : $_GET['action'];

        switch ( $action ) {
            case 'install_template':
                include FUE_TEMPLATES_DIR . '/add-ons/templates-install.php';
                break;

            case 'uninstall_template':
                include FUE_TEMPLATES_DIR . '/add-ons/templates-uninstall.php';
                break;

            case 'new':
                include FUE_TEMPLATES_DIR . '/add-ons/templates-new.php';
                break;

            default:
                include FUE_TEMPLATES_DIR . '/add-ons/templates-list.php';
                break;
        }
    }
    ?>

</div>