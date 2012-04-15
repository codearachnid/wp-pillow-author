<?php

/*
Plugin Name: Pillow Author
Plugin URI: 
Description: Embed your individual author information within the post as a pillow ad (single posts only)
Version: 1.0
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

// ------------------------------------------------------------------------
// REQUIRE MINIMUM VERSION OF WORDPRESS:                                               
// ------------------------------------------------------------------------
function IS_Pillow_Author_WP_Version() {
	global $wp_version;
	$plugin = plugin_basename( __FILE__ );
	$plugin_data = get_plugin_data( __FILE__, false );

	if ( version_compare($wp_version, "3.2", "<" ) ) {
		if( is_plugin_active($plugin) ) {
			deactivate_plugins( $plugin );
			wp_die( "'".$plugin_data['Name']."' requires WordPress 3.3 or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to <a href='".admin_url()."'>WordPress admin</a>." );
		}
	}
}
add_action( 'admin_init', 'IS_Pillow_Author_WP_Version' );

include( plugin_dir_path(__FILE__) . 'inc.encode_link.php' );

class IS_Pillow_Author {
    private $base_url;
    private $base_path;
    private $plugin;
    private $plugin_data;
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
        global $wpdb;
        $this->db = $wpdb;
        $this->base_path = plugin_dir_path(__FILE__);
        $this->base_url = plugin_dir_url(__FILE__);

		if( is_admin() ) {	
			register_activation_hook(__FILE__, array(&$this, 'add_defaults') );
			add_action('admin_init', array(&$this, 'is_pillow_author_init') );
			add_action( 'show_user_profile', array(&$this, 'add_user_fields'));
			add_action( 'edit_user_profile', array(&$this, 'add_user_fields') );
			add_action( 'personal_options_update', array(&$this, 'save_user_fields') );
			add_action( 'edit_user_profile_update', array(&$this, 'save_user_fields') );
			add_action('admin_menu', array(&$this, 'custom_options_menu') );
		} else {
			add_filter('the_content', array(&$this, 'front'));
		}
    }
	
	function add_defaults() {
		delete_option('is_pillow_author_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
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
		update_option('is_pillow_author_options', $arr);
	}
	function custom_options_menu() {
		add_options_page('Pillow Author', 'Pillow Author', 'manage_options', basename(__FILE__), array(&$this, 'custom_options') );
	}
	function custom_options() {
		include($this->base_path . '/tpl.plugin_options.php');
	}

	function is_pillow_author_init(){
		register_setting( 'is_pillow_author_plugin_options', 'is_pillow_author_options',  array(&$this, 'is_pillow_author_validate_options') );
	}
	
	// Sanitize and validate input. Accepts an array, return a sanitized array.
	function is_pillow_author_validate_options($input) {
		// if custom css is provided then we will merge the default into a combined css file
		if($input['custom_css'] != "") {
			$default_css = file_get_contents( $this->base_path . '/pillow-author.css' );
			file_put_contents( $this->base_path . '/pillow-author-custom.css' , $default_css . "\n\n" . '/*** CUSTOM CSS ***/' . "\n\n" . $input['custom_css'] );
		} else if ( $input['custom_css'] == "" && file_exists($this->base_path . '/pillow-author-custom.css') ) {
			unlink( $this->base_path . '/pillow-author-custom.css' );
		}
		$input['exclude_users'] =  wp_filter_nohtml_kses($input['exclude_users']); // Sanitize textbox input (strip html tags, and escape characters)
		return $input;
	}
	
	// Display a Settings link on the main Plugins page
	function posk_plugin_action_links( $links, $file ) {
	
		if ( $file == plugin_basename( __FILE__ ) ) {
			$posk_links = '<a href="'.get_admin_url().'options-general.php?page=pillow-author.php">'.__('Settings').'</a>';
			// make the 'Settings' link appear first
			array_unshift( $links, $posk_links );
		}
	
		return $links;
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
		if( $settings['show_social_icons'] == "1" ) {
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
			if( !$filemtime or (time() - $filemtime >= $this->cache_active_life) ) {
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
		$this->settings = get_option('is_pillow_author_options');
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
				$pillow_author = ( $this->settings['use_cache'] ) ? $this->load_cache() : $this->template();
				
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
}
$is_pillow_author = new IS_Pillow_Author;