<?php
if (! defined('WDM_SUBME_PEP_PLUGIN_PATH')) {
    exit;
}
class class_wdmsubme
{

    /**
    * 	Constructor which will involve all hooks
    */
    public function __construct()
    {
    }

    public function generateForm()
    {
        wp_enqueue_script('wdm-subme-js', WDM_SUBME_PEP_PLUGIN_URL . 'js/wdm_subme.js', array('jquery')); ?>
        <div id="wdm-subme-form" class=""> 
            <form method="post" action="">
                <!-- <span class='instruction-span'>Enter your email and get 20% discount on our premium products</span> -->
                <p class="wdm_enter_email_para">
                    <label id="enter-email-label">
                        <input type="email" id="wdm_subme_email_field" name="wdm_subme_email_field" placeholder="Enter your email & get 20% off on products"/>
                        <span id="wdm_subme_error" style="display:none;"></span>
                    </label>
                    <button disabled="disabled" class="wdm_sub_me_link disabled_button" type="text" id="wdm_subme_submit" name="wdm_subme_submit" value="Subscribe" title="Please opt-in for email subscription">Subscribe</button>
                </p>
                <p class="wdm_agree_para">
                    <span id="wdm_agree_span" style="color:#961914;">
                        <label>
                            <input name="wdm-agree" type="checkbox" value="1" /> Yes! I'd like to hear about important updates, exclusive offers, and informative content
                        </label>
                    </span>
                </p>
                <input type="text" id="wdm_honey" name="wdm_honey" value="wdm_honey" style="display:none;"/>
            </form>
        </div>
        <?php
        wp_enqueue_style('wdm-subme-style', WDM_SUBME_PEP_PLUGIN_URL . 'css/wdm-subme-style.css');
    }
}
