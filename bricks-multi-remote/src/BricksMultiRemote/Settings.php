<?php

namespace BricksMultiRemote;

class Settings
{
    private static $instance = null;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        add_action('admin_menu', [$this, 'bmr_settings_page'], 9999);
        add_action('admin_init', [$this, 'bmr_settings_init']);
    }

    function bmr_settings_page()
    {
        add_submenu_page(
            'bricks',
            'Bricks Remote Templates',
            'Multi Remotes',
            'manage_options',
            'bmr-plugin-settings',
            [$this, 'bmr_settings_content'],
            9
        );
    }

    function bmr_settings_content()
    {
        ?>
        <div class="wrap">
            <h1>Bricks Multi Remote Template Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('bmr_settings_group');
                do_settings_sections('bmr-plugin-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    function bmr_settings_init()
    {
        register_setting('bmr_settings_group', 'bmr_settings');

        add_settings_section('bmr_settings_section', 'Settings', [$this, 'bmr_settings_section_cb'], 'bmr-plugin-settings');

        add_settings_field('remote_templates', 'Remote Templates', [$this, 'remote_templates_cb'], 'bmr-plugin-settings', 'bmr_settings_section');

    }

    function bmr_settings_section_cb()
    {
        echo '<p>Add one Remote per line.</p><p>REMOTE_URL | PASSWORD</p>';
    }

    function remote_templates_cb()
    {
        $options = get_option('bmr_settings');
        ?>
        <textarea class="widefat" name="bmr_settings[remote_templates]" rows="5"
                  cols="50"><?php echo isset($options['remote_templates']) ? esc_textarea($options['remote_templates']) : ''; ?></textarea>
        <?php
    }

}