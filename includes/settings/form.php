<?php if ( false === $this->host_defined ) { ?>
	<form method="POST" action="options.php">
		<?php
		settings_fields( 'elasticpress' );
		do_settings_sections( 'elasticpress' );
		submit_button();
		?>
	</form>
<?php } else { ?>
	<form>
		<table class="form-table"><tr><th scope="row">ElasticPress Host:</th><td> <input type="text" disabled="disabled" value="<?php echo esc_html( EP_HOST ); ?>"></td></tr></table>
	</form>
<?php } ?>