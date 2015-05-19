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
			if ( defined( 'EP_OVERRIDE' ) && EP_OVERRIDE ) {
				add_action( 'init', array( $instance, 'setup' ) );
			}
		}

		return $instance;
	}

	/**
	 * Loads initial actions.
	 * @since X.X
	 */
	public function setup() {
		$this->check_constant();
		add_action( 'admin_menu', array( $this, 'setup_menu_item' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
	}

	/**
	 * Checks for existence of EP_HOST constant.
	 * If does not exist, defines it.
	 * @since X.X
	 */
	private function check_constant() {
		if ( defined( 'EP_HOST' ) ) {
			$this->host_defined = true;
		} else {
			$this->set_host();
		}
	}

	/**
	 * Retrieves the value set in options the host and defines EP_HOST constant.
	 * @since X.X
	 */
	private function set_host() {
		$ep_host = get_option( 'ep_host' );
		if ( $ep_host ) {
			define( 'EP_HOST', $ep_host );
		}
	}

	/**
	 * Adds options page to admin menu.
	 * @since X.X
	 */
	public function setup_menu_item() {
		$this->options_page = add_options_page( 'ElasticPress', 'ElasticPress', 'manage_options', 'elasticpress', array( $this, 'settings_page' ) );
	}

	/**
	 * Loads up the settings page.
	 * @since X.X
	 */
	public function settings_page() {
		$this->populate_columns();
		include dirname( __FILE__ ) . '/../includes/settings_page.php';
	}

	/**
	 * Sets up Settings API.
	 * @since X.X
	 */
	public function settings_init() {
		add_settings_section(
		'ep_setting_section', '', array( $this, 'setting_section' ), 'elasticpress'
		);

		add_settings_field(
		'ep_host', 'ElasticSearch Host:', array( $this, 'setting_callback' ), 'elasticpress', 'ep_setting_section'
		);

		register_setting( 'elasticpress', 'ep_host' );
	}

	/**
	 * Filler callback for the settings section.
	 * @since X.X
	 */
	public function setting_section() {
		
	}

	/**
	 * Callback for settings field. Displays textbox to specify the EP_HOST.
	 * @since X.X
	 */
	public function setting_callback() {
		echo '<input name="ep_host" id="ep_host" type="text" value="' . esc_attr( get_option( 'ep_host' ) ) . '">';
	}

	/**
	 * Creates meta boxes for the settings page columns.
	 * @since X.X
	 */
	private function populate_columns() {
		add_meta_box(
		'ep-contentbox-1', 'Settings', array( $this, 'load_view' ), $this->options_page, 'normal', 'core', array( 'form.php' )
		);

		add_meta_box(
		'ep-contentbox-2', 'Current Status', array( $this, 'load_view' ), $this->options_page, 'side', 'core', array( 'status.php' )
		);
	}

	/**
	 * Callback for add_meta_box to load column view.
	 * @param WP_Post|NULL $post Normally WP_Post object, but NULL in our case.
	 * @param array $args Arguments passed from add_meta_box.
	 * @since X.X
	 */
	public function load_view( $post, $args ) {
		include dirname( __FILE__ ) . '/../includes/settings/' . $args[ 'args' ][ 0 ];
	}

	/**
	 * Retrieves stats from ElasticSearch.
	 * @return array Contains the status message or the returned statistics.
	 * @since X.X
	 */
	public function get_cluster_status() {
		if ( !defined( 'EP_HOST' ) ) {
			return array(
				'status' => false,
				'msg'	 => 'ElasticSearch Host is not defined.'
			);
		}
		$url	 = EP_HOST . '/_cluster/stats';

		$request = wp_remote_request( $url, array( 'method' => 'GET' ) );
		if ( !is_wp_error( $request ) ) {
			$response = json_decode( wp_remote_retrieve_body( $request ) );
			return  $response ;
		}

		return array(
			'status' => false,
			'msg'	 => $request->get_error_message()
		);
	}
	
		/**
	 * Retrieves index stats from ElasticSearch.
	 * @return array Contains the status message or the returned statistics.
	 * @since X.X
	 */
	public function get_index_status() {
		if ( !defined( 'EP_HOST' ) ) {
			return array(
				'status' => false,
				'msg'	 => 'ElasticSearch Host is not defined.'
			);
		}
		$url	 = ep_get_index_url() . '/_stats/indexing/';
		$request = wp_remote_request( $url, array( 'method' => 'GET' ) );
		if ( !is_wp_error( $request ) ) {
			$response = json_decode( wp_remote_retrieve_body( $request ) );
			return $this->parse_response( $response );
		}

		return array(
			'status' => false,
			'msg'	 => $request->get_error_message()
		);
	}

	/**
	 * Determines if there is an issue or if the response is valid.
	 * @param object $response JSON decoded response from ElasticSearch.
	 * @return array Contains the status message or the returned statistics.
	 * @since X.X
	 */
	private function parse_response( $response ) {
		if ( isset( $response->error ) && stristr( $response->error, 'IndexMissingException' ) ) {
			return array(
				'status' => false,
				'msg'	 => 'Site not indexed. <p><strong>Please run:</strong> <code>wp elasticpress index --setup</code> <strong>using WP-CLI.</strong></p>'
			);
		}

		return array( 'status' => true, 'data' => $response->_all->primaries->indexing );
	}

}

EP_Settings::factory();

/**
 * Accessor functions for methods in above class. See doc blocks above for function details.
 */
function ep_get_index_status() {
	return EP_Settings::factory()->get_index_status();
}
function ep_get_status() {
	return EP_Settings::factory()->get_cluster_status();
}

/** Helper Functions **/

/**
 * Converts bytes to human-readable format.
 * @param int $bytes
 * @param int $precision
 * @return string
 */
function ep_byte_size( $bytes, $precision = 2 ) {
	$kilobyte	 = 1024;
	$megabyte	 = $kilobyte * 1024;
	$gigabyte	 = $megabyte * 1024;
	$terabyte	 = $gigabyte * 1024;

	if ( ($bytes >= 0) && ($bytes < $kilobyte) ) {
		return $bytes . ' B';
	} elseif ( ($bytes >= $kilobyte) && ($bytes < $megabyte) ) {
		return round( $bytes / $kilobyte, $precision ) . ' KB';
	} elseif ( ($bytes >= $megabyte) && ($bytes < $gigabyte) ) {
		return round( $bytes / $megabyte, $precision ) . ' MB';
	} elseif ( ($bytes >= $gigabyte) && ($bytes < $terabyte) ) {
		return round( $bytes / $gigabyte, $precision ) . ' GB';
	} elseif ( $bytes >= $terabyte ) {
		return round( $bytes / $terabyte, $precision ) . ' TB';
	} else {
		return $bytes . ' B';
	}
}
