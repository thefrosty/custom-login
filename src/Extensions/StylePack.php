<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Extensions;
use function __;
use function add_query_arg;
use function add_settings_error;
use function admin_url;
use function esc_attr;
use function file_exists;
use function filter_var;
use function plugin_dir_path;
use function remove_query_arg;
use function sprintf;
use function str_contains;
use function trailingslashit;
use function update_option;
use function urlencode;
use function wp_die;
use function wp_nonce_url;
use function wp_safe_redirect;
use function wp_verify_nonce;
use const FILTER_VALIDATE_BOOL;

trait StylePack
{

    /**
     * Add our settings error notification.
     * phpcs:disable SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
     */
    protected function addSettingsError(): void
    {
        if (isset($_GET['style_pack_updated']) && filter_var($_GET['style_pack_updated'], FILTER_VALIDATE_BOOL)) {
            add_settings_error(
                'style_pack',
                'settings_updated',
                sprintf(
                    __('The Custom Login Style Pack "%s" was successfully imported.', 'custom-login'),
                    esc_attr($_GET['style_pack_label'] ?? '')
                ),
                'updated'
            );
            unset($_GET['style_pack_updated'], $_GET['style_pack_label']);
        }
    }

    /**
     * Action run on admin_action_.
     *  phpcs:disable SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
     */
    protected function maybeImportStylePack(): never
    {
        if (!isset($_GET['action'], $_GET['cl_nonce']) || !wp_verify_nonce($_GET['cl_nonce'], 'style_pack')) {
            wp_die(__('Invalid nonce.', 'custom-login'));
        }

        $label = $style = '';
        $settings = [];

        foreach ($this->fields as $field) {
            if (!str_contains($_GET['action'], $field['name'])) {
                continue;
            }
            $style = $field['name'];
            $label = $field['label'];
        }

        if ($style !== '') {
            $filename = trailingslashit(plugin_dir_path($this->file)) . "styles/$style.php";

            if (file_exists($filename)) {
                $settings = include $filename;
            }

            if (!empty($settings)) {
                foreach ($settings as $setting_key => $setting) {
                    if ($setting !== false) {
                        if (update_option($setting_key, $setting)) {
                            add_settings_error(
                                $setting_key,
                                esc_attr('settings_updated'),
                                esc_html__('Custom Login style pack successfully imported.', 'custom-login'),
                                'updated'
                            );
                        }
                    }
                }
                wp_safe_redirect(
                    add_query_arg(
                        [
                            'page' => $this->parent->getSlug(),
                            'style_pack_updated' => true,
                            'style_pack_label' => urlencode($label),
                        ],
                        admin_url('options-general.php')
                    )
                );
                exit;
            }
        }

        wp_safe_redirect(
            remove_query_arg(
                ['action', 'cl_nonce'],
                sprintf(admin_url('options-general.php?page=%s'), $this->parent->getSlug())
            )
        );
        exit;
    }

    /**
     * Build an activation action URL.
     * @param string $name
     * @return string
     */
    private function buildActionUrl(string $name = ''): string
    {
        if (empty($name)) {
            return '';
        }

        return wp_nonce_url(
            add_query_arg(['action' => sprintf('%s_style_pack_%s', $this->parent->getSlug(), esc_attr($name)),], ''),
            'style_pack',
            'cl_nonce'
        );
    }
}
