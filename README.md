> [!IMPORTANT]  
> Version 3 is arriving quietly and will work if you download it on v3 branch. Put a look on [project](https://github.com/users/studiochampgauche/projects/1) if you want follow the progress.
>
> ### Change log since v3 is ready
> - We push some functions in the functions.php of v3 branch without make sure that the theme will not crash if the StudioChampGauche class is not found. It's now fixed.
> - We condition the code with is_tag() and is_category(), but we forgot is_tax(). Fixed.
> - When you play with `StudioChampGauche\Utils\Field::replace` and you have an array returned from `StudioChampGauche\Utils\Field::get()` or `scg::field()`, the site crash. Fixed.
> - Add recursive replacements on array returned from scg::field()


# Our WordPress Project
Build immersive 2D or 3D WordPress themes Awwwards in "no time" with our Boilerplate that your clients can manage with ACF.


## Guide
1. Put the WordPress Production Files on root
2. Install your Node Modules in `src > built`
3. In `src` directory, duplicate `wp-config-sample.php` to `wp-config.php` and setup it
4. If is the first setup for your project, run `gulp prod-watch` or `gulp prod` in `src > built`. If not, continue watching by only use `gulp`. You can put a look on the Gulp File for more commands. Like if you want add some Images from files, you can use `gulp images` for compress and send your assets in your theme in the WP Production Files. Same for fonts, use `gulp fonts`.
5. Start working


## Ready Libraries to import
- Pixi.js
- Three.js
- Barba.js
- Granim.js
- GSAP


## Plugins included
- ACF Pro
- Champ Gauche Helper (old version from 2016 but updated sometimes, we'll launch a new 2023 plugin soon with a documentation)


## JavaScript Lifecyle
***1. new Loader()***
From Loader.js, use the Promise for preload your assets and create a Front-end Preloader.

***2. new PageScroller()***
When the Loader is complete, The SmoothScroller/ScrollerTrigger from GSAP is initialized. You can manage it from PageScroller.js.

***3. new PageTransitor()***
Just after the Scroller, we init Barba.js for manage the transition of pages without reloading. Use the onStart() method from PageTransitor.js for remove directly or on a JS Event your Front-End Preloader. Play with other methods for create nice transition when you leaving and entering a page.
