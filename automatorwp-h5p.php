<?php
/**
 * Plugin Name:           AutomatorWP - H5P integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-h5p-integration/
 * Description:           Connect AutomatorWP with H5P.
 * Version:               1.0.2
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-h5p-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.9
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\H5P
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_H5P_Integration {

    /**
     * @var         AutomatorWP_H5P_Integration $instance The one true AutomatorWP_H5P_Integration
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_H5P_Integration self::$instance The one true AutomatorWP_H5P_Integration
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_H5P_Integration();
            
            if( ! self::$instance->pro_installed() ) {

                self::$instance->constants();
                self::$instance->includes();
                self::$instance->load_textdomain();

            }

            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'AUTOMATORWP_H5P_VER', '1.0.2' );

        // Plugin file
        define( 'AUTOMATORWP_H5P_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_H5P_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_H5P_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() ) {

            // Triggers
            require_once AUTOMATORWP_H5P_DIR . 'includes/triggers/complete-content.php';

            // Includes
            require_once AUTOMATORWP_H5P_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_H5P_DIR . 'includes/functions.php';

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {

        add_action( 'automatorwp_init', array( $this, 'register_integration' ) );

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * Registers this integration
     *
     * @since 1.0.0
     */
    function register_integration() {

        automatorwp_register_integration( 'h5p', array(
            'label' => 'H5P',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/h5p.svg',
        ) );

    }

    /**
     * Plugin admin notices.
     *
     * @since  1.0.0
     */
    public function admin_notices() {

        if ( ! $this->meets_requirements() && ! defined( 'AUTOMATORWP_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php printf(
                        __( 'AutomatorWP - H5P requires %s and %s in order to work. Please install and activate them.', 'automatorwp-h5p-integration' ),
                        '<a href="https://wordpress.org/plugins/automatorwp/" target="_blank">AutomatorWP</a>',
                        '<a href="https://wordpress.org/plugins/h5p/" target="_blank">H5P</a>'
                    ); ?>
                </p>
            </div>

            <?php define( 'AUTOMATORWP_ADMIN_NOTICES', true ); ?>

        <?php elseif ( $this->pro_installed() && ! defined( 'AUTOMATORWP_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php echo __( 'You can uninstall AutomatorWP - H5P Integration because you already have the pro version installed and includes all the features of the free version.', 'automatorwp-h5p-integration' ); ?>
                </p>
            </div>

            <?php define( 'AUTOMATORWP_ADMIN_NOTICES', true ); ?>

        <?php endif;

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( ! class_exists( 'AutomatorWP' ) ) {
            return false;
        }

        if ( ! class_exists( 'H5P_Plugin' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Check if the pro version of this integration is installed
     *
     * @since  1.0.0
     *
     * @return bool True if pro version installed
     */
    private function pro_installed() {

        if ( ! class_exists( 'AutomatorWP_H5P' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Internationalization
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function load_textdomain() {

        // Set filter for language directory
        $lang_dir = AUTOMATORWP_H5P_DIR . '/languages/';
        $lang_dir = apply_filters( 'automatorwp_h5p_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'automatorwp-h5p-integration' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'automatorwp-h5p-integration', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/automatorwp-h5p-integration/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/automatorwp-h5p-integration/ folder
            load_textdomain( 'automatorwp-h5p-integration', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/automatorwp-h5p-integration/languages/ folder
            load_textdomain( 'automatorwp-h5p-integration', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'automatorwp-h5p-integration', false, $lang_dir );
        }

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_H5P_Integration instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_H5P_Integration The one true AutomatorWP_H5P_Integration
 */
function AutomatorWP_H5P_Integration() {
    return AutomatorWP_H5P_Integration::instance();
}
add_action( 'plugins_loaded', 'AutomatorWP_H5P_Integration' );
