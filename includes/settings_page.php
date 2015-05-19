<div class="wrap">
	<h2>ElasticPress</h2>

	<div id="dashboard-widgets" class="metabox-holder columns-2 has-right-sidebar">
		<div id='postbox-container-1' class='postbox-container'>
			<?php $meta_boxes = do_meta_boxes( $this->options_page, 'normal', null ); ?>	
		</div>

		<div id='postbox-container-2' class='postbox-container'>
			<?php do_meta_boxes( $this->options_page, 'side', null ); ?>
		</div>

	</div>
</div>
