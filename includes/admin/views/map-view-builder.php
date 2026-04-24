<?php
if (!defined('ABSPATH')) {
    exit;
}

// Get all public post types
$post_types = get_post_types(['public' => true], 'objects');
unset($post_types['attachment']);

// Get all taxonomies
$taxonomies = get_taxonomies(['public' => true], 'objects');

// Try to find all field groups and their map fields to help the user
$map_fields = [];
$groups = get_posts(['post_type' => 'meowfield_group', 'posts_per_page' => -1]);
foreach ($groups as $g) {
    $fields = get_post_meta($g->ID, '_meowfield_fields', true);
    if (is_array($fields)) {
        foreach ($fields as $f) {
            if ($f['type'] === 'map') {
                $map_fields[$f['name']] = $f['label'] . ' (' . $f['name'] . ')';
            }
        }
    }
}
?>

<div class="mf-map-view-builder">
    <style>
        .mf-mv-row { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .mf-mv-row:last-child { border-bottom: none; }
        .mf-mv-label { display: block; font-weight: bold; margin-bottom: 8px; font-size: 14px; }
        .mf-mv-help { color: #666; font-size: 12px; margin-top: 4px; display: block; }
        .mf-mv-select { width: 100%; max-width: 400px; }
        .mf-mv-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; margin-top: 10px; }
        .mf-mv-checkbox-item { background: #fff; border: 1px solid #ddd; padding: 8px 12px; border-radius: 4px; display: flex; align-items: center; gap: 8px; }
    </style>

    <div class="mf-mv-row">
        <label class="mf-mv-label">1. Pilih Data Source (Post Type)</label>
        <select name="map_view[post_type]" class="mf-mv-select">
            <option value="any">Semua Tipe Pos (Any)</option>
            <?php foreach ($post_types as $slug => $obj): ?>
                <option value="<?php echo esc_attr($slug); ?>" <?php selected($settings['post_type'], $slug); ?>><?php echo esc_html($obj->label); ?></option>
            <?php endforeach; ?>
        </select>
        <span class="mf-mv-help">Tipe pos mana yang ingin Anda tampilkan titik lokasinya di peta?</span>
    </div>

    <div class="mf-mv-row">
        <label class="mf-mv-label">2. Pilih Field Peta (Map Field Name)</label>
        <input type="text" name="map_view[map_field]" value="<?php echo esc_attr($settings['map_field']); ?>" class="mf-mv-select" placeholder="Contoh: lokasi">
        <span class="mf-mv-help">Ketikkan <b>Field Name</b> bertipe Map yang Anda gunakan di Post Type tersebut.</span>
        <?php if (!empty($map_fields)): ?>
            <div style="margin-top:10px; font-size: 12px;">
                <b>Field Peta yang terdeteksi:</b> 
                <?php foreach($map_fields as $name => $label): ?>
                    <code style="cursor:pointer; color:#2271b1;" onclick="document.getElementsByName('map_view[map_field]')[0].value='<?php echo $name; ?>'"><?php echo $name; ?></code> 
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="mf-mv-row">
        <label class="mf-mv-label">3. Pilih Taksonomi untuk Filter</label>
        <div class="mf-mv-grid">
            <?php foreach ($taxonomies as $slug => $obj): ?>
                <label class="mf-mv-checkbox-item">
                    <input type="checkbox" name="map_view[taxonomies][]" value="<?php echo esc_attr($slug); ?>" <?php checked(in_array($slug, $settings['taxonomies'])); ?>>
                    <?php echo esc_html($obj->label); ?> <small style="color:#999; font-size: 10px;">(<?php echo esc_html($slug); ?>)</small>
                </label>
            <?php endforeach; ?>
        </div>
        <span class="mf-mv-help">Pilih kategori atau taksonomi apa saja yang ingin dimunculkan sebagai dropdown filter di atas peta.</span>
    </div>

    <div class="mf-mv-row">
        <label class="mf-mv-label">4. Pengaturan Tampilan</label>
        <div style="display:flex; gap: 30px; align-items: center;">
            <label>
                <input type="checkbox" name="map_view[enable_search]" value="1" <?php checked($settings['enable_search'], 1); ?>>
                Aktifkan Kotak Pencarian Teks
            </label>
            <label>
                Tinggi Peta:
                <input type="text" name="map_view[height]" value="<?php echo esc_attr($settings['height']); ?>" style="width: 80px;">
            </label>
        </div>
    </div>
</div>
