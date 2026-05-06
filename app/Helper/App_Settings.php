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
            'general'    => [
                'label'       => __( 'General', 'directorist-app-toolkit' ),
                'description' => __( 'Manage the core app identity, API credentials, and account access behavior.', 'directorist-app-toolkit' ),
                'option_key'  => 'directorist_app_toolkit_general_settings',
                'fields'      => [
                    'app_name'                             => [
                        'label'       => __( 'App Name', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => get_bloginfo( 'name' ),
                        'placeholder' => __( 'Enter the app name', 'directorist-app-toolkit' ),
                        'description' => __( 'The public name shown in the mobile app header, app metadata, and branded screens.', 'directorist-app-toolkit' ),
                    ],
                    'app_description'                      => [
                        'label'       => __( 'App Description', 'directorist-app-toolkit' ),
                        'type'        => 'textarea',
                        'default'     => get_bloginfo( 'description' ),
                        'placeholder' => __( 'Describe what users can do in the app', 'directorist-app-toolkit' ),
                        'description' => __( 'A short summary of the app experience, used anywhere the app needs descriptive brand copy.', 'directorist-app-toolkit' ),
                    ],
                    'app_id'                               => [
                        'label'       => __( 'App ID', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => '',
                        'placeholder' => __( 'com.example.directory', 'directorist-app-toolkit' ),
                        'description' => __( 'The unique identifier used by the mobile app build or external app services.', 'directorist-app-toolkit' ),
                    ],
                    'app_api_key'                          => [
                        'label'       => __( 'API Key', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => '',
                        'placeholder' => __( 'Enter the app API key', 'directorist-app-toolkit' ),
                        'description' => __( 'The key your app can use to identify or authenticate requests against app-specific services.', 'directorist-app-toolkit' ),
                    ],
                    'app_allow_account_registration'       => [
                        'label'       => __( 'Allow registering a new account from within the app', 'directorist-app-toolkit' ),
                        'type'        => 'checkbox',
                        'default'     => true,
                        'description' => __( 'When enabled, users can create a new account directly from the mobile app sign-up flow.', 'directorist-app-toolkit' ),
                    ],
                    'app_restrict_access_to_logged_in'     => [
                        'label'       => __( 'Restrict App Access to Only Logged In Members', 'directorist-app-toolkit' ),
                        'type'        => 'checkbox',
                        'default'     => false,
                        'description' => __( 'When enabled, app content and features require the user to be authenticated first.', 'directorist-app-toolkit' ),
                    ],
                ],
            ],
            'appearance' => [
                'label'       => __( 'Branding', 'directorist-app-toolkit' ),
                'description' => __( 'Define the colors, logo, icon, and typography used by the mobile app.', 'directorist-app-toolkit' ),
                'option_key'  => 'directorist_app_toolkit_appearance_settings',
                'fields'      => [
                    'app_primary_color'        => [
                        'label'       => __( 'Primary Color', 'directorist-app-toolkit' ),
                        'type'        => 'color',
                        'default'     => '#000000',
                        'description' => __( 'Used for primary buttons, links, active states, and key app accents.', 'directorist-app-toolkit' ),
                    ],
                    'app_secondary_color'      => [
                        'label'       => __( 'Secondary Color', 'directorist-app-toolkit' ),
                        'type'        => 'color',
                        'default'     => '#4f46e5',
                        'description' => __( 'Used for supporting accents, secondary actions, badges, and highlighted UI elements.', 'directorist-app-toolkit' ),
                    ],
                    'app_background_color'     => [
                        'label'       => __( 'Background Color', 'directorist-app-toolkit' ),
                        'type'        => 'color',
                        'default'     => '#ffffff',
                        'description' => __( 'Used as the base background color for app screens and content areas.', 'directorist-app-toolkit' ),
                    ],
                    'app_home_screen_logo'     => [
                        'label'        => __( 'Home Screen Logo', 'directorist-app-toolkit' ),
                        'type'         => 'media',
                        'default'      => '',
                        'placeholder'  => __( 'Paste or select a logo image URL', 'directorist-app-toolkit' ),
                        'button_text'  => __( 'Choose Logo', 'directorist-app-toolkit' ),
                        'remove_text'  => __( 'Remove Logo', 'directorist-app-toolkit' ),
                        'preview_text' => __( 'Home screen logo preview', 'directorist-app-toolkit' ),
                        'description'  => __( 'Displayed in the app home screen header or branded launch area.', 'directorist-app-toolkit' ),
                    ],
                    'app_icon'                 => [
                        'label'        => __( 'App Icon', 'directorist-app-toolkit' ),
                        'type'         => 'media',
                        'default'      => '',
                        'placeholder'  => __( 'Paste or select an app icon image URL', 'directorist-app-toolkit' ),
                        'button_text'  => __( 'Choose Icon', 'directorist-app-toolkit' ),
                        'remove_text'  => __( 'Remove Icon', 'directorist-app-toolkit' ),
                        'preview_text' => __( 'App icon preview', 'directorist-app-toolkit' ),
                        'description'  => __( 'Used anywhere the mobile app needs a compact brand mark, such as splash or launcher-style surfaces.', 'directorist-app-toolkit' ),
                    ],
                    'app_font'                 => [
                        'label'       => __( 'Font', 'directorist-app-toolkit' ),
                        'type'        => 'select',
                        'default'     => 'Inter',
                        'description' => __( 'Select the preferred typeface for app headings, body text, and controls.', 'directorist-app-toolkit' ),
                        'options'     => [
                            'Inter'      => __( 'Inter', 'directorist-app-toolkit' ),
                            'Roboto'     => __( 'Roboto', 'directorist-app-toolkit' ),
                            'Poppins'    => __( 'Poppins', 'directorist-app-toolkit' ),
                            'Montserrat' => __( 'Montserrat', 'directorist-app-toolkit' ),
                            'Open Sans'  => __( 'Open Sans', 'directorist-app-toolkit' ),
                        ],
                    ],
                ],
            ],
            'banner'     => [
                'label'       => __( 'Topbar Settings', 'directorist-app-toolkit' ),
                'description' => __( 'Control the topbar and banner content shown on the app home screen.', 'directorist-app-toolkit' ),
                'option_key'  => 'directorist_app_toolkit_banner_settings',
                'fields'      => [
                    'banner_section'             => [
                        'label'       => __( 'Banner', 'directorist-app-toolkit' ),
                        'type'        => 'section',
                        'description' => __( 'Choose which controls appear in the app banner and define its background color.', 'directorist-app-toolkit' ),
                    ],
                    'app_home_banner_title'      => [
                        'label'       => __( 'Banner Title', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => 'Explore anything',
                        'placeholder' => __( 'Enter the banner title', 'directorist-app-toolkit' ),
                        'description' => __( 'The main headline displayed in the app home screen banner.', 'directorist-app-toolkit' ),
                    ],
                    'app_home_banner_subtitle'   => [
                        'label'       => __( 'Banner Subtitle', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => 'Find the best match of your interest',
                        'placeholder' => __( 'Enter the banner subtitle', 'directorist-app-toolkit' ),
                        'description' => __( 'Supporting text shown below the home screen banner title.', 'directorist-app-toolkit' ),
                    ],
                    'app_home_banner_background_color' => [
                        'label'       => __( 'Background Color', 'directorist-app-toolkit' ),
                        'type'        => 'color',
                        'default'     => '#ffffff',
                        'description' => __( 'Defines the background color used behind the app home banner content.', 'directorist-app-toolkit' ),
                    ],
                    'app_home_banner_thumbnail'  => [
                        'label'        => __( 'Banner Thumbnail', 'directorist-app-toolkit' ),
                        'type'         => 'media',
                        'default'      => $default_banner,
                        'placeholder'  => __( 'Paste or select an image URL', 'directorist-app-toolkit' ),
                        'button_text'  => __( 'Choose Image', 'directorist-app-toolkit' ),
                        'remove_text'  => __( 'Remove Image', 'directorist-app-toolkit' ),
                        'preview_text' => __( 'Banner image preview', 'directorist-app-toolkit' ),
                        'description'  => __( 'The image used as the visual background or thumbnail for the app home banner.', 'directorist-app-toolkit' ),
                    ],
                    'topbar_section'             => [
                        'label'       => __( 'Topbar', 'directorist-app-toolkit' ),
                        'type'        => 'section',
                        'description' => __( 'Choose which controls appear in the app topbar and define its background color.', 'directorist-app-toolkit' ),
                    ],
                    'app_topbar_logo'            => [
                        'label'       => __( 'App Logo', 'directorist-app-toolkit' ),
                        'type'        => 'checkbox',
                        'default'     => true,
                        'description' => __( 'Controls whether the app logo is displayed in the topbar.', 'directorist-app-toolkit' ),
                    ],
                    'app_topbar_user_avatar'     => [
                        'label'       => __( 'User Avatar', 'directorist-app-toolkit' ),
                        'type'        => 'checkbox',
                        'default'     => true,
                        'description' => __( 'Controls whether the signed-in user avatar is displayed in the topbar.', 'directorist-app-toolkit' ),
                    ],
                    'app_topbar_search_form'     => [
                        'label'       => __( 'Search Form', 'directorist-app-toolkit' ),
                        'type'        => 'checkbox',
                        'default'     => true,
                        'description' => __( 'Controls whether the search form is displayed in the topbar.', 'directorist-app-toolkit' ),
                    ],
                    'app_topbar_search_filter'   => [
                        'label'       => __( 'Search Filter', 'directorist-app-toolkit' ),
                        'type'        => 'checkbox',
                        'default'     => true,
                        'description' => __( 'Controls whether the search filter action is displayed in the topbar.', 'directorist-app-toolkit' ),
                    ],
                    'app_topbar_notification_icon' => [
                        'label'       => __( 'Notification Icon', 'directorist-app-toolkit' ),
                        'type'        => 'checkbox',
                        'default'     => true,
                        'description' => __( 'Controls whether the notification icon is displayed in the topbar.', 'directorist-app-toolkit' ),
                    ],
                    'app_topbar_location_selector' => [
                        'label'       => __( 'Location Selector', 'directorist-app-toolkit' ),
                        'type'        => 'checkbox',
                        'default'     => true,
                        'description' => __( 'Controls whether the location selector is displayed in the topbar.', 'directorist-app-toolkit' ),
                    ],
                    'app_topbar_background_color' => [
                        'label'       => __( 'Background Color', 'directorist-app-toolkit' ),
                        'type'        => 'color',
                        'default'     => '#ffffff',
                        'description' => __( 'Defines the background color used behind topbar content.', 'directorist-app-toolkit' ),
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
                        'description' => __( 'The main greeting title shown on the app sign-in screen.', 'directorist-app-toolkit' ),
                    ],
                    'app_signin_greetings_subtitle' => [
                        'label'       => __( 'Sign-in Greetings Subtitle', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => 'Its\' good to see you',
                        'placeholder' => __( 'Enter the sign-in subtitle', 'directorist-app-toolkit' ),
                        'description' => __( 'Supporting greeting text shown below the sign-in title.', 'directorist-app-toolkit' ),
                    ],
                    'app_signup_greetings_title'    => [
                        'label'       => __( 'Sign-up Greetings Title', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => 'Wellcome to Directorist',
                        'placeholder' => __( 'Enter the sign-up title', 'directorist-app-toolkit' ),
                        'description' => __( 'The main greeting title shown on the app account registration screen.', 'directorist-app-toolkit' ),
                    ],
                    'app_signup_greetings_subtitle' => [
                        'label'       => __( 'Sign-up Greetings Subtitle', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => 'Get started in less then 30 seconds',
                        'placeholder' => __( 'Enter the sign-up subtitle', 'directorist-app-toolkit' ),
                        'description' => __( 'Supporting text shown below the sign-up title to guide new users.', 'directorist-app-toolkit' ),
                    ],
                ],
            ],
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
                        'description' => __( 'The Firebase project identifier used to build Firestore notification routes for the app.', 'directorist-app-toolkit' ),
                    ],
                    'app_firebase_authorization_key' => [
                        'label'       => __( 'Authorization Key', 'directorist-app-toolkit' ),
                        'type'        => 'text',
                        'default'     => '',
                        'placeholder' => __( 'Enter the Firebase authorization key', 'directorist-app-toolkit' ),
                        'description' => __( 'The Firebase authorization key used by app notification services when server-side authorization is enabled.', 'directorist-app-toolkit' ),
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
                        'description' => __( 'The support URL opened when app users request help or contact information.', 'directorist-app-toolkit' ),
                    ],
                    'app_terms_conditions_link' => [
                        'label'       => __( 'Terms & Condition Link', 'directorist-app-toolkit' ),
                        'type'        => 'url',
                        'default'     => '',
                        'placeholder' => __( 'https://example.com/terms', 'directorist-app-toolkit' ),
                        'description' => __( 'The URL opened when app users view the terms and conditions.', 'directorist-app-toolkit' ),
                    ],
                    'app_privacy_policy_link' => [
                        'label'       => __( 'Privacy Policy Link', 'directorist-app-toolkit' ),
                        'type'        => 'url',
                        'default'     => '',
                        'placeholder' => __( 'https://example.com/privacy-policy', 'directorist-app-toolkit' ),
                        'description' => __( 'The URL opened when app users view the privacy policy.', 'directorist-app-toolkit' ),
                    ],
                ],
            ],
            'layout'     => [
                'label'       => __( 'Layout', 'directorist-app-toolkit' ),
                'description' => __( 'Manage JSON layout configuration used by key app screens and navigation areas.', 'directorist-app-toolkit' ),
                'option_key'  => 'directorist_app_toolkit_layout_settings',
                'fields'      => [
                    'app_layout_homepage'          => [
                        'label'       => __( 'Homepage', 'directorist-app-toolkit' ),
                        'type'        => 'json',
                        'default'     => '',
                        'placeholder' => __( "{\n  \"sections\": []\n}", 'directorist-app-toolkit' ),
                        'description' => __( 'JSON configuration for the app homepage layout, including sections, ordering, and screen-specific display rules.', 'directorist-app-toolkit' ),
                    ],
                    'app_layout_single_listing'    => [
                        'label'       => __( 'Single Listing', 'directorist-app-toolkit' ),
                        'type'        => 'json',
                        'default'     => '',
                        'placeholder' => __( "{\n  \"sections\": []\n}", 'directorist-app-toolkit' ),
                        'description' => __( 'JSON configuration for the single listing screen, including visible content blocks and their order.', 'directorist-app-toolkit' ),
                    ],
                    'app_layout_bottom_navigation' => [
                        'label'       => __( 'Bottom Navigation', 'directorist-app-toolkit' ),
                        'type'        => 'json',
                        'default'     => '',
                        'placeholder' => __( "{\n  \"items\": []\n}", 'directorist-app-toolkit' ),
                        'description' => __( 'JSON configuration for bottom navigation items, labels, icons, destinations, and ordering.', 'directorist-app-toolkit' ),
                    ],
                    'app_layout_more_page'         => [
                        'label'       => __( 'More Page', 'directorist-app-toolkit' ),
                        'type'        => 'json',
                        'default'     => '',
                        'placeholder' => __( "{\n  \"items\": []\n}", 'directorist-app-toolkit' ),
                        'description' => __( 'JSON configuration for the More page menu, including links, grouped items, and visibility rules.', 'directorist-app-toolkit' ),
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
            if ( self::is_section_field( $field ) ) {
                continue;
            }

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
     * Get a single setting value formatted for REST API responses.
     *
     * @param string $field_key Field key.
     * @param mixed  $default   Optional default for unknown fields.
     *
     * @return mixed
     */
    public static function get_rest_setting( $field_key, $default = null ) {
        $field = self::get_field( $field_key );
        $value = self::get_setting( $field_key, $default );

        if ( empty( $field ) || empty( $field['type'] ) || 'json' !== $field['type'] ) {
            return $value;
        }

        return self::decode_json_value( $value );
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
            if ( self::is_section_field( $field ) ) {
                continue;
            }

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
     * Determine whether a field config is a visual section heading.
     *
     * @param array $field Field config.
     *
     * @return bool
     */
    public static function is_section_field( $field ) {
        return isset( $field['type'] ) && 'section' === $field['type'];
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

        if ( 'checkbox' === $type ) {
            return self::normalize_checkbox_value( $value );
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

            case 'textarea':
                return sanitize_textarea_field( (string) $value );

            case 'json':
                return sanitize_textarea_field( trim( (string) $value ) );

            case 'checkbox':
                return self::normalize_checkbox_value( $value );

            case 'select':
                $value   = sanitize_text_field( (string) $value );
                $options = isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : [];

                return array_key_exists( $value, $options ) ? $value : $default;

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

    /**
     * Decode a JSON field for API output.
     *
     * @param mixed $value Stored JSON text.
     *
     * @return mixed
     */
    protected static function decode_json_value( $value ) {
        if ( is_array( $value ) || is_object( $value ) ) {
            return $value;
        }

        $value = trim( (string) $value );

        if ( '' === $value ) {
            return [];
        }

        $decoded = json_decode( $value, true );

        return JSON_ERROR_NONE === json_last_error() ? $decoded : [];
    }

    /**
     * Normalize stored checkbox values, including legacy enable/disable strings.
     *
     * @param mixed $value Checkbox value.
     *
     * @return bool
     */
    protected static function normalize_checkbox_value( $value ) {
        if ( is_string( $value ) ) {
            $normalized = strtolower( trim( $value ) );

            if ( in_array( $normalized, [ 'enabled', 'enable', 'yes', 'on', '1', 'true' ], true ) ) {
                return true;
            }

            if ( in_array( $normalized, [ 'disabled', 'disable', 'no', 'off', '0', 'false', '' ], true ) ) {
                return false;
            }
        }

        return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
    }
}
