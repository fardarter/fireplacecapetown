<?php
/**
 * Plugin Name: Product Enquiry for WooCommerce
 * Description: Allows prospective customers or visitors to make enquiry about a product, right from within the product page.
 * Version: 2.6.4
 * Author: WisdmLabs
 * Author URI: https://wisdmlabs.com
 * Plugin URI: https://wordpress.org/plugins/product-enquiry-for-woocommerce
 * License: GPL2
 * Text Domain: product-enquiry-for-woocommerce
 * Domain Path: /languages/
 * WC requires at least: 3.0.0
 * WC tested up to: 3.8.0
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define('PEFREE_VERSION', '2.6.4');

// For subscription
if (is_admin()) {
    define('WDM_SUBME_PEP_PLUGIN_PATH', plugin_dir_path(__FILE__));
    define('WDM_SUBME_PEP_PLUGIN_URL', plugin_dir_url(__FILE__));
    global $pagenow;
    if ($pagenow == 'admin.php' && ($_GET['page'] == 'product-enquiry-for-woocommerce')) {
        if (file_exists(WDM_SUBME_PEP_PLUGIN_PATH . 'library/class_wdmsubme.php')) {
            require_once(WDM_SUBME_PEP_PLUGIN_PATH . 'library/class_wdmsubme.php');
            $wdmsubme_obj = new class_wdmsubme();
        }
    }
}

add_action('plugins_loaded', 'wdm_pe_init');
function wdm_pe_init()
{
    load_plugin_textdomain('product-enquiry-for-woocommerce', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('admin_init', 'check_woo_dependency');

//Check whether WooCommerce is active or not

function check_woo_dependency()
{
    if (!is_plugin_active('woocommerce/woocommerce.php')) {
        echo "<div class='error'><p>". sprintf(__('%s WooCommerce %s plugin is not active. In order to make %s Product Enquiry %s plugin work, you need to install and activate %s WooCommerce %s first', 'product-enquiry-for-woocommerce'), "<strong>", "</strong>", "<strong>", "</strong>", "<strong>", "</strong>") . "</p></div>";

        if (is_plugin_active('product-enquiry-for-woocommerce/product-enquiry-for-woocommerce.php')) {
            deactivate_plugins(plugin_basename(__FILE__));
        }
        unset($_GET['activate']);
    }
}

add_action('admin_init', 'messageProductEnquiryPro');

function messageProductEnquiryPro()
{
    if (is_plugin_active('product-enquiry-pro/product_enquiry_pro.php')) {
        echo "<div class='error'><p>". __('Product Enquiry Pro plugin is active. Please deactivate in order to install Product Enquiry Free', 'product-enquiry-for-woocommerce') ."</p></div>";
    }
}

add_action('admin_notices', 'privacyAdminNotice');

function privacyAdminNotice()
{
    if (!current_user_can('manage_options') || get_option('wdm_privacy_notice_dismissed', false)) {
        return;
    }

    ?>
    <div class="wdm-privacy-notice notice notice-info is-dismissible" data-notice-id="wdm_privacy_notice_dismissed">
        <p>
            <?php _e('Dear User,', 'product-enquiry-for-woocommerce'); ?><br>
            <?php
                _e('This is to inform you that WisdmLabs does not collect any user data. The data that is sent directly to your inbox after filling the enquiry form is your sole responsibility and we urge you to update the privacy policy of your websites.', 'product-enquiry-for-woocommerce')
                ?>
            <br>
            <?php _e('Regards,', 'product-enquiry-for-woocommerce'); ?><br>
            <?php _e('WisdmLabs', 'product-enquiry-for-woocommerce'); ?>
        </p>
    </div>
    <?php

    wp_enqueue_script('wdm-admin-notice-js', plugins_url('js/admin/notice.js', __FILE__),
        array('jquery'), PEFREE_VERSION, true);
}

add_action( 'wp_ajax_pe_notice_dismiss', 'peNoticeDismissed' );


function peNoticeDismissed()
{
    $notice_id = isset($_POST['notice_id']) ? $_POST['notice_id'] : '';

    if(empty($notice_id))
    {
        return;
    }

    update_option('wdm_privacy_notice_dismissed', 1);

    die(1);
}

/*function deactivate_dependent_product_enquiry_pro()
{
    $my_plugin = 'product-enquiry-pro/product_enquiry_pro.php';

    deactivate_plugins($my_plugin);
}
*/
add_action('wp_head', 'wdm_display_btn_func', 11);

function wdm_display_btn_func()
{
    $form_init_data = get_option('wdm_form_data');

    // if (!empty($form_init_data)) {
    if (isset($form_init_data['show_after_summary'])) {
        if ($form_init_data['show_after_summary'] == 'after_add_cart') {
            //show ask button after a single product summary add to cart
            add_action('woocommerce_single_product_summary', 'ask_about_product_button', 30);
        } elseif ($form_init_data['show_after_summary'] == 'after_product_summary') {
            add_action('woocommerce_after_single_product_summary', 'ask_about_product_button');
        }
    } else {
        add_action('woocommerce_single_product_summary', 'ask_about_product_button', 30);
    }
}

function ask_about_product_button()
{
    $form_data = get_option('wdm_form_data');
    global $isProduct;
    $isProduct = true;
    ?>
     <div id="enquiry">
            <input type="button" name="contact" value="<?php echo empty($form_data['custom_label']) ?
            __('Make an enquiry for this product', 'product-enquiry-for-woocommerce'): $form_data['custom_label'];?>" class="contact wpi-button single_add_to_cart_button button alt" />
     </div>
        
<?php }

add_action('wp_footer', 'ask_about_product');

function ask_about_product()
{
    if(!is_product())
    {
        return;
    }

    $form_data = get_option('wdm_form_data');
    global $wpdb,$post, $isProduct;
    $query = "SELECT user_email FROM {$wpdb->posts} AS p JOIN {$wpdb->users} AS u ON p.post_author=u.ID WHERE p.ID=%d";
    $authorEmail = $wpdb->get_var($wpdb->prepare($query, $post->ID));

    ?>
     <!-- Page styles -->
    <?php
       // wp_enqueue_style("wdm-contact-css", plugins_url("css/contact.css", __FILE__));
    if (is_product()) {
        wp_enqueue_style("wdm-juery-css", plugins_url("css/wdm-jquery-ui.css", __FILE__), [], PEFREE_VERSION);
        if (isset($form_data[ 'user_custom_css' ])) {
            wp_add_inline_style('wdm-juery-css', $form_data[ 'user_custom_css' ]);
        }
    }
    ?>
    <?php 
    if ($isProduct/*&&(!empty($form_data['show_at_page_end']))||(!empty($form_data['show_after_summary']))*/) { ?>
        <div id="contact-form" title="<?php _e("Product Enquiry", "product-enquiry-for-woocommerce");?>" style="display:none;">
        <form id="enquiry-form" action="#" method="POST">
            <?php do_action('pefree_before_enquiry_form'); ?>
            <label id="wdm_product_name" for='product_name'> <?php echo get_the_title();?> </label>
            <div class="wdm-pef-form-row">
                <input type="hidden" name="wdm_product_id" value="<?php echo $post->ID ?>" />
                <label for='contact-name'>*<?php _e("Name", "product-enquiry-for-woocommerce");?>:</label>
                <input type='hidden' name='author_email' id='author_email' value='<?php echo $authorEmail ?>'>
                <input type='text' id='contact-name' class='contact-input' name='wdm_customer_name' value=""/>
            </div>
            <div class="wdm-pef-form-row">
                <label for='contact-email'>*<?php _e("Email", "product-enquiry-for-woocommerce");?>:</label>
                <input type='text' id='contact-email' class='contact-input' name='wdm_customer_email'  />
            </div>
            <!-- Phone number field -->
            <?php if (!empty($form_data['enable_telephone_no'])) {?>
            <div class="wdm-pef-form-row">
            <label for='wdm_txtphone'>
            <?php
            $wdm_phone_required = '';
            if(isset($form_data['make_phone_mandatory'])) {
                $wdm_phone_required = 'required';
                ?>*<?php    
            }        
            _e("Phone No", "product-enquiry-for-woocommerce");?>:</label>
            <input type="text" name="wdm_txtphone" id="wdm_txtphone" class="contact-input phone-field" value="" <?php echo $wdm_phone_required; ?>>
            </div>
            <?php }?>
            <!-- Subject Field -->
            <div class="wdm-pef-form-row">
                <label for='contact-subject'><?php _e("Subject", "product-enquiry-for-woocommerce");?>:</label>
                <input type='text' id='contact-subject' class='contact-input' name='wdm_subject' value=''  />
            </div>
            <div class="wdm-pef-form-row">
                <label for='contact-message'>*<?php _e("Enquiry", "product-enquiry-for-woocommerce");?>:</label>
                <textarea id='contact-message' class='contact-input' name='wdm_enquiry' cols='40' rows='4' style="resize:none"></textarea>
            </div>
            <?php if (!empty($form_data['enable_send_mail_copy'])) {?>
            <div class="wdm-pef-send-copy">
                <input type='checkbox' id='contact-cc' name='cc' value='1' /> <span class='contact-cc'>
                <?php _e("Send me a copy", "product-enquiry-for-woocommerce");?></span>
            </div>
            <?php }?>
            <!-- Enquiry Terms and Conditions Checkbox -->
             <?php if (peShouldDisplayTermsCond()) {?>
            <div class="wdm-terms-cond-cb">
                <input type='checkbox' id='terms-cond-cb' name='enable_terms_conditions' value='1' required data-msg-required='<?php _e('Please select terms and conditions', 'product-enquiry-for-woocommerce'); ?>'/> <span class='terms-cond-text'>
                <?php echo getTermsConditionsText(); ?></span>
            </div>
            <?php }?>

            <div id="errors"></div>
            <div class="wdm-enquiry-action-btns">
                <button id="send-btn" type='submit' class='contact-send contact-button' ><?php _e("Send", "product-enquiry-for-woocommerce");?></button>
                <button id="cancel" type='button' class='contact-cancel contact-button' ><?php _e("Cancel", "product-enquiry-for-woocommerce");?></button>
            </div>
            <?php echo wp_nonce_field('enquiry_action', 'product_enquiry', true, false); ?>
            <?php do_action('pefree_after_enquiry_form'); ?>
        </form>
        <?php
     
        $site_url           = site_url();
        $domain_name        = htmlspecialchars(url_to_domain($site_url));
        $domain_name_value  = ord($domain_name);

        if ($domain_name_value >= 97 && $domain_name_value <= 102) {
            $display_url        = 'https://wisdmlabs.com/';
            $display_message    = 'WordPress Development Experts';
            $prefix             = 'Brought to you by WisdmLabs: ';
            $suffix             = '';
        } elseif ($domain_name_value >= 103 && $domain_name_value <= 108) {
            $display_url        = 'https://wisdmlabs.com/wordpress-development-services/plugin-development/';
            $display_message    = 'Expert WordPress Plugin Developer';
            $prefix             = 'Brought to you by WisdmLabs: ';
            $suffix             = '';
        } elseif ($domain_name_value >= 109 && $domain_name_value <= 114) {
            $display_url        = 'https://wisdmlabs.com/woocommerce-extension-development-customization-services/';
            $display_message    = 'Expert WooCommerce Developer';
            $prefix             = 'Brought to you by WisdmLabs: ';
            $suffix             = '';
        } else {
            $display_url        = 'https://goo.gl/gBtwX4';
            $display_message    = 'WooCommerce Enquiry Plugin';
            $prefix             = '';
            $suffix             = ' by WisdmLabs';
        }
        ?>
        <div class='contact-bottom'><a href='#' onclick="return false;"><?php echo $prefix; ?></a><a href='<?php echo $display_url ?>' target='_blank' rel='nofollow'><?php echo $display_message;?></a><a href='#' onclick="return false;"><?php echo $suffix; ?></a></div>
        </div>
        <!-- preload the images -->    
        <div id="loading" style='display:none'>
            <div id="send_mail"><p><?php _e("Sending...", "product-enquiry-for-woocommerce");?></p>
            <img src='<?php echo plugins_url("img/contact/loading.gif", __FILE__)?>' alt='' />
            </div>
        </div> 
    <?php
        unset($isProduct);
    }
        ?>
    <!-- Load JavaScript files -->
    <?php

    wp_enqueue_script("jquery-ui-core", array("jquery"));
    wp_enqueue_script("jquery-ui-dialog", array("jquery"));
    wp_enqueue_script("wdm-validate", plugins_url("js/wdm_jquery.validate.min.js", __FILE__), [], PEFREE_VERSION);
    //wp_enqueue_script("wdm-validate", plugins_url("js/jquery.validate.min.js", __FILE__));
    wp_enqueue_script("wdm-contact", plugins_url("js/contact.js", __FILE__), array("jquery"), PEFREE_VERSION);
    wp_localize_script(
        'wdm-contact',
        'object_name',
        array('ajaxurl'                 => admin_url('admin-ajax.php'),
                'product_name'          => get_the_title(),
                'wdm_customer_name'     => __('Name is required.', 'product-enquiry-for-woocommerce'),
                'wdm_customer_email'    => __('Enter valid Email Id.', 'product-enquiry-for-woocommerce'),
                'wdm_txtphone_required' => __('Phone number is required.', 'product-enquiry-for-woocommerce'),
                'wdm_txtphone_invalid'  => __('Enter valid phone number.', 'product-enquiry-for-woocommerce'),
                'wdm_enquiry'           => __(
                    'Enquiry length must be atleast 10 characters.',
                    'product-enquiry-for-woocommerce'
                ),
        )
    );
}
/* Thanks to davejamesmiller*/
function url_to_domain($site_url)
{
    $host = @parse_url($site_url, PHP_URL_HOST);

    // If the URL can't be parsed, use the original URL
    // Change to "return false" if you don't want that
    if (!$host) {
        $host = $site_url;
    }

    // The "www." prefix isn't really needed if you're just using
    // this to display the domain to the user
    if (substr($host, 0, 4) == "www.") {
        $host = substr($host, 4);
    }

    // You might also want to limit the length if screen space is limited
    if (strlen($host) > 50) {
        $host = substr($host, 0, 47) . '...';
    }

    return $host;
}


add_action('admin_menu', 'create_ask_product_menu');
function create_ask_product_menu()
{
    //create a submenu under Woocommerce 'Products' menu
    // add_submenu_page('edit.php?post_type=product', __('Product Enquiry', 'product-enquiry-for-woocommerce'), __('Product Enquiry', 'product-enquiry-for-woocommerce'), 'manage_options', 'pefw', 'add_ask_product_settings');
    if (is_plugin_active('woocommerce/woocommerce.php')) {
        add_menu_page(__('Product Enquiry', 'product-enquiry-for-woocommerce'), __('Product Enquiry', 'product-enquiry-for-woocommerce'), 'manage_options', 'product-enquiry-for-woocommerce', 'add_ask_product_settings');
    }
}

function add_ask_product_settings()
{
    wp_enqueue_script('wdm_bootstrap_js', plugins_url("/js/bootstrap.min.js", __FILE__), array('jquery'), PEFREE_VERSION);
    
    wp_enqueue_style('wdm_bootstrap_css', plugins_url('css/bootstrap.min.css', __FILE__), [], PEFREE_VERSION);
    //settings page
    wp_enqueue_script('wdm_wpi_validation', plugins_url("js/wdm_jquery.validate.min.js", __FILE__), array('jquery'), PEFREE_VERSION);
   // <?php echo plugins_url('/img/pro.png', __FILE__);
    ?>
    <div class="wrap wdm_leftwrap">
        <div class='wdm-pro-notification'>
            <?php $current_time = current_time( 'timestamp', 1 );
            if ( 1574879400 < $current_time && 1575354600 > $current_time ) : ?>
                <a href="http://bit.ly/35AKdOr" target="_blank"><img src="<?php echo plugins_url('/img/wisdm-BFCM.png', __FILE__); ?>" width="100%" alt="Product Enquiry Pro"></a>
            <?php else : ?>
            <a href="https://goo.gl/Ez4mZ9" target="_blank"><img src="<?php echo plugins_url('/img/pep.png', __FILE__); ?>" width="100%" alt="Product Enquiry Pro"></a>
            <?php endif; ?>
        </div> <!--wdm-pro-notification ends-->
        <h2><?php _e("Product Enquiry", "product-enquiry-for-woocommerce");?></h2>
        <br />
        <?php
        if (isset($_GET[ 'tab' ])) {
            $active_tab = $_GET[ 'tab' ];
        } else {
            $active_tab = 'form';
        }
    
        $dashboard_tabs = array(
            'form'      => __("Enquiry Settings", "product-enquiry-for-woocommerce"),
            'entry'     => __("Enquiry Details", "product-enquiry-for-woocommerce"),
            'contact'   => __("Product Enquiry Ideas", "product-enquiry-for-woocommerce"),
            'hireus'    => __("Hire Us", "product-enquiry-for-woocommerce"),
        );
        $dashboard_tabs = apply_filters('product_enquiry_dashboard_tab_titles', $dashboard_tabs);
        ?>
        <h2 class="nav-tab-wrapper">  
        <?php
            foreach ($dashboard_tabs as $single_tab_slug => $single_tab_title) { ?>
                <a href="admin.php?page=product-enquiry-for-woocommerce&tab=<?php echo $single_tab_slug ?>" class="nav-tab <?php echo $active_tab == $single_tab_slug ? 'nav-tab-active' : ''; ?>"><?php echo $single_tab_title; ?></a>
        <?php } ?>
            <a href="admin.php?page=product-enquiry-for-woocommerce&tab=premium" class="premium nav-tab <?php echo $active_tab == 'premium' ? 'nav-tab-active' : ''; ?>"><?php _e("Premium Version", "product-enquiry-for-woocommerce");?></a>
        </h2>  
    
        <?php
        if ($active_tab === 'entry') { ?>
            <div id='entry_dummy'>
                <div class="layer_parent">
                    <div class="pew_upgrade_layer">
                        <div class="pew_uptp_cont">
                            <p> <?php _e("This feature is available in the PRO version. Click below to know more.", "product-enquiry-for-woocommerce");?></p>
                            <a class="wdm_view_det_link" href="https://goo.gl/gBtwX4" target="_blank"> <?php _e("View Details", "product-enquiry-for-woocommerce");?></a>
                        </div>
                    </div>
                    <img src="<?php echo plugins_url('/img/entries.png', __FILE__); ?>" style='width:100%;'/>
                </div>
            </div>
        <?php
        } elseif ($active_tab === 'contact') { ?>
            <div class="wdm-tab-container">
                <div class="wdm-container">
                    <fieldset>
                        <div class="col-1 wdm-abt" >
                            <p class="wdm-about" style="text-align:center">
                                <?php
                                echo sprintf(__('Product Enquiry Pro is one of WisdmLabs early plugins and a very successful one. With over  %s 2500+ satisfied customers  %s we continue to improve PEP and give the best to our customers. We stand by our products and make sure we give our customers what they are looking for with great quality and even better features.', 'product-enquiry-for-woocommerce'), "<b class='wdm-color'>", "</b>"); ?>
                                <br><br>
                                <b class="wdm-color" style="width: 100%; margin: 0px auto; text-align: center; font-size: 16px;"><?php _e("THIS IS WHERE WE NEED YOU! ", "product-enquiry-for-woocommerce");?></b>
                                <br><br>
                                <?php
                                echo sprintf(__('We need you, the users of PEP to %s pitch in your ideas %s  for the plugin. Based on the number of interested users, we will incorporate the feature and make it available at a minimal cost. ', 'product-enquiry-for-woocommerce'), '<b style="color:#961914;">', '</b>'); ?>
                                <br>
                            </p>
                        </div>
                        <div class="clear"></div>
                    </fieldset>
                </div>
                <div class="wdm-container wdm-services-offered clearfix">
                    <?php
                    global $current_user;
                    wp_get_current_user();
                    ?>
                    <ul class="wdm-services-list clearfix">
                        <li class="wdm-services-item">
                            <div class="wdm-services-icon wdm-custom-eq-form" ></div>
                            <h3><?php _e("Visual Form Builder (Drag & Drop)", "product-enquiry-for-woocommerce");?></h3>
                            <p class="wdm-services-desc">
                                <?php _e("Flexibility to add, delete or modify fields using the drag and drop form builder. ", "product-enquiry-for-woocommerce");?>
                            </p>
                            <a class="wdm_upgrade_pro_link" href="https://goo.gl/gBtwX4" target="_blank"><?php _e("UPGRADE TO PRO", "product-enquiry-for-woocommerce"); ?></a>
                        </li>
                        <li class="wdm-services-item">
                            <div class="wdm-services-icon wdm-display-eq-button" ></div>
                            <h3><?php _e("Display Enquiry Button On Shop ", "product-enquiry-for-woocommerce");?></h3>
                            <p class="wdm-services-desc">
                                <?php _e("Allow visitors to enquire about your products directly from the shop page. ", "product-enquiry-for-woocommerce");?>
                            </p>
                            <a class="wdm_upgrade_pro_link" href="https://goo.gl/gBtwX4" target="_blank"><?php _e("UPGRADE TO PRO", "product-enquiry-for-woocommerce"); ?></a>
                            <div class="hide_class">
                                <?php echo "<h4 class='wdm-req-title'>Please confirm your feature request</h4>"; ?>
                                <form class="wdm-req-form display-enquiry-button-on-shop" >
                                    <br><small><?php _e("Confirm Email-id : ", "product-enquiry-for-woocommerce");?></small>
                                    <input type="text" class="wdm-req-text" name="wdm-req-email" value="<?php echo $current_user->user_email ?>" />
                                    <input type="button" class="wdm-req-button" value="Send Request" name="request-feature" />
                                    <input type="hidden" class="id" name="id" value="display-enquiry-button-on-shop" />
                                    <div class="loading"></div>
                                </form>
                                <span class="wdm-close" ></span>
                            </div>
                        </li>
                        <!---for selerating two rows---->
                        <!--<div class="clear"></div> -->
                        <li class="wdm-services-item">
                            <div class="wdm-services-icon wdm-hide-re-cart" ></div>
                            <h3><?php _e("Hide or Replace Add-to-Cart Button ", "product-enquiry-for-woocommerce");?></h3>
                            <p class="wdm-services-desc">
                                <?php _e("Replace the add-to-cart button with the enquiry button for your products. ", "product-enquiry-for-woocommerce");?>
                            </p>
                            <a class="wdm_upgrade_pro_link" href="https://goo.gl/gBtwX4" target="_blank"><?php _e("UPGRADE TO PRO", "product-enquiry-for-woocommerce"); ?></a>
                        </li>
                    </ul>
                    <!-- //code for displaying the hidden send button -->
                    <script type="text/javascript">
                        jQuery('.wdm-services-button').click(function (e) {
                            if (((e.target.id == 'hide_class') || (e.target.id == 'wdm-req-form') || (e.target.nodeName == 'H4') || (e.target.nodeName == 'INPUT') || (e.target.nodeName == 'SMALL'))) {
                                // console.log(jQuery(e.target));
                                jQuery(".hide_class").hide();
                                //jQuery(!jQuery(e.target).siblings(".hide_class")).hide();
                                jQuery(e.target).siblings(".hide_class").fadeIn();
                                // console.log('yes');
                            } else {
                                jQuery(".hide_class").fadeIn("slow");
                                // console.log('no');
                            }
                        });
                        jQuery('.wdm-close').click(function (event) {
                            // console.log('clicked');
                            jQuery(event.target).parent('.hide_class').fadeOut("slow");
                        });
                        //jQuery('#wdm-close').css('display','block');
                    </script>
                </div><!-- /.wdm-services-offered -->
                <div class="wdm-container wdm-services-details">
                    <fieldset>
                        <!--<legend>Details</legend>-->
                        <div class="wdm-details">
                            <h2 class="wdm-color" style="text-align: center;" ><?php _e("Get A Feature Developed in PEP ", "product-enquiry-for-woocommerce");?></h2>
                            <hr>
                            <?php _e("Select a feature you want us to develop and leave us a note about it. We will get in touch with you and keep you posted on its progress. ", "product-enquiry-for-woocommerce");?><br><br>
                            <b class="wdm-color"><?php _e("Need a custom feature developed in PEP ? ", "product-enquiry-for-woocommerce");?></b>
                            <br><br><a href="https://wisdmlabs.com/contact-us/" target="_blank" class="wdm-contact-button" title="Wisdmlabs" ><?php _e("Contact Us ", "product-enquiry-for-woocommerce");?></a>
                        </div>
                    </fieldset>
                </div><!-- /.wdm-services-details -->
            </div><!-- /.wdm-tab-container -->
        <?php
        } elseif ($active_tab === 'hireus') { ?>
            <div class="wdm-tab-container">
                <div class="wdm-container">
                    <fieldset class="wdm-plugin-customize">
                        <h2 class="wdm-color"><?php _e("Plugin Development and Customization ", "product-enquiry-for-woocommerce");?></h2><hr>
                        <p><?php _e("Our area of expertise is WordPress plugins. We specialize in creating bespoke plugin solutions for your business needs. Our competence with plugin development and customization has been certified by WordPress biggies like WooThemes and Event Espresso. ", "product-enquiry-for-woocommerce");?><br><br>
                            <a class="wdm-contact-button" style="padding:8px 30px; margin-top:10px;" href="https://wisdmlabs.com/contact-us/" target="_blank"><?php _e("Contact Us ", "product-enquiry-for-woocommerce");?></a>
                        </p>
                    </fieldset>
                    <div class="wdm-container">
                        <h2 style="text-align: center;" class="wdm-color"><?php _e("Our Premium Plugins ", "product-enquiry-for-woocommerce");?></h2>
                        <ul class="wdm-premium-plugins">
                            <li class="wdm-plugins-item first">
                                <h3><?php _e("Customer Specific Pricing for WooCommerce ", "product-enquiry-for-woocommerce");?></h3>
                                <p style="text-align: center;">
                                    <?php _e("This simple yet powerful plugin, allows you to set a different price for a WooCommerce product on a per customer basis. ", "product-enquiry-for-woocommerce");?><br><br></p>
                                <a class="wdm-contact-button wdm-know-more" href="https://wisdmlabs.com/woocommerce-user-specific-pricing-extension/" target="_blank"><?php _e("Know more ", "product-enquiry-for-woocommerce");?> </a>
                            </li>
                            <li class="wdm-plugins-item">
                                <h3><?php _e("WooCommerce Custom Product Boxes ", "product-enquiry-for-woocommerce");?></h3>
                                <p style="text-align: center;">
                                    <?php _e("This plugin allows customers, to select and create their own product bundles, which can be purchased as a single product! ", "product-enquiry-for-woocommerce");?></p>
                                <a class="wdm-contact-button wdm-know-more" href="https://wisdmlabs.com/assorted-bundles-woocommerce-custom-product-boxes-plugin/" target="_blank"><?php _e("Know more ", "product-enquiry-for-woocommerce");?> </a>
                            </li>
                            <li class="wdm-plugins-item">
                                <h3 style="min-height:54px;"><?php _e("Instagram WooCommerce Integration ", "product-enquiry-for-woocommerce");?>  </h3>
                                <p style="text-align: center;">
                                    <?php _e("A perfect solution, to create collages using Instagram images, right from your WooCommerce store. ", "product-enquiry-for-woocommerce");?></p>
                                <a class="wdm-contact-button wdm-know-more" href="https://wisdmlabs.com/instagram-woocommerce-integration-solution/" target="_blank"><?php _e("Know more ", "product-enquiry-for-woocommerce");?> </a>
                            </li>
                            <li class="wdm-plugins-item last">
                                <h3><?php _e("WooCommerce Moodle Integration", "product-enquiry-for-woocommerce");?></h3>
                                <p style="text-align: center;">
                                    <?php _e("Want to sell Moodle Courses in your WooCommerce store? This plugin allows you to do the same and much more. ", "product-enquiry-for-woocommerce");?></p>
                                <a class="wdm-contact-button wdm-know-more" href="https://wisdmlabs.com/woocommerce-moodle-integration-solution/" target="_blank"><?php _e("Know more ", "product-enquiry-for-woocommerce");?> </a>
                            </li>
                        </ul>
                        <ul class="wdm-premium-plugins three-grids">
                            <li class="wdm-plugins-item first">
                                <h3><?php _e("WooCommerce Scheduler", "product-enquiry-for-woocommerce");?></h3>
                                <p style="text-align: center;">
                                    <?php _e("With the WooCommerce Scheduler Plugin, you can manage product availability as per your business needs.", "product-enquiry-for-woocommerce");?>
                                </p>
                                <a class="wdm-contact-button wdm-know-more" href="https://wisdmlabs.com/woocommerce-scheduler-plugin-for-product-availability/" target="_blank"><?php _e("Know more ", "product-enquiry-for-woocommerce");?> </a>
                            </li>
                            <li class="wdm-plugins-item">
                                <h3><?php _e("WooCommerce Bookings: Availability Search Widget", "product-enquiry-for-woocommerce");?></h3>
                                <p style="text-align: center;">
                                    <?php _e("An WooCommerce Bookings plugin extension, which allows customers to search for Available Bookings on requested dates.", "product-enquiry-for-woocommerce");?>
                                </p>
                                <a class="wdm-contact-button wdm-know-more" href="https://wisdmlabs.com/woocommerce-bookings-availability-search-widget/" target="_blank"><?php _e("Know more ", "product-enquiry-for-woocommerce");?> </a>
                            </li>
                            <li class="wdm-plugins-item last">
                                <h3><?php _e("Empty Cart Timer for WooCommerce", "product-enquiry-for-woocommerce");?></h3>
                                <p style="text-align: center;">
                                    <?php _e("Empty Cart Timer for WooCommerce deletes products from the cart after a certain predefined amount of expiration time. ", "product-enquiry-for-woocommerce");?>
                                </p>
                                <a class="wdm-contact-button wdm-know-more" href="https://wisdmlabs.com/empty-cart-timer-woocommerce-plugin/" target="_blank"><?php _e("Know more ", "product-enquiry-for-woocommerce");?> </a>
                            </li>
                        </ul>
                    </div>
                    <div class="clear"></div>
                    <fieldset class="wdm-bouquet-of-services">
                        <h2 class="wdm-color"><?php _e("Entire Array of Services ", "product-enquiry-for-woocommerce");?></h2><hr>
                        <p><?php _e("We specialize in WordPress website development and customization with an entire range of services under our belt. We have expertise in domains such as eCommerce, LMS, Event Management System, etc. Explore our services now and cater to all your WordPress requirements under one roof. ", "product-enquiry-for-woocommerce");?><br><br>
                        <a class="wdm-contact-button" style="padding:8px 30px; margin-top:10px;" href="https://wisdmlabs.com/services/" target="_blank"><?php _e("View Our Services ", "product-enquiry-for-woocommerce");?></a>
                        </p>
                    </fieldset>
                </div>
            </div>
        <?php
        } elseif ($active_tab === 'premium') {
            wp_enqueue_script('jquery');
            wp_enqueue_style('premium-style', plugins_url('css/style.css', __FILE__), [], PEFREE_VERSION);
            wp_enqueue_style('premium-fontstyle', plugins_url('css/font.css', __FILE__), [], PEFREE_VERSION);
            wp_enqueue_script('viewportChecker-min', plugins_url('/js/jquery.viewportChecker.min.js', __FILE__), array('jquery'), PEFREE_VERSION);
            wp_enqueue_script('debouncedResize', plugins_url('/js/jquery.debouncedresize.js', __FILE__), array('jquery'), PEFREE_VERSION);
            wp_enqueue_script('main', plugins_url('/js/main.js', __FILE__), array('jquery'), PEFREE_VERSION);
            require('premium.php');
        } elseif ($active_tab === 'form') {
            wp_enqueue_script('wdm-tip', plugins_url("js/tooltip.js", __FILE__), array('jquery'), PEFREE_VERSION);
            wp_enqueue_script('wdm-settings', plugins_url("js/settings.js", __FILE__), array('jquery'), PEFREE_VERSION);
            wp_enqueue_style('woocommerce_admin_styles', plugin_dir_url((dirname(__FILE__))) . 'woocommerce/assets/css/admin.css', [], PEFREE_VERSION);
            wp_enqueue_script('jquery-tiptip', array( 'jquery' ));
            $pro = "<span title='Pro Feature' class='pew_pro_txt'>".__('[Available in PRO]', 'product-enquiry-for-woocommerce')."</span>";
            ?>
            <form name="ask_product_form" id="ask_product_form" method="POST" action="options.php" style="background: #fff; padding: 10px 15px 0 15px;">
                <?php
                settings_fields('wdm_form_options');
                $default_vals =   array('show_after_summary'=>'after_add_cart');
                $form_data = get_option('wdm_form_data', $default_vals);
                if (!isset($form_data['show_after_summary'])) {
                    $form_data['show_after_summary'] = "after_add_cart";
                }
                ?>
                <div id="ask_abt_product_panel">
                    <fieldset>
                        <?php echo '<legend>'. __("Emailing Information", 'product-enquiry-for-woocommerce').'</legend>'; ?>
                        <div class="fd">
                            <div class='left_div'>
                                <label for="wdm_user_email"><?php _e(" Recipient's Email ", "product-enquiry-for-woocommerce");?> </label> 
                            </div>
                            <div class='right_div'>
                                <?php
                                $helptip = __('You can add multiple email addresses separated by comma', 'product-enquiry-for-woocommerce');
                                echo wdmHelpTip($helptip);
                                ?> 
                                <input type="text" class="wdm_wpi_input wdm_wpi_text" name="wdm_form_data[user_email]" id="wdm_user_email" value="<?php echo empty($form_data['user_email']) ? get_option('admin_email') : $form_data['user_email'];?>" />
                                <span class='email_error'> </span>
                            </div>
                            <div class='clear'></div>
                        </div >
                        <div class="fd">
                            <div class='left_div'>
                                <label for="wdm_default_sub"> <?php _e("Default Subject ", "product-enquiry-for-woocommerce");?> </label>
                            </div>
                            <div class='right_div'>
                                <?php
                                $helptip = __('Subject to be used if customer leaves subject field blank', 'product-enquiry-for-woocommerce');
                                echo wdmHelpTip($helptip);
                                ?>      
                                <input type="text" class="wdm_wpi_input wdm_wpi_text" name="wdm_form_data[default_sub]" id="wdm_default_sub" value="<?php echo empty($form_data['default_sub']) ? __('Enquiry for a product from ', 'product-enquiry-for-woocommerce').get_bloginfo('name') : $form_data['default_sub'];?>"  />
                            </div>
                            <div class='clear'></div>
                        </div>
                        <div class="fd">
                            <div class='left_div'>
                                <label for="send-mail-to-admin"> <?php _e("Send mail to Admin ", "product-enquiry-for-woocommerce");?> </label>
                            </div>
                            <div class='right_div'>
                                <?php
                                $helptip = __('When checked, sends the enquiry email to the Admin email address specified under Settings -> General', 'product-enquiry-for-woocommerce');
                                echo wdmHelpTip($helptip);
                                ?>      
                                <input name="wdm_form_data[send_mail_to_admin]" type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" value="1" <?php checked(1, isset($form_data[ 'send_mail_to_admin' ]) ? $form_data[ 'send_mail_to_admin' ] : 0);
                                ?> id="send-mail-to-admin" />
                            </div>
                            <div class='clear'></div>
                        </div>
                        <div class="fd">
                            <div class='left_div'>
                                <label for="send-mail-to-product-author"> <?php _e("Send mail to Product Author ", "product-enquiry-for-woocommerce");?> </label>
                            </div>
                            <div class='right_div'>
                                <?php
                                $helptip = __('When checked, sends enquiry email to author/owner of the Product', 'product-enquiry-for-woocommerce');
                                echo wdmHelpTip($helptip);
                                ?>      
                                <input name="wdm_form_data[send_mail_to_author]" type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" value="1" <?php checked(1, isset($form_data[ 'send_mail_to_author' ]) ? $form_data[ 'send_mail_to_author' ] : 0); ?> id="send-mail-to-product-author" />
                            </div>
                            <div class='clear'></div>
                        </div>
                        <div class="fd">
                            <div class='left_div'>
                                <label for="deactivate-nonce"> <?php _e("Disable Nonce ", "product-enquiry-for-woocommerce");?> </label>
                            </div>
                            <div class='right_div'>
                                <?php
                                $helptip = __('Check this option if your enquiry system does not work properly or displays an "Unauthorised Enquiry" error. Note: In all other cases, we advise you to keep it  unchecked.', 'product-enquiry-for-woocommerce');
                                echo wdmHelpTip($helptip);
                                ?>      
                                <input name="wdm_form_data[deactivate_nonce]" type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" value="1" <?php checked(1, isset($form_data[ 'deactivate_nonce' ]) ? $form_data[ 'deactivate_nonce' ] : 0); ?> id="deactivate-nonce" />
                            </div>
                            <div class='clear'></div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <?php echo '<legend>'. __("Form Options", 'product-enquiry-for-woocommerce').'</legend>'; ?>
                        <div class="fd">
                            <div class='left_div'>
                                <label for="custom_label"> <?php _e("Enquiry Button Label", "product-enquiry-for-woocommerce");?> </label> 
                            </div>
                            <div class='right_div'>
                                <?php
                                $helptip = __('Add custom label for enquiry button', 'product-enquiry-for-woocommerce');
                                echo wdmHelpTip($helptip);
                                ?>
                                <input type="text" class="wdm_wpi_input wdm_wpi_text" name="wdm_form_data[custom_label]" value="<?php echo empty($form_data['custom_label']) ? __('Make an enquiry for this product', 'product-enquiry-for-woocommerce') : $form_data['custom_label'];?>" id="custom_label"  />
                            </div>
                            <div class='clear'></div>
                        </div>
                        <div class="fd">
                            <div class='left_div'>
                                <label> <?php _e("Enquiry Button Location", "product-enquiry-for-woocommerce");?> </label>
                            </div>
                            <div class='right_div'>  
                                <input type='radio' class='wdm_wpi_input wdm_wpi_checkbox input-without-tip' value='after_add_cart' name='wdm_form_data[show_after_summary]' <?php echo (($form_data['show_after_summary'])=='after_add_cart' ) ? ' checked="checked" ' : ''; ?> />
                                <label for="show_after_product_summary"><?php _e(" After add to cart button", "product-enquiry-for-woocommerce");?></label>
                                <br />
                                <input type='radio' class='wdm_wpi_input wdm_wpi_checkbox input-without-tip' value='after_product_summary' name='wdm_form_data[show_after_summary]' <?php echo (($form_data['show_after_summary']== 'after_product_summary') ? ' checked="checked" ' : '');?> />
                                <label for="show_after_cart"><?php _e(" After single product summary ", "product-enquiry-for-woocommerce");?></label>
                                <br />
                            </div>
                            <div class='clear'></div>
                        </div>
                        <div class="fd">
                            <div class='left_div'>
                                <label> <?php _e("Enable sending an email copy", "product-enquiry-for-woocommerce");?> </label>
                            </div>
                            <div class='right_div'>
                                <?php
                                $helptip = __('This will display \'send me a copy\' checkbox on enquiry form', 'product-enquiry-for-woocommerce');
                                echo wdmHelpTip($helptip);
                                ?> 
                                <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" name="wdm_form_data[enable_send_mail_copy]" value="1" <?php echo (isset($form_data["enable_send_mail_copy"]) ? "checked" : "" );?> id="enable_send_mail_copy" />
                              
                                <label for="enable_send_mail_copy"><?php _e(" Enable option on the enquiry form to send an email copy to customer", "product-enquiry-for-woocommerce");?> </label>
                            </div>
                            <div class='clear'></div>
                        </div>
                        <div class="fd">
                            <div class='left_div'>
                                <label for="enable_telephone_no_txtbox">
                                <?php _e("Display 'Telephone Number Field'", "product-enquiry-for-woocommerce");?>
                                </label>
                            </div>
                            <div class='right_div'>
                                <?php
                                $helptip = __('Displays telephone number field on the enquiry form', 'product-enquiry-for-woocommerce');
                                echo wdmHelpTip($helptip);
                                ?>
                                <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" name="wdm_form_data[enable_telephone_no]" value="1" <?php echo (isset($form_data["enable_telephone_no"]) ? "checked" : "" );?> id="enable_telephone_no_txtbox" />
                            </div>
                            <div class="clear"></div>
                        </div>
                        <?php 
                        $style = isset($form_data["enable_telephone_no"]) ? '' : 'display:none;';
                        ?>
                        <div class="fd" style="<?php echo $style; ?>">
                            <div class='left_div'>
                                <label for="make_phone_mandatory_chkbox">
                                <?php _e("Make Telephone Number Field Mandatory", "product-enquiry-for-woocommerce");?>
                                </label>
                            </div>
                            <div class='right_div'>
                                <?php
                                $helptip = __('Make telephone number field mandatory while submitting the form.', 'product-enquiry-for-woocommerce');
                                echo wdmHelpTip($helptip);
                                ?>
                                <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" name="wdm_form_data[make_phone_mandatory]" value="1" <?php echo (isset($form_data["make_phone_mandatory"]) ? "checked" : "" );?> id="make_phone_mandatory_chkbox" />
                            </div>
                            <div class="clear"></div>
                        </div>    
                        <div class="fd">
                            <div class='left_div'>
                                <label for="enable_for_out_stock">
                                <?php _e("Show enquiry button only when product is out of stock", "product-enquiry-for-woocommerce");?></label>
                            </div>
                            <div class='right_div'>
                                <?php
                                $helptip = __('Show enquiry button only when product is out of stock', 'product-enquiry-for-woocommerce');
                                echo wdmHelpTip($helptip);
                                ?>
                                <input type="checkbox" disabled class="wdm_wpi_input wdm_wpi_checkbox" value="1" id="enable_for_out_stock" />
                                <?php echo $pro; ?>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><?php _e("Styling Options", "product-enquiry-for-woocommerce");?> </legend>
                        <div class='fd'>
                            <div class='left_div'>
                                <label><?php _e(' Add Custom CSS ', 'product-enquiry-for-woocommerce') ?></label>
                            </div>
                            <div class='right_div'>
                                <?php
                                $user_custom_css = '';
                                if (isset($form_data[ 'user_custom_css' ])) {
                                    $user_custom_css = $form_data[ 'user_custom_css' ];
                                }
                                ?>
                                <textarea class="input-without-tip" name="wdm_form_data[user_custom_css]" rows="10" cols="30"><?php echo $user_custom_css ?></textarea>
                            </div>
                        </div>
                        <div class='fd'>
                            <div class='left_div'>
                                <label for="enable_telephone_no_txtbox"><?php _e("Custom Styling", "product-enquiry-for-woocommerce");?></label>
                            </div>
                            <div class='right_div'>
                                <input type="radio" class="wdm_wpi_input wdm_wpi_checkbox input-without-tip" value="theme_css" name="wdm_radio_data" id="theme_css" checked />
                                <em> <?php _e("Use Activated Theme CSS", "product-enquiry-for-woocommerce");?> </em><br>
                                <input type="radio" class="wdm_wpi_input wdm_wpi_checkbox input-without-tip" value="manual_css" name="wdm_radio_data" id="manual_css" />        
                                <em><?php _e("Manually specify color settings", "product-enquiry-for-woocommerce");?></em>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </fieldset>
                    <!-- Enquiry Terms and Conditions section -->
                    <fieldset>
                        <legend><?php _e("Enquiry Terms and Conditions", "product-enquiry-for-woocommerce");?> </legend>
                        <!-- Display Terms and Conditions Checkbox -->
                        <div class="fd">
                            <div class='left_div'>
                                <label for="enable_terms_conditions"> <?php _e("Display Terms and Conditions", "product-enquiry-for-woocommerce"); ?></label>
                            </div>
                            <div class='right_div'>
                                <?php
                                $helptip = __('To display \'Terms and Conditions\' checkbox on Enquiry form, you must enable this.', 'product-enquiry-for-woocommerce');
                                echo wdmHelpTip($helptip);
                                ?>
                                <input name="wdm_form_data[enable_terms_conditions]" type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" value="1" <?php checked(1, isset($form_data[ 'enable_terms_conditions' ]) ? $form_data[ 'enable_terms_conditions' ] : 0); ?> id="enable_terms_conditions" />
                            </div>
                            <div class='clear'></div>
                        </div>
                        <!-- Enquiry Privacy Policy Text -->
                        <div class='fd'>
                            <div class='left_div'>
                                <label><?php _e('Enquiry Privacy Policy', 'product-enquiry-for-woocommerce') ?></label>
                            </div>
                            <div class='right_div textarea-wrapper privacy-policy-textarea-wrapper'>
                                <?php
                                $enquiryPrivacyPolicy = isset($form_data['enquiry_privacy_policy_text']) ? $form_data['enquiry_privacy_policy_text'] : peDefaultTermsCondText();
                                $helptip = sprintf(__('For WP 4.9.6 and above %s will get automatically replaced with Privacy Policy Page link. If your site runs on WP below 4.9.6, then replace %s here with link to your Privacy Policy Page.', 'product-enquiry-for-woocommerce'), '[privacy_policy]', '[privacy_policy]');
                                echo wdmHelpTip($helptip);
                                ?>
                                <textarea class="wdm_wpi_input term-cond-text" name="wdm_form_data[enquiry_privacy_policy_text]" rows="3" cols="40"><?php echo $enquiryPrivacyPolicy ?></textarea>
                            </div>
                            <div class='clear'></div>
                        </div>
                    </fieldset>
                    <!-- <br /> -->
                    <div name="Other_Settings" id="Other_Settings" style="display: none;">
                        <fieldset style="padding: 0;">
                            <?php echo '<legend>'. __("Specify CSS Settings ", 'product-enquiry-for-woocommerce').'</legend>';?>
                            <br />
                            <div class="layer_parent">
                                <div class="pew_upgrade_layer">
                                    <div class="pew_uptp_cont">
                                        <p><?php _e(" This feature is available in the PRO version. Click below to know more. ", "product-enquiry-for-woocommerce");?></p>
                                        <a class="wdm_view_det_link" href="https://goo.gl/gBtwX4" target="_blank"><?php _e("View Details ", "product-enquiry-for-woocommerce");?> </a>
                                    </div>
                                </div>
                                <img src="<?php echo plugins_url('img/buttons-css.png', __FILE__);?>" />
                            </div>
                        </fieldset>
                    </div>
                    <p><input type="submit" class="wdm_wpi_input button-primary" value="Save Changes" id="wdm_ask_button" /></p>
                    <br/>
                </div>
            </form>
            <script type="text/javascript">
                jQuery(document).ready(
                   function($)
                   {
                        $("#ask_product_form").validate();        
                        if($("#manual_css").is(':checked')) {
                            $("#Other_Settings").show();
                        } else {
                            $("#Other_Settings").hide(); 
                        }        
                        $("#theme_css").click(function(){$("#Other_Settings").hide();});
                        $("#manual_css").click(function(){$("#Other_Settings").show();});
                    }
                );
            </script>
            <?php             }
            do_action('product_enquiry_tab_content', $active_tab);
            ?>
    </div>
    <?php
    global $wdmsubme_obj;
    $wdmsubme_obj->generateForm();
    unset($wdmsubme_obj);
    
    //add styles for settings page
    wp_enqueue_style("wdm-admin-css", plugins_url("css/wpi_admin.css", __FILE__), [], PEFREE_VERSION);
      
    //include WisdmLabs sidebar
    $plugin_data        = get_plugin_data(__FILE__);
    $plugin_name        = $plugin_data['Name'];
    $wdm_plugin_slug    = 'product-enquiry-for-woocommerce';
    
   // include_once('wisdm_sidebar/wisdm_sidebar.php');
   // pew_create_wisdm_sidebar($plugin_name, $wdm_plugin_slug);
}

add_action('wp_ajax_wdm_send', 'contact_email');
add_action('wp_ajax_nopriv_wdm_send', 'contact_email');
function contact_email()
{
    $form_data = get_option('wdm_form_data');

    if (isset($form_data['deactivate_nonce']) && $form_data['deactivate_nonce'] == 1) {
        $nonce = true;
    } else {
        $nonce = check_ajax_referer('enquiry_action', 'security', false);
    }
         
    if (isset($_POST['security']) && $nonce) {

        $product_id = isset($_POST['wdm_product_id']) ? (int)$_POST['wdm_product_id'] : "";
        $product    = wc_get_product($product_id);

        $name               = isset($_POST['wdm_name']) ? $_POST['wdm_name'] : "";
        $to                 = isset($_POST['wdm_emailid']) ? $_POST['wdm_emailid'] : "";
        $phoneNumber        = isset($_POST['wdm_phone']) ? $_POST['wdm_phone'] : '';
        $subject            = (isset($_POST['wdm_subject']) && !empty($_POST['wdm_subject'])) ? $_POST['wdm_subject'] :$form_data['default_sub'];
        $message            = isset($_POST['wdm_enquiry']) ?  $_POST['wdm_enquiry'] : "";
        $cc                 = isset($_POST['wdm_cc']) ? $_POST['wdm_cc'] : "";
        $product_name       = isset($_POST['wdm_product_name']) ? $_POST['wdm_product_name'] : "";
        $product_url        = isset($_POST['wdm_product_url']) ? $_POST['wdm_product_url'] : "";
        $variation_id       = isset($_POST['wdm_variation_id']) ? (int)$_POST['wdm_variation_id'] : "";
        $admin_email        = get_option('admin_email');
        $site_name          = get_bloginfo('name');
        $recipient_emails   = array();
        $recipient_email    = array();
        $enabledTermsCond   = peIsTermsCondEnabled();

        $prod_sku = $product->get_sku();
        if (!empty($prod_sku)) {
            $p_sku_text = " (SKU: $prod_sku) "; 
        } else {
            $p_sku_text = " (ID: #$product_id) "; 
        }

        // Variation info
        if($variation_id) {
            $variation  = wc_get_product($variation_id);
            $var_name   = $variation->get_formatted_name();
            // $var_sku    = $variation->get_sku();
            // if (!empty($var_sku)) {
            //     $v_sku_text = " (SKU: $var_sku) "; 
            // } else {
            //     $v_sku_text = " (ID: #$variation_id) "; 
            // }
        }

        if ($form_data[ 'user_email' ] != '') {
            $recipient_email = explode(',', $form_data[ 'user_email' ]);
            $recipient_email = array_map('trim', $recipient_email);
        }
        foreach ($recipient_email as $singleEmail) {
            array_push($recipient_emails, $singleEmail);
        }
        if (isset($form_data['send_mail_to_admin'])) {
            array_push($recipient_emails, $admin_email);
        }

        $authorEmail = $_POST['uemail'];

        //$group_emails = array($recipient_email,$admin_email);
        $headers = "Reply-To: $to \n";
        //UTF-8
        if (function_exists('mb_encode_mimeheader')) {
            $subject = mb_encode_mimeheader($subject, "UTF-8", "B", "\n");
        } else {
            // you need to enable mb_encode_mimeheader or risk
            // getting emails that are not UTF-8 encoded
        }
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=utf-8\n";
        // $headers .= "Content-Transfer-Encoding: quoted-printable\n";
    
        // Set and wordwrap message body

        $body = apply_filters('pe_enquire_body_before', $body);

        $body .= __('Product Enquiry from', 'product-enquiry-for-woocommerce')." <strong>". $site_name . "</strong> <br /><br />";
        $body .= "<strong>".__('Product Name:', 'product-enquiry-for-woocommerce')."</strong> ". $product_name . $p_sku_text . "<br /><br />";

        if ($variation_id) {
            $body .= "<strong>".__('Variation Name:', 'product-enquiry-for-woocommerce')."</strong> ". $var_name . "<br /><br />";
        }

        $body .= "<strong>".__('Product URL:', 'product-enquiry-for-woocommerce')."</strong> ". $product_url ."<br /><br />";
        $body .= "<strong>".__('Customer Name:', 'product-enquiry-for-woocommerce')."</strong> ". $name ."<br /><br />";
        $body .= "<strong>".__('Customer Email:', 'product-enquiry-for-woocommerce')."</strong> ". $to ."<br /><br />";
        $body .= "<strong>".__('Message:', 'product-enquiry-for-woocommerce')."</strong> <br />". $message ."<br /><br />";

        if (isset($form_data['enable_telephone_no']) && 1 == $form_data['enable_telephone_no'] && !empty($phoneNumber)) {
            $body .= "<strong>".__('Customer Phone Number:', 'product-enquiry-for-woocommerce')."</strong> ". $phoneNumber ."<br /><br />";
        }

        if(!empty($enabledTermsCond)) {
            $body .= "<br /><br />".sprintf(__('%s accepted the enquiry terms and conditions', 'product-enquiry-for-woocommerce'), $to);
        }

        $body = apply_filters('pe_enquire_body_after', $body);

        $adminMailBody  = $body;
        $linkStatement  = sprintf(__('%s Learn More %s', 'product-enquiry-for-woocommerce'), "<a href='https://goo.gl/gBtwX4'>", "</a>");
        // $linkStatement = "<a href='https://wisdmlabs.com/woocommerce-product-enquiry-pro/'> Upgrade to Pro </a>";
        $premiumMail    = "<br><hr>";
        $premiumMail    .= "<p>" . sprintf(__('Checkout %s Product Enquiry Pro %s for these <b>PREMIUM</b> features', 'product-enquiry-for-woocommerce'), "<a href='https://goo.gl/gBtwX4'>", "</a>") .": </p>";
        $premiumMail    .= "<ul>
                        <li> " . __('Request a Quote System: The pro version allows customers to request a quote for your products.', 'product-enquiry-for-woocommerce') . "</li>
                        <li>" . __('Create Quotation: Creating a fresh quotation, right from the dashboard takes only a few clicks.', 'product-enquiry-for-woocommerce') . "</li>
                        <li>" . __('Multiproduct Enquiry: Allow customers to add multiple products to the "Enquiry Cart" and send their queries on a product level in a single enquiry.', 'product-enquiry-for-woocommerce') . "</li>
                        <li>" . __('Compatibility with the latest versions of WordPress and WooCommerce at all times.', 'product-enquiry-for-woocommerce') . "</li>
                        <li>" . __('And much more...', 'product-enquiry-for-woocommerce') . "</li>
                </ul>";

        $premiumMail    .= $linkStatement;

        $adminMailBody  .= $premiumMail;
        $adminMailBody  = wordwrap($adminMailBody, 100);
        $send_mail      = false;
        $recipient_emails = apply_filters('pefree_recipient_emails', array_unique($recipient_emails));

        foreach ($recipient_emails as $recipient_email) {
            $send_mail = wp_mail($recipient_email, $subject, $adminMailBody, $headers); //or  die(__("Unfortunately, a server issue prevented delivery of your message.","product-enquiry-for-woocommerce"));
        }
        if (isset($form_data['send_mail_to_author']) && isset($authorEmail) && !in_array($authorEmail, $recipient_emails)) {
            wp_mail($authorEmail, $subject, $body, $headers);
        }
    
        if ($send_mail) {
            echo apply_filters('pefree_enquiry_sent_message', __("Enquiry was sent successfully", "product-enquiry-for-woocommerce"));
            
            if ($cc == 1) {
                wp_mail($to, $subject, wordwrap($body, 100), $headers);
            }
        } else {
            _e("Unfortunately, a server issue prevented delivery of your message.", "product-enquiry-for-woocommerce");
        }
        die();
    }
    die(__("Unauthorized access.", "product-enquiry-for-woocommerce"));
}

add_action('admin_init', 'reg_form_settings');

function reg_form_settings()
{
    //register plugin settings
    register_setting('wdm_form_options', 'wdm_form_data');
}

add_action('admin_footer', 'wdm_action_javascript'); // Write our JS below here

function wdm_action_javascript()
{
    if (isset($_GET["page"]) && $_GET["page"] == "product-enquiry-for-woocommerce") { ?>
        <script type="text/javascript" >
            jQuery(document).ready(function($) {
                jQuery(".wdm-req-button").click(function() {
                    var data        = {};
                    data.email      = $(this).siblings(".wdm-req-text").val();
                    data.id         = $(this).siblings( ".id" ).val();
                    data.updates    = $(this).siblings(".wdm-req-confirm").prop("checked");
                    data.action     = "wdm_pe_action";
                    var email       = $(this).siblings(".wdm-req-text").val();
                    var txt         = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

                    if (!txt.test(email)) {
                        $(this).siblings("input.wdm-req-text").css('border','1px solid red');
                        e.preventDefault();
                    } else {
                        $(this).siblings("input.wdm-req-text").css('border','1px solid #ccc');
                    }

                    var id      = $(this).siblings( ".id" ).val();
                    var loading = $(this).siblings(".loading").show();
                    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                    $.post(ajaxurl, data, function(response) {
                        //alert('Got this from the server: ' + response + '<br>id is :' + ids);
                        var id = jQuery.trim(response);
                        //jQuery(this).siblings('.wdm-req-title').hide();
                        jQuery('.loading').hide();
                        alert('Your request has been sent');
                        jQuery(id).find("div.loading").hide();
                    });
                });
            });
        </script>
        <?php
    }
}

add_action('wp_ajax_wdm_pe_action', 'wdm_pe_action_callback');

function wdm_pe_action_callback()
{
    global $wpdb; // this is how you get access to the database
    $email                      = $_POST['email'];
    $id                         = $_POST['id'];
    $updates                    = $_POST['updates'];
    $subscribes_message         = "";
    $subscribes_message_client  = "";

    if ($updates == "true") {
        $subscribes_message         = __("And Congrats! They have subscribed to your newsletter too!", 'product-enquiry-for-woocommerce');
        $subscribes_message_client  = __("We'll keep you updated with the latest developments.", 'product-enquiry-for-woocommerce');
    } else {
        //$subscribes_message = "Oh shoot! They haven't subscribed.";
        //$subscribes_message_client = "To stay up-to-date with the latest developments, do subscribe to our newsletter!";
    }
    $message        = __("Hi,A feature request has been made for Product enquiry free, by $email.Requested Feature: $id $subscribes_message Thanks and Regards,This is an automated mail, sent by WisdmLabs", "product-enquiry-for-woocommerce");

    $message_client = sprintf(__("Hi there, \n\nThank you for requesting the %s feature for Product Enquiry for WooCommerce.We will keep you updated with the latest developments. \n\nThanks and Regards,\nWisdmLabs", "product-enquiry-for-woocommerce"), $id);

    //echo $email . " id is --:" . $id;
    wp_mail('support@wisdmlabs.com', 'PE Feature Request', $message);
    //mail for client
    $client_header[] = 'From: WisdmLabs <donotreply@wisdmlabs.com>';
    wp_mail($email, 'Your Feature Request Has Been Sent!', $message_client, $client_header);
    echo "." . $id;
    die(); // this is required to terminate immediately and return a proper response
}

function checkIfNoData($fileData, $current_response_code, $valid_response_code)
{
    if ($fileData == null || ! in_array($current_response_code, $valid_response_code)) {
        $GLOBALS[ 'wdm_server_null_response' ] = true;
        return false;
    }
    return true;
}

function wdmHelpTip($helptip)
{
    $wooVersion = WC_VERSION;
    $wooVersion = floatval($wooVersion);
    if ($wooVersion < 2.5) {
        return '<img class="help_tip" data-tip="' . esc_attr($helptip) . '" src="' . WC()->plugin_url() . '/assets/images/help.png" height="16" width="16" />';
    } else {
        return wc_help_tip($helptip);
    }
}

function pePluginDir()
{
    return untrailingslashit(plugin_dir_path(__FILE__));
}

function pePluginUrl()
{
    return untrailingslashit(plugins_url('/', __FILE__));
}

function getTermsConditionsText()
{
    $termsConditionsText = peGetPrivacyPolicyText();
    return peReplacePolicyPageLinkPlaceholders($termsConditionsText);
}

function peGetPrivacyPolicyText()
{
    $settings   = get_option('wdm_form_data');
    $text       = isset($settings['enquiry_privacy_policy_text']) && !(empty($settings['enquiry_privacy_policy_text'])) ? $settings['enquiry_privacy_policy_text'] : peDefaultTermsCondText();

    return trim(apply_filters('pe_get_privacy_policy_text', $text));
}

function peReplacePolicyPageLinkPlaceholders($text)
{
    $privacy_page_id = pePrivacyPolicyPageId();
    $privacy_link    = $privacy_page_id ? '<a href="' . esc_url(get_permalink($privacy_page_id)) . '" class="pe-privacy-policy-link" target="_blank">' . __('privacy policy', 'product-enquiry-for-woocommerce') . '</a>' : __('privacy policy', 'product-enquiry-for-woocommerce');

    $find_replace = array(
        '[privacy_policy]' => $privacy_link,
    );

    return str_replace(array_keys($find_replace), array_values($find_replace), $text);
}

function pePrivacyPolicyPageId()
{
    $page_id = get_option('wp_page_for_privacy_policy', 0);
    return apply_filters('pe_privacy_policy_page_id', 0 < $page_id ? absint($page_id) : 0);
}

function peShouldDisplayTermsCond()
{
    return (!empty(peIsTermsCondEnabled())) ? true: false;
}

function peDefaultTermsCondText()
{
    $text = sprintf(__('I allow the Site owner to contact me via email/phone to discuss this enquiry. (If you want to know more about the way this site handles the data, then please go through our %s).', 'product-enquiry-for-woocommerce'), '[privacy_policy]');
    return apply_filters('pe_default_terms_cond_text', $text);
}

function peIsTermsCondEnabled()
{
    $form_data          = get_option('wdm_form_data');
    $isTermsCondEnabled = isset($form_data['enable_terms_conditions'])? $form_data['enable_terms_conditions']: false;
    return apply_filters('pe_is_terms_cond_enabled', $isTermsCondEnabled);
}

register_deactivation_hook( __FILE__, 'pePluginDeactivation' );

function pePluginDeactivation()
{
    delete_option('wdm_privacy_notice_dismissed');
}
// Include WisdmAdPackage
if( is_admin() || defined( 'DOING_CRON' ) ){
    include_once('WisdmAdPackage.php');
    $WisdmAdPackage = WisdmAd\WisdmAdPackage::getInstance();
}
?>
