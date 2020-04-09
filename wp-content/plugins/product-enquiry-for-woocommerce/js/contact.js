jQuery(function($) {
    dialog = $( "#contact-form" ).dialog({
        autoOpen:       false,
        height:         'auto',
        maxHeight:      570,
        width:          'auto',
        dialogClass:    "modal-enquiry-form",
        // maxWidth: $(window).width() - 180,
        modal:          true,
        fluid:          true,
        resizable:      false,
        //draggable: false,
        //opacity:0.75,
        show: function() {$(this).fadeIn(300);},
        hide:function() {$(this).fadeOut(300);},
    });
    $( "#enquiry input.contact" ).click(function() {
        dialog.dialog( "open" );
	   //  $("#contact-form").closest(".ui-dialog").css("position","fixed");
    });
    $( "#cancel" ).click(function() {
        dialog.dialog( "close" );
    });

    $('#send-btn').click(function() {
        //e.preventDefault();
        jQuery.validator.addMethod("validatePhone", function(value, element){
            if('0' == value.trim().length) {
                return true;
            }

            var validation = new RegExp('^[0-9(). \-+]{1,16}$');
            return validation.test(value);
        }); 

        $('#enquiry-form').validate({
            errorElement: "div",
            //onkeyup: false,
            // onfocusout: false,
            rules:
            {
                wdm_customer_name:{
                    required: true,
                },
                wdm_customer_email:{
                    required:true,
                    email:true
                },
                wdm_txtphone: {
                    validatePhone: true,
                },
                wdm_enquiry:{
                    required: true,
                    minlength:10,
                },
                agree:"required"
            },

            messages:{
                wdm_customer_name:object_name.wdm_customer_name,
                wdm_customer_email:object_name.wdm_customer_email,
                wdm_txtphone: {
                    required: object_name.wdm_txtphone_required,
                    validatePhone: object_name.wdm_txtphone_invalid,
                },
                wdm_enquiry:object_name.wdm_enquiry,
            },
            errorPlacement: function(error, element) {
                error.appendTo("div#errors");
            },
            submitHandler:function(form) {
                var name            = $("[name='wdm_customer_name']").val();
                var emailid         = $("[name='wdm_customer_email']").val();
                var phone_number    = $("[name='wdm_txtphone']").val();
                var subject         = $("[name='wdm_subject']").val();
                var enquiry         = $("[name='wdm_enquiry']").val();
                var cc              = $("[name='cc']").is(':checked') ? 1 : 0;
                var product_name    = $("#wdm_product_name").html();
                var product_url     = window.location.href;
                var security        = $("[name='product_enquiry']").val();
                var authoremail     = $('#author_email').val();
                var product_id      = $("[name='wdm_product_id']").val();
                var variation_id    = $('input.variation_id').val();

                dialog.dialog( "close" );

                $( "#loading" ).dialog({
                    create: function( event, ui ) {
                        var dialog = $(this).closest(".ui-dialog");
                        dialog.find(".ui-dialog-titlebar").remove();
                    },
                    resizable: false,
                    width:'auto',
                    height:'auto',
                    modal: true,
                    //draggable: false
                });

                $.ajax({
                    url: object_name.ajaxurl,
                    type:'POST',
                    data: {action:'wdm_send',security:security,wdm_name:name,wdm_emailid:emailid,wdm_subject:subject, wdm_phone: phone_number, wdm_enquiry:enquiry,wdm_cc:cc,wdm_product_name:product_name,wdm_product_url:product_url,uemail:authoremail, wdm_product_id: product_id, wdm_variation_id: variation_id},
                    success: function(response) {
                        $( "#send_mail" ).hide();
                        $( "#loading" ).text(response);
                        $( "#loading" ).dialog( "option", "buttons", {"OK": function() { $(this).dialog("close"); } });
                    }
                });
                form.reset();
            }
        });
    });
    $(".ui-dialog").addClass("wdm-enquiry-modal");
});
