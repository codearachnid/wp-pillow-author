<div class="wrap">
	
	<!-- Display Plugin Icon, Header, and Description -->
	<div class="icon32" id="icon-options-general"><br></div>
	<h2>WordPress Pillow Author</h2>
	<p>Configure the Pillow Author plugin display and functionality.</p>

	<!-- Beginning of the Plugin Options Form -->
	<form method="post" action="options.php">
		<?php settings_fields('is_pillow_author_plugin_options'); ?>
		<?php $options = get_option('is_pillow_author_options'); ?>

		<!-- Table Structure Containing Form Controls -->
		<!-- Each Plugin Option Defined on a New Table Row -->
		<table class="form-table">
			<tr>
				<th scope="row">Show After "X" Paragraphs</th>
				<td>
					<select name='is_pillow_author_options[show_after]'>
						<option value='0' <?php selected('0', $options['show_after']); ?>>Top of Content</option>
						<option value='1' <?php selected('1', $options['show_after']); ?>>First</option>
						<option value='2' <?php selected('2', $options['show_after']); ?>>Second</option>
						<option value='3' <?php selected('3', $options['show_after']); ?>>Third</option>
						<option value='4' <?php selected('4', $options['show_after']); ?>>Fourth</option>
						<option value='-1' <?php selected('-1', $options['show_after']); ?>>Bottom of Content</option>
					</select>
					<span style="color:#666666;margin-left:10px;">Select where you want the author box to show in relation to the content</span>
				</td>
			</tr>
			<tr>
				<th scope="row">Show Avatar</th>
				<td>
					<label><input name="is_pillow_author_options[show_avatar]" type="radio" value="0" <?php checked('0', $options['show_avatar']); ?> /> No</label><br />
					<label><input name="is_pillow_author_options[show_avatar]" type="radio" value="1" <?php checked('1', $options['show_avatar']); ?> /> Yes</label><br />
				</td>
			</tr>
			<tr>
				<th scope="row">Show Description</th>
				<td>
					<label><input name="is_pillow_author_options[show_description]" type="radio" value="0" <?php checked('0', $options['show_description']); ?> /> No</label><br />
					<label><input name="is_pillow_author_options[show_description]" type="radio" value="1" <?php checked('1', $options['show_description']); ?> /> Yes</label><br />
				</td>
			</tr>
			<tr>
				<th scope="row">Show Email</th>
				<td>
					<label><input name="is_pillow_author_options[show_email]" type="radio" value="0" <?php checked('0', $options['show_email']); ?> /> No</label><br />
					<label><input name="is_pillow_author_options[show_email]" type="radio" value="1" <?php checked('1', $options['show_email']); ?> /> Yes</label><br />
				</td>
			</tr>
			<tr>
				<th scope="row">Show Website</th>
				<td>
					<label><input name="is_pillow_author_options[show_website]" type="radio" value="0" <?php checked('0', $options['show_website']); ?> /> No</label><br />
					<label><input name="is_pillow_author_options[show_website]" type="radio" value="1" <?php checked('1', $options['show_website']); ?> /> Yes</label><br />
				</td>
			</tr>
			<tr>
				<th scope="row">Show Social Media Icons</th>
				<td>
					<label><input name="is_pillow_author_options[show_social_icons]" type="radio" value="0" <?php checked('0', $options['show_social_icons']); ?> /> No</label><br />
					<label><input name="is_pillow_author_options[show_social_icons]" type="radio" value="1" <?php checked('1', $options['show_social_icons']); ?> /> Yes</label><br />
				</td>
			</tr>
			<tr>
				<th scope="row">Show Related Posts</th>
				<td>
					<select name='is_pillow_author_options[related_posts]'>
						<option value='0' <?php selected('0', $options['related_posts']); ?>>None</option>
						<option value='1' <?php selected('1', $options['related_posts']); ?>>One</option>
						<option value='2' <?php selected('2', $options['related_posts']); ?>>Two</option>
						<option value='3' <?php selected('3', $options['related_posts']); ?>>Three</option>
						<option value='4' <?php selected('4', $options['related_posts']); ?>>Four</option>
						<option value='5' <?php selected('5', $options['related_posts']); ?>>Five</option>
					</select>
					<span style="color:#666666;margin-left:10px;">To disable from displaying select "None"</span>
				</td>
			</tr>
			<tr>
				<th scope="row">Custom CSS</th>
				<td>
					<textarea name="is_pillow_author_options[custom_css]" rows="7" cols="75" type='textarea'><?php echo $options['custom_css']; ?></textarea><br /><span style="color:#666666;margin-left:10px;">Override the default CSS for this plugin or add new styles.</span>
				</td>
			</tr>
			<tr>
				<th scope="row">Exclude users</th>
				<td>
					<input type="text" size="40" name="is_pillow_author_options[exclude_users]" value="<?php echo $options['exclude_users']; ?>" /><br />
					<span style="color:#666666;margin-left:10px;">If you do not want an author to show the author box add their username (comma delimited) to be excluded</span>
				</td>
			</tr>
			<tr>
				<th scope="row">Cache Author Box</th>
				<td>
					<label><input name="is_pillow_author_options[use_cache]" type="radio" value="0" <?php checked('0', $options['use_cache']); ?> /> No</label><br />
					<label><input name="is_pillow_author_options[use_cache]" type="radio" value="1" <?php checked('1', $options['use_cache']); ?> /> Yes</label><br />
				</td>
			</tr>
		</table>
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
</div>