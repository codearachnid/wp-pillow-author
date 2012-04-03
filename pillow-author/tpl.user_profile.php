<h3><?php _e('Social Networks'); ?></h3>
<table class="form-table">
	<?php foreach( $this->networks as $network ) : ?>
	<tr>
		<th>
			<label for="<?php echo $network; ?>"><?php _e( ucfirst($network) ); ?></label>
		</th>
		<td>
			<input type="text" name="user_profile_<?php echo $network; ?>" id="<?php echo $network; ?>" value="<?php echo esc_attr( get_the_author_meta( $network, $user->ID ) ); ?>" class="regular-text" />
		</td>
	</tr>
	<?php endforeach; ?>
</table>