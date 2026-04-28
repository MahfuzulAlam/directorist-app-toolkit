<?php
/**
 * Rest User Meta Controller
 *
 * @package DirectoristAppToolkit\Controller\Rest_API\Version_1
 * @version  2.0.0
 */

namespace DirectoristAppToolkit\Controller\Rest_API\Version_1\Users;

defined( 'ABSPATH' ) || exit;

use \WP_REST_Server;
use \WP_Error;

/**
 * Admin Settings class.
 */
class User_Meta extends User_Rest_Base {

	/**
	 * Register the routes
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace, '/'. $this->rest_base . '/(?P<id>[\d]+)/metas', [
				'args' => [
					'id' => [
						'description' => __( 'Unique identifier for the resource.', 'directorist-app-toolkit' ),
						'type'        => 'integer',
					],
				],
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
				],
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'create_item' ],
					'permission_callback' => [ $this, 'create_item_permissions_check' ],
				],
			],
		);
	}

	/**
	 * Get user metas
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$status = [];
		$status['success'] = false;
		$status['message'] = '';

		$user_id = ( isset( $request['id'] ) ) ? $request['id'] : 0;
		$metas = ( isset( $request['metas'] ) ) ? $request['metas'] : '';

		$user = get_userdata( $user_id );

		if ( false == $user ) {
			$status['message'] = 'No user found';
			return $status;
		}

		if ( empty( $metas ) ) {
			$user_metas = get_user_meta( $user_id );
			return $user_metas;
		}

		$metas = explode( ',', $metas );

		if ( ! is_array( $metas ) || empty( $metas ) ) {
			$user_metas = get_user_meta( $user_id );
			return $user_metas;
		}

		$meta_datas = [];

		foreach ( $metas as $meta_key ) {
			$user_meta = get_user_meta( $user_id, $meta_key, true );
			$meta_datas[ $meta_key ] = $user_meta;
		}

		return $meta_datas;
	}


	/**
	 * Update user meta
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {
		$status = [];
		$status['success'] = false;
		$status['message'] = '';

		$user_id = ( isset( $request['id'] ) ) ? $request['id'] : 0;
		$user_metas = ( isset( $request['metas'] ) && is_array( $request['metas'] ) ) ? $request['metas'] : [];

		$user = get_userdata( $user_id );

		if ( false == $user ) {
			$status['message'] = 'No user found';
			return $status;
		}

		if ( empty( $user_metas ) ) {
			$status['message'] = 'Nothing to update';
			return $status;
		}

		foreach ( $user_metas as $key => $value ) {
			update_user_meta(  $user_id, $key, $value );
		}

		$status['success'] = true;
		$status['message'] = 'Data updated successfully';
		$status['metas'] = $user_metas;

		return $status;
	}
	
}
