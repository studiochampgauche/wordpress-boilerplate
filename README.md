# Our WordPress Project
Build immersive 2D or 3D WordPress themes in no time that your clients can manage with ACF in just few clicks.


## Guide
1. Install your Node Modules in `src > built`
2. Put the WordPress Production Files on root
3. In `src` directory, duplicate `wp-config-sample.php` to `wp-config.php` and setup it
4. If is the first setup for your project, run `gulp prod-watch` or `gulp prod` in `src > built`. If not, continue watching by only use `gulp`. You can put a look on the Gulp File for more commands
5. Start working


## Ready Libraries to import
- Pixi.js
- Three.js
- Barba.js
- Granim.js
- GSAP


## Plugins included
- ACF Pro
- Champ Gauche Helper


## JavaScript Lifecyle
***1. new Loader()***
From Loader.js, use the Promise for preload your assets and create a Front-end Preloader.

***2. new PageScroller()***
When the Loader is complete, The SmoothScroller/ScrollerTrigger from GSAP is initialized. You can manage it from PageScroller.js.

***3. new PageTransitor()***
Just after the Scroller, we init Barba.js for manage the transition of pages without reloading. Use the onStart() method from PageTransitor.js for remove directly or on a JS Event your Front-End Preloader. Play with other methods for create nice transition when you leaving a page and entering it.