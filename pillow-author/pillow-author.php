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

include( plugin_dir_path(__FILE__) . 'inc.encode_link.php' );

class IS_Pillow_Author {
    private $base_url;
    private $base_path;
    private $db;
    private $uid = 'pillow_author';
    private $filter_types = array('post');
    public $cache_active_life = 100;
    public $use_cache = false;
    public $show_after = 1;
    public $networks = array('twitter','facebook','google','linkedin','tumblr','youtube','delicious');
    public $avatar_size = 200;
    public $related_posts = 3;
    
    function __construct() {
        // Setup common access properties
        global $wpdb;
        $this->db = $wpdb;
        $this->base_path = plugin_dir_path(__FILE__);
        $this->base_url = plugin_dir_url(__FILE__);

		if( is_admin() ) {	
			add_action( 'show_user_profile', array(&$this, 'add_user_fields'));
			add_action( 'edit_user_profile', array(&$this, 'add_user_fields') );
			add_action( 'personal_options_update', array(&$this, 'save_user_fields') );
			add_action( 'edit_user_profile_update', array(&$this, 'save_user_fields') );
		} else {
			add_filter('the_content', array(&$this, 'front'));
		}
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
		$related_posts = get_posts( array( 'author' => $user_id, 'post__not_in' => array( $post->ID ), 'posts_per_page' => $this->related_posts ) );	
		return $related_posts;
	}

	function add_user_fields( $user ) {
		include($this->base_path . '/tpl.user_profile.php');
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
    	// TODO allow user defined custom post types
	    if ( $post && in_array( $post->post_type, $this->filter_types ) ) {

	    	wp_enqueue_style( $this->uid, $this->base_url . '/pillow-author.css' );
	    	
	    	$content_blocks = explode( '</p>', $content );
	    	
	    	$pre_pillow = array_slice( $content_blocks, 0, $this->show_after );
	    	$post_pillow = array_slice( $content_blocks, $this->show_after );
	    	$pillow_author = ( $this->use_cache ) ? $this->load_cache() : $this->template();
	    	
	    	return implode( '</p>', $pre_pillow ) . $pillow_author . implode( '</p>', $post_pillow );
    	} else {
    		return $content;
    	}
    }
}
$is_pillow_author = new IS_Pillow_Author;