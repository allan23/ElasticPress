<?php

class EP_Settings {

	/**
	 * Is the EP_HOST constant defined?
	 * @var bool 
	 */
	var $host_defined = false;
	var $options_page;

	/**
	 * Placeholder method
	 *
	 * @since X.X
	 */
	public function __construct() {
		
	}

	/**
	 * Return a singleton instance of the current class
	 *
	 * @since X.X
	 * @return EP_Settings
	 */
	public static function factory() {
		static $instance = false;

		if ( !$instance ) {
			$instance = new self();
			add_action( 'init', array( $instance, 'setup' ) );
		}

		return $instance;
	}

	public function setup() {
		$this->check_constant();
		add_action( 'admin_menu', array( $this, 'setup_menu_item' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
	}

	public function check_constant() {
		if ( defined( 'EP_HOST' ) ) {
			$this->host_defined = true;
		}
	}

	public function setup_menu_item() {
		$this->options_page = add_options_page( 'ElasticPress', 'ElasticPress', 'manage_options', 'elasticpress', array( $this, 'settings_page' ) );
	}

	public function settings_page() {
		$this->populate_columns();
		include dirname( __FILE__ ) . '/../includes/settings_page.php';
	}

	function settings_init() {
		add_settings_section(
		'ep_setting_section', '', array( $this, 'setting_section' ), 'elasticpress'
		);

		add_settings_field(
		'ep_host', 'ElasticPress Host:', array( $this, 'setting_callback' ), 'elasticpress', 'ep_setting_section'
		);

		register_setting( 'elasticpress', 'ep_host' );
	}

	function setting_section() {
		
	}

	function setting_callback() {
		echo '<input name="ep_host" id="ep_host" type="text" value="' . esc_attr( get_option( 'ep_host' ) ) . '">';
	}

	function populate_columns() {
		add_meta_box(
		'ep-contentbox-1', 'Settings', array( $this, 'load_view' ), $this->options_page, 'normal', 'core', array( 'form.php' )
		);
		
				add_meta_box(
		'ep-contentbox-2', 'Current Status', array( $this, 'load_view' ), $this->options_page, 'side', 'core', array( 'status.php' )
		);
	}

	function load_view( $post, $args ) {
		include dirname( __FILE__ ) . '/../includes/settings/' . $args[ 'args' ][ 0 ];
	}

}

EP_Settings::factory();



