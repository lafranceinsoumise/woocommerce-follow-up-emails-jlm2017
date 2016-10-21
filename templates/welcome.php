<div class="wrap about-wrap">

    <h1><?php printf( __( 'Welcome to Follow-Ups %s', 'follow-up-emails' ), $major_version ); ?></h1>

    <div class="about-text fue-about-text">
        <?php
        if ( ! empty( $_GET['fue-installed'] ) ) {
            $message = __( 'Thank you - all done!', 'follow-up-emails' );
        } elseif ( ! empty( $_GET['fue-updated'] ) ) {
            $message = __( 'Thank you for updating to the latest version!', 'follow-up-emails' );
        } else {
            $message = __( 'Thank you for installing!', 'follow-up-emails' );
        }

        printf( __( '%s Follow-Up Emails %s is more stable, more feature packed, and easier to use than ever before. We hope you enjoy using it.', 'follow-up-emails' ), $message, $major_version );
        ?>
    </div>
    
	<div class="fue-badge">
        <span class="dashicons dashicons-email"></span>
        <?php printf( __( 'Version %s', 'follow-up-emails' ), FUE_VERSION ); ?>
    </div>

    <p class="fue-actions">
        <a href="<?php echo admin_url('admin.php?page=followup-emails-settings'); ?>" class="button button-primary"><?php _e( 'Settings', 'follow-up-emails' ); ?></a>
        <a href="<?php echo esc_url( apply_filters( 'fue_docs_url', 'http://docs.woothemes.com/document/automated-follow-up-emails/', 'follow-up-emails' ) ); ?>" target="_blank" class="docs button button-primary"><?php _e( 'Docs', 'follow-up-emails' ); ?></a>
        <a href="https://twitter.com/share" class="twitter-share-button" style="margin-top:10px;" data-url="http://www.woothemes.com/products/follow-up-emails/" data-text="<?php echo esc_attr( $this->tweets[0] ); ?>" data-via="75nineteen" data-size="large">Tweet</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
    </p>


    <h2>What's New</h2>

	<div class="feature-section two-col">
		<div class="col">
			<div class="media-container">
				<img src="http://www.75nineteen.com/twitter-followups.png">
			</div>
			<h3><span class="dashicons dashicons-twitter"></span> Twitter Follow-ups</h3>
				<p>Communication is changing. It is no longer all about email despite email still having the highest response rates. We've added the ability to Tweet your customers after their purchases. Continue to engage in a greater capacity on another medium your customers expect.</p>
				<a class="button button-primary" href="<?php echo admin_url('admin.php?page=followup-emails-settings&tab=integration'); ?>">Setup Twitter Now</a>
		</div>
		<div class="col">
			<div class="media-container">
                <img src="http://www.75nineteen.com/customer-data-manager.png">
			</div>
			<h3><span class="dashicons dashicons-awards"></span> Customer Insights and Reminder Manager</h3>
				<p>Want to see details on your customers? How many emails they are opening? What they are spending? Set up reminders, create tasks and follow-ups for you and your team, and set manual emails campaigns? You can do all that with Follow-ups.</p>
				<a class="button button-primary" href="<?php echo admin_url('admin.php?page=followup-emails-reports-customers'); ?>">See  Customer Insights Now</a>
		</div>
	</div>  
	
	<h2>Take your store marketing to #awesome</h2>

    <div class="changelog">
        <div class="changelog about-integrations">
            <div class="fue-feature feature-section last-feature-section col three-col">
	            <div>
                    <h4>Custom Email Templates</h4>
                    <p>Create your own custom templates. There is no reason be stuck with the stock WooCommerce templates. Use Follow-up's proprietary merge tags to create dynamic beautiful email templates, and create one or many custom email templates for your Follow-up Emails.</p>
            	</div>
				<div>
                	<h4>Newsletters</h4>
					<p>Have existing subscribers? Manage and import your lists to get the most out of Follow-ups. Even segment your buyers into one or more lists to further target them with emails. Create beautiful emails to keep in touch with your customers, and your subscribers.</p>
            	</div>                
				<div class="last-feature">
                    <h4>Free DKIM &amp; SPF</h4>
                    <p>Set up DKIM &amp; SPF to reduce spam. These two records in your DNS improve email deliverability and reduce spam. The DKIM check verifies that the message is signed and associated with the correct domain, SPF checks that your email comes from authorized servers.</p>
            	</div>
        	</div>
    	</div>
	</div>
	
    <hr />

    <div class="return-to-dashboard">
        <a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'followup-emails-settings' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to Follow-Up Emails Settings', 'follow-up-emails' ); ?></a>
    </div>
</div>