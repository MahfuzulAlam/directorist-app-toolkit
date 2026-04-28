jQuery(function ($) {
    var config = window.directoristAppToolkitSettings || {};
    var $page = $('.directorist-app-toolkit-settings');

    if (!$page.length) {
        return;
    }

    var $tabLinks = $page.find('.directorist-app-toolkit-settings__tabs .nav-tab');
    var $tabPanels = $page.find('.directorist-app-toolkit-tab-panel');

    function activateTab(tabKey, updateHash) {
        if (!tabKey) {
            return;
        }

        $tabLinks.removeClass('nav-tab-active');
        $tabLinks.filter('[data-tab="' + tabKey + '"]').addClass('nav-tab-active');

        $tabPanels.removeClass('is-active');
        $tabPanels.filter('[data-tab-panel="' + tabKey + '"]').addClass('is-active');

        if (updateHash !== false && window.history && window.history.replaceState) {
            window.history.replaceState(null, '', '#' + tabKey);
        }
    }

    function setFeedback($form, type, message) {
        var $feedback = $form.find('.directorist-app-toolkit-feedback');

        if (!message) {
            $feedback.empty();
            return;
        }

        var noticeClass = 'notice ';

        if (type === 'success') {
            noticeClass += 'notice-success';
        } else if (type === 'error') {
            noticeClass += 'notice-error';
        } else {
            noticeClass += 'notice-info';
        }

        $feedback.html(
            $('<div />', {
                'class': noticeClass + ' inline'
            }).append(
                $('<p />').text(message)
            )
        );
    }

    function setFormLoading($form, isLoading) {
        var $button = $form.find('button[type="submit"]');
        var $spinner = $form.find('.spinner');

        $form.toggleClass('is-saving', isLoading);
        $button.prop('disabled', isLoading);
        $spinner.toggleClass('is-active', isLoading);

        if (isLoading) {
            $button.data('label', $button.text());
            $button.text(config.i18n && config.i18n.saving ? config.i18n.saving : 'Saving…');
        } else {
            $button.text($button.data('label') || (config.i18n && config.i18n.saveChanges ? config.i18n.saveChanges : 'Save Changes'));
        }
    }

    function syncMediaPreview($field) {
        var $input = $field.find('.directorist-app-toolkit-media-url');
        var $preview = $field.find('.directorist-app-toolkit-media-preview');
        var $image = $preview.find('img');
        var url = $.trim($input.val());

        if (url) {
            $preview.removeClass('is-empty');
            $image.attr('src', url);
        } else {
            $preview.addClass('is-empty');
            $image.attr('src', '');
        }
    }

    function bindMediaField($field) {
        var frame;
        var $input = $field.find('.directorist-app-toolkit-media-url');
        var $select = $field.find('.directorist-app-toolkit-media-select');
        var $remove = $field.find('.directorist-app-toolkit-media-remove');

        $select.on('click', function (event) {
            event.preventDefault();

            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: $select.data('title') || (config.i18n && config.i18n.chooseImage ? config.i18n.chooseImage : 'Choose Image'),
                button: {
                    text: $select.data('button-text') || (config.i18n && config.i18n.useImage ? config.i18n.useImage : 'Use Image')
                },
                library: {
                    type: 'image'
                },
                multiple: false
            });

            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                $input.val(attachment.url).trigger('input');
            });

            frame.open();
        });

        $remove.on('click', function (event) {
            event.preventDefault();
            $input.val('').trigger('input');
        });

        $input.on('input change', function () {
            syncMediaPreview($field);
        });

        syncMediaPreview($field);
    }

    $page.find('.directorist-app-toolkit-color-picker').wpColorPicker();
    $page.find('.directorist-app-toolkit-media-field').each(function () {
        bindMediaField($(this));
    });

    $tabLinks.on('click', function (event) {
        event.preventDefault();
        activateTab($(this).data('tab'));
    });

    var initialTab = window.location.hash ? window.location.hash.replace('#', '') : config.activeTab;
    activateTab(initialTab || $tabLinks.first().data('tab'), false);

    $page.find('.directorist-app-toolkit-settings-form').on('submit', function (event) {
        event.preventDefault();

        var $form = $(this);

        setFeedback($form, '', '');
        setFormLoading($form, true);

        $.ajax({
            url: config.ajaxUrl,
            method: 'POST',
            dataType: 'json',
            data: $form.serialize()
        }).done(function (response) {
            if (response && response.success) {
                setFeedback($form, 'success', response.data && response.data.message ? response.data.message : '');
            } else {
                setFeedback($form, 'error', response && response.data && response.data.message ? response.data.message : (config.i18n && config.i18n.genericError ? config.i18n.genericError : 'Something went wrong. Please try again.'));
            }
        }).fail(function (xhr) {
            var message = config.i18n && config.i18n.genericError ? config.i18n.genericError : 'Something went wrong. Please try again.';

            if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                message = xhr.responseJSON.data.message;
            }

            setFeedback($form, 'error', message);
        }).always(function () {
            setFormLoading($form, false);
        });
    });
});
