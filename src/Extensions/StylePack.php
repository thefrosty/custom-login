<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Extensions;

trait StylePack
{

    /**
     * Add our settings error notification.
     */
    protected function addSettingsError(): void
    {
        if (isset($_GET['style_pack_updated']) && \filter_var($_GET['style_pack_updated'], \FILTER_VALIDATE_BOOL)) {
            \add_settings_error(
                'style_pack',
                'settings_updated',
                \sprintf(
                    \__('The Custom Login Style Pack "%s" was successfully imported.', 'custom-login'),
                    \esc_attr($_GET['style_pack_label'] ?? '')
                ),
                'updated'
            );
            unset($_GET['style_pack_updated'], $_GET['style_pack_label']);
        }
    }

    /**
     * Action run on admin_action_.
     */
    protected function maybeImportStylePack(): void
    {
        if (!isset($_GET['action'], $_GET['cl_nonce']) || !\wp_verify_nonce($_GET['cl_nonce'], 'style_pack')) {
            \wp_die(\__('Invalid nonce.', 'custom-login'));
        }

        $label = $style = '';
        $settings = [];

        foreach ($this->fields as $field) {
            if (!\str_contains($_GET['action'], $field['name'])) {
                continue;
            }
            $style = $field['name'];
            $label = $field['label'];
        }

        if ($style !== '') {
            $filename = \trailingslashit(\plugin_dir_path($this->file)) . "styles/$style.php";

            if (\file_exists($filename)) {
                $settings = include $filename;
            }

            if (!empty($settings)) {
                foreach ($settings as $setting_key => $setting) {
                    if ($setting !== false) {
                        if (\update_option($setting_key, $setting)) {
                            \add_settings_error(
                                $setting_key,
                                esc_attr('settings_updated'),
                                esc_html__('Custom Login style pack successfully imported.', 'custom-login'),
                                'updated'
                            );
                        }
                    }
                }
                \wp_safe_redirect(
                    \add_query_arg(
                        [
                            'page' => $this->parent->getSlug(),
                            'style_pack_updated' => true,
                            'style_pack_label' => \urlencode($label),
                        ],
                        \admin_url('options-general.php')
                    )
                );
                exit;
            }
        }

        \wp_safe_redirect(
            \remove_query_arg(['action', 'cl_nonce'],
                \sprintf(\admin_url('options-general.php?page=%s'), $this->parent->getSlug())
            )
        );
        exit;
    }

    /**
     * Build an activation action URL.
     */
    private function buildActionUrl(string $name = ''): string
    {
        if (empty($name)) {
            return '';
        }

        return \wp_nonce_url(
            \add_query_arg(['action' => \sprintf('%s_style_pack_%s', $this->parent->getSlug(), \esc_attr($name)),], ''),
            'style_pack',
            'cl_nonce'
        );
    }
}