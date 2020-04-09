jQuery(document).ready(function () {
    jQuery(document).on( 'click', 'div.wdm-privacy-notice .notice-dismiss', function() {
        var notice_id = jQuery("div.wdm-privacy-notice").data("notice-id");
        var data = {
            'action': 'pe_notice_dismiss',
            'notice_id': notice_id,
        };

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: data
        });
    });
});