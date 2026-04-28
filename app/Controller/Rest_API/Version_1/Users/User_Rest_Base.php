<?php
/**
 * Rest Listings Controller
 *
 * @package DirectoristAppToolkit\Controller\Rest_API\Version_1
 * @version  2.0.0
 */

namespace DirectoristAppToolkit\Controller\Rest_API\Version_1\Users;

defined( 'ABSPATH' ) || exit;

use DirectoristAppToolkit\Controller\Rest_API\Version_1\Helper\Rest_Base;

use \WP_Error;

/**
 * User Rest Base class.
 */
abstract class User_Rest_Base extends Rest_Base {

	protected $rest_base = 'users';

	/**
	 * Check if a given request has access to read the users.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		$permissions = $this->check_permissions( $request, 'read' );
		if ( is_wp_error( $permissions ) ) {
			return $permissions;
		}

		if ( ! $permissions ) {
			return new WP_Error( 'directorist_app_toolkit_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'directorist-app-toolkit' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check permissions.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @param string          $context Request context.
	 * @return bool|WP_Error
	 */
	protected function check_permissions( $request, $context = 'read' ) {
		// Check permissions for a single user.
		$id = intval( $request['id'] );
		if ( $id ) {
			$user = get_userdata( $id );

			if ( empty( $user ) ) {
				return new WP_Error( 'directorist_app_toolkit_rest_user_invalid', __( 'Resource does not exist.', 'directorist-app-toolkit' ), array( 'status' => 404 ) );
			}

			return $this->rest_check_user_permissions( $context, $user->ID );
		}

		return $this->rest_check_user_permissions( $context );
	}


	/**
	 * Check if a given request has access to create a user.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function create_item_permissions_check( $request ) {
		$permissions = $this->check_permissions( $request, 'create' );
		if ( is_wp_error( $permissions ) ) {
			return $permissions;
		}

		if ( ! $permissions ) {
			return new WP_Error( 'directorist_rest_cannot_create', __( 'Sorry, you are not allowed to create resources.', 'directorist' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check permissions of users on REST API.
	 *
	 * Copied from wc_rest_check_user_permissions
	 *
	 * @param string $context   Request context.
	 * @param int    $object_id Post ID.
	 * @return bool
	 */
	protected function rest_check_user_permissions( $context = 'read', $object_id = 0 ) {
		$contexts = array(
			'read'   => 'edit_user',
			'create' => 'edit_user',
			'edit'   => 'edit_user',
			'delete' => 'edit_user',
		);

		$permission = current_user_can( $contexts[ $context ], $object_id );

		return apply_filters( 'directorist_app_kit_rest_check_permissions', $permission, $context, $object_id, 'user' );
	}

}
