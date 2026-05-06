<?php
  /**
 * Rest Admin Settings Controller
 *
 * @package DirectoristAppToolkit\Controller\Rest_API\Version_1
 * @version  2.0.0
 */

namespace DirectoristAppToolkit\Controller\Rest_API\Version_1\Admin_Settings;

use DirectoristAppToolkit\Controller\Rest_API\Version_1\Helper\Rest_Base;
use DirectoristAppToolkit\Helper\App_Settings as Settings_Helper;

defined( 'ABSPATH' ) || exit;

use \WP_REST_Server;

  /**
 * Admin Settings class.
 */
class Admin_Settings extends Rest_Base {

	protected $rest_base = 'admin-settings';

	protected $legacy_settings = [
		'enable_multi_directory'        => null,
		'radius_search_unit'            => null,
		'admin_email_lists'             => null,
		'privacy_policy'                => null,
		'terms_conditions'              => null,
		'skip_plan_page'                => null,
		'plan_direct_purchase'          => null,
		'payment_currency'              => null,
		'payment_thousand_separator'    => null,
		'payment_decimal_separator'     => null,
		'payment_currency_position'     => null,
		'payment_currency_symbol'       => null,
		'g_currency'                    => 'listing_currency',
		'g_currency_position'           => 'listing_currency_position',
		'listing_currency_symbol'       => null,
	];

	/**
	 * Get all settings that should be returned by the admin settings API.
	 *
	 * This keeps the API aligned with the admin settings schema automatically.
	 *
	 * @return array
	 */
	protected function get_available_settings() {
		$settings = [];

		foreach ( Settings_Helper::get_tabs() as $tab ) {
			if ( empty( $tab['fields'] ) || ! is_array( $tab['fields'] ) ) {
				continue;
			}

			foreach ( $tab['fields'] as $field_key => $field ) {
				if ( Settings_Helper::is_section_field( $field ) ) {
					continue;
				}

				$settings[ $field_key ] = null;
			}
		}

		return array_merge( $settings, $this->legacy_settings );
	}

	  /**
	 * Register the routes
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace, '/'. $this->rest_base, 
			[
				[
					'methods'  => WP_REST_Server::READABLE,
					'callback' => [ $this, 'get_items' ],
					'permission_callback' => '__return_true',
					'args' => [],
				],
			]
			
		);
	}

	  /**
	 * Get Admin Settings
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$_raw_settings = get_option('atbdp_option');
		$settings      = [];

		if ( ! is_array( $_raw_settings ) ) {
			$_raw_settings = [];
		}

		foreach ( $this->get_available_settings() as $setting_key => $rest_key ) {
			$rest_key = is_null( $rest_key ) ? $setting_key : $rest_key;

			if ( Settings_Helper::has_field( $setting_key ) ) {
				$settings[ $rest_key ] = Settings_Helper::get_rest_setting( $setting_key );
			} elseif ( isset( $_raw_settings[ $setting_key ] ) ) {
				$settings[ $rest_key ] = $_raw_settings[ $setting_key ];
			} else {
				$settings[ $rest_key ] = null;
			}
		}

		if ( $settings['payment_currency'] !== '' ) {
			$settings['payment_currency_symbol'] = html_entity_decode( atbdp_currency_symbol( $settings['payment_currency'] ) );
		}

		if ( $settings['listing_currency'] !== '' ) {
			$settings['listing_currency_symbol'] = html_entity_decode( atbdp_currency_symbol( $settings['listing_currency'] ) );
		}
		
		return rest_ensure_response( $settings );
	}
}
