<?php
$categories = get_terms( 'product_cat', array( 'order_by' => 'name', 'order' => 'ASC' ) );
$coupon_id  = isset($_GET['id']) ? absint( $_GET['id'] ) : 0;
$data       = FUE_Coupons::get_coupon_data( $coupon_id );

if ( $coupon_id > 0 )
    $action = 'update';
else
    $action = 'create';

?>
<div class="wrap woocommerce">
<div class="icon32"><img src="<?php echo FUE_TEMPLATES_URL .'/images/send_mail.png'; ?>" /></div>
    <h2>
        <?php
        if ( $action == 'create' ) {
            _e('Create a New Coupon', 'follow_up_emails');
        } else {
            _e('Update Coupon', 'follow_up_emails');
        }
        ?>
    </h2>
    <form action="admin-post.php" method="post">

        <div id="poststuff">
            <div id="post-body">
                <div class="postbox-container" id="postbox-container-2" style="float:none;">
                    <div id="normal-sortables">
                        <div id="woocommerce-coupon-data" class="postbox">
                            <div class="handlediv"><br/></div>
                            <h3 class="hndle"><span><?php _e('Coupon Data', 'follow_up_emails'); ?></span></h3>
                            <div class="inside">
                                <div id="coupon_options" class="panel-wrap coupon_data" style="padding-top: 0px;">

                                    <div class="wc-tabs-back"></div>

                                    <ul class="coupon_data_tabs wc-tabs" style="display: none;">
                                        <?php
                                        $coupon_data_tabs = apply_filters( 'woocommerce_coupon_data_tabs', array(
                                            'general' => array(
                                                'label'  => __( 'General', 'woocommerce' ),
                                                'target' => 'general_coupon_data',
                                                'class'  => 'general_coupon_data',
                                            ),
                                            'usage_restriction' => array(
                                                'label'  => __( 'Usage Restriction', 'woocommerce' ),
                                                'target' => 'usage_restriction_coupon_data',
                                                'class'  => '',
                                            ),
                                            'usage_limit' => array(
                                                'label'  => __( 'Usage Limits', 'woocommerce' ),
                                                'target' => 'usage_limit_coupon_data',
                                                'class'  => '',
                                            )
                                        ) );

                                        foreach ( $coupon_data_tabs as $key => $tab ) {
                                            ?><li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ' , (array) $tab['class'] ); ?>">
                                            <a href="#<?php echo $tab['target']; ?>"><?php echo esc_html( $tab['label'] ); ?></a>
                                            </li><?php
                                        }
                                        ?>
                                    </ul>
                                    <div id="general_coupon_data" class="panel woocommerce_options_panel">
                                        <div class="options_group">
                                            <p class="form-field">
                                                <label for="name"><?php _e('Name', 'follow_up_emails'); ?></label>
                                                <input type="text" name="name" id="name" value="<?php echo esc_attr($data['name']); ?>" class="short" />
                                                <span class="description"><?php _e('For internal use only', 'follow_up_emails'); ?></span>
                                            </p>
                                            <p class="form-field">
                                                <label for="prefix"><?php _e('Coupon Prefix', 'follow_up_emails'); ?></label>
                                                <input type="text" name="prefix" id="prefix" value="<?php echo esc_attr($data['prefix']); ?>" class="input-text sized" size="25" />
                                                <select id="prefixes">
                                                    <option value=""><?php _e('Choose a Variable', 'follow_up_emails'); ?></option>
                                                    <option value="{customer_first_name}"><?php _e('Customer\'s First Name', 'follow_up_emails'); ?></option>
                                                    <option value="{customer_last_name}"><?php _e('Customer\'s Last Name', 'follow_up_emails'); ?></option>
                                                </select>
                                                <img class="help_tip" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" width="16" height="16" title="<?php _e('Add a prefix to the generated coupon code', 'follow_up_emails'); ?>">
                                            </p>
                                            <p class="form-field">
                                                <label for="type"><?php _e('Discount type', 'follow_up_emails'); ?></label>
                                                <select id="type" name="type">
                                                    <?php
                                                    $types = self::get_discount_types();

                                                    foreach ($types as $key => $type) {
                                                        echo '<option value="'. $key .'" '. selected($data['type'], $key, false) .'>'. $type .'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </p>
                                            <p class="form-field">
                                                <label for="amount"><?php _e('Coupon Amount', 'follow_up_emails'); ?></label>
                                                <input type="text" name="amount" id="amount" class="short" value="<?php echo esc_attr($data['amount']); ?>" placeholder="0.0" />
                                                <img class="help_tip" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" width="16" height="16" title="<?php _e('e.g. 5.99 (do not include the percent symbol)', 'follow_up_emails'); ?>">
                                            </p>
                                            <p class="form-field">
                                                <label for="free_shipping"><?php _e('Allow free shipping', 'follow_up_emails'); ?></label>
                                                <input type="checkbox" class="checkbox" name="free_shipping" id="free_shipping" value="yes" <?php if ($data['free_shipping'] != 0) echo 'checked'; ?> />
                                                <span class="description"><?php _e('Check this box if the coupon grants free shipping. The <a href="admin.php?page=wc-settings&tab=shipping&section=WC_Shipping_Free_Shipping">free shipping method</a> must be enabled and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'follow_up_emails'); ?></span>
                                            </p>
                                        </div>
                                        <div class="options_group">
                                            <p class="form-field">
                                                <label for="expiry"><?php _e('Expiry', 'follow_up_emails'); ?></label>
                                                <select name="expiry_value">
                                                    <option value="" <?php if ($data['expiry_value'] == 0) echo 'selected'; ?>><?php _e('Does not expire', 'follow_up_emails'); ?></option>
                                                    <?php for ($x = 1; $x <= 30; $x++): ?>
                                                        <option value="<?php echo $x; ?>" <?php if ($data['expiry_value'] == $x) echo 'selected'; ?>><?php echo $x; ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                                <select name="expiry_type">
                                                    <option value="" <?php if ($data['expiry_type'] == '') echo 'selected'; ?>>-</option>
                                                    <option value="days" <?php if ($data['expiry_type'] == 'days') echo 'selected'; ?>><?php _e('days', 'follow_up_emails'); ?></option>
                                                    <option value="weeks" <?php if ($data['expiry_type'] == 'weeks') echo 'selected'; ?>><?php _e('weeks', 'follow_up_emails'); ?></option>
                                                    <option value="months" <?php if ($data['expiry_type'] == 'months') echo 'selected'; ?>><?php _e('months', 'follow_up_emails'); ?></option>
                                                </select>
                                                <img class="help_tip" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" width="16" height="16" title="<?php _e('after the discount has been sent to the user', 'follow_up_emails'); ?>">
                                            </p>
                                        </div>
                                    </div>
                                    <div id="usage_restriction_coupon_data" class="panel woocommerce_options_panel">
                                        <div class="options_group">
                                            <p class="form-field">
                                                <label for="minimum_amount"><?php _e('Minimum spend', 'woocommerce'); ?></label>
                                                <input type="text" class="short" name="minimum_amount" id="minimum_amount" value="<?php echo esc_attr( $data['minimum_amount'] ); ?>" placeholder="<?php _e('No minimum', 'woocommerce'); ?>">
                                                <img class="help_tip" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" width="16" height="16" title="<?php _e('This field allows you to set the minimum subtotal needed to use the coupon.', 'woocommerce'); ?>">
                                            </p>
                                            <p class="form-field">
                                                <label for="maximum_amount"><?php _e('Maximum spend', 'woocommerce'); ?></label>
                                                <input type="text" class="short" name="maximum_amount" id="maximum_amount" value="<?php echo esc_attr( $data['maximum_amount'] ); ?>" placeholder="<?php _e('No maximum', 'woocommerce'); ?>">
                                                <img class="help_tip" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" width="16" height="16" title="<?php _e('This field allows you to set the maximum subtotal allowed when using the coupon.', 'woocommerce'); ?>">
                                            </p>
                                        </div>
                                        <div class="options_group">
                                            <p class="form-field">
                                                <label for="individual"><?php _e('Individual use', 'follow_up_emails'); ?></label>
                                                <input type="checkbox" class="checkbox" name="individual_use" id="individual" value="yes" <?php if ($data['individual'] != 0) echo 'checked'; ?> />
                                                <span class="description"><?php _e('Check this box if the coupon cannot be used in conjunction with other coupons', 'follow_up_emails'); ?></span>
                                            </p>

                                            <p class="form-field">
                                                <label for="exclude_sale_items"><?php _e('Exclude sale items', 'follow_up_emails'); ?></label>
                                                <input type="checkbox" value="yes" <?php if ($data['exclude_sale_items'] != 0) echo 'checked'; ?> id="exclude_sale_items" name="exclude_sale_items" style="" class="checkbox">
                                                <span class="description"><?php _e('Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are no sale items in the cart.', 'follow_up_emails'); ?></span>
                                            </p>
                                        </div>


                                        <div class="options_group">
                                            <p class="form-field">
                                                <label for="product_ids"><?php _e('Products', 'woocommerce'); ?></label>
                                                <?php
                                                if ( !is_array( $data['products']) ) {
                                                    $data['products'] = explode( ',', $data['products'] );
                                                }

                                                $product_ids    = array_filter( array_map( 'absint', $data['products'] ) );
                                                $json_ids       = array();

                                                foreach ( $product_ids as $product_id ) {
                                                    $product = WC_FUE_Compatibility::wc_get_product( $product_id );
                                                    $json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
                                                }
                                                ?>
                                                <input
                                                    type="hidden"
                                                    id="product_ids"
                                                    name="product_ids"
                                                    class="ajax_select2_products_and_variations"
                                                    data-multiple="true"
                                                    data-placeholder="Search for a product..."
                                                    style="width: 400px"
                                                    value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>"
                                                    data-selected="<?php echo esc_attr( json_encode( $json_ids ) ); ?>"
                                                    >

                                                <img class="help_tip" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" width="16" height="16" title="<?php _e('Products which need to be in the cart to use this coupon or, for &quot;Product Discounts&quot;, which products are discounted.', 'follow_up_emails'); ?>">
                                            </p>
                                            <p class="form-field">
                                                <label for="exclude_product_ids"><?php _e('Exclude Products', 'woocommerce'); ?></label>
                                                <?php
                                                if ( !is_array( $data['exclude_products'] ) ) {
                                                    $data['exclude_products'] = explode( ',', $data['exclude_products'] );
                                                }
                                                $product_ids    = array_filter( array_map( 'absint', $data['exclude_products'] ) );
                                                $json_ids       = array();

                                                foreach ( $product_ids as $product_id ) {
                                                    $product = WC_FUE_Compatibility::wc_get_product( $product_id );
                                                    $json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
                                                }
                                                ?>
                                                <input
                                                    type="text"
                                                    id="exclude_product_ids"
                                                    name="exclude_product_ids"
                                                    class="ajax_select2_products_and_variations"
                                                    data-multiple="true"
                                                    data-placeholder="Search for a product..."
                                                    style="width: 400px"
                                                    value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>"
                                                    data-selected="<?php echo esc_attr( json_encode( $json_ids ) ); ?>"
                                                    >

                                                <img class="help_tip" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" width="16" height="16" title="<?php _e('Products which must not be in the cart to use this coupon or, for &quot;Product Discounts&quot;, which products are not discounted.', 'follow_up_emails'); ?>">
                                            </p>
                                        </div>
                                        <div class="options_group">
                                            <p class="form-field">
                                                <label for="product_categories"><?php _e('Product Categories', 'woocommerce'); ?></label>
                                                <select id="product_categories" name="product_categories[]" class="select2" multiple="multiple" data-placeholder="Any category" style="width: 400px">
                                                    <?php
                                                    foreach ($categories as $category):
                                                        $selected = (!in_array($category->term_id, $data['categories'])) ? '' : 'selected';
                                                        ?>
                                                        <option value="<?php _e($category->term_id); ?>" <?php echo $selected; ?>><?php echo esc_html($category->name); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <img class="help_tip" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" width="16" height="16" title="<?php _e('A product must be in this category for the coupon to remain valid or, for &quot;Product Discounts&quot;, products in these categories will be discounted.', 'follow_up_emails'); ?>">
                                            </p>
                                            <p class="form-field">
                                                <label for="exclude_product_categories"><?php _e('Exclude Categories', 'woocommerce'); ?></label>
                                                <select id="exclude_product_categories" name="exclude_product_categories[]" class="select2" multiple="multiple" data-placeholder="No categories" style="width: 400px">
                                                    <?php
                                                    foreach ($categories as $category):
                                                        $selected = (!in_array($category->term_id, $data['exclude_categories'])) ? '' : 'selected';
                                                        ?>
                                                        <option value="<?php _e($category->term_id); ?>" <?php echo $selected; ?>><?php echo esc_html($category->name); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <img class="help_tip" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" width="16" height="16" title="<?php _e('Product must not be in this category for the coupon to remain valid or, for &quot;Product Discounts&quot;, products in these categories will not be discounted.', 'follow_up_emails'); ?>">
                                            </p>
                                        </div>
                                    </div>
                                    <div id="usage_limit_coupon_data" class="panel woocommerce_options_panel">
                                        <p class="form-field usage_limit_field">
                                            <label for="usage_limit"><?php _e('Usage limit per coupon', 'woocommerce'); ?></label>
                                            <input type="number" min="0" step="1" class="short" name="usage_limit" id="usage_limit" value="<?php echo esc_attr( $data['usage_limit'] ); ?>" placeholder="Unlimited usage">
                                            <img class="help_tip" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" width="16" height="16" title="<?php _e('How many times this coupon can be used before it is void', 'follow_up_emails'); ?>">
                                        </p>
                                        <p class="form-field usage_limit_per_user_field">
                                            <label for="usage_limit_per_user"><?php _e('Usage limit per user', 'woocommerce'); ?></label>
                                            <input type="number" min="0" step="1" placeholder="Unlimited usage" value="<?php echo esc_attr( $data['usage_limit_per_user'] ); ?>" id="usage_limit_per_user" name="usage_limit_per_user" style="" class="short">
                                            <img class="help_tip" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" width="16" height="16" title="<?php _e('How many times this coupon can be used by an individual user. Uses billing email for guests, and user ID for logged in users.', 'follow_up_emails'); ?>">
                                        </p>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                    <div id="advanced-sortables" class="meta-box-sortables ui-sortable"></div>
                </div>
                <div class="clear"></div>
            </div> <!-- /post-body -->
            <br class="clear">
        </div>

        <p class="submit">
            <input type="hidden" name="action" value="fue_save_coupon" />
            <?php if ( $action == 'create' ): ?>
                <input type="submit" name="save" value="<?php _e('Create Coupon', 'follow_up_emails'); ?>" class="button-primary" />
            <?php else: ?>
                <input type="hidden" name="id" value="<?php echo $data['id']; ?>" />
                <input type="submit" name="save" value="<?php _e('Update Coupon', 'follow_up_emails'); ?>" class="button-primary" />
            <?php endif; ?>
        </p>
    </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        // TABS
        $('ul.coupon_data_tabs').show();
        $('div.panel-wrap').each(function(){
            $(this).find('div.panel:not(:first)').hide();
        });
        $('#coupon_options').on("click", "ul.coupon_data_tabs a", function(){
            var panel_wrap =  $(this).closest('div.panel-wrap');
            $('ul.coupon_data_tabs li', panel_wrap).removeClass('active');
            $(this).parent().addClass('active');
            $('div.panel', panel_wrap).hide();
            $( $(this).attr('href') ).show();
            return false;
        });
        $('ul.coupon_data_tabs li:visible').eq(0).find('a').click();

        jQuery("#prefixes").change(function() {
            jQuery("#prefix").val(jQuery(this).val());
        });

        jQuery(":input.ajax_select2_products_and_variations").filter( ':not(.enhanced)' ).each( function() {
            var select2_args = {
                allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
                placeholder: $( this ).data( 'placeholder' ),
                minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                escapeMarkup: function( m ) {
                    return m;
                },
                ajax: {
                    url:         ajaxurl,
                    dataType:    'json',
                    quietMillis: 250,
                    data: function( term, page ) {
                        return {
                            term:     term,
                            action:   'woocommerce_json_search_products_and_variations',
                            security: '<?php echo wp_create_nonce("search-products"); ?>'
                        };
                    },
                    results: function( data, page ) {
                        var terms = [];
                        if ( data ) {
                            $.each( data, function( id, text ) {
                                terms.push( { id: id, text: text } );
                            });
                        }
                        return { results: terms };
                    },
                    cache: true
                }
            };

            if ( $( this ).data( 'multiple' ) === true ) {
                select2_args.multiple = true;
                select2_args.initSelection = function( element, callback ) {
                    var data     = $.parseJSON( element.attr( 'data-selected' ) );
                    var selected = [];

                    $( element.val().split( "," ) ).each( function( i, val ) {
                        selected.push( { id: val, text: data[ val ] } );
                    });
                    return callback( selected );
                };
                select2_args.formatSelection = function( data ) {
                    return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
                };
            } else {
                select2_args.multiple = false;
                select2_args.initSelection = function( element, callback ) {
                    var data = {id: element.val(), text: element.attr( 'data-selected' )};
                    return callback( data );
                };
            }


            jQuery(this).select2(select2_args);
        } );

        jQuery(":input.select2").select2().addClass( 'enhanced' );
        jQuery(".tips, .help_tip").tipTip({
            'attribute' : 'title',
            'fadeIn' : 50,
            'fadeOut' : 50,
            'delay' : 200
        });
    });
</script>