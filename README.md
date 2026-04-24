# MeowField

**MeowField** is a premium, comprehensive WordPress content modeling plugin. Built with modern aesthetics and simplicity in mind, it empowers you to effortlessly create Custom Post Types, Custom Taxonomies, and Custom Fields directly from your dashboard. It provides robust functionality without the bloat, and includes built-in, completely free interactive map support using **OpenStreetMap** (via Leaflet.js).

## ✨ Features

- **Custom Post Type Builder**: Create and manage Custom Post Types directly from your WordPress dashboard.
- **Custom Taxonomy Builder**: Create hierarchical or flat Custom Taxonomies and link them to your post types effortlessly.
- **Field Group Builder**: A modern, drag-and-drop interface for creating and managing custom field groups.
- **Multiple Field Types**:
  - `Text`: Standard single-line text input.
  - `Text Area`: Multi-line text input.
  - `Image`: Fully integrated with the native WordPress Media Library.
  - `Map (OpenStreetMap)`: Interactive map location picker. **100% Free**, no Google Maps API key required!
- **Location Rules**: Easily assign field groups to specific Post Types (e.g., Posts, Pages, or custom post types).
- **Shortcode Integration**: Display your custom field data effortlessly on the frontend using simple shortcodes.
- **Automatic Updates**: Seamlessly pulls the latest updates directly from the public GitHub repository right to your WordPress dashboard.

## 🚀 Installation

1. Download the latest release `.zip` from this repository.
2. Go to your WordPress Admin Dashboard.
3. Navigate to **Plugins > Add New > Upload Plugin**.
4. Choose the downloaded `.zip` file and click **Install Now**.
5. Click **Activate Plugin**.

## 💡 How to Use

### 1. Registering Custom Post Types & Taxonomies
1. Navigate to **MeowField > Post Types** or **MeowField > Taxonomies**.
2. Click **Add New**.
3. Fill in the Slugs, Labels, and Advanced Settings (such as enabling Archives or Hierarchical structures).
4. Publish to register them instantly.

### 2. Creating Custom Fields
1. Navigate to **MeowField > Field Groups** in the WordPress admin menu.
2. Enter a title for your Field Group.
3. Click **+ Add Field** to start building your fields.
4. Set the **Location Rule** to attach the fields to your newly created Post Types.
5. Publish the Field Group.

### 3. Entering Data
When you create or edit a post that matches your location rules, you will see the MeowField meta box. Fill in your data, upload images, or pick a location on the map.

### 4. Displaying Data on the Frontend
You can display the saved data anywhere on your site using the following shortcodes:

**For Standard Fields (Text, Text Area, Image ID):**
```markdown
[meowfield name="your_field_name"]
```

**For the Map Field (Displays an interactive map):**
```markdown
[meowfield_map name="your_map_field_name" height="400px"]
```

## 🔄 Updates
MeowField checks for updates directly from the `main` branch of its GitHub repository. You will receive standard WordPress update notifications when a new commit is pushed. You can also manually check for updates via the "Check for Updates" link on the Plugins page.

## 👨‍💻 Developer Information
Developed by Antigravity / Akbar Bahaulloh.
Built from scratch to deliver a clean, secure, and truly premium experience for WordPress developers and creators.
