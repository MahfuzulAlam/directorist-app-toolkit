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
