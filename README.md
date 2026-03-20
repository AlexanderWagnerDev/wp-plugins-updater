# AWDev Plugins Updater

**Updater for AlexanderWagnerDev plugins — without WordPress.org.**

> Version 0.1.3 | Requires WordPress 6.3+ | Requires PHP 7.4+ | License: GPLv2

---

## What it does

AWDev Plugins Updater integrates seamlessly into the native WordPress update system. Managed plugins appear in the standard **Dashboard > Updates** view and the **Plugins** list — with version badges, one-click updates, and automatic background updates.

---

## Features

- Native WordPress update hook integration (no custom update pages)
- Per-plugin auto-update toggles with instant AJAX save
- Global auto-update on/off switch
- Configurable check interval (1–168 hours, default 6 h)
- One-click manual re-check per plugin
- One-click Update button in the settings table when a new version is available
- "View version details" popup with changelog, requires, tested, author
- Built-in support for DarkAdmin – Dark Mode for Adminpanel
- Translations: de\_DE, de\_AT, en\_US

---

## Built-in managed plugins

| Plugin | API slug |
|---|---|
| AWDev Plugins Updater | `awdev-plugins-updater` |
| DarkAdmin – Dark Mode for Adminpanel | `darkadmin-dark-mode-for-adminpanel` |

---

## Installation

1. Download the latest release ZIP from [GitHub Releases](https://github.com/AlexanderWagnerDev/wp-plugins-updater/releases)
2. Upload via **Plugins > Add New > Upload Plugin** or extract to `wp-content/plugins/awdev-plugins-updater/`
3. Activate the plugin
4. Go to **Settings > AWDev Plugins Updater**

---

## Settings

| Option | Description |
|---|---|
| Auto-Update (all plugins) | Master switch — disabling this prevents all AWDev auto-updates regardless of per-plugin toggles |
| Per-plugin auto-update | Individual toggle per managed plugin |
| Check interval | How often the updater polls the update server (1–168 h) |
| Flush Update Cache | Forces an immediate re-check on next page load |

---

## How updates work

1. AWDev Plugins Updater hooks into `pre_set_site_transient_update_plugins`
2. It fetches version metadata from AWDev Plugins Updater API
3. If a newer version is available it injects an update object into the WordPress transient
4. WordPress handles the download, extraction, and installation natively
5. `upgrader_source_selection` fixes the folder name after extraction if needed

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for the full history.

**0.1.3** — Fix misleading files 
**0.1.2** — Removed error logging calls  
**0.1.1** — Fix rename-into-self crash on flat ZIP extraction, plugins_loaded priority 20, get_plugins() caching  
**0.1.0** — Multiple bug fixes and improvements (see CHANGELOG.md)  
**0.0.8** — Fix auto-update filter null return; fix silent rename failure  

---

## License

GPLv2 or later — see [LICENSE](LICENSE)
