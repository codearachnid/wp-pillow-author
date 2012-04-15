<div class="pillow_author">
	<?php 
		
		if( $this->settings['show_avatar'] == "1" ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( (is_plugin_active('user-avatar/user-avatar.php'))  or function_exists('get_avatar')) { 
				echo get_avatar( get_the_author_meta('email'), $this->avatar_size ); 
			}
		}
			
	?>
    <h4>About <?php the_author_posts_link(); ?></h4>
    <?php if ($this->settings['show_description'] == "1" && get_the_author_meta( 'description' ) != "" ) : ?>
    <p class="description"><?php the_author_meta('description'); ?></p>
    <?php 
    
    	endif;
    	if ( $this->settings['show_email'] == "1" || $this->settings['show_website'] == "1" ) : 
    
    ?>
    <p class="email_website">
    <?php 
    	
    	if ( $this->settings['show_email'] == "1" ) {
    		echo IS_Pillow_Author_URL_Encoder::encodelink( 'mailto:' . get_the_author_meta( 'email' ), 'Email Me!' );
    	}
    	
    	if ( $this->settings['show_website'] == "1" && get_the_author_meta( 'user_url' ) != "" ) :  	
    		if ( $this->settings['show_email'] == "1" ) {
    			echo " | ";
    		}

    		?><a href="<?php the_author_meta('user_url'); ?>" target="_blank">Website</a><?php 
    	
    	endif; 
    	
	?>
    </p>
    <?php 
    
		endif; 
		if( $this->settings['show_social_icons'] == "1" ) :
    
    ?>
    <ul class="social_network">
    	<?php foreach( $this->networks as $network ) : if (get_the_author_meta( $network ) == "" ) continue; ?>
    	<li><a href="<?php echo $this->the_network( $network, get_the_author_meta( $network ) ); ?>" name="Follow <?php the_author(); ?> on <?php echo ucfirst($network); ?>" target="_blank"><img src="<?php echo $this->base_url . 'icons/' . $network . '.png'; ?>" alt="Follow <?php the_author(); ?> on <?php echo ucfirst($network); ?>" /></a></li>
    	<?php endforeach; ?>
    </ul>
    <?php
    
    	endif;
    	
    	if( $this->settings['related_posts'] != "0" ) :
			$related_posts = $this->get_related_author_posts( get_the_author_meta( 'ID' ) );
			if(count($related_posts) > 0 ) :
    ?>
    <h5>Related Posts by <?php the_author(); ?></h5>
	<ul class="related_posts">
		<?php foreach ($related_posts as $related_post ) : ?>
		<li><a href="<?php echo get_permalink( $related_post->ID ); ?>"><?php echo apply_filters( 'the_title', $related_post->post_title, $related_post->ID ); ?></a></li>
		<?php endforeach; ?>
	</ul>
	<?php endif; endif; ?>
</div>