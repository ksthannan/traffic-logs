<?php
/*
Plugin Name: Traffic Logs
Description: Get traffic details easily
Version:     1.0.0
Author:      Traffic Logs
Author URI:  #
Text Domain: traffic-logs
Domain Path: /languages
*/

defined( 'ABSPATH' ) or die;

define( 'TRAFFIC_LOGS_CONTENT_FILE', __FILE__ );
define( 'TRAFFIC_LOGS_CONTENT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'TRAFFIC_LOGS_CONTENT_OPTSGROUP_NAME', 'traffic_logs' );
define( 'TRAFFIC_LOGS_CONTENT_OPTIONS_NAME', 'traffic_logs_options' );
define( 'TRAFFIC_LOGS_CONTENT_VER', '1.0.0' );

if ( ! class_exists( 'Traffic_logs_Content' ) ) {
	class Traffic_logs_Content {
		public static function get_instance() {
			if ( self::$instance == null ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		private static $instance = null;

		private function __construct() {
			$this->options = null;
			$this->link = '';

			// Actions
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
			add_action( 'wp_footer', array( $this, 'traffic_logs_functions' ) );
			add_action( 'init', array( $this, 'clear_traffic_logs_data' ) );
		}

		public function init() {
			load_plugin_textdomain( 'traffic-logs', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		public function register_settings() {
			register_setting( TRAFFIC_LOGS_CONTENT_OPTSGROUP_NAME, TRAFFIC_LOGS_CONTENT_OPTIONS_NAME );
		}

		public function add_menu_item() {
			add_menu_page(
				__( 'Traffic Logs', 'traffic-logs' ),
				__( 'Traffic Logs', 'traffic-logs' ),
				'manage_options',
				'traffic-logs',
				array( $this, 'traffic_info_render_options_page' ),
				'dashicons-visibility'
			);
			add_submenu_page(
				'traffic-logs',
				__( 'Settings', 'traffic-logs' ),
				__( 'Settings', 'traffic-logs' ),
				'manage_options',	
				'traffic-logs-settings',
				array( $this, 'traffic_render_options_page' )
			);
		}

		public function traffic_render_options_page() {

			$links = $this->get_option( 'links', array() );

			require( __DIR__ . '/options.php' );

		}

		public function enqueue_admin_assets( $n ) {
			wp_enqueue_style( 'traffic-logs-style', plugins_url( 'css/admin.css', __FILE__ ), array(), TRAFFIC_LOGS_CONTENT_VER, 'all' );
			wp_enqueue_script( 'traffic-logs-admin', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), TRAFFIC_LOGS_CONTENT_VER, true );
			wp_localize_script( 'traffic-logs-admin', 'LinkData', array(
				'pairTpl' => $this->get_pair_tpl()
			) );
		}

		private function get_option( $option_name, $default = '' ) {
			if ( is_null( $this->options ) ) $this->options = ( array ) get_option( TRAFFIC_LOGS_CONTENT_OPTIONS_NAME, array() );
			if ( isset( $this->options[$option_name] ) ) return $this->options[$option_name];
			return $default;
		}

		private function get_pair_tpl( $link = '' ) {
			ob_start();
			require TRAFFIC_LOGS_CONTENT_PLUGIN_PATH . 'pair.php';
			return ob_get_clean();
		}

		public function traffic_info_render_options_page(){

			require( __DIR__ . '/traffic_info.php' );

		}

		public function traffic_logs_functions(){
			// if(! is_single()) return;

			$id = get_the_ID();

			$visitor_ip = $this->getRealIpAddr();

			$current_user = '';
			$username = '';
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
			}
			
			$this->save_data($id, $visitor_ip, $current_user->user_login);
			
			return;
		}

		public function getRealIpAddr() {
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			return $ip;
		}

		public function IpLocation($ip) {
		    
			$apiKey = $this->get_option( 'api_key' );
			$api_url = "http://ipinfo.io/{$ip}?token={$apiKey}";
		
	        $ch = curl_init($api_url);
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            
            $response = curl_exec($ch);
			
			$details = json_decode($response);

			return $details;

		}


		public function save_data($id, $visitor_ip, $user = ''){

            $list_pages = $this->get_option( 'links', array() );
            
            foreach($list_pages as $list_page){
                $post_id = url_to_postid($list_page);
                if($post_id == $id){
                    
                $current_time = current_time('mysql');
    
    			$existing_data = get_post_meta(get_the_ID(), 'traffic_logs', false);
    			
    			$ip_location = $this->IpLocation($visitor_ip);
    			
    			$data[] = array(
        			    'visitor_ip' => $visitor_ip,
        				'page_id' => $id,
        				'time' => $current_time,
        				'location' => $ip_location,
        				'user' => $user
        			);
    
                    if($existing_data[0] == NULL || $existing_data[0] == '' || $existing_data == ''){
                         update_post_meta( $id, 'traffic_logs', $data );
                    }else{
                        $data = array_merge($existing_data[0], $data);
                        update_post_meta( $id, 'traffic_logs', $data );
                    }
                    
                    $recent_logs = array();
                    $existing_logs = get_option('tr_recent_logs');
                    if($existing_logs !== ''){
                        $new_log = array(
            			    'visitor_ip' => $visitor_ip,
            				'page_id' => $id,
            				'time' => $current_time,
            				'location' => $ip_location,
            				'user' => $user
            			);
                        array_push($existing_logs, $new_log);
                        update_option('tr_recent_logs', $existing_logs);
                    }else{
                        $recent_logs[0] = array(
            			    'visitor_ip' => $visitor_ip,
            				'page_id' => $id,
            				'time' => $current_time,
            				'location' => $ip_location,
            				'user' => $user
            			);
            			update_option('tr_recent_logs', $recent_logs);
                    }
                }
            }
            
			return;

		}
		
		public function clear_traffic_logs_data(){
		    
		    if(isset($_REQUEST['clear_traffic_log'])){
		        
		        $pages = $this->get_option( 'links', array() );
		        
		         foreach($pages as $page){
        
                    $post_id = url_to_postid($page);
                    
                     update_post_meta($post_id, 'traffic_logs', '');
                    
		         }
		         
		         update_option('tr_recent_logs', '');

		    }
		}




	}

	Traffic_logs_Content::get_instance();
}