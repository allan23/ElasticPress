<?php
$stats = $this->get_index_status();
if ( $stats[ 'status' ] ) {
	?>
	<span class="dashicons dashicons-yes" style="color:green;"></span> Connected to ElasticSearch.<br/><br/>
	<?php if ( ep_is_activated() ) { ?>
		<span class="dashicons dashicons-yes" style="color:green;"></span> ElasticPress can override WP search.<br/><br/>
	<?php } ?>
	<strong>Index Total: </strong> <?php echo esc_html( $stats[ 'data' ]->index_total ); ?><br/>
	<strong>Index Time: </strong> <?php echo esc_html( $stats[ 'data' ]->index_time_in_millis ); ?>ms <br/><br/>
	<?php
	$stats		 = $this->get_cluster_status();
	$fs			 = $stats->nodes->fs;
	$disk_usage	 = $fs->total_in_bytes - $fs->available_in_bytes;
	?>
	<strong>Disk Usage:</strong> <?php echo esc_html(number_format( ($disk_usage / $fs->total_in_bytes) * 100, 0 )); ?>% <br/>
	<strong>Disk Space Available:</strong> <?php echo esc_html(ep_byte_size( $fs->available_in_bytes )); ?><br/>
	<strong>Total Disk Space:</strong> <?php echo esc_html(ep_byte_size( $fs->total_in_bytes )); ?> <br/>

	<?php
} else {
	echo '<span class="dashicons dashicons-no" style="color:red;"></span> <strong>ERROR:</strong> ' . $stats[ 'msg' ];
}
