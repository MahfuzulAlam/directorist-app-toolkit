<?php
/**
 * Initialize Rest API JWT Setup
 *
 * @package DirectoristAppToolkit\Controller\Rest_API\Setup
 * @version  2.0.0
 */

namespace DirectoristAppToolkit\Controller\Rest_API\Setup;

defined( 'ABSPATH' ) || exit;


class JWT_Setup {

    public function __construct() {
        add_action( 'jwt_auth_token_before_dispatch', array( $this, 'add_additional_auth_meta' ), 20, 2 );
    }

    /**
     * Adds additional auth meta to the JWT token endpoint
     * 
     * @return array
     * 
     */
    public static function add_additional_auth_meta( $data = [], $user = null ) {
        if ( $user == null ) {
            return $data;
        }

        $data['user_id'] = $user->id;

        return $data;
    }
}
