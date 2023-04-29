# Our WordPress Project
A WordPress Project that allow you start theming super fast.

## Ready to use
- Three.js
- Barba.js
- Granim.js
- GSAP

## Plugins included
- ACF Pro
- Polylang Free
- Champ Gauche Helper

## Guide
1. Clone the repo and install the Node Modules

```
cd your_project_root_path
&& git clone https://github.com/studiochampgauche/wordpress-project-starter.git .
&& npm i
```

2. In `src` directory, duplicate wp-config-sample.php to wp-config.php and setup it

3. On your project root, add a directory named `dist` and put inner the WordPress production files

4. If is your first time setup, run the command `gulp prod or gulp prod-watch`. If isn't, just run `gulp` command for watching.

5. If you play with the Helper Plugin, you need to activate the required plugin `ACF PRO`

## Requirements

- NodeJS (tested with nodeJS 18.9.0, 18.12.0 and 18.16.0)


## Champ Gauche Helper Plugin

We have built this plugin around ACF Pro for fix basic things. Manage quickly:

- SEO
- SVG Acceptance
- Theme locations
- Maintenance Mode
- ACF Options Pages
- Gutenberg Presence
- Basic Styles Presence
- Front-end Top Bar Presence
- Source Code Clearance
- Admin Panel Appearance
- Scripts before/after opened/closed head/body tags

### Languages
- Default: French
- Existing Translations: en_CA, en_US, en_GB

### Static PHP Functions (Documentation is coming soon)
The plugin provide some PHP functions too. Put a look at the end of file `plugins/scg-helper/helper.php`

You can call it like that:
```
scg::menu($theme_location, ['add_mobile_bars' => 2]);
```

## The Theme
The Theme is built for work with Barba transitions and GSAP ScrollSmoother/ScrollTrigger on start.