<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Pro;

use Dwnload\WpSettingsApi\Api\Options;
use TheFrosty\CustomLogin\Pro\Modules\LastLoginColumns;
use TheFrosty\CustomLogin\Pro\Modules\Module;
use TheFrosty\CustomLogin\Pro\Modules\WpLogin;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\CustomLogin\Settings\ActiveModuleSettings;
use TheFrosty\CustomLogin\Utils\AbstractPluginProviderReference;
use TheFrosty\WpUtilities\Plugin\HttpFoundationRequestInterface;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;

/**
 * Class CustomLoginPro
 *
 * @package TheFrosty\CustomLogin\Pro
 */
class CustomLoginPro extends AbstractPluginProviderReference
{
    const MODULE_LAST_LOGIN_COLUMNS = LastLoginColumns::class;
    const MODULE_WP_LOGIN = WpLogin::class;

    /**
     * Add class hooks.
     */
    public function addHooks()
    {
        $this->addFilter(ActiveModuleSettings::TAG_REGISTERED_MODULES, [$this, 'registerModules']);
        $modules = $this->getModules();
        \array_walk($modules, [$this, 'instantiateModules']);
    }

    /**
     * Register this module to the settings page to be activated.
     * @param array $modules
     * @return array
     */
    protected function registerModules(array $modules): array
    {
        $modules = [
            [
                Module::DESCRIPTION => 'Something WpLogin stuff.',
                Module::FULLY_QUALIFIED_CLASS => WpLogin::class,
                Module::IMAGE => $this->getPlugin()->getUrl('assets/img/modules/wp_login.jpg'),
                Module::TITLE => 'WpLogin',
            ],
            [
                Module::DESCRIPTION => 'Something about the last login columns things.',
                Module::FULLY_QUALIFIED_CLASS => LastLoginColumns::class,
                Module::IMAGE => $this->getPlugin()->getUrl('assets/img/modules/last_login_columns.jpg'),
                Module::TITLE => 'LastLoginColumns',
            ],
        ];
        return $modules;
    }

    /**
     * Get all `MODULE_` prefixed constants in an array key/value pair.
     * @return array
     */
    protected function getModules(): array
    {
        return \array_filter($this->getReflection($this)->getConstants(), function (string $key): bool {
            return \strpos($key, 'MODULE_') !== false;
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Instantiate all our modules.
     * @param string $module
     */
    protected function instantiateModules(string $module)
    {
        $this->addAction('after_setup_theme', function () use ($module) {
            $active_modules = $this->getActiveModules();
            if (!\class_exists($module) || !\array_key_exists($module, $active_modules)) {
                return;
            }
            $module_object = new $module();
            if ($module_object instanceof HttpFoundationRequestInterface) {
                $module_object->setRequest($this->getPlugin()->getContainer()->get(ServiceProvider::HTTP_FOUNDATION_REQUEST));
            }
            if ($module_object instanceof WpHooksInterface) {
                $this->getPlugin()->add($module_object)->initialize();
            }
        });
    }

    /**
     * Return the active modules form the database.
     * @return array
     */
    private function getActiveModules(): array
    {
        static $options;
        $options = $options ?: Options::getOption(
            ActiveModuleSettings::FIELD_ACTIVE_MODULES,
            ActiveModuleSettings::SECTION,
            []
        );
        return $options;
    }
}
