jQuery( document ).ready( function () {
    // Trigger WooCommerce Tooltips. This is used to trigger tooltips added by function \wc_help_tip
    var tiptip_args = {
        'attribute': 'data-tip',
        'fadeIn': 50,
        'fadeOut': 50,
        'delay': 200
    };
    jQuery( '.tips, .help_tip, .woocommerce-help-tip' ).tipTip( tiptip_args );
})