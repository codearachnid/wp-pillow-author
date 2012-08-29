<?php

/*
Plugin Name: Pillow Author
Plugin URI: 
Description: Embed your individual author information within the post as a pillow ad (single posts only)
Version: 2.0
Author: Timothy Wood (@codearachnid)
Author URI: http://www.codearachnid.com	
Author Email: tim@imaginesimplicity.com
Icons Source: http://www.elegantthemes.com/blog/resources/free-social-media-icon-set
Notes:

Enhance your profile photo with http://wordpress.org/extend/plugins/user-avatar/

License:

  Copyright 2011 Imagine Simplicity (tim@imaginesimplicity.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

include( plugin_dir_path(__FILE__) . 'inc.encode_link.php' );

if( !class_exists('WP_Pillow_Author')) {
	class WP_Pillow_Author {

		protected static $instance;

		const WP_VERSION = '3.4';
		const VERSION = '2.0';
		const TOKEN = 'wp-pillow-author';
		// const OPTION_GROUP = 'wp_pillow_author_options';
		// const OPTION = 'wp_pillow_author';

	    private $base_url;
	    private $base_path;
	    private $base_name;

	    private $db;
	    private $uid = 'pillow_author';
	    private $filter_types = array('post');
	    public $cache_active_life = 100;
	    public $use_cache = 0;
	    public $show_after = 1;
	    public $networks = array('twitter','facebook','google','linkedin','tumblr','youtube','delicious');
	    public $avatar_size = 200;
	    public $related_posts = 3;
	    public $settings = null;
	    
	    function __construct() {
	        // Setup common access properties
	        $this->base_path = plugin_dir_path( __FILE__ );
	        $this->base_url = plugin_dir_url( __FILE__ );
	        $this->base_name = plugin_basename( __FILE__ );
	        $this->load_plugin_textdomain();

			if( is_admin() ) {	
				register_activation_hook(__FILE__, array( &$this, 'on_activate' ) );
				add_action( 'plugin_action_links_' . $this->base_name, array( &$this, 'plugin_action_links' ) );
				add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
				add_action( 'admin_init', array( &$this, 'admin_init' ) );

				add_action( 'show_user_profile', array(&$this, 'add_user_fields'));
				add_action( 'edit_user_profile', array(&$this, 'add_user_fields') );
				add_action( 'personal_options_update', array(&$this, 'save_user_fields') );
				add_action( 'edit_user_profile_update', array(&$this, 'save_user_fields') );
				
			} else {
				add_filter('the_content', array(&$this, 'front'));
			}
	    }

	    public function admin_init(){
	    	// verify that we can run this plugin properly
	    	$this->version_check();

			// load saved settings
		 	$this->settings = get_option( self::TOKEN );

	    	// register option group for settings and validation
			register_setting( self::TOKEN, self::TOKEN,  array( &$this, 'sanitize_options' ) );
			add_settings_section( 'display', __( 'Display Options', 'wp-pillow-author'), function(){}, self::TOKEN );
			add_settings_section( 'advanced', __( 'Advanced Plugin Options', 'wp-pillow-author'), function(){}, self::TOKEN );
			// field: where to embed the author meta box
			add_settings_field( 'show_after', __( 'Embed Position', 'wp-pillow-author'), array( &$this, 'option_builder'), self::TOKEN, 'display', array( 
				'label_for' => 'show_after',
				'type' => 'select',
				'description' => __('Select where you want the author box to show in relation to the content.', 'wp-pillow-author'),
				'options' => array(
					__( 'Top of Content', 'wp-pillow-author'),
					__( 'After First Paragraph', 'wp-pillow-author'),
					__( 'After Second Paragraph', 'wp-pillow-author'),
					__( 'After Third Paragraph', 'wp-pillow-author'),
					__( 'After Fourth Paragraph', 'wp-pillow-author'),
					'-1' => __( 'Bottom of Content', 'wp-pillow-author')
					)
				));
			// field: show avatar
			add_settings_field( 'show_avatar', __( 'Enable Avatar', 'wp-pillow-author'), array( &$this, 'option_builder'), self::TOKEN, 'display', array( 
				'label_for' => 'show_avatar',
				'type' => 'radio',
				'options' => array( '1' => __( 'Yes', 'wp-pillow-author'), '-1' => __( 'No', 'wp-pillow-author') )
				));
			// field: show description
			add_settings_field( 'show_description', __( 'Enable Description', 'wp-pillow-author'), array( &$this, 'option_builder'), self::TOKEN, 'display', array( 
				'label_for' => 'show_description',
				'type' => 'radio',
				'options' => array( '1' => __( 'Yes', 'wp-pillow-author'), '-1' => __( 'No', 'wp-pillow-author') )
				));
			// field: show email
			add_settings_field( 'show_email', __( 'Enable Email', 'wp-pillow-author'), array( &$this, 'option_builder'), self::TOKEN, 'display', array( 
				'label_for' => 'show_email',
				'type' => 'radio',
				'options' => array( '1' => __( 'Yes', 'wp-pillow-author'), '-1' => __( 'No', 'wp-pillow-author') )
				));
			// field: show website
			add_settings_field( 'show_website', __( 'Enable Website', 'wp-pillow-author'), array( &$this, 'option_builder'), self::TOKEN, 'display', array( 
				'label_for' => 'show_website',
				'type' => 'radio',
				'options' => array( '1' => __( 'Yes', 'wp-pillow-author'), '-1' => __( 'No', 'wp-pillow-author') )
				));
			// field: show social media icons
			add_settings_field( 'show_social_icons', __( 'Enable Social Icons', 'wp-pillow-author'), array( &$this, 'option_builder'), self::TOKEN, 'display', array( 
				'label_for' => 'show_social_icons',
				'type' => 'radio',
				'options' => array( '1' => __( 'Yes', 'wp-pillow-author'), '-1' => __( 'No', 'wp-pillow-author') )
				));
			// field: show related posts
			add_settings_field( 'related_posts', __( 'Related Posts', 'wp-pillow-author'), array( &$this, 'option_builder'), self::TOKEN, 'display', array( 
				'label_for' => 'related_posts',
				'type' => 'select',
				'description' => __('To disable from displaying select "None".', 'wp-pillow-author'),
				'options' => array(
					__( 'None', 'wp-pillow-author'),
					__( 'One', 'wp-pillow-author'),
					__( 'Two', 'wp-pillow-author'),
					__( 'Three', 'wp-pillow-author'),
					__( 'Four', 'wp-pillow-author'),
					__( 'Fifth', 'wp-pillow-author')
					)
				));
			// field: custom CSS override
			add_settings_field( 'custom_css', __( 'Custom CSS', 'wp-pillow-author'), array( &$this, 'option_builder'), self::TOKEN, 'advanced', array( 
				'label_for' => 'custom_css',
				'type' => 'textarea',
				'description' => __( 'Override the default CSS for this plugin or add new styles.', 'wp-pillow-author')
				));
			// field: exclude users
			$user_list = array();
			// override the get_users() with add_filter('wp-pillow-author_filter_users')
			foreach ( apply_filters( self::TOKEN . '_filter_users', get_users() ) as $user) $user_list[ $user->ID ] = $user->display_name;
			add_settings_field( 'exclude_users', __( 'Exclude Users', 'wp-pillow-author'), array( &$this, 'option_builder'), self::TOKEN, 'advanced', array( 
				'label_for' => 'exclude_users',
				'type' => 'select',
				'description' => __('Prevent an author box from appearing for specific author.', 'wp-pillow-author'),
				'multiple' => true,
				'options' => $user_list
				));
			// field: enable caching
			add_settings_field( 'use_cache', __( 'Enable Caching', 'wp-pillow-author'), array( &$this, 'option_builder'), self::TOKEN, 'advanced', array( 
				'label_for' => 'use_cache',
				'type' => 'select',
				'description' => __( 'This will cache the author meta box for the time specified.', 'wp-pillow-author'),
				'options' => array(
					__( 'No caching', 'wp-pillow-author'),
					'-1 minute' => __( 'One Minute', 'wp-pillow-author'),
					'-5 minutes' => __( 'Five Minutes', 'wp-pillow-author'),
					'-10 minutes' => __( 'Ten Minutes', 'wp-pillow-author'),
					'-15 minutes' => __( 'Fifteen Minutes', 'wp-pillow-author'),
					'-30 minutes' => __( 'Thirty Minutes', 'wp-pillow-author'),
					'-1 hour' => __( 'One Hour', 'wp-pillow-author'),
					'-2 hours' => __( 'Two Hours', 'wp-pillow-author'),
					'-4 hours' => __( 'Four Hours', 'wp-pillow-author'),
					'-8 hours' => __( 'Eight Hours', 'wp-pillow-author'),
					'-1 day' => __( 'One Day', 'wp-pillow-author')
					)
				));
		}

		public function option_builder( $args ){
			$html = '';
			$args['label_for'] = ! isset($args['label_for']) ? 'default' : $args['label_for'];
			// $args['default'] = ! isset($args['default']) ? false : $args['default'];
			switch( $args['type'] ) {
				case 'select':
					$check_value = !isset($this->settings[ $args['label_for'] ]) || empty($this->settings[ $args['label_for'] ]) ? '' : $this->settings[ $args['label_for'] ];
					$multiple = isset($args['multiple']) && $args['multiple'] ? 'multiple' : '';
					$html .= '<select name="' . $this->field_name( $args['label_for'] ) . '" ' . $multiple . '>';
					foreach( $args['options'] as $value => $title ) {
						$html .= '<option value="' . $value . '" ' . selected( $value, $check_value, false ) . '>' . $title . '</option>';
					}
					$html .= '</select>';
					break;
				case 'radio':
					$check_value = !isset($this->settings[ $args['label_for'] ]) || empty($this->settings[ $args['label_for'] ]) ? key( $args['options'] ) : $this->settings[ $args['label_for'] ];
					foreach( $args['options'] as $value => $title ) {
						$html .= '<input type="radio" name="' . $this->field_name( $args['label_for'] ) . '" value="' . $value . '" ' . checked( $value, $check_value, false ) . ' /><span class="name">' . $title . '</span>';
					}
					break;
				case 'textarea':
					$value = !isset($this->settings[ $args['label_for'] ]) || empty($this->settings[ $args['label_for'] ]) ? '' : $this->settings[ $args['label_for'] ];
					$html .= '<textarea name="' . $this->field_name( $args['label_for'] ) . '" rows="7" cols="75" type="textarea">' . $value . '</textarea>';
					break;
				default :
					break;
			}
			$html .= isset($args['description']) ? '<p class="description">' . $args['description'] . '</p>' : '';

			// override the html for the field with add_filter('wp-pillow-author_{label}')
			echo apply_filters( self::TOKEN . '_' . $args['label_for'], $html);
		}

		// Sanitize and validate input. Accepts an array, return a sanitized array.
		public function sanitize_options( $input ) {
			// if custom css is provided then we will merge the default into a combined css file
			// override filename with add_filter('wp-pillow-author_cache_css_filename')
			$cache_css_filename = apply_filters(self::TOKEN . '_cache_css_filename', $this->base_path . '/cache/pillow-author-custom.css');
			if($input['custom_css'] != "") {
				// append custom css overrides to the default plugin stylesheet and create cached custom stylesheet
				file_put_contents( $cache_css_filename , file_get_contents( $this->base_path . '/pillow-author.css' ) . "\n\n" . '/*** CUSTOM CSS OVERRIDES ***/' . "\n\n" . $input['custom_css'] );
			} else if ( $input['custom_css'] == "" && file_exists( $cache_css_filename ) ) {
				// remove cached css overrides
				unlink( $cache_css_filename );
			}
			$input['exclude_users'] =  wp_filter_nohtml_kses($input['exclude_users']); // Sanitize textbox input (strip html tags, and escape characters)
			return $input;
		}

		public function manage_options() {

			// enqueue stylesheet for admin screens
			wp_enqueue_style(self::TOKEN . '-admin-css', $this->base_url . 'assets/admin.css' );

		 	// override the template path with add_filter('wp-pillow-author-manage_options')
			include $this->get_template( 'manage_options' );
		}

		/**
		 * quickly build option field names and echo if needed
		 *
		 * @return $name
		 */
		public function field_name( $name, $echo = false ) {
	        $name = self::TOKEN . "[{$name}]";
	        if( $echo ) {
	            echo $name;
	        } else {
	            return $name;
	        }
	    }

	    /**
		 * Setup menues inside the WordPress admin.
		 *
		 * @return void
		 */
		public function admin_menu() {
			add_options_page('Pillow Author', 'Pillow Author', 'manage_options', self::TOKEN, array(&$this, 'manage_options') );
		}

		/**
		 * Loads theme files in appropriate hierarchy: 
		 * 1) child theme,
		 * 2) parent template, 
		 * 3) plugin resources. will look in the wp-pillow-author/
		 * directory in a theme and the views/ directory in the plugin
		 *
		 * You may also override the @return var directly by using:
		 * add_filter('wp-pillow-author_{template_name}')
		 *
		 * @param string $template template file to search for
		 * @param string $class pass through class filters
		 * @return template path
		 **/

		public function get_template( $template, $class = null ) {
			// whether or not .php was added
			$template = rtrim($template, '.php');

			if ( $theme_file = locate_template( array(self::TOKEN . '/' . $template . '.php') ) ) {
				$file = $theme_file;
			} else if ( $theme_file = locate_template(array(self::TOKEN . '/' . $template . '_' . $class . '.php')) ) {
				$file = $theme_file;
			} else {
				$file = $this->base_path . '/views/' . $template . '.php';
			}

			return apply_filters( self::TOKEN . '_' . $template, $file, $class);
		}

		/**
		 * load_plugin_textdomain for I18n translation files
		 *
		 * @return void
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'wp-pillow-author', false, trailingslashit( dirname( $this->base_name ) ) . 'lang/');
		}

		/**
		 * Add links to the plugin listing on the plugins list page. (aka settings et al)
		 *
		 * @param $action_links
		 * @return $action_links
		 */
		public function plugin_action_links( $action_links ) {

			// add link to plugin settings
			$action_links['settings'] = '<a href="' . add_query_arg( array( 'page' => self::TOKEN ), admin_url( 'options-general.php' ) ) .'">' . __( 'Settings', 'wp-pillow-author') . '</a>';

			return $action_links;
		}

		/**
		 * Checks WordPress versioning requirements for running the plugin
		 *
		 * @return void
		 */
		public function version_check() {
			global $wp_version;
			$plugin = plugin_basename( __FILE__ );
			$plugin_data = get_plugin_data( __FILE__, false );

			// ensure the current version of WordPress is greater than the min required
			if ( version_compare($wp_version, self::WP_VERSION, "<" ) ) {
				if( is_plugin_active($plugin) ) {
					deactivate_plugins( $plugin );
					wp_die( sprintf(__('%s requires WordPress version %s or higher, and has been deactivated! Please upgrade WordPress and try again. Back to <a href="%s">WordPress admin</a>.', 'wp-pillow-author'), $plugin_data['Name'], self::WP_VERSION, admin_url()) );
				}
			}
		}
		
		

		function on_activate() {
			delete_option( self::TOKEN ); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
			$arr = array(	"show_after" => $this->show_after,
							"show_avatar" => "1",
							"show_description" => "1",
							"show_social_icons" => "1",
							"avatar_size" => $this->avatar_size,
							"related_posts" => $this-related_posts,
							"custom_css" => "",
							"exclude_users" => "",
							"use_cache" => $this->use_cache
			);
			update_option( self::TOKEN, $arr);
		}
		
		

		
		
			

		function the_network( $network, $user ) {
			switch( $network ) {
				case 'twitter':
					$html = ( strpos($user,'@') === false ) ? $user : 'http://www.twitter.com/' . trim( str_replace( '@', '', $user ) );
					break;
				default: $html = $user; break;
			}
			return trim( $html );
		}
		
		function get_related_author_posts( $user_id) {
			global $post;
			$related_posts = get_posts( array( 'author' => $user_id, 'post__not_in' => array( $post->ID ), 'posts_per_page' => $this->settings['related_posts'] ) );	
			return $related_posts;
		}

		function add_user_fields( $user ) {
			if( $this->settings['show_social_icons'] == "1" ) {
				include($this->base_path . '/tpl.user_profile.php');
			}
		}
		
		function save_user_fields( $user_id ) {
			if ( !current_user_can( 'edit_user', $user_id ) )
				return FALSE;
			foreach ( $this->networks as $network ) {
				update_usermeta( $user_id, $network, $_POST['user_profile_'.$network] );
			}
		}

		function template(){
			ob_start();
				include($this->base_path . '/tpl.layout.php');
			return ob_get_clean();
		}
	    
	    function load_cache() {
		
			$cache_filename = $this->base_path . '/cache/' . md5( AUTH_SALT . get_the_author_meta('id') ) . '.author';
	    	
			if( file_exists( $cache_filename ) ) {
				$filemtime = filemtime($cache_filename);
				if( !$filemtime || $filemtime < strtotime ( $this->use_cache ) /* (time() - $filemtime >= $this->use_cache) */ ) {
					$pillow_author = $this->template();
					file_put_contents($cache_filename, $pillow_author);
				} else {
					$pillow_author = file_get_contents( $cache_filename );
				}
			} else {
				$pillow_author = $this->template();
				file_put_contents($cache_filename, $pillow_author);
			}
			
			return $pillow_author;
	    	
	    }
	    
	    function front($content){
	    	global $post;
			$this->settings = get_option( self::OPTION );
			
			// setup user list to exclude from showing
			$this->settings['exclude_users'] = isset( $this->settings['exclude_users'] ) ? array_map('trim', explode( ",", $this->settings['exclude_users'] ) ) : array();

			// if user is to be exclude return content immediately
			if( in_array( get_the_author_meta( 'user_login' ), $this->settings['exclude_users'] ) ) {
				return $content;
			}
			
	    	// TODO allow user defined custom post types
	    	// http://codex.wordpress.org/Function_Reference/get_post_types
		    if ( $post && in_array( $post->post_type, $this->filter_types ) ) {
				// load CSS
		    	if ( file_exists($this->base_path . '/pillow-author-custom.css') ) {
		    		wp_enqueue_style( $this->uid, $this->base_url . '/pillow-author-custom.css' );
		    	} else {
			    	wp_enqueue_style( $this->uid, $this->base_url . '/pillow-author.css' );
			    }

		    	// show pillow author afer "x" paragraphs
		    	if( $this->settings['show_after'] > 0 ) {
					// split the_content on paragraph for inserting box
					$content_blocks = explode( '</p>', $content );
					
					// setup the pillow wrapper
					$pre_pillow = array_slice( $content_blocks, 0, $this->settings['show_after'] );
					$post_pillow = array_slice( $content_blocks, $this->settings['show_after'] );
					$pillow_author = ( $this->settings['use_cache'] != '0' ) ? $this->load_cache() : $this->template();
					
					// build the return package
					return implode( '</p>', $pre_pillow ) . $pillow_author . implode( '</p>', $post_pillow );

				// prepend pillow author to beginning of the_content
				} else if ( $this->settings['show_after'] == 0 ) {
					return $pillow_author . $content;
					
				// append pillow author to end of the_content
				} else if ( $this->settings['show_after'] < 0) {
					return $content . $pillow_author;
				}
	    	} else {
	    		return $content;
	    	}
	    }

	    /* Static Singleton Factory Method */
		public static function instance() {
			if ( !isset( self::$instance ) ) {
				$className = __CLASS__;
				self::$instance = new $className;
			}
			return self::$instance;
		}
	}
	// $is_pillow_author = new IS_Pillow_Author;

	/**
	 * Instantiate class and set up WordPress actions.
	 *
	 * @return void
	 */
	function Load_WP_Pillow_Author() {
		$run_or_not = class_exists( 'WP_Pillow_Author' ) && defined( 'WP_Pillow_Author::VERSION' );
		if ( apply_filters( 'wp_pillow_author_to_run_or_not_to_run', $run_or_not ) ) {
			$wp_pillow_author = WP_Pillow_Author::instance();

			// require_once( $wp_pillow_author->pluginPath.'lib/template-tags.php' );
			
		}
	}
	add_action( 'plugins_loaded', 'Load_WP_Pillow_Author', 1); // high priority so that it's not too late for addon overrides
}