<?php declare(strict_types=1);

use TheFrosty\CustomLogin\WpAdmin\SettingsUpgrades;

?>
    <div class="wrap">
        <h2><?php esc_html_e('Custom Login - Upgrades', 'custom-login'); ?></h2>
        <div id="custom-login-upgrade-status">
            <p>
                <img alt="" src="<?php echo esc_url(admin_url('images/spinner-2x.gif')); ?>"
                     id="custom-login-upgrade-loader" width="16px" style="display: none">
                <span><?php
                    esc_html_e(
                        'The upgrade process has started, please be patient. This could take several minutes. You will be automatically redirected when the upgrade is finished.',
                        'custom-login'
                    ); ?>
                </span>
            </p>
        </div>
        <script>
          jQuery(document).ready(function () {
            jQuery.ajax({
              method: 'POST',
              url: window.ajaxurl,
              data: {
                action: '<?php echo esc_attr(SettingsUpgrades::AJAX_ACTION); ?>',
                _ajax_nonce: '<?php echo wp_create_nonce(SettingsUpgrades::ACTION_NONCE); ?>'
              },
              dataType: 'json',
              beforeSend: function () {
                jQuery('#custom-login-upgrade-loader').show()
              }
            }).done(function (response) {
              if (!response.success) {
                console.log('Error')
              } else {
                jQuery('#custom-login-upgrade-loader').hide()
                document.location.href = 'options-general.php?page=custom-login'
              }
            })
          })
        </script>
    </div>
<?php
