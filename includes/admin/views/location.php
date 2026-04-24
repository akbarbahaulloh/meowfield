<?php
if (!defined('ABSPATH')) exit;
$post_types = get_post_types(['public' => true], 'objects');
?>

<div class="mf-location-rules">
    <p>Show this field group if</p>
    <div class="mf-rule-row">
        <select name="meowfield_rules[param]">
            <option value="post_type">Post Type</option>
        </select>
        <span>is equal to</span>
        <select name="meowfield_rules[value]">
            <?php foreach ($post_types as $pt): ?>
                <option value="<?php echo esc_attr($pt->name); ?>" <?php selected($rules['value'], $pt->name); ?>>
                    <?php echo esc_html($pt->label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
