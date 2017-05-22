<?php
/*
* Plugin Name: Plugin Default
* Version: 1.0.0
* Description:
* Author: Le Van Toan
* Author URI: http://levantoan.com
* Plugin URI: http://levantoan.com
* Text Domain: devvn
* Domain Path: /languages
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if ( !class_exists( 'Plugin_Default_Class' ) ) {
    class Plugin_Default_Class
    {
        protected static $instance;
        public $_version = '1.0.0';

        public $_optionName = 'tragop_options';
        public $_optionGroup = 'tragop-options-group';
        public $_defaultOptions = array(
            'luu_y' =>	'',
        );

        public static function init()
        {
            is_null(self::$instance) AND self::$instance = new self;
            return self::$instance;
        }
        public function __construct()
        {
            $this->define_constants();
            global $tragop_settings;
            $tragop_settings  = $this->get_dvlsoptions();

            add_action( 'plugins_loaded', array($this,'dvls_load_textdomain') );
            
            add_action( 'wp_enqueue_scripts', array($this, 'load_plugins_scripts') );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

            add_filter( 'plugin_action_links_' . DEVVN_DF_BASENAME, array( $this, 'add_action_links' ), 10, 2 );

            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'admin_init', array( $this, 'dvls_register_mysettings') );
        }
        public function define_constants() {
            if ( !defined( 'DEVVN_DF_VERSION_NUM' ) )
                define( 'DEVVN_DF_VERSION_NUM', $this->_version );
            if ( !defined( 'DEVVN_DF_URL' ) )
                define( 'DEVVN_DF_URL', plugin_dir_url( __FILE__ ) );
            if ( !defined( 'DEVVN_DF_BASENAME' ) )
                define( 'DEVVN_DF_BASENAME', plugin_basename( __FILE__ ) );
            if ( !defined( 'DEVVN_DF_PLUGIN_DIR' ) )
                define( 'DEVVN_DF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }
        public function add_action_links( $links, $file ) {
            if ( strpos( $file, 'plugin-default.php' ) !== false ) {
                $settings_link = '<a href="' . admin_url( 'options-general.php?page=setting-deefault' ) . '" title="View DevVN Local Store Settings">' . __( 'Settings', 'devvn-localstore' ) . '</a>';
                array_unshift( $links, $settings_link );
            }
            return $links;
        }
        function load_plugins_scripts()
        {
            global $dvls_settings;
            wp_register_style('devvn-localstore-style', plugins_url('assets/css/devvn-localstore.css', __FILE__), array(), $this->_version, 'all');
            wp_register_script('devvn-localstore-script', plugins_url('assets/js/devvn-localstore-jquery.js', __FILE__), array('jquery'), $this->_version, true);
            $array = array(
                'ajaxurl'       => admin_url('admin-ajax.php'),
                'siteurl'       => home_url(),
            );
            wp_localize_script('devvn-localstore-script', 'devvn_localstore_array', $array);
        }
        public function admin_enqueue_scripts() {
            $current_screen = get_current_screen();
            global $dvls_settings;
            if ( isset( $current_screen->post_type ) && $current_screen->post_type == 'local-store' ) {
                wp_enqueue_style('devvn-localstore-admin-styles', plugins_url('/assets/css/admin-style.css', __FILE__), array(), $this->_version, 'all');
                wp_enqueue_script('devvn-localstore-admin-js', plugins_url('/assets/js/admin-ls-jquery.js', __FILE__), array('jquery'), $this->_version, true);
                wp_localize_script('devvn-localstore-admin-js', 'dvls_admin', array(
                    'delete_box_nonce'  =>  wp_create_nonce("delete-box"),
                ));
            }
        }
        function dvls_load_textdomain() {
            load_plugin_textdomain( 'plugin-default', FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
        }
        function get_dvlsoptions(){
            return wp_parse_args(get_option($this->_optionName),$this->_defaultOptions);
        }

        function admin_menu() {
            add_options_page(
                __('Setting Default','devvn'),
                __('Setting Default','devvn'),
                'manage_options',
                'setting-deefault',
                array(
                    $this,
                    'devvn_settings_page'
                )
            );
        }

        function dvls_register_mysettings() {
            register_setting( $this->_optionGroup, $this->_optionName );
        }

        function  devvn_settings_page() {
            global $tragop_settings;
            ?>
            <div class="wrap">
                <h1>Cài đặt trả góp</h1>
                <form method="post" action="options.php" novalidate="novalidate">
                    <?php settings_fields( $this->_optionGroup );?>
                    <table class="form-table">
                        <tbody>
                        <tr>
                            <th scope="row"><label for="luu_y">Nội dung Lưu ý</label></th>
                            <td>
                                <?php
                                $settings = array(
                                    'textarea_name' => $this->_optionName.'[luu_y]',
                                );
                                wp_editor( $tragop_settings['luu_y'], 'luu_y', $settings );?>
                            </td>
                        </tr>
                        <?php do_settings_fields('tragop-options-group', 'default'); ?>
                        </tbody>
                    </table>
                    <?php do_settings_sections('tragop-options-group', 'default'); ?>
                    <?php submit_button();?>
                </form>
            </div>
            <?php
        }
    }
    new Plugin_Default_Class();
}