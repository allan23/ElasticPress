<?php
$stats = $this->get_status();
if ( $stats[ 'status' ] ) {
	?>
	<span class="dashicons dashicons-yes" style="color:green;"></span> Connected to ElasticSearch.<br/><br/>
	<?php if ( ep_is_activated() ) { ?>
		<span class="dashicons dashicons-yes" style="color:green;"></span> ElasticPress can override WP search.<br/><br/>
	<?php } ?>
	<strong>Index Total: </strong> <?php echo esc_html( $stats[ 'data' ]->index_total ); ?><br/>
	<strong>Index Time: </strong> <?php echo esc_html( $stats[ 'data' ]->index_time_in_millis ); ?>ms
	<?php
} else {
	echo '<span class="dashicons dashicons-no" style="color:red;"></span> <strong>ERROR:</strong> ' . $stats[ 'msg' ];
}