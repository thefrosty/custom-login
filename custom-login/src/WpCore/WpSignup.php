<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\WpCore;

use Symfony\Component\HttpFoundation\Response;
use TheFrosty\CustomLogin\AbstractHookRequestProvider;
use function TheFrosty\CustomLogin\terminate;

/**
 * Class WpSignup
 * @package TheFrosty\CustomLogin\WpCore
 */
class WpSignup extends AbstractHookRequestProvider
{
    /**
     * Add class hooks.
     */
    public function addHooks()
    {
        $this->addAction('before_signup_header', [$this, 'redirectWpSignup']);
    }

    /**
     * Redirect all requests to the 'wp-signup.php' page back to the network home URL.
     */
    protected function redirectWpSignup()
    {
        // Don't allow POST requests to the wp-signup.php page
        if (!empty($this->getRequest()->request->all())) {
            \wp_die(
                \esc_html__('Ah ah ah, you didn\'t say the magic word.', 'custom-login'),
                \esc_html__('Access Denied', 'custom-login')
            );
        }
        \wp_safe_redirect(\network_home_url(), Response::HTTP_PERMANENTLY_REDIRECT);
        terminate();
    }
}
