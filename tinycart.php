<?php
    /*
    Plugin Name: Tinycart
    Plugin URI: https://tinycart.com/plugins/wordpress/
    Description: Tinycart lets you easily sell from your Wordpress webwebsite
    Version: 1.0.2
    Author: Tinycart
    Author URI: https://tinycart.com/
    License: GPL2
    License URI: http://www.gnu.org/licenses/gpl-2.0.html
    */

    // Ensure the plugin isn't being accessed directly
    defined('ABSPATH') or die('Sorry...');

    // Constants
    define('TINYCART_VERSION', '1.0.2');

    add_action('admin_init', 'tinycart_settings_init');
    add_action('admin_menu', 'add_tinycart_settings_page');
    add_action('media_buttons', 'tinycart_media_button');
    add_action('admin_enqueue_scripts', 'tinycart_admin_media');
    add_action('wp_footer', 'tinycart_website_script');
    add_action('admin_notices', 'tinycart_settings_notice');

    // Adds the Tinycart settings page to the settings menu
    function add_tinycart_settings_page () {
        add_options_page(
            'Tinycart Settings',
            'Tinycart',
            'manage_options',
            'tinycart',
            'tinycart_settings_page'
        );
    }

    // Echos a Tinycart media button
    function tinycart_media_button () {
        if (get_option('tinycart_account_uuid')) {
            echo '<a href="#" id="tinycart-media-button" data-tinycart-media-account="' . get_option('tinycart_account_uuid') . '" class="button">Sell Item</a>';
        }
    }

    // Adds Tinycart admin media
    function tinycart_admin_media () {
        global $hook_suffix;

        if (!in_array($hook_suffix, array('post-new.php', 'post.php', 'settings_page_tinycart'))) {
            return;
        }

        // Include Tinycart admin JS
        wp_register_script(
            'tinycart',
            plugin_dir_url( __FILE__ ) . 'assets/js/common.js',
            array('jquery'),
            TINYCART_VERSION
        );
        wp_enqueue_script('tinycart');

        // Include Tinycart admin CSS
        wp_register_style(
            'tinycart',
            plugin_dir_url( __FILE__ ) . 'assets/css/common.css',
            array(),
            TINYCART_VERSION
        );
        wp_enqueue_style('tinycart');

        // Include Sweetalert JS
        wp_register_script(
            'tinycart_sweetalert',
            plugin_dir_url( __FILE__ ) . 'assets/sweetalert/sweetalert2.min.js',
            array('jquery'),
            TINYCART_VERSION
        );
        wp_enqueue_script('tinycart_sweetalert');

        // Include Sweetalert CSS
        wp_register_style(
            'tinycart_sweetalert',
            plugin_dir_url( __FILE__ ) . 'assets/sweetalert/sweetalert2.min.css',
            array(),
            TINYCART_VERSION
        );
        wp_enqueue_style('tinycart_sweetalert');
    }

    // Tinycart.js
    function tinycart_website_script () {
        if (get_option('tinycart_account_uuid')) {
            echo '<script src="https://s3.amazonaws.com/tinycart.prod.static/tinycart.js" data-tinycart-account="' . get_option('tinycart_account_uuid') . '"></script>';
        }
    }

    // The Tinycart settings page
    function tinycart_settings_init () {
        register_setting('tinycart', 'tinycart_account_uuid');

        add_settings_section(
            'tinycart_section',
            '',
            'tinycart_section_callback',
            'tinycart'
        );

        add_settings_field(
            'tinycart_account_uuid',
            'Tinycart account ID',
            'tinycart_account_uuid_callback',
            'tinycart',
            'tinycart_section'
        );
    }

    // Outputs HTML about the settings section
    function tinycart_section_callback () {
        ?>
        <p>If you <strong>already have a Tinycart account</strong>, visit <a href="https://tinycart.com/manage/plugins/wordpress/" target="_blank">Tinycart.com/manage/plugins/wordpress</a> to get your Tinycart account ID.</p>
        <p>Otherwise, sign up for a Tinycart account at <a href="https://tinycart.com/signup/" target="_blank">Tinycart.com/signup</a> - it takes 30 seconds.</p>
        <?php
    }

    // Outputs the primary HTML of the settings page
    function tinycart_settings_page () {
        if (!current_user_can('manage_options')) {
            return;
        }

        echo '<div class="wrap">';

        // Display any Tinycart messages
        settings_errors('tinycart_messages');

        echo '<h1>Tinycart Settings</h1>';
        echo '<form action="options.php" method="post" class="tinycart-admin-box">';

        settings_fields('tinycart');
        do_settings_sections('tinycart');
        submit_button('Save Settings');

        echo '</form>';
        echo '</div>';
    }

    // Outputs an input for entering / editing the Tinycart account ID
    function tinycart_account_uuid_callback ($args) {
        $value = get_option('tinycart_account_uuid');

        if ($value) {
            ?>
                <input type="text" class="regular-text" name="tinycart_account_uuid" value="<?php esc_html_e($value); ?>" placeholder="Tinycart account ID">
            <?php
        } else {
            ?>
                <input type="text" class="regular-text" name="tinycart_account_uuid" placeholder="Tinycart account ID">
            <?php
        }
    }

    // Outputs a notice, if the Tinycart account ID isn't populated
    function tinycart_settings_notice () {
        global $hook_suffix;

        if ($hook_suffix == 'plugins.php' && !get_option('tinycart_account_uuid')) {
            ?>
                <div id="tincyart-settings-notice" class="notice notice-error">
                    <p>To activate Tinycart, enter your Tinycart account ID at the <a href="<?php menu_page_url('tinycart') ?>">Tinycart settings page</a>.</p>
                </div>
            <?php
        }
    }
?>
