<?php

/**
 * class EvCwPluginSettings
 * 
 * custom option and settings
 */
class EvCwPluginSettings
{

    public function __construct()
    {
        add_action('admin_init', array($this, 'evcw_settings_init'));
        /**
         * Register our evcw_options_page to the admin_menu action hook.
         */
        add_action('admin_menu', array($this, 'evcw_options_page'));

        add_action('admin_enqueue_scripts', array($this, 'enqueue_custom_settings_assets'));
    }

    function evcw_settings_init()
    {

        $options = get_option('evcw_config_options');

        // Register a new setting for "evcw" page.
        register_setting('ev_config', 'evcw_config_options');

        // Register a new section in the "evcw" page.
        add_settings_section(
            'evcw_config_section',
            __('Crossword AI Config', 'evcw'),
            array($this, 'evcw_config_section_callback'),
            'ev_config'
        );


        // Todo - implement a single field callback with a switch statement or conditional template
        // Register a new field in the "evcw_config_section" section, inside the "evcw" page.
        add_settings_field(
            'evcw_ai_provider', // As of WP 4.6 this value is used only internally.
            // Use $args' label_for to populate the id inside the callback.
            __('AI Provider', 'evcw'),
            array($this, 'evcw_ai_provider_cb'),
            'ev_config',
            'evcw_config_section',
            array(
                'label_for'         => 'evcw_ai_provider',
                'class'             => 'evcw_row',
                'evcw_custom_data' => 'custom',
            )
        );


        $css_class = $options['evcw_ai_provider'] == 'local_model' ? '' : 'hide';

        add_settings_field(
            'evcw_ai_local_provider_url', // As of WP 4.6 this value is used only internally.
            // Use $args' label_for to populate the id inside the callback.
            __('Local Provider URL', 'evcw'),
            array($this, 'evcw_ai_local_provider_url_cb'),
            'ev_config',
            'evcw_config_section',
            array(
                'label_for'         => 'evcw_ai_local_provider_url',
                'class'             => 'evcw_row evcw_ai_local_provider_url_wrap ' . $css_class,
                'evcw_custom_data' => 'custom',
            )
        );


        add_settings_field(
            'evcw_ai_provider_api_key', // As of WP 4.6 this value is used only internally.
            // Use $args' label_for to populate the id inside the callback.
            __('AI Provider API KEY', 'evcw'),
            array($this, 'evcw_ai_provider_api_key_cb'),
            'ev_config',
            'evcw_config_section',
            array(
                'label_for'         => 'evcw_ai_provider_api_key',
                'class'             => 'evcw_row',
                'evcw_custom_data' => 'custom',
            )
        );
    }

    /**
     * Custom option and settings:
     *  - callback functions
     */


    /**
     * Developers section callback function.
     *
     * @param array $args  The settings array, defining title, id, callback.
     */
    function evcw_config_section_callback($args)
    {
?>
        <p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('Choose your AI configuration to generate the word list for your crosswords', 'ev-crosswords'); ?></p>
    <?php
    }

    /**
     * Pill field callbakc function.
     *
     * WordPress has magic interaction with the following keys: label_for, class.
     * - the "label_for" key value is used for the "for" attribute of the <label>.
     * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
     * Note: you can add custom key value pairs to be used inside your callbacks.
     *
     * @param array $args
     */
    function evcw_ai_provider_cb($args)
    {
        // Get the value of the setting we've registered with register_setting()
        $options = get_option('evcw_config_options');
    ?>
        <select
            id="<?php echo esc_attr($args['label_for']); ?>"
            data-custom="<?php echo esc_attr($args['evcw_custom_data']); ?>"
            name="evcw_config_options[<?php echo esc_attr($args['label_for']); ?>]">
            <option value="OpenAI" <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], 'OpenAI', false)) : (''); ?>>
                <?php esc_html_e('Open AI', 'ev-crosswords'); ?>
            </option>
            <option value="Anthropic" <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], 'Anthropic', false)) : (''); ?>>
                <?php esc_html_e('Anthropic', 'ev-crosswords'); ?>
            </option>
            <option value="local_model" <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], 'local_model', false)) : (''); ?>>
                <?php esc_html_e('Local Model', 'ev-crosswords'); ?>
            </option>
        </select>
    <?php
    }

    function evcw_ai_local_provider_url_cb($args)
    {
        $options = get_option('evcw_config_options');

    ?>
        <input type="text" data-custom="<?php echo esc_attr($args['evcw_custom_data']); ?>" id="<?php echo esc_attr($args['label_for']); ?>" name="evcw_config_options[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo $options[$args['label_for']]; ?>">
    <?php
    }

    function evcw_ai_provider_api_key_cb($args)
    {
        // Get the value of the setting we've registered with register_setting()
        $options = get_option('evcw_config_options');
    ?>
        <input type="password" data-custom="<?php echo esc_attr($args['evcw_custom_data']); ?>" id="<?php echo esc_attr($args['label_for']); ?>" name="evcw_config_options[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo $options[$args['label_for']]; ?>">
    <?php
    }

    /**
     * Add the top level menu page.
     */
    function evcw_options_page()
    {
        add_submenu_page(
            'edit.php?post_type=ev_crossword',
            'Config',
            'Config',
            'manage_options',
            'ev-config',
            array($this, 'evcw_options_page_html')
        );
    }


    /**
     * Top level menu callback function
     */
    function evcw_options_page_html()
    {
        // check user capabilities
        if (! current_user_can('manage_options')) {
            return;
        }

        // add error/update messages

        // check if the user have submitted the settings
        // WordPress will add the "settings-updated" $_GET parameter to the url
        if (isset($_GET['settings-updated'])) {
            // add settings saved message with the class of "updated"
            add_settings_error('evcw_messages', 'evcw_message', __('Settings Saved', 'evcw'), 'updated');
        }

        // show error/update messages
        settings_errors('evcw_messages');
    ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                // output security fields for the registered setting "evcw"
                settings_fields('ev_config');
                // output setting sections and their fields
                // (sections are registered for "evcw", each field is registered to a specific section)
                do_settings_sections('ev_config');
                // output save settings button
                submit_button('Save Settings');
                ?>
            </form>
        </div>
<?php
    }

    function enqueue_custom_settings_assets($hook)
    {

        $screen = get_current_screen();

        if ($screen->post_type !== 'ev_crossword') return;

        // loading css
        wp_register_style('evcw-settings-css', EVCWV_PLUGIN_URL . 'admin/assets/settings.css', false, '1.0.0');
        wp_enqueue_style('evcw-settings-css');

        // loading js
        wp_register_script('evcw-settings-js', EVCWV_PLUGIN_URL . 'admin/assets/settings.js', array('jquery-core'), false, true);
        wp_enqueue_script('evcw-settings-js');

        wp_localize_script('evcw-settings-js', 'evcw_obj', array('ajax_url' => admin_url('admin-ajax.php')));
    }
}


new EvCwPluginSettings();
