# AWDev Plugin Updater

A self-hosted WordPress plugin updater for [AlexanderWagnerDev](https://alexanderwagnerdev.com) plugins. Updates are served from `wp-plugins-updates.awdev.space` instead of WordPress.org — giving full control over versioning and distribution.

## Features

- ✅ Full WordPress Settings Page (*Settings → AWDev Updater*)
- ✅ Consistent UI with DarkAdmin design system (Dark Mode compatible)
- ✅ DarkAdmin built-in — auto-registered when installed
- ✅ Add/remove additional plugins dynamically via Settings UI
- ✅ Manual cache flush button
- ✅ Configurable API base URL
- ✅ "View version details" popup support in WP update screen
- ✅ Automatic folder name fix after ZIP extraction

## Folder Structure

```
wp-plugins-updater/
├── wp-plugins-updater.php       ← Main plugin file
├── includes/
│   ├── class-awdev-updater.php  ← Reusable updater class (one instance per plugin)
│   └── settings.php             ← Settings page, admin hooks, cache flush
└── assets/
    ├── css/settings.css         ← Settings page styles (DarkAdmin-compatible)
    └── js/settings.js           ← Dynamic add/remove plugin rows
```

## Update Server JSON Format

Each endpoint at `https://wp-plugins-updates.awdev.space/api/{slug}.php` must return:

```json
{
  "slug":         "your-plugin-folder",
  "name":         "Your Plugin Name",
  "version":      "1.2.3",
  "download_url": "https://wp-plugins-updates.awdev.space/downloads/your-plugin.zip",
  "details_url":  "https://alexanderwagnerdev.com/plugins/your-plugin",
  "changelog":    "<h4>1.2.3</h4><ul><li>Fixed XYZ</li></ul>",
  "tested":       "6.9",
  "requires":     "6.0",
  "requires_php": "7.4"
}
```

## Release Workflow

1. Update `version` in your plugin's main PHP file
2. Upload the new ZIP to `/downloads/` on the update server
3. Update `version` in the API endpoint
4. WordPress detects the update automatically within 6 hours (or flush cache manually via Settings)

## License

GPLv2 or later
