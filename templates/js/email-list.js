jQuery(window).load(function(){
    // Subsubsub tabs
    jQuery('div.subsubsub_section ul.subsubsub.types li a:eq(0)').addClass('current');
    jQuery('div.subsubsub_section .section:gt(0)').hide();

    jQuery('div.subsubsub_section ul.subsubsub li a').click(function(){
        var $clicked = jQuery(this);
        var $section = $clicked.closest('.subsubsub_section');
        var $target  = $clicked.attr('href');

        $section.find('a').removeClass('current');

        if ( $section.find('.section:visible').size() > 0 ) {
            $section.find('.section:visible').fadeOut( 100, function() {
                $section.find( $target ).fadeIn('fast');
            });
        } else {
            $section.find( $target ).fadeIn('fast');
        }

        $clicked.addClass('current');
        jQuery('#last_tab').val( $target );

        return false;
    });

    // Active/Archived Tabs
    jQuery(".email-tab.inactive").hide();

    jQuery(".status-tab").click(function(e) {
        e.preventDefault();

        var $clicked    = jQuery(this);
        var $section    = $clicked.closest('.nav-tab-wrapper');
        var $target     = $clicked.attr('href');
        var $key        = $clicked.data('key');

        $section.find('a').removeClass('nav-tab-active');

        if ( jQuery('.' + $key +'-tab:visible').size() > 0 ) {
            jQuery('.'+ $key +'-tab:visible').fadeOut( 100, function() {
                jQuery($target).fadeIn('fast');
            });
        } else {
            jQuery( $target ).fadeIn('fast');
        }

        $clicked.addClass('nav-tab-active');

        return false;
    });

    var url_hash = window.location.hash;
    if (url_hash != "") {
        jQuery("a[href="+ url_hash +"]").click();
    }

    // Sorting
    jQuery('table.fue-sortable tbody').sortable({
        items:'tr',
        cursor:'move',
        axis:'y',
        handle: 'td',
        scrollSensitivity:40,
        helper:function(e,ui){
            ui.children().each(function(){
                jQuery(this).width(jQuery(this).width());
            });
            ui.css('left', '0');
            return ui;
        },
        start:function(event,ui){
            ui.item.css('background-color','#f6f6f6');
        },
        stop:function(event,ui){
            ui.item.removeAttr('style');
            update_priorities();
        }
    });

    // Cloning
    jQuery("a.clone-email").click(function(e) {
        e.preventDefault();

        var name        = prompt(FUE.email_name);
        var email_id    = jQuery(this).data("id");
        var parent      = jQuery(this).parents("table");

        if (name) {
            jQuery(parent).block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });

            var data = {
                action: 'fue_clone_email',
                id:     email_id,
                name:   name,
                woo_nonce: ''
            };

            jQuery.post(ajaxurl, data, function(resp) {
                resp = JSON.parse(resp);

                if (resp.status == "OK") {
                    window.location.href = resp.url;
                } else {
                    alert(resp.message);
                    jQuery(parent).unblock();
                }

            });

        }
    });

    jQuery("table.fue").on("click", ".archive-email", function(e) {
        e.preventDefault();

        var table   = jQuery(this).parents("table");
        var parent  = jQuery(this).parents("tr");
        var id      = jQuery(this).data("id");
        var key     = jQuery(this).data("key");
        var that    = this;

        jQuery(table).block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });

        var data = {
            action: 'fue_archive_email',
            id:     id,
            woo_nonce: ''
        };

        jQuery.post(ajaxurl, data, function(resp) {
            resp = jQuery.parseJSON(resp);
            if (resp.ack != "OK") {
                alert(resp.error);
            } else {
                var $tr = jQuery(parent).clone();;
                jQuery(parent).fadeOut(function() {
                    jQuery(parent).remove();
                });

                jQuery($tr).find("td.status").html(resp.status_html);

                jQuery( "#"+ key +"_archived_tab table."+ key +"-table tbody").append($tr);
                jQuery( "#"+ key +"_archived_tab table."+ key +"-table tr.no-archived-emails").hide();

            }
            jQuery(table).unblock();
        });

    });

    jQuery(".unarchive").live("click", function(e) {
        e.preventDefault();

        var table   = jQuery(this).parents("table");
        var parent  = jQuery(this).parents("tr");
        var id      = jQuery(this).data("id");
        var key     = jQuery(this).data("key");
        var that    = this;

        jQuery(table).block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });

        var data = {
            action: 'fue_unarchive_email',
            id:     id,
            woo_nonce: ''
        };

        jQuery.post(ajaxurl, data, function(resp) {
            resp = jQuery.parseJSON(resp);
            if (resp.ack != "OK") {
                alert(resp.error);
            } else {
                var $tr = jQuery(parent).clone();;
                jQuery(parent).fadeOut(function() {
                    jQuery(parent).remove();

                    if ( jQuery( "#"+ key +"_archived_tab table."+ key +"-table tbody tr").length == 1 ) {
                        jQuery( "#"+ key +"_archived_tab table."+ key +"-table tr.no-archived-emails").show();
                    }

                });

                jQuery($tr).find("td.status").html(resp.status_html);

                jQuery( "#"+ key +"_active_tab table."+ key +"-table tbody").append($tr);

            }
            jQuery(table).unblock();
        });

    });

    jQuery(".toggle-activation").live("click", function(e) {
        e.preventDefault();

        var parent  = jQuery(this).parents("table");
        var id      = jQuery(this).data("id");
        var that    = this;

        jQuery(parent).block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });

        var data = {
            action: 'fue_toggle_email_status',
            id:     id,
            woo_nonce: ''
        };

        jQuery.post(ajaxurl, data, function(resp) {
            resp = jQuery.parseJSON(resp);
            if (resp.ack != "OK") {
                alert(resp.error);
            } else {
                var el = jQuery(that).parents("td.status").eq(0).find("span.status-toggle");
                jQuery(el).html(resp.new_status + '<br/><small><a href="#" class="toggle-activation" data-id="'+ id +'">'+ resp.new_action +'</a></small>');
            }
            jQuery(parent).unblock();
        });

    });

});
function update_priorities() {
    jQuery('table tbody').each(function(i) {

        jQuery(this).find("tr").each(function(x) {
            jQuery(this).find("td .priority").html(x+1);
        });

    });
}