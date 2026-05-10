<?php

use DirectoristAppToolkit\Helper\App_Settings;

if ( ! function_exists( 'directorist_app_toolkit_get_setting' ) ) {
    /**
     * Read a single app setting with new-option and legacy fallback support.
     *
     * @param string $field_key Setting key.
     * @param mixed  $default   Optional fallback when the field is unknown.
     *
     * @return mixed
     */
    function directorist_app_toolkit_get_setting( $field_key, $default = null ) {
        return App_Settings::get_setting( $field_key, $default );
    }
}

if ( ! function_exists( 'directorist_app_toolkit_get_tab_settings' ) ) {
    /**
     * Read all settings for a tab with new-option and legacy fallback support.
     *
     * @param string $tab_key Tab key.
     *
     * @return array
     */
    function directorist_app_toolkit_get_tab_settings( $tab_key ) {
        return App_Settings::get_tab_values( $tab_key );
    }
}


add_filter( 'directorist_rest_prepare_user', function( $response ) {
    // Support both WP_REST_Response object and associative array
    if ( is_object( $response ) && method_exists( $response, 'get_data' ) ) {
        $data = $response->get_data();
    } else {
        $data = is_array( $response ) ? $response : [];
    }

    $user_id = isset( $data['id'] ) && ! empty( $data['id'] ) ? $data['id'] : 0;

    // Add "is_admin": true|false by checking user capabilities
    $is_admin = false;
    if ( $user_id ) {
        $user = get_userdata( $user_id );
        if ( $user && is_a( $user, 'WP_User' ) ) {
            $is_admin = user_can( $user, 'manage_options' );
        }
    }

    $data['can_manage'] = $is_admin;

    // Set data back if WP_REST_Response object, else just return array
    if ( is_object( $response ) && method_exists( $response, 'set_data' ) ) {
        $response->set_data( $data );
        return $response;
    }

    return $data;
}, 10 );


add_filter( 'directorist_rest_prepare_user', function( $response ) {
    // Support both WP_REST_Response object and associative array
    if ( is_object( $response ) && method_exists( $response, 'get_data' ) ) {
        $data = $response->get_data();
    } else {
        $data = is_array( $response ) ? $response : [];
    }

    $user_type = isset( $data['user_type'] ) && ! empty( $data['user_type'] ) ? $data['user_type'] : '';
    $can_manage = isset( $data['can_manage'] ) && ! empty( $data['can_manage'] ) ? $data['can_manage'] : false;

    // Add "can_edit": true|false by checking user capabilities
    $can_edit = false;

    if( $can_manage || $user_type == 'author' )
    {
        $can_edit = true;
    }

    $data['can_edit'] = $can_edit;

    // Set data back if WP_REST_Response object, else just return array
    if ( is_object( $response ) && method_exists( $response, 'set_data' ) ) {
        $response->set_data( $data );
        return $response;
    }

    return $data;
}, 11 );