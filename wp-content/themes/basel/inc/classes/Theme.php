<?php if ( ! defined('BASEL_THEME_DIR')) exit('No direct script access allowed');

/**
 * Main Theme class that initialize all
 * other classes like assets, layouts, options
 *
 * Also includes files with theme functions
 * template tags, 3d party plugins etc.
 */
class BASEL_Theme {

	/**
	 * Classes array to register in BASEL_Registery object
	 * @var array
	 */
	private $register_classes = array();

	/**
	 * Files array to include from inc/ folder
	 * @var array
	 */
	private $files_include = array();

	/**
	 * Array of files to include in admin area
	 * @var array
	 */
	private $admin_files_include = array();

	/**
	 * 3d party plugins array
	 * @var array
	 */
	private $third_party_plugins = array();


	/**
	 * Call init methods
	 */
	public function __construct() {

		$this->register_classes = array(
			'options',
			'ajaxresponse',
			'notices',
			'metaboxes',
			'layout',
			'import',
			'swatches',
			'search',
			'catalog',
			'maintenance',
			'api',
			'license',
		);	

		$this->files_include = array(
			'functions',
			'theme-setup',
			'template-tags',
			'shortcodes',
			'woocommerce',
			'woocommerce/attributes-meta-boxes',
			'woocommerce/product-360-view',
			'woocommerce/size-guide',
			'vc-config',
			'settings',
			'widgets',
			'styles',
			'configs/assets',
		);	

		$this->admin_files_include = array(
			'admin/dashboard/dashboard',
			'admin/init',
			'admin/functions',
		);	


		$this->third_party_plugins = array(
			'plugin-activation/class-tgm-plugin-activation',
			'nav-menu-images/nav-menu-images',
			'wph-widget-class',
		);	

		$this->_third_party_plugins();
		$this->_core_plugin_classes();
		$this->_include_files();
		$this->_register_classes();

		if( is_admin() ) {
			$this->_include_admin_files();
		}

	}

	/**
	 * Register classes in BASEL_Registry
	 * 
	 */
	private function _register_classes() {

		foreach ($this->register_classes as $class) {
			BASEL_Registry::getInstance()->$class;
		}

	}

	/**
	 * Include files fron inc/ folder
	 * 
	 */
	private function _include_files() {

		foreach ($this->files_include as $file) {
			$path = apply_filters('basel_require', BASEL_FRAMEWORK . '/' . $file . '.php');
			if( file_exists( $path ) ) {
				require_once $path;
			}
		}

	}

	/**
	 * Include files in admin area
	 * 
	 */
	private function _include_admin_files() {

		foreach ($this->admin_files_include as $file) {
			$path = apply_filters('basel_require', BASEL_FRAMEWORK . '/' . $file . '.php');
			if( file_exists( $path ) ) {
				require_once $path;
			}
		}

	}

	/**
	 * Register 3d party plugins
	 * 
	 */
	private function _third_party_plugins() {

		foreach ($this->third_party_plugins as $file) {
			$path = apply_filters('basel_require', BASEL_3D . '/' . $file . '.php');
			if( file_exists( $path ) ) {
				require_once $path;
			}
		}

	}
	
	private function _core_plugin_classes() {
		if ( class_exists( 'BASEL_Auth' ) ) {
			$file_path = array(
				'vendor/autoload'
			);
			foreach ( $file_path as $file ) {
				$path = apply_filters( 'basel_require', BASEL_PT_3D . $file . '.php' );
				if( file_exists( $path ) ) {
					require_once $path;
				}
			}
			$this->register_classes[] = 'auth';
		}
	}
}