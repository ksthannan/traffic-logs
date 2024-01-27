<?php defined( 'ABSPATH' ) or die; ?>
<div class="wrap wrap-traffic-logs-content">
	<h1><?php _e( 'Links', 'traffic-logs' ); ?></h1>
	<form method="post" action="options.php">
		<?php settings_errors(); ?>
		<?php settings_fields( TRAFFIC_LOGS_CONTENT_OPTSGROUP_NAME ); ?>
		<?php do_settings_sections( TRAFFIC_LOGS_CONTENT_OPTSGROUP_NAME ); ?>
		<h3><?php _e( 'Settings', 'traffic-logs' ); ?></h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th><?php _e( 'API Key Or Token', 'traffic-logs' );?></th>
					<td><input type="text" name="<?php esc_attr_e( TRAFFIC_LOGS_CONTENT_OPTIONS_NAME ); ?>[api_key]" placeholder="3d87369ed3e021" value="<?php esc_attr_e( $this->get_option( 'api_key' ) ); ?>">
					<p><?php _e( '<a href="https://ipinfo.io/signup" target="_blank">Get an API key from here</a>', 'traffic-logs' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<hr>
		<h3><?php _e( 'Links', 'traffic-logs' ); ?></h3>
		<div class="traffic-logs-content-pairs">
			<?php if ( ! empty( $links ) ) : ?>
				<?php foreach( $links as $index => $link ) : ?>
					<?php echo $this->get_pair_tpl( $links[$index] ); ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<div class="traffic-logs-content-buttons">
			<a href="#" class="button button-secondary traffic-logs-content-add"><?php esc_html_e( 'Add new', 'traffic-logs' ); ?></a>
		</div>
		<?php submit_button(); ?>
	</form>
</div>