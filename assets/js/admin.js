(function($) {
    'use strict';

    const MeowField = {
        init: function() {
            this.cacheDOM();
            this.bindEvents();
            this.makeSortable();
        },

        cacheDOM: function() {
            this.$list = $('.mf-fields-list');
            this.$addBtn = $('.mf-add-field-btn');
            this.$template = $('#mf-field-template');
        },

        bindEvents: function() {
            const self = this;

            this.$addBtn.on('click', function(e) {
                e.preventDefault();
                self.addField();
            });

            this.$list.on('click', '.mf-field-row-header', function(e) {
                if ($(e.target).closest('.mf-btn-danger').length) return;
                $(this).closest('.mf-field-row').toggleClass('is-open');
            });

            this.$list.on('click', '.mf-delete-field', function(e) {
                e.preventDefault();
                if (confirm(meowfield.i18n.confirm_delete)) {
                    $(this).closest('.mf-field-row').fadeOut(300, function() {
                        $(this).remove();
                    });
                }
            });

            this.$list.on('keyup', '.mf-input-label', function() {
                const val = $(this).val();
                const $row = $(this).closest('.mf-field-row');
                $row.find('.mf-field-label-text').text(val || '(no label)');
                
                // Auto-fill name if empty
                const $nameInput = $row.find('.mf-input-name');
                let name = $nameInput.val();
                if (!$nameInput.data('edited')) {
                    name = val.toLowerCase().replace(/[^a-z0-9]/g, '_').replace(/_+/g, '_').replace(/^_+|_+$/g, '');
                    $nameInput.val(name);
                    $row.find('.mf-field-name-text').text(name);
                }
                self.updateShortcode($row, name);
            });

            this.$list.on('change', '.mf-input-name', function() {
                $(this).data('edited', true);
                const val = $(this).val();
                const $row = $(this).closest('.mf-field-row');
                $row.find('.mf-field-name-text').text(val);
                self.updateShortcode($row, val);
            });

            this.$list.on('change', '.mf-input-type', function() {
                const val = $(this).val();
                const text = $(this).find('option:selected').text();
                const $row = $(this).closest('.mf-field-row');
                $row.find('.mf-field-type-text').text(text);
                const name = $row.find('.mf-input-name').val();
                self.updateShortcode($row, name);
            });
        },

        updateShortcode: function($row, name) {
            const type = $row.find('.mf-input-type').val();
            const tag = type === 'map' ? 'meowfield_map' : 'meowfield';
            const shortcode = name ? '[' + tag + ' name="' + name + '"]' : '';
            $row.find('.mf-field-shortcode-text').text(shortcode);
            $row.find('.mf-input-shortcode').val(shortcode);
        },

        makeSortable: function() {
            this.$list.sortable({
                handle: '.mf-field-handle',
                axis: 'y',
                placeholder: 'mf-sortable-placeholder',
                helper: function(e, ui) {
                    ui.children().each(function() {
                        $(this).width($(this).width());
                    });
                    return ui;
                }
            });
        },

        addField: function() {
            const index = this.$list.find('.mf-field-row').length;
            let html = this.$template.html();
            
            html = html.replace(/\[INDEX\]/g, index);
            
            const $newRow = $(html).hide();
            this.$list.append($newRow);
            $newRow.fadeIn(300);
            
            // Scroll to new field
            $('html, body').animate({
                scrollTop: $newRow.offset().top - 100
            }, 500);
            
            // Focus on label
            $newRow.find('.mf-input-label').focus();
        }
    };

    $(document).ready(function() {
        MeowField.init();
    });

})(jQuery);
