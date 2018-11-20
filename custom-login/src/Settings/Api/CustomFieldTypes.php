<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Settings\Api;

use Dwnload\WpSettingsApi\Api\Options;
use Dwnload\WpSettingsApi\Settings\FieldTypes;
use TheFrosty\CustomLogin\Pro\Modules\Module;

/**
 * Class CustomFieldTypes
 * - This allows new fields types (methods) to be created as output.
 * @see FieldTypes for more
 * @package OpenFit\Settings\Api
 */
class CustomFieldTypes extends FieldTypes
{
    const FIELD_TYPE_TEXT = 'html';
    const FIELD_TYPE_MODULES = 'modules';

    /**
     * Renders a html field.
     *
     * @param array $args Array of Field object parameters
     */
    public function html(array $args)
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());

        $output = '<div class="FieldType_html">';
        $output .= \wp_kses_post($value);
        $output .= '</div>';
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }

    /**
     * Renders an input multi-checkbox field for Custom Login Modules.
     *
     * @param array $args Array of Field object parameters
     */
    public function modules(array $args)
    {
        $field = $this->getSettingFieldObject($args);
        $value = Options::getOption($field->getId(), $field->getSectionId(), $field->getDefault());
        $value = \is_array($value) ? \array_map('esc_attr', $value) : \esc_attr($value);
        $module = null;

        $output = '<div class="FieldType_modules">';
        $output .= '<ul>';
        foreach ($field->getOptions() as $key => $label) {
            if (!empty($field->getAttributes()[$key]) && \is_array($field->getAttributes()[$key])) {
                $module = new Module($field->getAttributes()[$key]);
            }
            $checked = isset($value[$key]) ? $value[$key] : '0';
            $output .= '<li class="Module_container">';
            $output .= sprintf(
                '<div class="Module_label"><strong>%s</strong></div>',
                \esc_html($label)
            );
            $output .= sprintf(
                '<div class="Module_image"><img src="%s"></div>',
                $module instanceof Module ? \esc_url($module->getImage()) : ''
            );
            $output .= sprintf(
                '<div class="Module_description">%s</div>',
                $module instanceof Module ? \wp_kses_post($module->getDescription()) : ''
            );
            $output .= \sprintf(
                '<div class="Module_checkbox"><label for="%1$s[%2$s][%3$s]" title="%5$s" class="switch">
<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s"%4$s>
<div class="slider round"></div></label></div>',
                $field->getSectionId(),
                $field->getId(),
                $key,
                \checked($checked, $key, false),
                \esc_attr($label)
            );
            $output .= '</li>';
        }
        $output .= '</ul>';
        $output .= '</div>';
        $output .= $this->getFieldDescription($args);

        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
    }
}
