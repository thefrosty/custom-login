<?php

use Dwnload\WpSettingsApi\Api\Options;
use TheFrosty\CustomLogin\Settings\MailSettings;

/**
 * @param string $url
 * @return string
 */
$get_attachment_image = function (string $url): string {
    static $image;
    $image[$url] = $image[$url] ?: \wp_get_attachment_image(\attachment_url_to_postid($url), 'full');
    return trim($image[$url]);
};
?>
<center style="width: 100%; background-color: #222222;">
    <!--[if mso | IE]>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
           style="background-color: #222222;">
        <tr>
            <td>
    <![endif]-->

    <!-- Visually Hidden Preheader Text : BEGIN -->
    <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
        <?php echo esc_html(Options::getOption(
            MailSettings::FIELD_PRE_HEADER,
            MailSettings::SECTION,
            ''
        )); ?>
    </div>
    <!-- Visually Hidden Preheader Text : END -->

    <!-- Create white space after the desired preview text so email clients don’t pull other distracting text into the inbox preview. Extend as necessary. -->
    <!-- Preview Text Spacing Hack : BEGIN -->
    <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
    </div>
    <!-- Preview Text Spacing Hack : END -->

    <!--
        Set the email width. Defined in two places:
        1. max-width for all clients except Desktop Windows Outlook, allowing the email to squish on narrow but never go wider than 600px.
        2. MSO tags for Desktop Windows Outlook enforce a 600px width.
    -->
    <div style="max-width: 600px; margin: 0 auto;" class="email-container">
        <!--[if mso]>
        <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="600">
            <tr>
                <td>
        <![endif]-->

        <!-- Email Body : BEGIN -->
        <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
               style="margin: 0 auto;">
            <?php $header_logo = Options::getOption(
                MailSettings::FIELD_HEADER_IMAGE,
                MailSettings::SECTION,
                ''
            );
            if (!empty($header_logo) &&
                preg_match('/(^.*\.jpg|jpeg|png|gif|ico*)/i', $header_logo) !== false &&
                !empty($get_attachment_image($header_logo))
            ) {
                ?>
                <!-- Email Header : BEGIN -->
                <tr>
                    <td style="padding: 20px 0; text-align: center">
                        <?php echo $get_attachment_image($header_logo); ?>
                    </td>
                </tr>
                <!-- Email Header : END -->
            <?php } ?>

            <!-- Hero Image, Flush : BEGIN -->
            <tr>
                <td style="background-color: #ffffff;">
                    <?php $hero_image = Options::getOption(
                        MailSettings::FIELD_HERO_IMAGE,
                        MailSettings::SECTION,
                        ''
                    );
                    if (!empty($hero_image) &&
                        preg_match('/(^.*\.jpg|jpeg|png|gif|ico*)/i', $hero_image) !== false &&
                        !empty($get_attachment_image($hero_image))
                    ) {
                        echo $get_attachment_image($header_logo);
                    }
                    ?>
                </td>
            </tr>
            <!-- Hero Image, Flush : END -->

            <!-- 1 Column Text + Button : BEGIN -->
            <tr>
                <td style="background-color: #ffffff;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                                %%CONTENT%%
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- 1 Column Text + Button : END -->

            <!-- Clear Spacer : BEGIN -->
            <tr>
                <td aria-hidden="true" height="40" style="font-size: 0px; line-height: 0px;">
                    &nbsp;
                </td>
            </tr>
            <!-- Clear Spacer : END -->

        </table>
        <!-- Email Body : END -->

        <!-- Email Footer : BEGIN -->
        <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
               style="margin: 0 auto;">
            <tr>
                <td style="padding: 20px; font-family: sans-serif; font-size: 12px; line-height: 15px; text-align: center; color: #888888;">
                    <?php $email_footer = Options::getOption(
                        MailSettings::FIELD_FOOTER,
                        MailSettings::SECTION,
                        ''
                    );
                    if (!empty($email_footer)) {
                        echo esc_html($email_footer);
                    }
                    ?>
                </td>
            </tr>
        </table>
        <!-- Email Footer : END -->

        <!--[if mso]>
        </td>
        </tr>
        </table>
        <![endif]-->
    </div>

    <!--[if mso | IE]>
    </td>
    </tr>
    </table>
    <![endif]-->
</center>