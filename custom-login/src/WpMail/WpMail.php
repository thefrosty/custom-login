<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\WpMail;

use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\WpUtilities\Plugin\HooksTrait;
use TheFrosty\WpUtilities\Plugin\PluginAwareInterface;
use TheFrosty\WpUtilities\Plugin\PluginAwareTrait;

/**
 * Class WpMail
 *
 * @package Dwnload\WpMail
 */
class WpMail implements PluginAwareInterface
{
    use HooksTrait, PluginAwareTrait;

    const CONTENT_TYPE_HTML = 'text/html';
    const CONTENT_TYPE_PLAIN = 'text/plain';

    /**
     * Holds the from address
     *
     * @var string $from_address
     */
    private $from_address = '';

    /**
     * Holds the from name
     *
     * @var string $from_name
     */
    private $from_name = '';

    /**
     * Holds the email content type
     *
     * @var string $content_type
     */
    private $content_type = '';

    /**
     * Holds the email headers
     *
     * @var string $headers
     */
    private $headers = '';

    /**
     * Whether to send email in HTML
     *
     * @var bool $html
     */
    private $html = true;

    /**
     * The email template to use
     *
     * @var string $template
     */
    private $template = '';

    /**
     * The header text for the email
     *
     * @var string $heading
     */
    private $heading = '';

    /**
     * The visually hidden "pretext".
     *
     * @var string $pretext
     */
    private $pretext = '';

    /**
     * WpMail constructor.
     */
    public function __construct()
    {
        if ($this->getTemplate() === 'none') {
            $this->html = false;
        }

        $this->addAction(CustomLogin::HOOK_PREFIX . 'email_send_before', [$this, 'sendBefore']);
        $this->addAction(CustomLogin::HOOK_PREFIX . 'email_send_after', [$this, 'sendAfter']);
    }

    /**
     * Set a property.
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set(string $key, $value)
    {
        $this->$key = $value;
    }

    /**
     * Get a property.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->$key;
    }

    /**
     * Get the email from name
     *
     * @return string
     */
    public function getFromName(): string
    {
        if (empty($this->from_name)) {
            $this->from_name = \get_bloginfo('name');
        }

        return (string)\apply_filters(
            CustomLogin::HOOK_PREFIX . 'email_from_name',
            \wp_specialchars_decode($this->from_name),
            $this
        );
    }

    /**
     * Get the email from address.
     *
     * @return string
     */
    public function getFromAddress(): string
    {
        if (empty($this->from_address)) {
            $this->from_address = \get_site_option('admin_email');
        }

        return (string)\apply_filters(CustomLogin::HOOK_PREFIX . 'email_from_address', $this->from_address, $this);
    }

    /**
     * Get the email content type.
     *
     * @return string
     */
    public function getContentType(): string
    {
        if (empty($this->content_type) && $this->html) {
            $this->content_type = (string)\apply_filters(
                CustomLogin::HOOK_PREFIX . 'email_default_content_type',
                self::CONTENT_TYPE_HTML,
                $this
            );
        } elseif (!$this->html) {
            $this->content_type = self::CONTENT_TYPE_PLAIN;
        }

        return (string)\apply_filters(CustomLogin::HOOK_PREFIX . 'email_content_type', $this->content_type, $this);
    }

    /**
     * Get the email headers.
     *
     * @return string
     */
    public function getHeaders(): string
    {
        if (!$this->headers) {
            $this->headers = "From: {$this->getFromName()} <{$this->getFromAddress()}>\r\n";
            $this->headers .= "Reply-To: {$this->getFromAddress()}\r\n";
            $this->headers .= "Content-Type: {$this->getContentType()}; charset=utf-8\r\n";
        }

        return (string)\apply_filters(CustomLogin::HOOK_PREFIX . 'email_headers', $this->headers, $this);
    }

    /**
     * Get the enabled email template
     *
     * @return string
     */
    public function getTemplate(): string
    {
        if (!$this->template) {
            $this->template = 'default';
        }

        return (string)\apply_filters(CustomLogin::HOOK_PREFIX . 'email_template', $this->template);
    }

    /**
     * Get the header text for the email
     *
     * @return string
     */
    public function getHeading(): string
    {
        return (string)\apply_filters(CustomLogin::HOOK_PREFIX . 'email_heading', $this->heading);
    }

    /**
     * Parse email template tags
     *
     * @param string $content
     *
     * @return string
     */
    public function parseTags($content): string
    {
        return $content;
    }

    /**
     * Build the final email.
     *
     * @param string $message
     *
     * @return string
     */
    public function buildEmail(string $message): string
    {
        if (!$this->html) {
            return (string)\apply_filters(
                CustomLogin::HOOK_PREFIX . 'email_message',
                \wp_strip_all_tags($message),
                $this
            );
        }

        $message = $this->textToHtml($message);

        \ob_start();

        // Render the header
        include $this->getPlugin()->getDirectory() . 'templates/email/header.php';

        /**
         * Hooks into the email header
         */
        \do_action(CustomLogin::HOOK_PREFIX . 'email_header', $this);

        // Render the body
        include $this->getPlugin()->getDirectory() . 'templates/email/body.php';

        /**
         * Hooks into the body of the email
         *
         * @param WpMail $this
         */
        \do_action(CustomLogin::HOOK_PREFIX . 'email_body', $this);

        // Render the footer
        include $this->getPlugin()->getDirectory() . 'templates/email/footer.php';

        /**
         * Hooks into the footer of the email
         *
         * @param WpMail $this
         */
        \do_action(CustomLogin::HOOK_PREFIX . 'email_footer', $this);

        $body = \ob_get_clean();
        $message = \str_replace(['{pretext}', '{email}'], [$this->pretext, $message], $body);

        return (string)\apply_filters(CustomLogin::HOOK_PREFIX . 'email_message', $message, $this);
    }

    /**
     * Send the email
     *
     * @param  string $to The To address to send to.
     * @param  string $subject The subject line of the email to send.
     * @param  string $message The body of the email to send.
     * @param  string|array $attachments Attachments to the email in a format supported by wp_mail()
     *
     * @return bool
     */
    public function send($to, $subject, $message, $attachments = ''): bool
    {
        if (!\did_action('init') && !\did_action('admin_init')) {
            \_doing_it_wrong(
                __FUNCTION__,
                \sprintf('You cannot send email with `%s` until `init` or `admin_init` has been reached.', self::class),
                null
            );

            return false;
        }

        /**
         * Hook before the email is sent.
         *
         * @param WpMail $this
         */
        \do_action(CustomLogin::HOOK_PREFIX . 'email_send_before', $this);

        $subject = $this->parseTags($subject);
        $message = $this->parseTags($message);
        $message = $this->buildEmail($message);

        $attachments = \apply_filters(CustomLogin::HOOK_PREFIX . 'email_attachments', $attachments, $this);

        $sent = \wp_mail($to, $subject, $message, $this->getHeaders(), $attachments);
        $log_errors = \apply_filters(CustomLogin::HOOK_PREFIX . 'log_email_errors', true, $to, $subject, $message);

        if (!$sent && $log_errors) {
            if (\is_array($to)) {
                $to = \implode(',', $to);
            }

            $log_message = sprintf(
                "Email from %s failed to send.\nSend time: %s\nTo: %s\nSubject: %s\n\n",
                self::class,
                \date_i18n('F j Y H:i:s', \current_time('timestamp')),
                $to,
                $subject
            );

            \error_log($log_message);
        }

        /**
         * Hook after the email is sent.
         *
         * @param WpMail $this
         * @param bool $sent Whether the email was sent
         */
        \do_action(CustomLogin::HOOK_PREFIX . 'email_send_after', $this, $sent);

        return $sent;
    }

    /**
     * Add filters / actions before the email is sent.
     */
    public function sendBefore()
    {
        $this->addFilter('wp_mail_from', [$this, 'getFromAddress']);
        $this->addFilter('wp_mail_from_name', [$this, 'getFromName']);
        $this->addFilter('wp_mail_content_type', [$this, 'getContentType']);
    }

    /**
     * Remove filters / actions after the email is sent.
     */
    public function sendAfter()
    {
        $this->removeFilter('wp_mail_from', [$this, 'getFromAddress']);
        $this->removeFilter('wp_mail_from_name', [$this, 'getFromName']);
        $this->removeFilter('wp_mail_content_type', [$this, 'getContentType']);

        // Reset heading to an empty string
        $this->heading = '';
    }

    /**
     * Converts text to formatted HTML. This is primarily for turning line breaks into <p> and
     * <br/> tags.
     *
     * @param string $message
     *
     * @return string
     */
    public function textToHtml(string $message): string
    {
        if ($this->content_type === self::CONTENT_TYPE_HTML || $this->html === true) {
            $message = \apply_filters(CustomLogin::HOOK_PREFIX . 'email_template_wpautop', true) ?
                \wpautop($message) : $message;
        }

        return $message;
    }
}
