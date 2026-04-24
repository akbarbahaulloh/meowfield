<?php
if (!defined('ABSPATH')) exit;
wp_nonce_field('meowfield_save_fields', 'meowfield_fields_nonce');
?>

<div class="mf-builder-wrapper">
    <div class="mf-builder-header">
        <div class="mf-header-col mf-col-label">Label</div>
        <div class="mf-header-col mf-col-name">Name</div>
        <div class="mf-header-col mf-col-type">Type</div>
        <div class="mf-header-col mf-col-key">Key</div>
    </div>
    
    <div class="mf-fields-list">
        <?php if (!empty($fields)): foreach ($fields as $index => $field): 
            $key = isset($field['key']) ? $field['key'] : 'field_' . uniqid();
        ?>
            <div class="mf-field-row" data-id="<?php echo $index; ?>">
                <div class="mf-field-row-header">
                    <div class="mf-col-label">
                        <div class="mf-col-label-top">
                            <div class="mf-field-handle dashicons dashicons-menu"></div>
                            <div class="mf-field-label-text"><?php echo esc_html($field['label']); ?></div>
                        </div>
                        <div class="mf-field-actions">
                            <button type="button" class="mf-delete-field mf-btn mf-btn-danger">Delete</button>
                        </div>
                    </div>
                    <div class="mf-col-name mf-field-name-text"><?php echo esc_html($field['name']); ?></div>
                    <div class="mf-col-type mf-field-type-text"><?php echo esc_html($field['type']); ?></div>
                    <div class="mf-col-key mf-field-key-text" style="font-size: 12px; color: #999;"><?php echo esc_html($key); ?></div>
                </div>
                
                <div class="mf-field-content">
                    <div class="mf-field-settings-grid">
                        <div class="mf-setting-group">
                            <label>Field Label</label>
                            <input type="text" name="meowfield_fields[<?php echo $index; ?>][label]" value="<?php echo esc_attr($field['label']); ?>" class="mf-input-label">
                        </div>
                        <div class="mf-setting-group">
                            <label>Field Name</label>
                            <input type="text" name="meowfield_fields[<?php echo $index; ?>][name]" value="<?php echo esc_attr($field['name']); ?>" class="mf-input-name">
                        </div>
                        <div class="mf-setting-group">
                            <label>Field Type</label>
                            <select name="meowfield_fields[<?php echo $index; ?>][type]" class="mf-input-type">
                                <option value="text" <?php selected($field['type'], 'text'); ?>>Text</option>
                                <option value="textarea" <?php selected($field['type'], 'textarea'); ?>>Text Area</option>
                                <option value="select" <?php selected($field['type'], 'select'); ?>>Select</option>
                                <option value="image" <?php selected($field['type'], 'image'); ?>>Image</option>
                                <option value="map" <?php selected($field['type'], 'map'); ?>>Map (OpenStreetMap)</option>
                            </select>
                        </div>
                        <div class="mf-setting-group">
                            <label>Field Key</label>
                            <input type="text" name="meowfield_fields[<?php echo $index; ?>][key]" value="<?php echo esc_attr($key); ?>" readonly style="background: #f1f1f1;">
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <div class="mf-add-field-container">
        <button type="button" class="mf-btn mf-btn-primary mf-add-field-btn">+ Add Field</button>
    </div>
</div>

<script type="text/template" id="mf-field-template">
    <div class="mf-field-row is-open">
        <div class="mf-field-row-header">
            <div class="mf-col-label">
                <div class="mf-col-label-top">
                    <div class="mf-field-handle dashicons dashicons-menu"></div>
                    <div class="mf-field-label-text">(no label)</div>
                </div>
                <div class="mf-field-actions">
                    <button type="button" class="mf-delete-field mf-btn mf-btn-danger">Delete</button>
                </div>
            </div>
            <div class="mf-col-name mf-field-name-text"></div>
            <div class="mf-col-type mf-field-type-text">Text</div>
            <div class="mf-col-key mf-field-key-text" style="font-size: 12px; color: #999;">[KEY]</div>
        </div>
        
        <div class="mf-field-content">
            <div class="mf-field-settings-grid">
                <div class="mf-setting-group">
                    <label>Field Label</label>
                    <input type="text" name="meowfield_fields[[INDEX]][label]" value="" class="mf-input-label" placeholder="New Field">
                </div>
                <div class="mf-setting-group">
                    <label>Field Name</label>
                    <input type="text" name="meowfield_fields[[INDEX]][name]" value="" class="mf-input-name" placeholder="new_field">
                </div>
                <div class="mf-setting-group">
                    <label>Field Type</label>
                    <select name="meowfield_fields[[INDEX]][type]" class="mf-input-type">
                        <option value="text">Text</option>
                        <option value="textarea">Text Area</option>
                        <option value="select">Select</option>
                        <option value="image">Image</option>
                        <option value="map">Map (OpenStreetMap)</option>
                    </select>
                </div>
                <div class="mf-setting-group">
                    <label>Field Key</label>
                    <input type="text" name="meowfield_fields[[INDEX]][key]" value="[KEY]" readonly style="background: #f1f1f1;">
                </div>
            </div>
        </div>
    </div>
</script>
