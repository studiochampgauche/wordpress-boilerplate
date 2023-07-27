# Our WordPress Project
A WordPress Project that allow us start theming super fast.

## Ready Libraries
- Three.js
- Barba.js
- Granim.js
- GSAP

## Plugins included
- ACF Pro
- Champ Gauche Helper


## Champ Gauche Helper Plugin
Handle WordPress with ACF. Manage quickly:

- SEO
- Cache Busting
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
- Save ACF in JSON (https://www.advancedcustomfields.com/resources/local-json/)

### Languages
- Default: French
- Existing Translations: en_CA, en_US, en_GB

## How it work?
1. Install your Node Modules in `src > built`
2. Put the WordPress Production Files on root
3. In `src` directory, duplicate wp-config-sample.php to wp-config.php and setup it
4. If is the first setup for your project, run `gulp prod-watch` or `gulp prod`. If not, continue watching by only use `gulp`. You can put a look on the Gulp File for more commands
5. Start working

## Structure has changed
Yup! But, why?

We normally place the WordPress Production Files in a folder named "site" on the final server for be it clean, but in a recent WordPress Project in Multisites, we have understand that we have no choice to place the WP Files on the root for setup the Multisite throught Subdomains.

In parallel, we also needed to create a local, test and production environment as usual, except that this time, our WordPress starter had the WP Production Files in a "dist" directory instead to be on the root and that was a problem because when you try to place a local "dist" file to the Test/Prod Server with a FTP system like the one from DreamWeaver, the directory dist will also be placed too.

So we fix this by removing the "dist" folder receiving WP and allowing WP to be placed on root.