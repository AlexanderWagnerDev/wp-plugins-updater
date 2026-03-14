# AWDev Plugin Updater

A self-hosted WordPress plugin updater for [AlexanderWagnerDev](https://alexanderwagnerdev.com) plugins. Instead of relying on WordPress.org, updates are fetched from a custom domain — giving full control over versioning and distribution.

## How It Works

1. This plugin registers a WordPress update hook per managed plugin.
2. On each WordPress update check, it queries a self-hosted JSON endpoint (e.g. `https://updates.alexanderwagnerdev.com/api/darkadmin.php`).
3. If a newer version is found, WordPress shows the update notification in the admin backend.
4. The ZIP is downloaded directly from your own server.

## Adding a New Plugin

Open `wp-plugins-updater.php` and add a new line inside the `init` action:

```php
new AWDev_Updater(
    'your-plugin-folder/your-plugin.php',
    'https://updates.alexanderwagnerdev.com/api/your-plugin.php'
);
```

Then deploy a corresponding API endpoint to your update server (see `server/` folder for examples).

## Update Server Endpoint

Each endpoint returns a JSON object:

```json
{
    "slug":         "your-plugin-folder",
    "version":      "1.2.3",
    "download_url": "https://updates.alexanderwagnerdev.com/zips/your-plugin.zip",
    "details_url":  "https://alexanderwagnerdev.com/plugins/your-plugin",
    "changelog":    "<h4>1.2.3</h4><ul><li>Fixed XYZ</li></ul>",
    "tested":       "6.9",
    "requires":     "6.0",
    "requires_php": "7.4"
}
```

## Folder Structure

```
wp-plugins-updater/
├── wp-plugins-updater.php       <- Main plugin file
├── includes/
│   └── class-awdev-updater.php  <- Reusable updater class
└── server/
    └── darkadmin.php            <- Example API endpoint (deploy to update server)
```

## License

GPLv2 or later
