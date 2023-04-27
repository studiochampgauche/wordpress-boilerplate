# Our WordPress Project
Start theming fast with this repo:

## Ready to use:
- Three.js
- Barba.js
- Granim.js
- GSAP
- ACF Pro Plugin
- Polylang Plugin
- Champ Gauche Helper Plugin

## Guide
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


## Custom Post Types by Champ Gauche Helper Plugin
This functionality has been removed in version 1.0.1 because ACF has launch this option with ACF 6.1

## Translation added
Since Helper Plugin version 1.0.1, translation has been added. Default language is French and existing translation is en_CA and en_US.