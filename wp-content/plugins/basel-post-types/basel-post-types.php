<?php 
/*
Plugin Name: XTEMOS Post Types plugin
Description: Register post types needed for XTEMOS themes
Version: 1.3
Text Domain: basel_post_types
*/

class BASEL_Post_Types {

	public $domain = 'basel_starter';

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'basel' ), '2.1' );
	}

	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'basel' ), '2.1' );
	}

	public function __construct() {
		
		// Hook into the 'init' action
		add_action( 'init', array($this, 'register_blocks'), 1 );
		add_action( 'init', array($this, 'size_guide'), 1 );

		// Add shortcode column to block list
		add_filter( 'manage_edit-cms_block_columns', array($this, 'edit_html_blocks_columns') ) ;
		add_action( 'manage_cms_block_posts_custom_column', array($this, 'manage_html_blocks_columns'), 10, 2 );

		add_filter( 'manage_edit-portfolio_columns', array($this, 'edit_portfolio_columns') ) ;
		add_action( 'manage_portfolio_posts_custom_column', array($this, 'manage_portfolio_columns'), 10, 2 );

		add_action( 'init', array($this, 'register_sidebars'), 1 );
		add_action( 'init', array($this, 'register_portfolio'), 1 );

	}

	// **********************************************************************// 
	// ! Register Custom Post Type for Size Guide
	// **********************************************************************// 
	public function size_guide() {
		
		if ( function_exists( 'basel_get_opt' ) && !basel_get_opt( 'size_guides' ) ) return;

		$labels = array(
			'name'                => _x( 'Size Guides', 'Post Type General Name', $this->domain ),
			'singular_name'       => _x( 'Size Guide', 'Post Type Singular Name', $this->domain ),
			'menu_name'           => __( 'Size Guides', $this->domain ),
			'add_new'             => _x( 'Add new', 'size guide', $this->domain ),
			'add_new_item'        => __( 'Add new size guide', $this->domain ),
			'new_item'            => __( 'New size guide', $this->domain ),
			'edit_item'           => __( 'Edit size guide', $this->domain ),
			'view_item'           => __( 'View size guide', $this->domain ),
			'all_items'           => __( 'All size guides', $this->domain ),
			'search_items'        => __( 'Search size guides', $this->domain ),
			'not_found'           => __( 'No size guides found.', $this->domain ),
			'not_found_in_trash'  => __( 'No size guides found in trash.', $this->domain )
		);

		$args = array(
			'label'               => __( 'basel_size_guide', $this->domain ),
			'description'         => __( 'Size guide to place in your products', $this->domain ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 29,
			'menu_icon'           => 'dashicons-editor-kitchensink',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capability_type'     => 'page',
		);

		register_post_type( 'basel_size_guide', $args );
	}

	public function register_blocks() {

		$labels = array(
			'name'                => _x( 'HTML Blocks', 'Post Type General Name', $this->domain ),
			'singular_name'       => _x( 'HTML Block', 'Post Type Singular Name', $this->domain ),
			'menu_name'           => __( 'HTML Blocks', $this->domain ),
			'parent_item_colon'   => __( 'Parent Item:', $this->domain ),
			'all_items'           => __( 'All Items', $this->domain ),
			'view_item'           => __( 'View Item', $this->domain ),
			'add_new_item'        => __( 'Add New Item', $this->domain ),
			'add_new'             => __( 'Add New', $this->domain ),
			'edit_item'           => __( 'Edit Item', $this->domain ),
			'update_item'         => __( 'Update Item', $this->domain ),
			'search_items'        => __( 'Search Item', $this->domain ),
			'not_found'           => __( 'Not found', $this->domain ),
			'not_found_in_trash'  => __( 'Not found in Trash', $this->domain ),
		);

		$args = array(
			'label'               => __( 'cms_block', $this->domain ),
			'description'         => __( 'CMS Blocks for custom HTML to place in your pages', $this->domain ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 29,
			'menu_icon'           => 'dashicons-schedule',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capability_type'     => 'page',
		);

		register_post_type( 'cms_block', $args );

	}


	public function edit_html_blocks_columns( $columns ) {

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title', $this->domain ),
			'shortcode' => __( 'Shortcode', $this->domain ),	   
			'date' => __( 'Date', $this->domain ),
		);

		return $columns;
	}


	public function manage_html_blocks_columns($column, $post_id) {
		switch( $column ) {
			case 'shortcode' :
				echo '<strong>[html_block id="'.$post_id.'"]</strong>';
			break;
		}	
	}

	// **********************************************************************// 
	// ! Register Custom Post Type for additional sidebars
	// **********************************************************************// 
	public function register_sidebars() {

		$labels = array(
			'name'                => _x( 'Sidebars', 'Post Type General Name', $this->domain ),
			'singular_name'       => _x( 'Sidebar', 'Post Type Singular Name', $this->domain ),
			'menu_name'           => __( 'Sidebars', $this->domain ),
			'parent_item_colon'   => __( 'Parent Item:', $this->domain ),
			'all_items'           => __( 'All Items', $this->domain ),
			'view_item'           => __( 'View Item', $this->domain ),
			'add_new_item'        => __( 'Add New Item', $this->domain ),
			'add_new'             => __( 'Add New', $this->domain ),
			'edit_item'           => __( 'Edit Item', $this->domain ),
			'update_item'         => __( 'Update Item', $this->domain ),
			'search_items'        => __( 'Search Item', $this->domain ),
			'not_found'           => __( 'Not found', $this->domain ),
			'not_found_in_trash'  => __( 'Not found in Trash', $this->domain ),
		);

		$args = array(
			'label'               => __( 'basel_sidebar', $this->domain ),
			'description'         => __( 'You can create additional custom sidebar and use them in Visual Composer', $this->domain ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 67,
			'menu_icon'           => 'dashicons-welcome-widgets-menus',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capability_type'     => 'page',
		);

		register_post_type( 'basel_sidebar', $args );

	}



	// **********************************************************************// 
	// ! Register Custom Post Type for portfolio
	// **********************************************************************// 
	public function register_portfolio() {

		$labels = array(
			'name'                => _x( 'Portfolio', 'Post Type General Name', $this->domain ),
			'singular_name'       => _x( 'Project', 'Post Type Singular Name', $this->domain ),
			'menu_name'           => __( 'Projects', $this->domain ),
			'parent_item_colon'   => __( 'Parent Item:', $this->domain ),
			'all_items'           => __( 'All Items', $this->domain ),
			'view_item'           => __( 'View Item', $this->domain ),
			'add_new_item'        => __( 'Add New Item', $this->domain ),
			'add_new'             => __( 'Add New', $this->domain ),
			'edit_item'           => __( 'Edit Item', $this->domain ),
			'update_item'         => __( 'Update Item', $this->domain ),
			'search_items'        => __( 'Search Item', $this->domain ),
			'not_found'           => __( 'Not found', $this->domain ),
			'not_found_in_trash'  => __( 'Not found in Trash', $this->domain ),
		);

		$args = array(
			'label'               => __( 'portfolio', $this->domain ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 28,
			'menu_icon'           => 'dashicons-images-alt2',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'rewrite'             => array('slug' => 'portfolio'),
			'capability_type'     => 'page',
		);

		register_post_type( 'portfolio', $args );

		/**
		 * Create a taxonomy category for portfolio
		 *
		 * @uses  Inserts new taxonomy object into the list
		 * @uses  Adds query vars
		 *
		 * @param string  Name of taxonomy object
		 * @param array|string  Name of the object type for the taxonomy object.
		 * @param array|string  Taxonomy arguments
		 * @return null|WP_Error WP_Error if errors, otherwise null.
		 */
		
		$labels = array(
			'name'					=> _x( 'Project Categories', 'Taxonomy plural name', $this->domain ),
			'singular_name'			=> _x( 'Project Category', 'Taxonomy singular name', $this->domain ),
			'search_items'			=> __( 'Search Categories', $this->domain ),
			'popular_items'			=> __( 'Popular Project Categories', $this->domain ),
			'all_items'				=> __( 'All Project Categories', $this->domain ),
			'parent_item'			=> __( 'Parent Category', $this->domain ),
			'parent_item_colon'		=> __( 'Parent Category', $this->domain ),
			'edit_item'				=> __( 'Edit Category', $this->domain ),
			'update_item'			=> __( 'Update Category', $this->domain ),
			'add_new_item'			=> __( 'Add New Category', $this->domain ),
			'new_item_name'			=> __( 'New Category', $this->domain ),
			'add_or_remove_items'	=> __( 'Add or remove Categories', $this->domain ),
			'choose_from_most_used'	=> __( 'Choose from most used text-domain', $this->domain ),
			'menu_name'				=> __( 'Category', $this->domain ),
		);
	
		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => false,
			'hierarchical'      => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => true,
			'query_var'         => true,
			'capabilities'      => array(),
		);
	
		register_taxonomy( 'project-cat', array( 'portfolio' ), $args );

	}


	public function edit_portfolio_columns( $columns ) {

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'thumb' => '',
			'title' => __( 'Title', $this->domain ),
			'project-cat' => __( 'Categories', $this->domain ),	   
			'date' => __( 'Date', $this->domain ),
		);

		return $columns;
	}


	public function manage_portfolio_columns($column, $post_id) {
		switch( $column ) {
			case 'thumb' :
				if( has_post_thumbnail( $post_id ) ) {
					the_post_thumbnail( array(60,60) );
				}
			break;
			case 'project-cat' :
				$terms = get_the_terms( $post_id, 'project-cat' );
										
				if ( $terms && ! is_wp_error( $terms ) ) : 

					$cats_links = array();

					foreach ( $terms as $term ) {
						$cats_links[] = $term->name;
					}
										
					$cats = join( ", ", $cats_links );
				?>

				<span><?php echo $cats; ?></span>

				<?php endif; 
			break;
		}	
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}


}

function BASEL_Theme_Plugin() {
	return BASEL_Post_Types::instance();
}

$GLOBALS['basel_theme_plugin'] = BASEL_Theme_Plugin();

require_once 'vendor/opauth/twitteroauth/twitteroauth.php';
require_once 'inc/auth.php';

?>
