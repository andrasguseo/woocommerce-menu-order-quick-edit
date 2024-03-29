<?php
/**
 * Plugin Name:       WooCommerce Extension: Menu Order Quick Edit
 * Plugin URI:
 * GitHub Plugin URI: https://github.com/andrasguseo/woo-commerce-extension-menu-order-quick-edit
 * Description:
 * Version:           1.0.0
 * Plugin Class:      AGU_Woo_Menu_Order_Quick_Edit
 * Author:            Andras Guseo
 * Author URI:        https://andrasguseo.com
 * License:           GPL version 3 or any later version
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       woocommerce-menu-order-quick-edit
 *
 *     This plugin is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     any later version.
 *
 *     This plugin is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *     GNU General Public License for more details.
 */

if ( ! class_exists( 'AGU_Woo_Menu_Order_Quick_Edit' ) ) {
	class AGU_Woo_Menu_Order_Quick_Edit {

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			// Add custom column to WooCommerce products list.
			add_filter( 'manage_edit-product_columns', [ $this, 'custom_product_columns' ] );

			// Display data for custom column.
			add_action( 'manage_product_posts_custom_column', [ $this, 'custom_product_column_data' ], 10, 2 );

			// Make custom column editable in quick edit mode.
			add_action( 'quick_edit_custom_box', [ $this, 'custom_product_quick_edit_fields' ], 10, 2 );

			// Save the custom column value from quick edit mode.
			add_action( 'save_post', [ $this, 'save_custom_product_quick_edit_fields' ], 10, 2 );

			// Enqueue scripts
			add_action( 'admin_enqueue_scripts', [ $this, 'tec_enqueue_quick_edit_population_script' ] );
		}

		/**
		 * Add custom column to WooCommerce products list.
		 *
		 * @param array $columns Array of column names.
		 *
		 * @return array
		 * @since 1.0.0
		 */
		function custom_product_columns( array $columns ): array {
			$columns[ 'menu_order' ] = __( 'Menu order', 'woocommerce' );

			return $columns;
		}


		/**
		 * Display data for custom column.
		 *
		 * @param string $column  The column name.
		 * @param int    $post_id The post ID.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		function custom_product_column_data( string $column, int $post_id ) {
			if ( $column === 'menu_order' ) {
				$menu_order = $this->custom_get_menu_order( $post_id );
				echo $menu_order;
			}
		}


		/**
		 * Make custom column editable in quick edit mode.
		 *
		 * @param string $column_name Name of the column to edit.
		 * @param string $post_type   The post type.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		function custom_product_quick_edit_fields( string $column_name, string $post_type ) {
			if ( 'menu_order' === $column_name && 'product' === $post_type ) {
				?>
				<fieldset class="inline-edit-col-left">
					<div class="inline-edit-col">
						<label>
							<span class="title"><?php _e( 'Menu order', 'woocommerce' ); ?></span>
							<span class="input-text-wrap">
                        <input type="text" name="menu_order" class="menu-order" value="">
                    </span>
						</label>
					</div>
				</fieldset>
				<?php
			}
		}

		/**
		 * Save the custom column value from quick edit mode
		 *
		 * @param int     $post_id The post ID.
		 * @param WP_Post $post    The post object.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		function save_custom_product_quick_edit_fields( int $post_id, WP_Post $post ) {
			global $wpdb;
			if ( 'product' !== $post->post_type ) {
				return;
			}

			if ( isset( $_REQUEST[ 'menu_order' ] ) ) {
				$menu_order = intval( $_REQUEST[ 'menu_order' ] );
				$wpdb->update( $wpdb->posts, [ 'menu_order' => $menu_order ], [ 'ID' => $post_id ] );
			}
		}

		/**
		 * Get menu_order from the database.
		 *
		 * @param int $post_id The post ID for which to get the menu_order.
		 *
		 * @return string|null
		 * @since 1.0.0
		 */
		function custom_get_menu_order( int $post_id ): ?string {
			global $wpdb;

			return $wpdb->get_var(
				$wpdb->prepare(
					"SELECT menu_order FROM $wpdb->posts WHERE ID = %d",
					$post_id
				)
			);
		}

		/**
		 * Enqueue the javascript file.
		 *
		 * @param string $pagehook The current admin page.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		function tec_enqueue_quick_edit_population_script( string $pagehook ) {

			// Bail if we are not on the target pages.
			if ( 'edit.php' != $pagehook ) {
				return;
			}
			if ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === 'product' ) {
				wp_enqueue_script( 'woo-menu_order-quick-edit', plugin_dir_url( __FILE__ ) . 'resources/js/populate.js' );
			}
		}
	}

	new AGU_Woo_Menu_Order_Quick_Edit();
}