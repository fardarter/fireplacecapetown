jQuery(document).ready(function () {
    // Sets interval...what is transition slide speed?
    jQuery('#myCarousel').carousel({
        interval: 8000
    });

    jQuery("#ask_product_form").submit(function ( e ) {
        error = 0;
        em_regex = /^(\s)*(([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+)((\s)*,(\s)*(([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+)(\s)*(,)*)*(\s)*$/;
        email = jQuery('#wdm_user_email').val();
        if (email == '') {
            jQuery('.email_error').text("Email cannot be blank");
            error = 1;
        } else {
            jQuery('.email_error').text('');
        }
        if ( error == 1 ) {
            return false;
        }

    });

    jQuery('#wdm_user_email').keyup(function(){
        if(jQuery('#wdm_user_email').val().length < 1) {
            jQuery('.email_error').text("Email cannot be blank");
        } else {
            jQuery('.email_error').text("");
        }
    })

    jQuery('#enable_telephone_no_txtbox').click(function () {
        if ( jQuery(this).is(':checked') ) {
            jQuery('#make_phone_mandatory_chkbox').closest('div.fd').show();
        } else {
            jQuery('#make_phone_mandatory_chkbox').closest('div.fd').hide();
        }
    });

})
