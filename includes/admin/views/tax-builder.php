<?php
if (!defined('ABSPATH')) exit;
wp_nonce_field('meowfield_save_tax', 'meowfield_tax_nonce');
?>

<div class="mf-builder-wrapper" style="padding: 10px;">
    <div class="mf-setting-group" style="margin-bottom: 20px;">
        <label style="font-weight:bold; display:block; margin-bottom:5px;">Taxonomy Slug (e.g., genre)</label>
        <input type="text" name="meowfield_tax[slug]" value="<?php echo esc_attr($config['slug']); ?>" required style="width: 100%; padding: 8px;">
        <p class="description">Only lowercase letters and underscores.</p>
    </div>

    <div class="mf-setting-group" style="margin-bottom: 20px;">
        <label style="font-weight:bold; display:block; margin-bottom:5px;">Plural Label (e.g., Genres)</label>
        <input type="text" name="meowfield_tax[plural]" value="<?php echo esc_attr($config['plural']); ?>" required style="width: 100%; padding: 8px;">
    </div>

    <div class="mf-setting-group" style="margin-bottom: 20px;">
        <label style="font-weight:bold; display:block; margin-bottom:5px;">Singular Label (e.g., Genre)</label>
        <input type="text" name="meowfield_tax[singular]" value="<?php echo esc_attr($config['singular']); ?>" required style="width: 100%; padding: 8px;">
    </div>

    <hr style="margin: 30px 0;">
    <h3>Advanced Settings</h3>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <div class="mf-setting-group" style="margin-bottom: 15px;">
                <label>
                    <input type="checkbox" name="meowfield_tax[hierarchical]" value="1" <?php checked(isset($config['hierarchical']) ? $config['hierarchical'] : 1); ?>>
                    Hierarchical (Like Categories, instead of Tags)
                </label>
            </div>
        </div>
        <div>
            <div class="mf-setting-group" style="margin-bottom: 15px;">
                <label style="font-weight:bold; display:block; margin-bottom:5px;">Attach to Post Types</label>
                <?php
                $current_pts = isset($config['post_types']) && is_array($config['post_types']) ? $config['post_types'] : [];
                foreach ($available_post_types as $pt) {
                    // Skip attachments and internal types usually not needed, but let's just list all public ones
                    if ($pt->name === 'attachment') continue;
                    echo '<label style="display:block; margin-bottom:5px;">';
                    echo '<input type="checkbox" name="meowfield_tax[post_types][]" value="' . esc_attr($pt->name) . '" ' . checked(in_array($pt->name, $current_pts), true, false) . '> ';
                    echo esc_html($pt->label) . ' (' . esc_html($pt->name) . ')';
                    echo '</label>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
