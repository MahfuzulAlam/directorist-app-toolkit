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
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

defined( 'ABSPATH' ) || exit;

/**
 * Admin Settings class.
 */
class Admin_Settings extends Rest_Base {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'admin-settings';

	/**
	 * Settings that can be saved but should not be exposed by the public API.
	 *
	 * @var array
	 */
	protected $excluded_settings = [
		'app_api_key',
		'app_firebase_authorization_key',
		'app_firebase_project_id',
		'admin_email_lists',
		'app_id',
	];

	/**
	 * Legacy Directorist settings included in the public API.
	 *
	 * A string value maps the legacy option key to a different response key.
	 *
	 * @var array
	 */
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

		$settings = array_merge( $settings, $this->legacy_settings );

		foreach ( $this->excluded_settings as $setting_key ) {
			unset( $settings[ $setting_key ] );
		}

		return $settings;
	}

	/**
	 * Register the routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'permission_callback' => '__return_true',
					'args'                => [],
				],
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'create_item' ],
					'permission_callback' => [ $this, 'admin_permissions_check' ],
					'args'                => [],
				],
			]
		);
	}

	/**
	 * Get admin settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$raw_settings = get_option( 'atbdp_option', [] );
		$settings     = [];

		if ( ! is_array( $raw_settings ) ) {
			$raw_settings = [];
		}

		foreach ( $this->get_available_settings() as $setting_key => $rest_key ) {
			$rest_key = is_null( $rest_key ) ? $setting_key : $rest_key;

			if ( Settings_Helper::has_field( $setting_key ) ) {
				$settings[ $rest_key ] = Settings_Helper::get_rest_setting( $setting_key );
			} elseif ( array_key_exists( $setting_key, $raw_settings ) ) {
				$settings[ $rest_key ] = $this->sanitize_legacy_setting_value( $setting_key, $raw_settings[ $setting_key ] );
			} else {
				$settings[ $rest_key ] = null;
			}
		}

		if ( ! empty( $settings['payment_currency'] ) && function_exists( 'atbdp_currency_symbol' ) ) {
			$settings['payment_currency_symbol'] = html_entity_decode( atbdp_currency_symbol( $settings['payment_currency'] ) );
		}

		if ( ! empty( $settings['listing_currency'] ) && function_exists( 'atbdp_currency_symbol' ) ) {
			$settings['listing_currency_symbol'] = html_entity_decode( atbdp_currency_symbol( $settings['listing_currency'] ) );
		}

		return rest_ensure_response( $settings );
	}

	/**
	 * Check if a given request has access to create or update admin settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error
	 */
	public function admin_permissions_check( $request ) {
		$user = wp_get_current_user();

		if ( ! $user || ! in_array( 'administrator', (array) $user->roles, true ) ) {
			return new WP_Error(
				'directorist_app_toolkit_rest_cannot_manage_settings',
				__( 'Sorry, you are not allowed to manage admin settings.', 'directorist-app-toolkit' ),
				[ 'status' => rest_authorization_required_code() ]
			);
		}

		return true;
	}

	/**
	 * Save admin settings into the same option source each field currently resolves from.
	 *
	 * Schema fields are stored either in their tab option or legacy option fallback.
	 * Legacy-only fields continue to write into atbdp_option.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_item( $request ) {
		$payload = $this->get_request_settings_payload( $request );

		if ( empty( $payload ) ) {
			return new WP_Error(
				'directorist_app_toolkit_rest_invalid_payload',
				__( 'No settings payload was provided.', 'directorist-app-toolkit' ),
				[ 'status' => 400 ]
			);
		}

		$known_settings = $this->get_writable_settings();
		$unknown_keys   = array_diff( array_keys( $payload ), array_keys( $known_settings ) );

		if ( ! empty( $unknown_keys ) ) {
			return new WP_Error(
				'directorist_app_toolkit_rest_unknown_settings',
				sprintf(
					/* translators: %s: comma separated list of keys */
					__( 'Unknown settings: %s', 'directorist-app-toolkit' ),
					implode( ', ', $unknown_keys )
				),
				[ 'status' => 400 ]
			);
		}

		$legacy_settings = get_option( 'atbdp_option', [] );
		$legacy_settings = is_array( $legacy_settings ) ? $legacy_settings : [];
		$tabs            = Settings_Helper::get_tabs();
		$tab_option_data = [];
		$updated_tabs    = [];
		$legacy_updated  = false;

		foreach ( $tabs as $tab_key => $tab ) {
			$stored = get_option( $tab['option_key'], [] );
			$tab_option_data[ $tab_key ] = is_array( $stored ) ? $stored : [];
		}

		foreach ( $payload as $setting_key => $value ) {
			if ( Settings_Helper::has_field( $setting_key ) ) {
				$tab_key = $this->get_tab_key_for_field( $setting_key );

				if ( empty( $tab_key ) ) {
					continue;
				}

				$field            = Settings_Helper::get_field( $setting_key );
				$normalized_value = $this->normalize_request_value( $value, $field );
				$validation       = Settings_Helper::validate_tab_values( $tab_key, [ $setting_key => $normalized_value ] );

				if ( ! empty( $validation[ $setting_key ] ) ) {
					return new WP_Error(
						'directorist_app_toolkit_rest_invalid_setting',
						$validation[ $setting_key ],
						[ 'status' => 400 ]
					);
				}

				$sanitized_values = Settings_Helper::sanitize_tab_values(
					$tab_key,
					array_merge(
						Settings_Helper::get_tab_values( $tab_key ),
						[ $setting_key => $normalized_value ]
					)
				);
				$sanitized_value = $sanitized_values[ $setting_key ];

				if ( array_key_exists( $setting_key, $tab_option_data[ $tab_key ] ) || ! array_key_exists( $setting_key, $legacy_settings ) ) {
					$tab_option_data[ $tab_key ][ $setting_key ] = $sanitized_value;
					$updated_tabs[ $tab_key ]                   = true;
				} else {
					$legacy_settings[ $setting_key ] = $sanitized_value;
					$legacy_updated                  = true;
				}

				continue;
			}

			$legacy_settings[ $setting_key ] = $this->sanitize_legacy_setting_value( $setting_key, $value );
			$legacy_updated                  = true;
		}

		foreach ( array_keys( $updated_tabs ) as $tab_key ) {
			$tab = $tabs[ $tab_key ];

			update_option( $tab['option_key'], $tab_option_data[ $tab_key ], false );
		}

		if ( $legacy_updated ) {
			update_option( 'atbdp_option', $legacy_settings, false );
		}

		$response = $this->get_items( new WP_REST_Request( 'GET' ) );
		$response->set_status( 201 );

		return $response;
	}

	/**
	 * Build the set of writable settings, including excluded API-read fields.
	 *
	 * @return array
	 */
	protected function get_writable_settings() {
		$settings = [];

		foreach ( Settings_Helper::get_tabs() as $tab ) {
			if ( empty( $tab['fields'] ) || ! is_array( $tab['fields'] ) ) {
				continue;
			}

			foreach ( $tab['fields'] as $field_key => $field ) {
				if ( Settings_Helper::is_section_field( $field ) ) {
					continue;
				}

				$settings[ $field_key ] = $field;
			}
		}

		foreach ( $this->legacy_settings as $setting_key => $rest_key ) {
			if ( ! isset( $settings[ $setting_key ] ) ) {
				$settings[ $setting_key ] = $rest_key;
			}
		}

		return $settings;
	}

	/**
	 * Resolve the tab key that owns a field.
	 *
	 * @param string $field_key Field key.
	 * @return string
	 */
	protected function get_tab_key_for_field( $field_key ) {
		foreach ( Settings_Helper::get_tabs() as $tab_key => $tab ) {
			if ( isset( $tab['fields'][ $field_key ] ) ) {
				return $tab_key;
			}
		}

		return '';
	}

	/**
	 * Normalize request payload so callers can send either root-level fields or a settings object.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return array
	 */
	protected function get_request_settings_payload( $request ) {
		$payload = $request->get_json_params();

		if ( ! is_array( $payload ) || empty( $payload ) ) {
			$payload = $request->get_body_params();
		}

		if ( isset( $payload['settings'] ) && is_array( $payload['settings'] ) ) {
			$payload = $payload['settings'];
		}

		if ( ! is_array( $payload ) ) {
			return [];
		}

		unset( $payload['context'] );

		return $this->sanitize_payload_keys( $payload );
	}

	/**
	 * Sanitize request payload keys before comparing them to known setting IDs.
	 *
	 * @param array $payload Raw payload.
	 * @return array
	 */
	protected function sanitize_payload_keys( $payload ) {
		$sanitized_payload = [];
		$rest_key_map      = $this->get_rest_key_to_setting_key_map();

		foreach ( $payload as $key => $value ) {
			$sanitized_key = sanitize_key( $key );

			if ( '' === $sanitized_key ) {
				continue;
			}

			if ( isset( $rest_key_map[ $sanitized_key ] ) ) {
				$sanitized_key = $rest_key_map[ $sanitized_key ];
			}

			$sanitized_payload[ $sanitized_key ] = $value;
		}

		return $sanitized_payload;
	}

	/**
	 * Map public REST response keys back to their stored setting keys.
	 *
	 * @return array
	 */
	protected function get_rest_key_to_setting_key_map() {
		$map = [];

		foreach ( $this->legacy_settings as $setting_key => $rest_key ) {
			if ( is_string( $rest_key ) ) {
				$map[ $rest_key ] = $setting_key;
			}
		}

		return $map;
	}

	/**
	 * Normalize request values before validation and sanitization.
	 *
	 * JSON fields accept either a JSON string or decoded array/object payload.
	 *
	 * @param mixed $value Raw request value.
	 * @param array $field Field config.
	 * @return mixed
	 */
	protected function normalize_request_value( $value, $field ) {
		$type = isset( $field['type'] ) ? $field['type'] : 'text';

		if ( 'json' === $type && ( is_array( $value ) || is_object( $value ) ) ) {
			return wp_json_encode( $value );
		}

		return $value;
	}

	/**
	 * Sanitize legacy-only settings that are not part of the app toolkit tab schema.
	 *
	 * @param string $setting_key Setting key.
	 * @param mixed  $value       Raw value.
	 * @return mixed
	 */
	protected function sanitize_legacy_setting_value( $setting_key, $value ) {
		switch ( $setting_key ) {
			case 'admin_email_lists':
				return sanitize_textarea_field( is_scalar( $value ) ? (string) $value : '' );

			case 'enable_multi_directory':
			case 'skip_plan_page':
			case 'plan_direct_purchase':
				return filter_var( $value, FILTER_VALIDATE_BOOLEAN );

			default:
				if ( is_array( $value ) ) {
					return map_deep( $value, 'sanitize_text_field' );
				}

				return sanitize_text_field( is_scalar( $value ) ? (string) $value : '' );
		}
	}
}
