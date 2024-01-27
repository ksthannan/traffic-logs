<?php defined( 'ABSPATH' ) or die; ?>
<div class="traffic-logs-content-pair">
	<input type="text" class="regular-text" name="<?php esc_attr_e( TRAFFIC_LOGS_CONTENT_OPTIONS_NAME ); ?>[links][]" value="<?php esc_attr_e( $link ); ?>" placeholder="<?php esc_attr_e( 'Link', 'traffic-logs-content' ); ?>">
	<?php if($link):?>
	<a href="<?php echo $link ? admin_url("admin.php?page=traffic-logs&traffic_url=" . $link ) : '#';?>" class="button button-primary traffic-logs-content-traffic"><?php esc_html_e( 'View Traffic Logs', 'traffic-logs-content' ); ?></a> 
	<?php endif;?>
	<a href="#" class="button button-secondary traffic-logs-content-del"><?php esc_html_e( 'Delete', 'traffic-logs-content' ); ?></a>
</div>