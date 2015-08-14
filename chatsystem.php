<?php
/**
 * @package Chatsystem
 */
/*
Plugin Name: ChatSystem.io
Plugin URI: http://ChatSystem.io/
Description: For use with a ChatSystem.io account into WordPress enabled Websites. 
If you are not already a user, please head to ChatSystem.io 
If you are a current customer, you will need you Unique customer key to activate this plugin. If you need your key, please email support@chatsystem.io
Go to setting Page under Setting menu in Dashboard
Version: 1.0
Author: Web Developer
Author URI: www.bhoopendra.com
License: GPLv2 or later
Text Domain: chatsystem
*/

/* Code Start for Add Script in Footer */

function add_script_footer() {
$chatsystem = get_option( 'chat_system_option_name' );
$customerID = $chatsystem['customer_number'];

	echo '<!-- Start Chatsystem.io Code -->
		<script type="text/javascript" src="https://chatsystem.io/'.$customerID.'"></script>
		<!-- End Chatsystem.io Code -->';
}
add_action('wp_footer', 'add_script_footer',20000000);

/* Code End for Add Script in Footer */

/*************************************************************/
/* Code Start for Plugin Settng Page */

class ChatSystemSetting
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'chat_system_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'chat_page_init' ) );
    }

    /**
     * Add options page
     */
    public function chat_system_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'ChatSystem Settings', 
            'manage_options', 
            'chat-setting-admin', 
			array( $this, 'create_admin_page' )
			
        );
		
		
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'chat_system_option_name' );
        ?>
        <div class="wrap">                   
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'chat_system_option_group' );   
                do_settings_sections( 'chat-setting-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function chat_page_init()
    {        
        register_setting(
            'chat_system_option_group', // Option group
            'chat_system_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'ChatSystem.io Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'chat-setting-admin' // Page
        );  

        add_settings_field(
            'customer_number', // ID
            'Customer Unique Number', // Title 
            array( $this, 'customer_number_callback' ), // Callback
            'chat-setting-admin', // Page
            'setting_section_id' // Section           
        );      

              
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['customer_number'] ) )
            $new_input['customer_number'] = absint( $input['customer_number'] );

         return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
	 $url = plugins_url();
      print '<img src="'.$url.'/chatsystemio/img/chat-system.png" alt="ChatSystem.io"/>'; 
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function customer_number_callback()
    {
        printf(
            '<input class="style-1" type="text" id="customer_number" name="chat_system_option_name[customer_number]" value="%s" />',
            isset( $this->options['customer_number'] ) ? esc_attr( $this->options['customer_number']) : ''
        );
		printf('<style> input[type="text"] {  padding: 10px;  border: solid 1px #dcdcdc;  transition: box-shadow 0.3s, border 0.3s;} input[type="text"]:focus,.style-1 input[type="text"].focus {  border: solid 1px #97c93c;  box-shadow: 0 0 5px 1px #97c93c;} .form-table th{line-height:2.3; font-size: 15px;}</style>');
    }   
   
}


function plugin_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=chat-setting-admin">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link' );

if(is_admin())
    $my_settings_page = new ChatSystemSetting();

/* Code End for Plugin Setting  Page */
?>