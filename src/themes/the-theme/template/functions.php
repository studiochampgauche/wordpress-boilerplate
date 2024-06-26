<?php

	class helloChamp{

		function __construct(){
            
			if(!class_exists('scg')) return;
			
            /*
            * str_replace your return when you use scg::field() or StudioChampGauche\Utils\Field::get();
            *
            * StudioChampGauche\Utils\Field::replace(['{MAIN_EMAIL}'], [scg::field('contact_email_main')]);
			*
			* You need to use ::replace Method in acf/init hook if you play with acf Field
            */
            
            
            /*
            * Set defaults when you call scg::cpt() or StudioChampGauche\Utils\CustomPostType::get();
            */
            StudioChampGauche\Utils\CustomPostType::default('posts_per_page', -1);
            StudioChampGauche\Utils\CustomPostType::default('paged', 1);
            
            
            /*
            * Set defaults when you call scg::menu() or StudioChampGauche\Utils\Menu::get();
            */
            StudioChampGauche\Utils\Menu::default('container', null);
            StudioChampGauche\Utils\Menu::default('items_wrap', '<ul>%3$s</ul>');
            
            
            /*
            * Set defaults when you call scg::button() or StudioChampGauche\Utils\Button::get();
            *
            * StudioChampGauche\Utils\Button::default('text', 'x');
            * StudioChampGauche\Utils\Button::default('href', 'x');
            * StudioChampGauche\Utils\Button::default('class', 'x');
            * StudioChampGauche\Utils\Button::default('attr', 'x');
            * StudioChampGauche\Utils\Button::default('before', 'x');
            * StudioChampGauche\Utils\Button::default('after', 'x');
            */
            
            
            /*
            * Set defaults when you call scg::source() or StudioChampGauche\Utils\Source::get();
            *
            * StudioChampGauche\Utils\Source::default('base', '/');
            * StudioChampGauche\Utils\Source::default('url', true);
            */
			
			
			/*
			* Modify SCG Part in wp_head
			*
			add_filter('scg_wp_head', function($wp_heads){
				
				/*
				* Add Open Graph article:section and article:tag on Post Type 'post'
				*
				
				if(is_singular(['post'])){
				
					$wp_heads['og_article_section'] = <meta property="article:section" content="" />';
					
					$wp_heads['og_article_tag'] = <meta property="article:tag" content="" />';
					
				}
			
			 	return $wp_heads;
			
			});
            */
            
            /*
			* Preload
			add_action('wp_head', function(){
				echo '<link rel="preload" as="font" href="" type="font/woff2" crossorigin />';
				echo '<link rel="preload" as="image" href="">';
				
			}, 3);
			*/
			
			
			/*
			* Enqueue Scripts
			*/
			add_action('wp_enqueue_scripts', function(){

				wp_localize_script('scg-main', 'SYSTEM', [
					'ajaxurl' => admin_url('admin-ajax.php')
				]);
				
			}, 11);
			
			
			/*
			* Shot events on init
			*/
			add_action('init', function(){
				
				/*
				* Remove default posts, pages
				*/
				$_ids = $ids = [1, 2, 3];
				if(isset($_ids) && $_ids){

					foreach($ids as $id){

						if(!get_post_status($id)) continue;

						wp_delete_post($id, true);

					}

					$phpFile = str_replace('$_ids = $ids = [1, 2, 3];', '$ids = [];', file_get_contents(__FILE__));

					file_put_contents(__FILE__, $phpFile);

				}
				
			});

		}

	}

	new helloChamp();
?>