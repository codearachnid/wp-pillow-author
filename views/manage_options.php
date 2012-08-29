<div class="wrap">
	
	<!-- Display Plugin Icon, Header, and Description -->
	<div class="icon32" id="icon-users"><br></div>
	<h2>WordPress Pillow Author</h2>

	<!-- Beginning of the Plugin Options Form -->
	<form method="post" action="options.php">
		<?php settings_fields( self::TOKEN ); ?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<h3>Display Preview</h3>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent convallis orci ipsum, nec sodales velit. Aliquam erat volutpat. Praesent congue viverra augue nec pellentesque. Quisque mollis, risus eget tempus dapibus, urna elit ultrices neque, eu ultrices leo nisi ac ante. Sed blandit quam sed tellus mattis vitae fermentum lacus bibendum. Aliquam luctus laoreet lacus, ut pharetra neque malesuada vitae. Maecenas nec dolor eu magna mattis molestie.</p>
					<p>Nam elementum pellentesque auctor. Cras rhoncus libero justo. Aliquam a lorem eros, vel dignissim eros. Sed nisl neque, blandit vel faucibus a, lacinia vitae ipsum. Cras sollicitudin ornare lobortis. Donec sollicitudin, elit sit amet fermentum vulputate, leo erat vulputate nibh, a laoreet turpis mi at nisl. Suspendisse cursus felis vitae tortor ullamcorper dapibus. In turpis orci, scelerisque et faucibus at, tincidunt eget sapien.</p>
					<p>Morbi hendrerit ante eu libero posuere vestibulum id quis arcu. Pellentesque at ipsum sit amet nulla dignissim interdum ut sed nisi. Nunc magna augue, eleifend sed varius nec, eleifend eu tortor. Fusce eu orci justo, sit amet vestibulum libero. Pellentesque ultrices cursus nisl in faucibus. Nunc odio augue, pretium id rutrum ut, pretium eget lorem. Nam commodo laoreet viverra.</p>
					<p>Cras nisi lectus, dignissim auctor vestibulum at, feugiat in turpis. Etiam lobortis pellentesque gravida. Nunc ac fermentum massa. Donec consectetur ante id magna scelerisque vel aliquet quam consequat. Suspendisse potenti. Donec ac metus dolor, at luctus enim. Morbi dictum interdum justo sit amet feugiat. Nullam quis cursus ligula. Fusce turpis mauris, sollicitudin sit amet ornare nec, fermentum id enim. Nam turpis arcu, sollicitudin vitae fermentum nec, gravida non ipsum. Curabitur sapien tellus, congue nec adipiscing a, egestas non nisl.</p>
					<p>Mauris blandit neque a magna iaculis dictum. Curabitur rutrum augue at velit vestibulum non lobortis orci porta. Cras id dictum nisl. Quisque lobortis condimentum mauris, at ullamcorper massa gravida eu. Pellentesque rutrum tristique commodo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Morbi nisl nisl, molestie imperdiet consectetur vitae, aliquam at lacus. Cras tincidunt, leo ut tincidunt egestas, urna ante luctus est, at feugiat augue nulla interdum ipsum. Morbi laoreet tempor libero et pretium. Aliquam consequat dui et eros egestas ut pharetra dui dignissim. Duis vel venenatis metus. Proin suscipit odio sit amet ante porta quis lobortis lacus rutrum.</p>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<?php do_settings_sections( self::TOKEN ); ?>
				</div>
			</div>
		</div>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
</div>