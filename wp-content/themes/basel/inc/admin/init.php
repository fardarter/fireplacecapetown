<?php if ( ! defined('BASEL_THEME_DIR')) exit('No direct script access allowed');

/**
 * ------------------------------------------------------------------------------------------------
 * Enqueue admin scripts
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_admin_scripts' ) ) {
	function basel_admin_scripts() {
		wp_enqueue_script( 'basel-admin-scripts', BASEL_ASSETS . '/js/admin.js', array(), '', true );

		if( apply_filters( 'basel_gradients_enabled', true ) ) {
			wp_enqueue_script( 'basel-colorpicker-scripts', BASEL_ASSETS . '/js/colorpicker.min.js', array(), '', true );
			wp_enqueue_script( 'basel-gradient-scripts', BASEL_ASSETS . '/js/gradX.min.js', array(), '', true );
		}

		if ( basel_get_opt( 'size_guides' ) ) {
			wp_enqueue_script( 'basel-edittable-scripts', BASEL_ASSETS . '/js/jquery.edittable.min.js', array(), '', true );
		}

		basel_admin_scripts_localize();

	}
	add_action('admin_init','basel_admin_scripts', 100);
}

/**
 * ------------------------------------------------------------------------------------------------
 * Localize admin script function
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_admin_scripts_localize' ) ) {
	function basel_admin_scripts_localize() {
		wp_localize_script( 'basel-admin-scripts', 'baselConfig', basel_admin_script_local() );
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Get localization array for admin scripts
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_admin_script_local' ) ) {
	function basel_admin_script_local() {
		$localize_data = array(
			'ajax' => admin_url( 'admin-ajax.php' ),
		);

		// If we are on edit product attribute page
		if( ! empty( $_GET['page'] ) && $_GET['page'] == 'product_attributes' && ! empty( $_GET['edit'] ) && function_exists('wc_attribute_taxonomy_name_by_id')) {
			$attribute_id = absint( $_GET['edit'] );
			$attribute_name = wc_attribute_taxonomy_name_by_id( $attribute_id );
			$localize_data['attributeSwatchSize'] = basel_wc_get_attribute_term( $attribute_name, 'swatch_size' );
			
			$localize_data['attributeShowOnProduct'] = basel_wc_get_attribute_term( $attribute_name, 'show_on_product' );
		}
		
		if( class_exists('Redux') ) {
			$redux_options = array();
			$options_key = 'basel_options';

			$redux_sections = Redux::getSections($options_key);


			foreach ($redux_sections as $id => $section) {
				if( ! isset( $section['subsection'] ) ) {
					$parent_name = $section['title'];
					$parent_icon = $section['icon'];
				} else {
					$redux_sections[$id]['parent_name'] = $parent_name;
					$redux_sections[$id]['icon'] = $parent_icon;
				}
			}

			$options = Redux::$fields[$options_key];

			foreach ($options as $id => $option) {
				if( ! isset( $option['title'] ) ) continue;
				$text = $option['title'];
				if( isset($option['desc']) ) $text .= ' ' . $option['desc'];
				if( isset($option['subtitle']) ) $text .= ' ' . $option['subtitle'];
				if( isset($option['tags']) ) $text .= ' ' . $option['tags'];

				if( isset( $redux_sections[$option['section_id']]['subsection'] ) ) {
					 $path = $redux_sections[$option['section_id']]['parent_name'] . ' -> ' . $redux_sections[$option['section_id']]['title'];
				} else {
					 $path = $redux_sections[$option['section_id']]['title'];
				}

				$redux_options[] = array(
					'id' => $id,
					'title' => $option['title'],
					'text' => $text,
					'section_id' => $redux_sections[$option['section_id']]['priority'],
					'icon' => $redux_sections[$option['section_id']]['icon'],
					'path' => $path,
				);
			}

			$localize_data['reduxOptions'] = $redux_options;
		}

		$localize_data['searchOptionsPlaceholder'] = esc_js(__('Search for options', 'basel'));

		return apply_filters( 'basel_admin_script_local', $localize_data );
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Enqueue admin styles
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_enqueue_admin_styles' ) ) {
	function basel_enqueue_admin_styles() {
		if ( is_admin() ) {
			wp_enqueue_style( 'basel-admin-style', BASEL_ASSETS . '/css/theme-admin.css');
			if( apply_filters( 'basel_gradients_enabled', true ) ) {
				wp_enqueue_style( 'basel-colorpicker-style', BASEL_ASSETS . '/css/colorpicker.css', array() );
				wp_enqueue_style( 'basel-gradient-style', BASEL_ASSETS . '/css/gradX.css', array() );
			}
			if ( basel_get_opt( 'size_guides' ) ) {
				wp_enqueue_style( 'basel-edittable-style', BASEL_ASSETS . '/css/jquery.edittable.min.css', array() );
			}
		}

	}

	add_action( 'admin_enqueue_scripts', 'basel_enqueue_admin_styles' );
}

