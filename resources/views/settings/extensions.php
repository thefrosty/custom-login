<?php

declare(strict_types=1);

$all_plugins = function_exists('get_plugins') ? get_plugins() : [];
$checkout_url ??= TheFrosty\CustomLogin\CustomLogin::getApiUrl();
$extensions ??= [];
?>
<div class="wrap">
    <h2><?php
        esc_html_e('Available Custom Login Extensions', 'custom-login'); ?></h2>
    <form method="post">
        <div class="section">
            <?php
            foreach ($extensions as $key => $extension) { ?>
                <div class="col addon">
                    <div class="addon-container">
                        <div class="img-wrap">
                            <a href="<?php
                            echo esc_url(
                                add_query_arg([
                                    'utm_source' => 'plugin-extensions-page',
                                    'utm_medium' => 'custom-login',
                                    'utm_campaign' => 'extensions',
                                    'utm_content' => esc_attr($extension['title']),
                                ], $extension['url'])
                            ); ?>" target="_blank">
                                <img class="thumbnail" src="<?php
                                echo esc_url($extension['image']); ?>" alt="">
                            </a>
                            <p><?php
                                echo wp_kses_post($extension['description']); ?></p>
                        </div>

                        <h3><?php
                            echo esc_html($extension['title']); ?></h3>
                        <div class="status" data-status="not-installed" style="display:none">
                            <?php
                            if (!array_key_exists($extension['slug'], $all_plugins)) {
                                printf('<p>%s</p>', esc_html__('Not Installed', 'custom-login'));
                            } elseif (is_plugin_inactive($extension['slug'])) {
                                printf('<p>%s</p>', esc_html__('Installed - Not Active', 'custom-login'));
                            } else {
                                printf('<p>%s</p>', esc_html__('Active', 'custom-login'));
                            }
                            ?>
                        </div>
                        <hr>
                        <a class="button primary show-if-not-purchased"
                           href="<?php
                           echo esc_url(
                               add_query_arg(
                                   [
                                       'edd_action' => 'add_to_cart',
                                       'download_id' => $extension['download_id'],
                                   ],
                                   TheFrosty\CustomLogin\CustomLogin::getApiUrl('checkout/')
                               )
                           ); ?>" target="_blank"
                           data-toggle="purchase-links-<?php
                           echo esc_attr($key); ?>"
                           style="display:none"><?php
                            esc_html_e('Get this Extension', 'custom-login'); ?></a>
                        <div id="purchase-links-<?php
                        echo esc_attr($key); ?>" style="display:none">
                            <ul>
                                <?php
                                foreach ($extension['links'] as $link) { ?>
                                    <li>
                                        <?php
                                        echo wp_kses_post($link['description']); ?>
                                        (<?php
                                        echo esc_html($link['price']); ?>):
                                        <a href="<?php
                                        echo esc_url(
                                            add_query_arg([
                                                'edd_action' => 'straight_to_gateway',
                                                'download_id' => $link['download_id'],
                                                'edd_options[price_id]' => $link['price_id'],
                                            ], $checkout_url)
                                        ); ?>"><?php
                                            esc_html_e('PayPal', 'custom-login'); ?></a>
                                        |
                                        <a href="<?php
                                        echo esc_url(
                                            add_query_arg([
                                                'edd_action' => 'add_to_cart',
                                                'download_id' => $link['download_id'],
                                                'edd_options[price_id]' => $link['price_id'],
                                            ], $checkout_url)
                                        ); ?>"><?php
                                            esc_html_e('Credit Card', 'custom-login'); ?></a>
                                    </li>
                                    <?php
                                } // Links ?>
                            </ul>

                        </div>
                    </div>
                </div>
                <?php
            } // Extensions ?>
        </div>
    </form>
    <script type="text/javascript">
      jQuery(document).ready(function ($) {
        setTimeout(function () {
          const $container = $('.addon-container')
          $container.find('div.status').addClass('notice').show()
          $container.each(function () {
            const $this = $(this)
            setTimeout(function () {
              if ($this.find('div.status').attr('data-status') === 'not-installed') {
                $this.children('a.button').hide()
                $this.children('a.button.show-if-not-purchased').show()
              }
            }, 100)
          })
        }, 100) // Timeout, so we can add the `notice` class.
      })
    </script>
</div>
