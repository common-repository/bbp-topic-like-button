<?php

class bbPtlbCheckReady
{
    public $min, $ver, $errors, $plugin, $plugins;

    /**
     * CheckReady constructor.
     * @param string $minVersion
     * @param array $plugins
     * @param string $name
     */
    public function __construct($minVersion = '5.6', $plugins = [], $name = '') {
        $this->min = $minVersion;
        $this->ver = PHP_VERSION;
        $this->errors = [];
        $this->plugin = trim($name) ? $name : '';

        if (!version_compare($this->ver, $this->min, '>=')) {
            $this->errors[] = sprintf(
                '%s plugin requires at least PHP %s (You have %s).',
                $this->plugin ? $this->plugin : 'This plugin',
                $this->min,
                $this->ver
            );
        }

        if ($plugins) {
            $this->getPlugins();

            foreach ((array)$plugins as $name => $plugin) {
                if (!in_array($plugin, $this->plugins)) {
                    $error = sprintf(
                        '%s plugin is required!',
                        strlen($name) > 2 ? $name : $plugin
                    );

                    $this->errors[] = $error;
                }
            }
        }

        if ($this->hasErrors()) {
            $prefix = is_multisite() && is_network_admin() ? 'network_' : '';
            add_action($prefix . 'admin_notices', [$this, 'notice'], 999);
        }
    }

    /**
     * Echo notices if any errors.
     */
    public function notice() {
        if (!$this->errors) {
            return;
        }

        $this->errors = array_unique(array_merge(["<strong>$this->plugin notices</strong>:"], $this->errors));

        printf(
            '<div class="error notice is-dismissible"><p>%s</p></div>',
            implode('<br/> &mdash; ', $this->errors)
        );

        printf(
            '<div class="error notice is-dismissible"><p>%s</p></div>',
            __('Deactivating plugin..')
        );
    }

    /**
     * Check if there are any errors.
     *
     * @return bool
     */
    public function hasErrors() {
        return (bool)$this->errors;
    }

    /**
     * Set the this plugins property.
     *
     * @return $this
     */
    public function getPlugins() {
        $this->plugins = apply_filters('active_plugins', get_option('active_plugins'));

        if (is_multisite()) {
            $network_plugins = get_site_option('active_sitewide_plugins');
            if ($network_plugins) {
                $network_plugins = array_keys($network_plugins);
                $this->plugins = array_merge($this->plugins, $network_plugins);
            }
        }

        return $this;
    }

    /**
     * If there are any errors set a site option to trigger deactivating this plugin.
     */
    public function check() {
        if ($this->hasErrors()) {
            update_site_option('bbptlb_force_deactivate', true);
            return false;
        }

        delete_site_option('bbptlb_force_deactivate');
        return true;
    }
}
