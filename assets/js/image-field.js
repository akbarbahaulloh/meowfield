(function($) {
    'use strict';

    $(document).ready(function() {
        $('body').on('click', '.mf-select-image', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $wrapper = $btn.closest('.mf-image-input-wrapper');
            const $input = $wrapper.find('.mf-image-value');
            const $preview = $wrapper.find('.mf-image-preview');
            const $removeBtn = $wrapper.find('.mf-remove-image');

            const frame = wp.media({
                title: 'Select or Upload Image',
                button: {
                    text: 'Use this image'
                },
                multiple: false
            });

            frame.on('select', function() {
                const attachment = frame.state().get('selection').first().toJSON();
                $input.val(attachment.id);
                
                const thumb = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                $preview.html('<img src="' + thumb + '" style="max-width:100%; max-height:100%;">');
                $removeBtn.show();
            });

            frame.open();
        });

        $('body').on('click', '.mf-remove-image', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $wrapper = $btn.closest('.mf-image-input-wrapper');
            $wrapper.find('.mf-image-value').val('');
            $wrapper.find('.mf-image-preview').html('<span class="dashicons dashicons-format-image" style="font-size:40px; width:40px; height:40px; color:#ccc;"></span>');
            $btn.hide();
        });
    });

})(jQuery);
