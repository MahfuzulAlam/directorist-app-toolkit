<?php
/**
 * Initialize Rest_API Version 1
 *
 * @package DirectoristAppToolkit\Controller\Rest_API\Version_1
 * @version  2.0.0
 */

namespace DirectoristAppToolkit\Controller\Rest_API\Version_1;

defined( 'ABSPATH' ) || exit;

use DirectoristAppToolkit\Helper\Traits as HelperTraits;


class Init {

    use HelperTraits\Service_Registrar;

    public function __construct() {
        $this->register_serivces( $this->get_controllers() );
    }

    /**
     * Get the controllers
     * 
     * @return array a list of controllers
     * 
     */
    public static function get_controllers() {
        return [
            Admin_Settings\Init::class,
            Users\Init::class,
        ];
    }
}
