<?php

printf('<small>%s</small>',
    sprintf(
        esc_html__('If you have suggestions for a new add-on, feel free to open a support request on %1$s.
    Want regular updates? Follow me on %2$s or visit my %3$s.'),
        '<a href="https://github.com/thefrosty/custom-login/issues" target="_blank">GitHub</a>',
        '<a href="https://twitter.com/TheFrosty" target="_blank">Twitter</a>',
        '<a href="http://austin.passy.co" target="_blank">blog</a>'
    )
);
