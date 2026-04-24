<?php
if (!defined('ABSPATH')) exit;
wp_nonce_field('meowfield_save_cpt', 'meowfield_cpt_nonce');
?>

<div class="mf-builder-wrapper" style="padding: 10px;">
    <div class="mf-setting-group" style="margin-bottom: 20px;">
        <label style="font-weight:bold; display:block; margin-bottom:5px;">Post Type Slug (e.g., movies)</label>
        <input type="text" name="meowfield_cpt[slug]" value="<?php echo esc_attr($config['slug']); ?>" required style="width: 100%; padding: 8px;">
        <p class="description">Only lowercase letters and underscores.</p>
    </div>

    <div class="mf-setting-group" style="margin-bottom: 20px;">
        <label style="font-weight:bold; display:block; margin-bottom:5px;">Plural Label (e.g., Movies)</label>
        <input type="text" name="meowfield_cpt[plural]" value="<?php echo esc_attr($config['plural']); ?>" required style="width: 100%; padding: 8px;">
    </div>

    <div class="mf-setting-group" style="margin-bottom: 20px;">
        <label style="font-weight:bold; display:block; margin-bottom:5px;">Singular Label (e.g., Movie)</label>
        <input type="text" name="meowfield_cpt[singular]" value="<?php echo esc_attr($config['singular']); ?>" required style="width: 100%; padding: 8px;">
    </div>

    <hr style="margin: 30px 0;">
    <h3>Advanced Settings</h3>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <div class="mf-setting-group" style="margin-bottom: 15px;">
                <label>
                    <input type="checkbox" name="meowfield_cpt[public]" value="1" <?php checked(isset($config['public']) ? $config['public'] : 1); ?>>
                    Public
                </label>
            </div>
            <div class="mf-setting-group" style="margin-bottom: 15px;">
                <label>
                    <input type="checkbox" name="meowfield_cpt[has_archive]" value="1" <?php checked(isset($config['has_archive']) ? $config['has_archive'] : 1); ?>>
                    Has Archive
                </label>
            </div>
            <div class="mf-setting-group" style="margin-bottom: 15px;">
                <label>
                    <input type="checkbox" name="meowfield_cpt[hierarchical]" value="1" <?php checked(isset($config['hierarchical']) ? $config['hierarchical'] : 0); ?>>
                    Hierarchical (like Pages)
                </label>
            </div>
        </div>
        <div>
            <div class="mf-setting-group" style="margin-bottom: 15px;">
                <label style="font-weight:bold; display:block; margin-bottom:5px;">Supports</label>
                <?php
                $available_supports = ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions', 'author', 'page-attributes'];
                $current_supports = isset($config['supports']) && is_array($config['supports']) ? $config['supports'] : ['title', 'editor', 'thumbnail'];
                foreach ($available_supports as $feature) {
                    echo '<label style="display:block; margin-bottom:5px;">';
                    echo '<input type="checkbox" name="meowfield_cpt[supports][]" value="' . esc_attr($feature) . '" ' . checked(in_array($feature, $current_supports), true, false) . '> ';
                    echo ucfirst(str_replace('-', ' ', $feature));
                    echo '</label>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
