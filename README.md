# Our WordPress Project
Start theming fast with this repo:

- barba.js Ready
- GSAP Ready
- ACF Pro Plugin included
- Free Polylang Plugin included
- three.module.js and granim.js are included
- Champ Gauche Helper Plugin included:
	- Manage SEO & favicons
	- Maintenance mode in one click
	- Add menu/theme locations
	- Add ACF Page Options
	- Change Admin Panel Display (optional)
	- Clean Dashboard (optional)
	- Enable/disable Gutenberg
	- Enable/disable Front-end Global Styles
	- Enable/disable Front-end WP Block Library
	- Enable/disable Front-end Classic Theme Styles
	- Add scripts quickly before/after opened/closed head/body tags



# Guide
***1. Clone the repo and install the Node Modules***

```
cd your_project_root_path
&& git clone https://github.com/studiochampgauche/wordpress-project-starter.git .
&& npm i
```

***2. In `src` directory, duplicate wp-config-sample.php to wp-config.php and setup it***

***3. On your project root, add a directory named `dist` and put inner the WordPress production files***

***4. If is your first time setup, run the command `gulp prod or gulp prod-watch`. If isn't, just run `gulp` command for continue watching.***

***5. Activate required `ACF PRO` and `Champ Gauche Helper` plugins***

___

When you add plugins, fonts, images, external css/scss/js, you need to rerun the `gulp prod or gulp prod-watch` command.

#### Concerned directories:
- `src > extensions`
- `src > fonts`
- `src > images`
- `src > scss > inc`
- `src > js > inc`
___

## Custom Post Types by Champ Gauche Helper Plugin
This functionality has been removed in version 1.0.1 because ACF has launch this option with ACF 6.1