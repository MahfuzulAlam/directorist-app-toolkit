<?php

namespace DirectoristAppToolkit\Helper;

defined( 'ABSPATH' ) || exit;

class App_Settings {
    /**
     * Get all tab definitions.
     *
     * @return array
     */
    public static function get_tabs() {
        $default_banner = defined( 'DIRECTORIST_ASSETS' ) ? DIRECTORIST_ASSETS . 'images/grid.jpg' : '';

        $tabs = [
            'firebase'   => [
                'label'       => __( 'Firebase', 'directorist-app-toolkit' ),
                'description' => __( 'Configure the Firebase credentials used by the app toolkit.', 'directorist-app-toolkit' ),
                'option_key'  => 'directorist_app_toolkit_firebase_settings',
                'fields'      => [
                    'app_firebase_project_id'        => [
                        'label'       => __( 'Project ID', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => '',
                        'placeholder' => __( 'Enter the Firebase project ID', 'directorist-app-toolkit' ),
                    ],
                    'app_firebase_authorization_key' => [
                        'label'       => __( 'Authorization Key', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => '',
                        'placeholder' => __( 'Enter the Firebase authorization key', 'directorist-app-toolkit' ),
                    ],
                ],
            ],
            'appearance' => [
                'label'       => __( 'Appearance', 'directorist-app-toolkit' ),
                'description' => __( 'Define the primary visual settings used by the mobile app.', 'directorist-app-toolkit' ),
                'option_key'  => 'directorist_app_toolkit_appearance_settings',
                'fields'      => [
                    'app_primary_color' => [
                        'label'   => __( 'Primary Color', 'directorist-app-toolkit' ),
                        'type'    => 'color',
                        'default' => '#000000',
                    ],
                ],
            ],
            'banner'     => [
                'label'       => __( 'Banner', 'directorist-app-toolkit' ),
                'description' => __( 'Control the banner content shown on the app home screen.', 'directorist-app-toolkit' ),
                'option_key'  => 'directorist_app_toolkit_banner_settings',
                'fields'      => [
                    'app_home_banner_title'      => [
                        'label'       => __( 'Banner Title', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => 'Explore anything',
                        'placeholder' => __( 'Enter the banner title', 'directorist-app-toolkit' ),
                    ],
                    'app_home_banner_subtitle'   => [
                        'label'       => __( 'Banner Subtitle', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => 'Find the best match of your interest',
                        'placeholder' => __( 'Enter the banner subtitle', 'directorist-app-toolkit' ),
                    ],
                    'app_home_banner_thumbnail'  => [
                        'label'        => __( 'Banner Thumbnail', 'directorist-app-toolkit' ),
                        'type'         => 'media',
                        'default'      => $default_banner,
                        'placeholder'  => __( 'Paste or select an image URL', 'directorist-app-toolkit' ),
                        'button_text'  => __( 'Choose Image', 'directorist-app-toolkit' ),
                        'remove_text'  => __( 'Remove Image', 'directorist-app-toolkit' ),
                        'preview_text' => __( 'Banner image preview', 'directorist-app-toolkit' ),
                    ],
                ],
            ],
            'labels'     => [
                'label'       => __( 'Labels', 'directorist-app-toolkit' ),
                'description' => __( 'Customize greetings and labels used on app authentication screens.', 'directorist-app-toolkit' ),
                'option_key'  => 'directorist_app_toolkit_label_settings',
                'fields'      => [
                    'app_signin_greetings_title'    => [
                        'label'       => __( 'Sign-in Greetings Title', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => 'Hi There',
                        'placeholder' => __( 'Enter the sign-in title', 'directorist-app-toolkit' ),
                    ],
                    'app_signin_greetings_subtitle' => [
                        'label'       => __( 'Sign-in Greetings Subtitle', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => 'Its\' good to see you',
                        'placeholder' => __( 'Enter the sign-in subtitle', 'directorist-app-toolkit' ),
                    ],
                    'app_signup_greetings_title'    => [
                        'label'       => __( 'Sign-up Greetings Title', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => 'Wellcome to Directorist',
                        'placeholder' => __( 'Enter the sign-up title', 'directorist-app-toolkit' ),
                    ],
                    'app_signup_greetings_subtitle' => [
                        'label'       => __( 'Sign-up Greetings Subtitle', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => 'Get started in less then 30 seconds',
                        'placeholder' => __( 'Enter the sign-up subtitle', 'directorist-app-toolkit' ),
                    ],
                ],
            ],
            'other'      => [
                'label'       => __( 'Other', 'directorist-app-toolkit' ),
                'description' => __( 'Manage remaining app-specific links and utility settings.', 'directorist-app-toolkit' ),
                'option_key'  => 'directorist_app_toolkit_other_settings',
                'fields'      => [
                    'app_support_link' => [
                        'label'       => __( 'Support Link', 'directorist-app-toolkit' ),
                        'type'        => 'url',
                        'default'     => home_url( '/' ),
                        'placeholder' => __( 'https://example.com/support', 'directorist-app-toolkit' ),
                    ],
                ],
            ],
        ];

        return apply_filters( 'directorist_app_toolkit_settings_tabs', $tabs );
    }

    /**
     * Get a single tab definition.
     *
     * @param string $tab_key Tab key.
     *
     * @return array|null
     */
    public static function get_tab( $tab_key ) {
        $tabs = self::get_tabs();

        return isset( $tabs[ $tab_key ] ) ? $tabs[ $tab_key ] : null;
    }

    /**
     * Get the field config for a setting key.
     *
     * @param string $field_key Field key.
     *
     * @return array|null
     */
    public static function get_field( $field_key ) {
        foreach ( self::get_tabs() as $tab ) {
            if ( isset( $tab['fields'][ $field_key ] ) ) {
                return $tab['fields'][ $field_key ];
            }
        }

        return null;
    }

    /**
     * Check if a field belongs to the app toolkit settings.
     *
     * @param string $field_key Field key.
     *
     * @return bool
     */
    public static function has_field( $field_key ) {
        return null !== self::get_field( $field_key );
    }

    /**
     * Get all settings for a single tab.
     *
     * @param string $tab_key Tab key.
     *
     * @return array
     */
    public static function get_tab_values( $tab_key ) {
        $tab = self::get_tab( $tab_key );

        if ( empty( $tab ) ) {
            return [];
        }

        $stored_values = get_option( $tab['option_key'], null );
        $stored_values = is_array( $stored_values ) ? $stored_values : null;
        $legacy_values = self::get_legacy_settings();
        $values        = [];

        foreach ( $tab['fields'] as $field_key => $field ) {
            if ( is_array( $stored_values ) && array_key_exists( $field_key, $stored_values ) ) {
                $values[ $field_key ] = self::prepare_field_value( $stored_values[ $field_key ], $field );
                continue;
            }

            if ( array_key_exists( $field_key, $legacy_values ) ) {
                $values[ $field_key ] = self::prepare_field_value( $legacy_values[ $field_key ], $field );
                continue;
            }

            $values[ $field_key ] = isset( $field['default'] ) ? $field['default'] : '';
        }

        return $values;
    }

    /**
     * Get a single setting value with fallback support.
     *
     * @param string $field_key Field key.
     * @param mixed  $default   Optional default for unknown fields.
     *
     * @return mixed
     */
    public static function get_setting( $field_key, $default = null ) {
        foreach ( self::get_tabs() as $tab_key => $tab ) {
            if ( ! isset( $tab['fields'][ $field_key ] ) ) {
                continue;
            }

            $values = self::get_tab_values( $tab_key );

            if ( array_key_exists( $field_key, $values ) ) {
                return $values[ $field_key ];
            }
        }

        return $default;
    }

    /**
     * Sanitize a tab payload before saving it into its own option key.
     *
     * @param string $tab_key     Tab key.
     * @param array  $raw_values  Raw input values.
     *
     * @return array
     */
    public static function sanitize_tab_values( $tab_key, $raw_values ) {
        $tab = self::get_tab( $tab_key );

        if ( empty( $tab ) || ! is_array( $raw_values ) ) {
            return [];
        }

        $sanitized = [];

        foreach ( $tab['fields'] as $field_key => $field ) {
            $value = array_key_exists( $field_key, $raw_values ) ? $raw_values[ $field_key ] : '';

            if ( is_string( $value ) ) {
                $value = wp_unslash( $value );
            }

            $sanitized[ $field_key ] = self::sanitize_field_value( $value, $field );
        }

        return $sanitized;
    }

    /**
     * Get the legacy Directorist settings array.
     *
     * @return array
     */
    protected static function get_legacy_settings() {
        $settings = get_option( 'atbdp_option', [] );

        return is_array( $settings ) ? $settings : [];
    }

    /**
     * Normalize field values after reading from new or legacy storage.
     *
     * @param mixed $value Field value.
     * @param array $field Field config.
     *
     * @return mixed
     */
    protected static function prepare_field_value( $value, $field ) {
        $type = isset( $field['type'] ) ? $field['type'] : 'text';

        if ( 'media' === $type && is_numeric( $value ) ) {
            $attachment_url = wp_get_attachment_url( (int) $value );

            return $attachment_url ? $attachment_url : '';
        }

        return $value;
    }

    /**
     * Sanitize an individual field value based on its type.
     *
     * @param mixed $value Field value.
     * @param array $field Field config.
     *
     * @return mixed
     */
    protected static function sanitize_field_value( $value, $field ) {
        $type    = isset( $field['type'] ) ? $field['type'] : 'text';
        $default = isset( $field['default'] ) ? $field['default'] : '';

        switch ( $type ) {
            case 'url':
                return esc_url_raw( trim( (string) $value ) );

            case 'color':
                $color = sanitize_hex_color( (string) $value );

                return $color ? $color : $default;

            case 'media':
                if ( is_numeric( $value ) ) {
                    $attachment_url = wp_get_attachment_url( (int) $value );

                    return $attachment_url ? esc_url_raw( $attachment_url ) : '';
                }

                return esc_url_raw( trim( (string) $value ) );

            default:
                return sanitize_text_field( (string) $value );
        }
    }
}
