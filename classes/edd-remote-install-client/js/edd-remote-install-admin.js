jQuery(document).ready(function ($) {

    if(edd_ri_options.skipplugincheck != true) {

        $('a[data-edd-install]').each(function() {

            var downloadButton = $(this);

            var data = {
                action: 'edd-check-plugin-status-' + edd_ri_options.page,
                download: downloadButton.data('edd-install')
            }

            $.post(ajaxurl, data, function (res) {

                if(res) {
                    if(res == 'installed') {
                        $(downloadButton).prev(".eddri-status").text("Inactive").addClass('eddri-inactive');
                        $(downloadButton).text('Activate');
                    }
                    if(res == 'active') {
                        $(downloadButton).prev(".eddri-status").text("Active").addClass('eddri-active');
                        $(downloadButton).text('Deactivate');
                    }

                }
                
            });

        });
    }

    $('body').on('click', 'a[data-edd-install]', function (e) {
        e.preventDefault();

        var downloadButton = $(this);
        var statusContainer = $(this).prev(".eddri-status");

        if(downloadButton.text() == 'Activate') {

            var data = {
                action: 'edd-activate-plugin-' + edd_ri_options.page,
                download: downloadButton.data('edd-install')
            }

            $.post(ajaxurl, data, function (res) {

                if(res == 'activated') {

                    $(statusContainer).text("Active").addClass('eddri-active');
                    $(downloadButton).text('Deactivate');

                } else {

                    $(statusContainer).addClass('eddri-error').text("Error activating. Try activating manually from the plugins screen.");

                }

            });

        } else if(downloadButton.text() == 'Deactivate') {

            var data = {
                action: 'edd-deactivate-plugin-' + edd_ri_options.page,
                download: downloadButton.data('edd-install')
            }

            $.post(ajaxurl, data, function (res) {

                if(res == 'deactivated') {

                    $(statusContainer).text("Inactive").removeClass('eddri-active success finished').addClass('eddri-inactive');
                    $(downloadButton).text('Activate');

                } else {

                    $(statusContainer).addClass('eddri-error').text("Error deactivating. Try deactivating manually from the plugins screen.");

                }

            });

        } else {

            var data = {
                action: 'edd-check-remote-install-' + edd_ri_options.page,
                download: downloadButton.data('edd-install')
            }

            statusContainer.progressInitialize()
            .progressStart()
            .attr({'data-loading': "Requesting package..."});

            $.post(ajaxurl, data, function (res) {
                res = $.parseJSON( res );
                if (res == '0') {
                    // Free download found

                    var data = {
                        action: 'edd-do-remote-install-' + edd_ri_options.page,
                        download: downloadButton.data('edd-install')
                    };

                    statusContainer.progressSet(50)
                    .attr({'data-loading': "Found package. Installing..."});

                    $.post(ajaxurl, data, function (res) {
                        $(downloadButton).text('Deactivate');
                        return statusContainer.progressFinish(res, downloadButton);
                    });

                } else if (res == '1') {
                    // License key required to continue
                    statusContainer.validateLicense(downloadButton);
                } else {

                    return statusContainer.progressFinish(res, downloadButton);
                }

            });
        }
    });

    // Progress meter functionality defined in jQuery plugins.

    $.fn.validateLicense = function(downloadButton) {
        var statusContainer = $(downloadButton).prev(".eddri-status");
        var licenseInput;

        // Pause auto-updating of progress bar and create license key input field
        statusContainer.progressStop(40)
        .attr({'data-loading': "Enter license key to continue:"})
        .after("<input id='license-input' placeholder='License key'></input>")
        .off('click')
        .removeClass('success failure');

        licenseInput = $('#license-input');

        licenseInput.click(function() {

            statusContainer.attr({'data-loading': "Click to proceed."})

            statusContainer.click(function (e) {
                e.stopPropagation();
                validPost();
            });

            downloadButton.click(function (e) {
                e.stopPropagation();
                validPost();
            });

            $(document).keypress(function (e) {
                if(e.which == 13) {
                    e.preventDefault();
                    validPost();
                }
            });

            function validPost() {
                var license = licenseInput.val();
                if (!license) return false;

                var data = {
                    action: 'edd-do-remote-install-' + edd_ri_options.page,
                    download: downloadButton.data('edd-install'),
                    license: license,
					  url: edd_ri_options.url
                }

                licenseInput.remove();

                statusContainer.progressStart().progressSet(50)
                .attr({'data-loading': "Validating license..."});

                $.post(ajaxurl, data, function (res) {
                    statusContainer.progressFinish(res, downloadButton, license);
                });
            };
        });
    }

    $.fn.progressInitialize = function() {
        var button = this;
        var progress = 0;

        // Add markup for the progress bar.
        var bar = $('<span class="tz-bar background-horizontal">').appendTo(button);

        button.on('progress', function (e, val, absolute, finished) {
            var finished = finished;

            // Make sure button has `in-progress` class when initialized.
            // And that local var `progress` = 0 to start.
            // Then show the progress bar.
            if (!button.hasClass('in-progress')) {
                button.removeClass('finished').addClass('in-progress');
                progress = 0;
                bar.show();
            }

            if (absolute) {
                progress = val;
            } else if (progress >= 100) {
                progress = 100;
                finished = true;
            } else {
                progress += val;
            }

            if (finished) {
                button.removeClass('in-progress').addClass('finished');

                bar.delay(500).fadeOut(function() {
                    button.trigger('progress-finish');
                    setProgress(0);
                });
            }

            setProgress(progress);
        });
        
        function setProgress (percentage) {
            bar.filter('.background-horizontal,.background-bar').width(percentage+'%');
            bar.filter('.background-vertical').height(percentage+'%');
        }

        return button;
    };

    $.fn.progressStart = function() {
        var button = this;
        var last_progress = new Date().getTime();

        if (button.hasClass('in-progress')) {
            // Don't start it a second time!
            return button;
        }

        button.on('progress', function() {
            last_progress = new Date().getTime();
        });

        // Every half a second check whether the progress 
        // has been incremented in the last two seconds

        var interval = window.setInterval(function() {

            // Check every half-second to see whether
            // progress has incremented in past 2 seconds.
            if ( new Date().getTime() > 2000+last_progress && !button.hasClass('stopped')) {

                // There has been no activity for 2s. Increment the progress
                // bar a little bit to show that something is happening.
                button.progressIncrement(5);
            }

        }, 500);

        button.on('progress-finish', function() {
            window.clearInterval(interval);
        }).progressIncrement(10);
        return button;
    };

    $.fn.progressSet = function (val) {
        var button = this;
        var finished = false;
        val = val || 10;

        if (button.hasClass('stopped')) {
            button.removeClass('stopped');
        }

        if (val >= 100) {
            finished = true;
        }

        button.trigger('progress', [val, true, finished]);
        return button;
    };

    $.fn.progressIncrement = function (val) {
        var button = this;
        val = val || 10;
        button.trigger('progress', [val]);
        return button;
    };

    $.fn.progressStop = function (val) {
        var button = this;
        button.progressSet(val).addClass('stopped');
        return button;
    };

    $.fn.progressFinish = function (res, downloadButton, license) {
//		alert(res);

        var statusContainer = $(downloadButton).prev(".eddri-status");

        statusContainer.progressSet(100);

        if (res === 'invalid') {
            statusContainer.attr({'data-finished': "Invalid License"});

            setTimeout(function() {
                statusContainer.validateLicense(downloadButton);
            }, 1200);

        } else if (res.search('error') > 0) {

            // If there was an unknown error, try to run the install manually

            statusContainer.attr({'data-finished': "Unknown error. Redirecting..."})
            .addClass('eddri-error');

            var data = {
                action: 'edd-do-manual-install-' + edd_ri_options.page,
                download: downloadButton.data('edd-install'),
                license: license
            }

            $.post(ajaxurl, data, function (res) {
                window.location.href = res;
            });

        } else if (res.search('already exists') > 0) {
            statusContainer.attr({'data-finished': "Error: Already installed"})
            .addClass('eddri-error');

        } else if (res.search('not exist') > 0) {
            statusContainer.attr({'data-finished': "Error: Plugin file does not exist."})
            .addClass('eddri-error');

        } else if (res.search('installed successfully') > 0) {
            statusContainer.attr({'data-finished': "Success!"})
            .addClass('success');
            $(downloadButton).text('Deactivate');

        } else {

            // If there was an unknown error, try to run the install manually

            statusContainer.attr({'data-finished': "Unknown error. Redirecting..."})
            .addClass('eddri-error');

            var data = {
                action: 'edd-do-manual-install-' + edd_ri_options.page,
                download: downloadButton.data('edd-install'),
                license: license
            }

            $.post(ajaxurl, data, function (res) {
                window.location.href = res;
            });

        }

        return statusContainer;
    };

});