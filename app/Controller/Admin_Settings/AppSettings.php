<?php

namespace DirectoristAppToolkit\Controller\Admin_Settings;

use DirectoristAppToolkit\Helper\App_Settings as Settings_Helper;

defined( 'ABSPATH' ) || exit;

class AppSettings {
    const PAGE_SLUG   = 'directorist-app-settings';
    const AJAX_ACTION = 'directorist_app_toolkit_save_settings_tab';

    /**
     * Hook suffix for the submenu page.
     *
     * @var string
     */
    protected $page_hook = '';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ], 40 );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_ajax_' . self::AJAX_ACTION, [ $this, 'handle_save_tab' ] );
    }

    /**
     * Register the native WordPress submenu page.
     *
     * @return void
     */
    public function register_menu() {
        $this->page_hook = add_submenu_page(
            'edit.php?post_type=at_biz_dir',
            __( 'App Settings', 'directorist-app-toolkit' ),
            __( 'App Settings', 'directorist-app-toolkit' ),
            $this->get_capability(),
            self::PAGE_SLUG,
            [ $this, 'render_page' ]
        );
    }

    /**
     * Render the settings page.
     *
     * @return void
     */
    public function render_page() {
        if ( ! current_user_can( $this->get_capability() ) ) {
            wp_die( esc_html__( 'You do not have permission to access this page.', 'directorist-app-toolkit' ) );
        }

        $tabs       = Settings_Helper::get_tabs();
        $active_tab = $this->get_active_tab( $tabs );
        ?>
        <div class="wrap directorist-app-toolkit-settings">
            <h1><?php esc_html_e( 'App Settings', 'directorist-app-toolkit' ); ?></h1>
            <p class="directorist-app-toolkit-settings__intro">
                <?php esc_html_e( 'Manage app-specific settings in dedicated tabs. Each tab saves independently and falls back to your legacy Directorist values when no new value exists yet.', 'directorist-app-toolkit' ); ?>
            </p>

            <nav class="nav-tab-wrapper directorist-app-toolkit-settings__tabs" aria-label="<?php esc_attr_e( 'App settings tabs', 'directorist-app-toolkit' ); ?>">
                <?php foreach ( $tabs as $tab_key => $tab ) : ?>
                    <a
                        href="#<?php echo esc_attr( $tab_key ); ?>"
                        class="nav-tab<?php echo $tab_key === $active_tab ? ' nav-tab-active' : ''; ?>"
                        data-tab="<?php echo esc_attr( $tab_key ); ?>"
                    >
                        <?php echo esc_html( $tab['label'] ); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="directorist-app-toolkit-settings__panels">
                <?php foreach ( $tabs as $tab_key => $tab ) : ?>
                    <?php $this->render_tab_panel( $tab_key, $tab, $tab_key === $active_tab ); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Enqueue scripts and styles only on the app settings page.
     *
     * @param string $hook_suffix Current admin page hook.
     *
     * @return void
     */
    public function enqueue_assets( $hook_suffix ) {
        if ( $hook_suffix !== $this->page_hook ) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );

        wp_enqueue_style(
            'directorist-app-toolkit-admin-settings',
            DIRECTORIST_APP_TOOLKIT_URL . 'assets/admin/css/app-settings.css',
            [],
            $this->get_asset_version( 'assets/admin/css/app-settings.css' )
        );

        wp_enqueue_script(
            'directorist-app-toolkit-admin-settings',
            DIRECTORIST_APP_TOOLKIT_URL . 'assets/admin/js/app-settings.js',
            [ 'jquery', 'wp-color-picker' ],
            $this->get_asset_version( 'assets/admin/js/app-settings.js' ),
            true
        );

        wp_localize_script(
            'directorist-app-toolkit-admin-settings',
            'directoristAppToolkitSettings',
            [
                'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
                'nonce'     => wp_create_nonce( self::AJAX_ACTION ),
                'activeTab' => $this->get_active_tab( Settings_Helper::get_tabs() ),
                'i18n'      => [
                    'saving'          => __( 'Saving…', 'directorist-app-toolkit' ),
                    'saveChanges'     => __( 'Save Changes', 'directorist-app-toolkit' ),
                    'chooseImage'     => __( 'Choose Image', 'directorist-app-toolkit' ),
                    'useImage'        => __( 'Use Image', 'directorist-app-toolkit' ),
                    'genericError'    => __( 'Something went wrong. Please try again.', 'directorist-app-toolkit' ),
                    'removedPreview'  => __( 'Preview removed', 'directorist-app-toolkit' ),
                ],
            ]
        );
    }

    /**
     * AJAX callback to save one tab per request.
     *
     * @return void
     */
    public function handle_save_tab() {
        if ( ! current_user_can( $this->get_capability() ) ) {
            wp_send_json_error(
                [ 'message' => __( 'You do not have permission to update app settings.', 'directorist-app-toolkit' ) ],
                403
            );
        }

        if ( ! check_ajax_referer( self::AJAX_ACTION, 'nonce', false ) ) {
            wp_send_json_error(
                [ 'message' => __( 'Security check failed. Please refresh the page and try again.', 'directorist-app-toolkit' ) ],
                403
            );
        }

        $tab_key = isset( $_POST['tab'] ) ? sanitize_key( wp_unslash( $_POST['tab'] ) ) : '';
        $tab     = Settings_Helper::get_tab( $tab_key );

        if ( empty( $tab ) ) {
            wp_send_json_error(
                [ 'message' => __( 'Invalid settings tab.', 'directorist-app-toolkit' ) ],
                400
            );
        }

        $raw_settings = isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ? $_POST['settings'] : [];
        $settings     = Settings_Helper::sanitize_tab_values( $tab_key, $raw_settings );

        update_option( $tab['option_key'], $settings, false );

        wp_send_json_success(
            [
                'message' => sprintf(
                    /* translators: %s: tab label */
                    __( '%s settings saved successfully.', 'directorist-app-toolkit' ),
                    $tab['label']
                ),
                'tab'     => $tab_key,
                'values'  => Settings_Helper::get_tab_values( $tab_key ),
            ]
        );
    }

    /**
     * Render a single tab panel.
     *
     * @param string $tab_key   Tab key.
     * @param array  $tab       Tab config.
     * @param bool   $is_active Active state.
     *
     * @return void
     */
    protected function render_tab_panel( $tab_key, $tab, $is_active ) {
        $values = Settings_Helper::get_tab_values( $tab_key );
        ?>
        <section
            class="directorist-app-toolkit-tab-panel<?php echo $is_active ? ' is-active' : ''; ?>"
            data-tab-panel="<?php echo esc_attr( $tab_key ); ?>"
        >
            <form class="directorist-app-toolkit-settings-form" data-tab="<?php echo esc_attr( $tab_key ); ?>">
                <input type="hidden" name="action" value="<?php echo esc_attr( self::AJAX_ACTION ); ?>">
                <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( self::AJAX_ACTION ) ); ?>">
                <input type="hidden" name="tab" value="<?php echo esc_attr( $tab_key ); ?>">

                <div class="directorist-app-toolkit-card">
                    <div class="directorist-app-toolkit-card__header">
                        <div>
                            <h2><?php echo esc_html( $tab['label'] ); ?></h2>
                            <?php if ( ! empty( $tab['description'] ) ) : ?>
                                <p><?php echo esc_html( $tab['description'] ); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <table class="form-table" role="presentation">
                        <tbody>
                            <?php foreach ( $tab['fields'] as $field_key => $field ) : ?>
                                <?php $this->render_field_row( $field_key, $field, isset( $values[ $field_key ] ) ? $values[ $field_key ] : '' ); ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="directorist-app-toolkit-card__footer">
                        <div class="directorist-app-toolkit-feedback" aria-live="polite"></div>
                        <div class="directorist-app-toolkit-actions">
                            <span class="spinner"></span>
                            <button type="submit" class="button button-primary">
                                <?php esc_html_e( 'Save Changes', 'directorist-app-toolkit' ); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <?php
    }

    /**
     * Render a field row.
     *
     * @param string $field_key Field key.
     * @param array  $field     Field config.
     * @param mixed  $value     Field value.
     *
     * @return void
     */
    protected function render_field_row( $field_key, $field, $value ) {
        $field_id = 'directorist-app-toolkit-' . $field_key;
        $type     = isset( $field['type'] ) ? $field['type'] : 'text';
        ?>
        <tr>
            <th scope="row">
                <label for="<?php echo esc_attr( $field_id ); ?>">
                    <?php echo esc_html( $field['label'] ); ?>
                </label>
            </th>
            <td>
                <?php
                switch ( $type ) {
                    case 'color':
                        $this->render_color_field( $field_key, $field_id, $field, $value );
                        break;

                    case 'media':
                        $this->render_media_field( $field_key, $field_id, $field, $value );
                        break;

                    case 'url':
                        $this->render_text_field( $field_key, $field_id, $field, $value, 'url' );
                        break;

                    default:
                        $this->render_text_field( $field_key, $field_id, $field, $value, 'text' );
                        break;
                }

                if ( ! empty( $field['description'] ) ) {
                    printf( '<p class="description">%s</p>', esc_html( $field['description'] ) );
                }
                ?>
            </td>
        </tr>
        <?php
    }

    /**
     * Render a text or URL field.
     *
     * @param string $field_key Field key.
     * @param string $field_id  Field ID.
     * @param array  $field     Field config.
     * @param mixed  $value     Field value.
     * @param string $type      HTML input type.
     *
     * @return void
     */
    protected function render_text_field( $field_key, $field_id, $field, $value, $type = 'text' ) {
        printf(
            '<input type="%1$s" class="regular-text" id="%2$s" name="settings[%3$s]" value="%4$s" placeholder="%5$s">',
            esc_attr( $type ),
            esc_attr( $field_id ),
            esc_attr( $field_key ),
            esc_attr( (string) $value ),
            esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' )
        );
    }

    /**
     * Render a color picker field.
     *
     * @param string $field_key Field key.
     * @param string $field_id  Field ID.
     * @param array  $field     Field config.
     * @param mixed  $value     Field value.
     *
     * @return void
     */
    protected function render_color_field( $field_key, $field_id, $field, $value ) {
        printf(
            '<input type="text" class="directorist-app-toolkit-color-picker" id="%1$s" name="settings[%2$s]" value="%3$s" data-default-color="%4$s">',
            esc_attr( $field_id ),
            esc_attr( $field_key ),
            esc_attr( (string) $value ),
            esc_attr( isset( $field['default'] ) ? $field['default'] : '' )
        );
    }

    /**
     * Render the media field.
     *
     * @param string $field_key Field key.
     * @param string $field_id  Field ID.
     * @param array  $field     Field config.
     * @param mixed  $value     Field value.
     *
     * @return void
     */
    protected function render_media_field( $field_key, $field_id, $field, $value ) {
        $preview_url = esc_url( (string) $value );
        ?>
        <div class="directorist-app-toolkit-media-field">
            <div class="directorist-app-toolkit-media-field__controls">
                <input
                    type="url"
                    class="regular-text directorist-app-toolkit-media-url"
                    id="<?php echo esc_attr( $field_id ); ?>"
                    name="settings[<?php echo esc_attr( $field_key ); ?>]"
                    value="<?php echo esc_attr( (string) $value ); ?>"
                    placeholder="<?php echo esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' ); ?>"
                >
                <button
                    type="button"
                    class="button directorist-app-toolkit-media-select"
                    data-title="<?php esc_attr_e( 'Choose Image', 'directorist-app-toolkit' ); ?>"
                    data-button-text="<?php esc_attr_e( 'Use Image', 'directorist-app-toolkit' ); ?>"
                >
                    <?php echo esc_html( isset( $field['button_text'] ) ? $field['button_text'] : __( 'Choose Image', 'directorist-app-toolkit' ) ); ?>
                </button>
                <button type="button" class="button-link-delete directorist-app-toolkit-media-remove">
                    <?php echo esc_html( isset( $field['remove_text'] ) ? $field['remove_text'] : __( 'Remove Image', 'directorist-app-toolkit' ) ); ?>
                </button>
            </div>

            <div class="directorist-app-toolkit-media-preview<?php echo $preview_url ? '' : ' is-empty'; ?>">
                <img
                    src="<?php echo $preview_url ? $preview_url : ''; ?>"
                    alt="<?php echo esc_attr( isset( $field['preview_text'] ) ? $field['preview_text'] : __( 'Selected image preview', 'directorist-app-toolkit' ) ); ?>"
                >
                <span class="directorist-app-toolkit-media-preview__empty">
                    <?php esc_html_e( 'No image selected', 'directorist-app-toolkit' ); ?>
                </span>
            </div>
        </div>
        <?php
    }

    /**
     * Resolve the active tab.
     *
     * @param array $tabs Registered tabs.
     *
     * @return string
     */
    protected function get_active_tab( $tabs ) {
        $requested_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';

        if ( $requested_tab && isset( $tabs[ $requested_tab ] ) ) {
            return $requested_tab;
        }

        return (string) key( $tabs );
    }

    /**
     * Get the page capability.
     *
     * @return string
     */
    protected function get_capability() {
        return (string) apply_filters( 'directorist_app_toolkit_settings_capability', 'manage_options' );
    }

    /**
     * Get a filemtime-based asset version.
     *
     * @param string $relative_path Relative path from the plugin root.
     *
     * @return string
     */
    protected function get_asset_version( $relative_path ) {
        $file_path = trailingslashit( DIRECTORIST_APP_TOOLKIT_PATH ) . ltrim( $relative_path, '/' );

        if ( file_exists( $file_path ) ) {
            return (string) filemtime( $file_path );
        }

        return DIRECTORIST_APP_TOOLKIT_VERSION;
    }

}
