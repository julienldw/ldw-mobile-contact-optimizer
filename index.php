<?php
/*
Plugin Name:    LDW Mobile Contact Optimizer
Version:    0.1.2
Author: Lamour du Web
Description:    Don’t waste any contact! Be reached in 1 click from mobile.
Author URI: http://lamourduweb.com
Text Domain:    ldw_mco
Domain Path:    /lang
GitHub Plugin URI: https://github.com/julienldw/ldw-mobile-contact-optimizer
*/

class LDW_Mobile_Contact_Optimizer{

    function __construct() {
		add_action('admin_menu', array($this, 'admin_menu') );
        add_action('wp_footer', array($this, 'wp_footer') );
        add_action('admin_init', array($this,'admin_init') );
        add_action('wp_enqueue_scripts',array($this,'enqueue_scripts') );
        add_action( 'plugins_loaded', array($this,'load_plugin_textdomain') );
        add_action( 'add_meta_boxes', array($this,'add_meta_boxes' ) );
        add_action('save_post', array($this,'save_metaboxes') );
        register_activation_hook( __FILE__, array( $this, 'install' ) );
    }

    function add_meta_boxes() {
        $options = get_option('ldw_mco');
        if($options['custom'] == 'on')
            add_meta_box( 'ldw-mco', 'Mobile Contact Optimizer', array($this,'metabox_callback'), 'page' );
    }

    function save_metaboxes($post_ID){
        $options = get_option('ldw_mco');
        if($options['custom'] == 'on'){
            update_post_meta($post_ID, '_ldw_custom',$_POST['ldw_mco']);
        }
    }

    function metabox_callback($post){
        $options = get_post_meta($post->ID,'_ldw_custom', true);
        if(!is_array($options)) $options = array('phone'=>'','email'=>'','mapurl'=>'');
        ?>
<p><?php _e('Leave blank for no customization.','ldw_mco'); ?></p>
<p><label for="ldw_phone"><?php _e('Phone number','ldw_mco'); ?></label><br><input type="tel" id="ldw_phone" name="ldw_mco[phone]" value="<?php echo $options['phone']; ?>"/></p>
<p><label for="ldw_email"><?php _e('E-mail address','ldw_mco'); ?></label><br><input type="email" id="ldw_email" name="ldw_mco[email]" value="<?php echo $options['email']; ?>"/></p>
<p><label for="ldw_mapurl"><?php _e('Location map link','ldw_mco'); ?></label><br><input type="url" id="ldw_mapurl" name="ldw_mco[mapurl]" value="<?php echo $options['mapurl']; ?>"/></p>
        <?php
    }

    function load_plugin_textdomain() {
        load_plugin_textdomain( 'ldw_mco', FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
    }

    function install(){
        if(false == get_option('ldw_mco')){
             update_option('ldw_mco',array(
                    'width'     => 480,
                    'phone'       => '',
                    'email'      => '',
                    'mapurl'       => '',
                    'icon'     => '#ffffff',
                    'bg'        => '#55C3DC',
                    'border'    => '#E9F2F9',
                    'backtotop' => 'on',
                    'css'       => ''
             ));
        }
    }

    function enqueue_scripts(){
	   wp_enqueue_script('ldw-mco', plugins_url( 'assets/js/script.js' , __FILE__ ),array('jquery'));
	   wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
    }

    function admin_menu(){
        $page_hook_suffix = add_options_page('LDW Mobile Contact Optimizer','LDW Mobile Contact Optimizer', 'manage_options','ldw-mco',array($this,'admin_page'));
        add_action('admin_print_scripts-' . $page_hook_suffix, array( $this, 'wp_enqueue_scripts'));
    }

    function wp_enqueue_scripts(){
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
    }

    function admin_init(){

        add_settings_section('ldw_mco_data',__('Datas','ldw_mco'),'','ldw-mco');
        add_settings_field('ldw_mco_phone',__('Phone number','ldw_mco'),array($this,'phone_callback'),'ldw-mco','ldw_mco_data',array('label_for' => 'ldw_phone'));
        add_settings_field('ldw_mco_email',__('E-mail address','ldw_mco'),array($this,'email_callback'),'ldw-mco','ldw_mco_data',array('label_for' => 'ldw_email'));
        add_settings_field('ldw_mco_mapurl',__('Location map link','ldw_mco'),array($this,'mapurl_callback'),'ldw-mco','ldw_mco_data',array('label_for' => 'ldw_mapurl'));

        add_settings_section('ldw_mco_setting',__('Settings','ldw_mco'),'','ldw-mco');
        add_settings_field('ldw_mco_width',__('Max width','ldw_mco'),array($this,'width_callback'),'ldw-mco','ldw_mco_setting',array('label_for' => 'ldw_width'));
        add_settings_field('ldw_mco_icon',__('Icons color','ldw_mco'),array($this,'icon_callback'),'ldw-mco','ldw_mco_setting',array('label_for' => 'ldw_icon'));
        add_settings_field('ldw_mco_bg',__('Background color','ldw_mco'),array($this,'bg_callback'),'ldw-mco','ldw_mco_setting',array('label_for' => 'ldw_bg'));
        add_settings_field('ldw_mco_border',__('Border color','ldw_mco'),array($this,'border_callback'),'ldw-mco','ldw_mco_setting',array('label_for' => 'ldw_border'));
        add_settings_field('ldw_mco_others',__('Other settings','ldw_mco'),array($this,'others_callback'),'ldw-mco','ldw_mco_setting');


        register_setting('ldw-mco','ldw_mco');
    }

    function width_callback(){
        $options = get_option('ldw_mco'); ?>
<input type="text" id="ldw_width" name="ldw_mco[width]" value="<?php echo $options['width']; ?>"/>
<p class="description"><?php _e('In pixels, maximum screen width from which the buttons are no longer displayed.','ldw_mco'); ?></p>
<?php }
    function phone_callback(){
        $options = get_option('ldw_mco'); ?>
<input type="tel" id="ldw_phone" name="ldw_mco[phone]" value="<?php echo $options['phone']; ?>"/>
<?php }
    function email_callback(){
        $options = get_option('ldw_mco'); ?>
<input type="email" id="ldw_email" name="ldw_mco[email]" value="<?php echo $options['email']; ?>"/>
<?php }
    function mapurl_callback(){
        $options = get_option('ldw_mco'); ?>
<input type="url" id="ldw_mapurl" name="ldw_mco[mapurl]" value="<?php echo $options['mapurl']; ?>"/>
<?php }
    function icon_callback(){
        $options = get_option('ldw_mco'); ?>
<input type="text" id="ldw_icon" name="ldw_mco[icon]" class="color" value="<?php echo $options['icon']; ?>"/>
<?php }
    function bg_callback(){
        $options = get_option('ldw_mco'); ?>
<input type="text" id="ldw_bg" name="ldw_mco[bg]" class="color" value="<?php echo $options['bg']; ?>"/>
<?php }
    function border_callback(){
        $options = get_option('ldw_mco'); ?>
<input type="text" id="ldw_border" name="ldw_mco[border]" class="color" value="<?php echo $options['border']; ?>"/>
<?php }
    function others_callback(){
        $options = get_option('ldw_mco'); ?>
<label for="ldw_mco_backtotop"><input type="checkbox" name="ldw_mco[backtotop]" id="ldw_mco_backtotop" <?php if($options['backtotop'] == 'on') echo 'checked="checked"'; ?>><?php _e('Activate the “back to top” link.','ldw_mco'); ?></label>
<br><br>
 <label for="ldw_mco_css"><input type="checkbox" name="ldw_mco[css]" id="ldw_mco_css" <?php if($options['css'] == 'on') echo 'checked="checked"'; ?>><?php _e('Embed myself the CSS code.','ldw_mco'); ?></label>
<?php /*<br><br>
 <label for="ldw_mco_custom"><input type="checkbox" name="ldw_mco[custom]" id="ldw_mco_custom" <?php if($options['custom'] == 'on') echo 'checked="checked"'; ?>><?php _e('Allows data customization by pages.','ldw_mco'); ?></label>*/ ?>
<?php }

    function admin_page(){
        $ldw_mco = get_option('ldw_mco');
        ?>
        <style>
        #ldw-mco-css{
            padding:10px; background:#dfdfdf;
        }
        #ldw-mco-settings{ overflow:hidden;}
        #ldw-mco-credits{
            width:280px; margin:0 0 0 50px; float:right;
            padding:10px;
            background: #FFF;
            box-shadow: 0px 0px 0px 1px rgba(0, 0, 0, 0.05);
            text-align:center;
        }
        #ldw-mco-credits a img{ width:100%; height:auto;}
        </style>
        <script>
        jQuery(document).ready(function($) {
            $('.color').wpColorPicker();
        });
        </script>
        <div class="wrap">
            <h2>LDW Mobile Contact Optimizer</h2>
            <div id="ldw-mco-credits">
                <p><a href="http://lamourduweb.com" target="_blank"><img src="<?php echo plugins_url( 'assets/lamour-du-web.png', __FILE__ ); ?>" alt="Lamour du Web" /></a></p>
                <p><?php _e('Need help? Any improvement ideas?','ldw_mco'); ?> <a href="http://lamourduweb.com/contact" target="_blank"><?php _e('Contact us!','ldw_mco'); ?></a></p>

            </div>
            <div id="ldw-mco-settings">
                <p><?php _e('Don’t waste any contact! Be reached in 1 click from mobile.','ldw_mco'); ?></p>
                <p><?php _e('Add links (phone, e-mail address and location map) available at all times at the bottom of your pages.','ldw_mco'); ?></p>
                <form action="options.php" method="post">
<?php
settings_fields( 'ldw-mco' );
do_settings_sections( 'ldw-mco' );
submit_button();
?>
                </form>
                <h3><?php _e('Generated CSS code.','ldw_mco'); ?></h3>
                <p><?php _e('By default, the CSS code is automatically added to the bottom of your HTML pages. If you check "embed myself the CSS code" in the form above, the automatic code is disabled and you just have to place it in the style.css file of your theme.','ldw_mco'); ?></p>
                <div id="ldw-mco-css">
                <pre><?php $this->mco_css($ldw_mco); ?></pre>
                </div>
            </div>
        </div>
        <?php
    }

    function mco_css($ldw_mco){
        ?>#ldw-mco{
    display:none;
}
@media (max-width: <?php echo $ldw_mco['width']; ?>px) {
    body{ margin-bottom:50px;}
    #ldw-mco{ display:block;}
    #ldw-mco{
        position:fixed; bottom:0; display:block;
        width:100%; height:50px;
        background:<?php echo $ldw_mco['bg']; ?>; z-index:9999;
    }
    #ldw-mco ul{ list-style:none; text-align:center; border-top:1px solid <?php echo $ldw_mco['border']; ?>; margin:0; padding-left: 0;}
    #ldw-mco ul li i{ font-size:30px; line-height:50px; color:<?php echo $ldw_mco['icon']; ?>;}
    #ldw-mco ul li:last-child{ border-right:none;}
    <?php if($ldw_mco['backtotop'] == 'on'){ ?>
    #ldw-mco ul li{ display:block; float:left; width:25%; border-right:1px solid <?php echo $ldw_mco['border']; ?>;box-sizing: border-box;}
    <?php } else { ?>
    #ldw-mco ul li{ display:block; float:left; width:33%; border-right:1px solid <?php echo $ldw_mco['border']; ?>;box-sizing: border-box;}

    <?php } ?>
}<?php
    }

    function wp_footer(){
        global $post;

        $ldw_mco = get_option('ldw_mco');
        if($ldw_mco['custom'] == 'on'){
            $options = get_post_meta($post->ID,'_ldw_custom', true);
            if(!is_array($options)) $options = array('phone'=>'','email'=>'','mapurl'=>'');

            if(strlen($options['phone'])>0) $ldw_mco['phone'] = $options['phone'];
            if(strlen($options['email'])>0) $ldw_mco['email'] = $options['email'];
            if(strlen($options['mapurl'])>0) $ldw_mco['mapurl'] = $options['mapurl'];
        }
        ?>
        <div id="ldw-mco">
            <ul>
                <?php if(strlen($ldw_mco['phone'])>0){ ?><li class="ldw_mco_phone"><a href="tel:<?php echo $ldw_mco['phone']; ?>"><i class="fa fa-phone"></i></a></li><?php } ?>
                <?php if(strlen($ldw_mco['email'])>0){ ?><li class="ldw_mco_email"><a href="mailto:<?php echo $ldw_mco['email']; ?>"><i class="fa fa-envelope-o"></i></a></li><?php } ?>
                <?php if(strlen($ldw_mco['mapurl'])>0){ ?><li class="ldw_mco_mapurl"><a target="_blank" href="<?php echo $ldw_mco['mapurl']; ?>"><i class="fa fa-map-marker"></i></a></li><?php } ?>
                <?php if($ldw_mco['backtotop'] == 'on'){ ?><li class="ldw_mco_backtotop"><a href="#top"><i class="fa fa-arrow-up"></i></a></li><?php } ?>
            </ul>
        </div>
        <?php if($ldw_mco['css'] != 'on'){ ?>
        <style>
        <?php $this->mco_css($ldw_mco); ?>
        </style>
        <?php }
    }
}
new LDW_Mobile_Contact_Optimizer();
