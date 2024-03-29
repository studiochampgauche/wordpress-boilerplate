<?php
/*
Plugin Name: Champ Gauche Helper
Description: WordPress Handler
Version: 2.0.0
Author: Studio Champ Gauche
Author URI: https://champgauche.studio
Copyright: Studio Champ Gauche
Text Domain: scg-helper
Domain Path: /langs
*/


if (!defined('ABSPATH')) exit;

class scg{

	private $acf_path;
	
	function __construct(){

		require_once ABSPATH . 'wp-admin/includes/plugin.php';



		if(!class_exists('ACF')) return;

		/*
		* ACF in JSON
		*/
		$this->acf_json();


		/*
		* Init Action
		*/
		add_action('init', function(){

			/*
			* Load Languages
			*/
			load_plugin_textdomain('scg-helper', false, basename(__DIR__) . '/langs/');


			/*
			* Remove Top Bar
			*/
			if(self::field('top_bar') !== 'enable')
				add_filter('show_admin_bar', '__return_false');

		});


		/*
		* Remove / Register Styles and Scripts
		*/
		add_action('wp_enqueue_scripts', function(){

			/*
			* Remove Basics Styles
			*/

			if(self::field('global_styles') !== 'enable')
				wp_dequeue_style('global-styles');
			
			if(self::field('wp_block_library') !== 'enable')
				wp_dequeue_style('wp-block-library');
			
			if(self::field('classic_theme_styles') !== 'enable')
				wp_dequeue_style('classic-theme-styles');
			

			/*
			* Main Style
			*/
			wp_enqueue_style('scg-main', get_bloginfo('stylesheet_directory').'/assets/css/main.min.css?v=' . self::field('files_versioning_style'), null, null, null);


			/*
			* Main Javascript
			*/
			wp_enqueue_script('scg-main', get_bloginfo('stylesheet_directory') .'/assets/js/App.js?v=' . self::field('files_versioning_javascript'), null, null, true);

		}, 10);


		add_filter('script_loader_tag', function($tag, $handle, $src){
			if ( 'scg-main' !== $handle )
				return $tag;

			$tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';

			return $tag;

		} , 10, 3);

		
		/*
		* Clean wp_head
		*/		
		remove_action('wp_head', 'rsd_link');
		remove_action('wp_head', 'wlwmanifest_link');
		remove_action('wp_head', 'wp_generator');
		remove_action('wp_head', 'start_post_rel_link');
		remove_action('wp_head', 'index_rel_link');
		remove_action('wp_head', 'feed_links', 2);
		remove_action('wp_head', 'feed_links_extra', 3);
		remove_action('wp_head', 'adjacent_posts_rel_link');
		remove_action('wp_head', 'rest_output_link_wp_head');
		remove_action('wp_head', 'wp_oembed_add_discovery_links');
		remove_action('wp_head', 'wp_resource_hints', 2);
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_action('wp_head', 'rel_canonical');
		remove_action('wp_head', 'wp_shortlink_wp_head', 10);
		remove_action('template_redirect', 'wp_shortlink_header', 11);


		/*
		* Remove Upload Resizes
		*/
		add_filter('intermediate_image_sizes_advanced', function($size, $metadata){
			return [];
		}, 10, 2);


		/*
		* Allow SVG to be uploaded
		*/
		add_filter('upload_mimes', function($mimes){
			$mimes['svg'] = 'image/svg+xml';
			return $mimes;
		});

		add_filter('wp_check_filetype_and_ext', function( $data, $file, $filename, $mimes) {
			global $wp_version;

			if($wp_version == '4.7' || ((float)$wp_version < 4.7 )) return $data;

			$filetype = wp_check_filetype($filename, $mimes);

			return [
				'ext' => $filetype['ext'],
				'type' => $filetype['type'],
				'proper_filename' => $data['proper_filename']
			];
			
		}, 10, 4);


		/*
		* Maintenance Mode
		*/
		add_action('template_redirect', function(){
			$user = wp_get_current_user();
			$roleArray = $user->roles;
			$userRole = isset($roleArray[0]) ? $roleArray[0] : '';
			if(!is_front_page() && self::field('maintenance') === 'enable' && !in_array($userRole, ['administrator'])){
				
				wp_redirect(home_url());

				exit;
			}
		});

		/*
		* WP HEAD
		*/
		add_action('wp_head', function(){

			if(self::field('seo_management') === 'disable') return;

			$html = '';

			if(
				!self::field('index_se')

				||

				(
					is_author()

					&&

					!self::field('index_se', 'user_' . get_queried_object()->ID)
				)

				||

				(
					(is_tax() || is_tag() || is_category())

					&&

					!self::field('index_se', get_queried_object()->taxonomy . '_' . get_queried_object()->term_id)
				)
			)
				$html .= '<meta name="robots" content="noindex, nofollow">';

			$html .= '<meta charset="'. get_bloginfo('charset') .'">';
			$html .= '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
			$html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">';


			$title = get_the_title() . ' - ' . get_bloginfo('name');
			$og_title = null;
			$description = null;
			$og_description = null;
			$og_image = null;

			/*
			* Manage SEO for Authors and Terms
			*/
			if(is_author()){
				$author = get_queried_object();

				$title = $author->first_name . ' ' . $author->last_name . ' - ' . (self::field('global_seo_title_se') ? self::field('global_seo_title_se') : get_bloginfo('name'));

				if(self::field('title_se', 'user_' . $author->ID))
					$title = self::field('title_se', 'user_' . $author->ID);

				if(self::field('description_se', 'user_' . $author->ID))
					$description = self::field('description_se', 'user_' . $author->ID);

				if(self::field('title_sn', 'user_' . $author->ID))
					$og_title = self::field('title_sn', 'user_' . $author->ID);

				if(self::field('description_sn', 'user_' . $author->ID))
					$og_description = self::field('description_sn', 'user_' . $author->ID);

				if(self::field('image_sn', 'user_' . $author->ID))
					$og_image = self::field('image_sn', 'user_' . $author->ID);

			}
			elseif(is_tax() || is_tag() || is_category()){
				$term = get_queried_object();
				
				$title = $term->name . ' - ' . (self::field('global_seo_title_se') ? self::field('global_seo_title_se') : get_bloginfo('name'));

				if(self::field('title_se', $term->taxonomy . '_' . $term->term_id))
					$title = self::field('title_se', $term->taxonomy . '_' . $term->term_id);

				if(self::field('description_se', $term->taxonomy . '_' . $term->term_id))
					$description = self::field('description_se', $term->taxonomy . '_' . $term->term_id);

				if(self::field('title_sn', $term->taxonomy . '_' . $term->term_id))
					$og_title = self::field('title_sn', $term->taxonomy . '_' . $term->term_id);

				if(self::field('description_sn', $term->taxonomy . '_' . $term->term_id))
					$og_description = self::field('description_sn', $term->taxonomy . '_' . $term->term_id);

				if(self::field('image_sn', $term->taxonomy . '_' . $term->term_id))
					$og_image = self::field('image_sn', $term->taxonomy . '_' . $term->term_id);

			}

			$normal = !is_author() && !is_tax() && !is_tag() && !is_category() ? true : false;
			/*
			* Manage SEO For everything else
			*
			* Title
			*/
			if($normal && !empty(self::field('title_se')))
				$title = self::field('title_se');

			elseif($normal && !empty(self::field('global_seo_title_se')))
				$title = get_the_title() . ' - ' . self::field('global_seo_title_se');


			/*
			* Description
			*/
			if($normal && !empty(self::field('description_se')))
				$description = self::field('description_se');

			elseif($normal && !empty(self::field('global_seo_description_se')))
				$description = self::field('global_seo_description_se');


			/*
			* og:title
			*/
			if($normal && !empty(self::field('title_sn')))
				$og_title = self::field('title_sn');

			elseif($normal && !empty(self::field('global_seo_title_sn')))
				$og_title = self::field('global_seo_title_sn');


			/*
			* og:description
			*/
			if($normal && !empty(self::field('description_sn')))
				$og_description = self::field('description_sn');

			elseif($normal && !empty(self::field('global_seo_description_sn')))
				$og_description = self::field('global_seo_description_sn');


			/*
			* og:image
			*/
			if($normal && !empty(self::field('image_sn')))
				$og_image = self::field('image_sn');
			elseif($normal && !empty(self::field('global_seo_image_sn')))
				$og_image = self::field('global_seo_image_sn');



			$html .= '<title>'. wp_strip_all_tags($title) .'</title>';

			$html .= '<meta property="og:site_name" content="'. get_bloginfo('name') .'">';

			if($description)
				$html .= '<meta name="description" content="'. $description .'">';

			if($og_title)
				$html .= '<meta name="og:title" content="'. $og_title .'">';

			if($og_description)
				$html .= '<meta name="og:description" content="'. $og_description .'">';

			if($og_image)
				$html .= '<meta property="og:image" content="'. $og_image .'">';

			$html .= '<meta property="og:locale" content="'. get_locale() .'">';

			$og_type = '<meta property="og:type" content="website" />';

			if(is_singular('post')){
				global $post;

				$author = $post->post_author;
				$author_posts_url = get_author_posts_url($author);
				$publish_date = get_the_date('Y-m-d');
				$tags = get_the_tags();
				$recap_tags = [];
				if($tags){
					foreach ($tags as $tag) {
						$recap_tags[] = $tag->name;
					}
				}
				$tags = implode(',', $recap_tags);

				$og_type = '<meta property="og:type" content="article" />';
				$og_type .= '<meta property="article:author" content="'. $author_posts_url .'" />';
				$og_type .= '<meta property="article:published_time" content="'. $publish_date .'" />';
				
				if($recap_tags)
					$og_type .= '<meta property="article:tags" content="'. $tags .'" />';

			}	elseif(is_author()){

				$author = get_queried_object();

				$og_type = '<meta property="og:type" content="profile" />';

				if($author->first_name)
					$og_type .= '<meta property="profile:first_name" content="'. $author->first_name .'" />';

				if($author->last_name)
					$og_type .= '<meta property="profile:last_name" content="'. $author->last_name .'" />';


				$og_type .= '<meta property="profile:username" content="'. $author->user_login .'" />';

			}

			$html .= $og_type;

			if(self::field('favicons_seo_internet_explorer'))
				$html .= '<!--[if IE]><link rel="shortcut icon" href="'. self::field('favicons_seo_internet_explorer') .'"><![endif]-->';

			if(self::field('favicons_seo_apple_touch'))
				$html .= '<link rel="apple-touch-icon" sizes="180x180" href="'. self::field('favicons_seo_apple_touch') .'">';

			if(self::field('favicons_seo_all_browsers_and_android'))	
				$html .= '<link rel="icon" sizes="192x192" href="'. self::field('favicons_seo_all_browsers_and_android') .'">';

			if(self::field('favicons_seo_msapplication_tileimage'))
				$html .= '<meta name="msapplication-TileImage" content="'. self::field('favicons_seo_msapplication_tileimage') .'">';

			echo $html;

		}, 1);

		/*
		* On Admin Init
		*/
		add_action('admin_init', function(){

			global $pagenow;


			/*
			* Clean Dashboard
			*/
			if(self::field('clean_dashboard') === 'enable'){
				remove_action('welcome_panel', 'wp_welcome_panel');
				remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
				remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
				remove_meta_box('dashboard_primary', 'dashboard', 'side');
				remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
				remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
				remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
				remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
				remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
				remove_meta_box('dashboard_activity', 'dashboard', 'normal');
				remove_meta_box('woocommerce_dashboard_recent_reviews', 'dashboard', 'normal');
				remove_meta_box('dlm_popular_downloads', 'dashboard', 'normal');
				remove_meta_box('dashboard_site_health', 'dashboard', 'normal');

			}

			if(
				self::field('change_display') === 'enable'

				&&

				!empty(self::field('restrict_scg_tab'))

				&&

				!in_array(wp_get_current_user()->ID, self::field('restrict_scg_tab'))

				&&

				(
					(isset($_GET['page']) && $_GET['page'] === 'scg-settings')

					||

					(isset($_GET['post_type']) && in_array($_GET['post_type'], ['acf-field-group']))

					||

					(isset($_GET['post']) && in_array(get_post_type($_GET['post']), ['acf-field-group']))

					||

					in_array($pagenow, [
						'import.php',
						'export.php',
						'themes.php',
						'plugins.php',
						'theme-editor.php',
						'plugin-editor.php',
					])
				)
			) {

				wp_redirect(admin_url());

				exit;
			}


			if(self::field('gutenberg') !== 'enable')
				add_filter('use_block_editor_for_post_type', '__return_false', 10);


			return;

		});


		/*
		* Admin Bar Menu
		*/
		add_action('admin_bar_menu', function(){

			if(self::field('change_display') === 'enable'){

				global $wp_admin_bar;

				$admin_url = admin_url();

				/*
				* Remove not wanted elements
				*/
				$wp_admin_bar->remove_node('wp-logo');
				$wp_admin_bar->remove_node('site-name');
				$wp_admin_bar->remove_node('comments');
				$wp_admin_bar->remove_node('new-content');


				if(!current_user_can('update_core') || !current_user_can('update_plugins') || !current_user_can('update_themes'))
					$wp_admin_bar->remove_node( 'updates' );


				/*
				* Add Home Site URL
				*/
				$args = array(
					'id' => 'goto-website',
					'title' => get_bloginfo('name'),
					'href' => home_url(),
					'target' => '_blank',
					'meta' => array(
						'class' => 'goto-website'
					)
				);
				$wp_admin_bar->add_node($args);


				/*
				* Add Menus Management
				*/
				$args = array(
					'id' => 'gest-menus',
					'title' => __('Menus', 'scg-helper'),
					'href' => $admin_url . 'nav-menus.php',
					'meta' => array(
						'class' => 'gest-menus'
					)
				);
				if(current_user_can('edit_theme_options') && !empty(self::field('register_nav_menus')))
					$wp_admin_bar->add_node($args);


				/*
				* Add Files Management
				*/
				$args = array(
					'id' => 'gest-files',
					'title' => __('Images et fichiers', 'scg-helper'),
					'href' => $admin_url . 'upload.php',
					'meta' => array(
						'class' => 'gest-files'
					)
				);
				if(current_user_can('upload_files'))
					$wp_admin_bar->add_node($args);


				/*
				* Add Users Management
				*/
				$args = array(
					'id' => 'gest-users-list',
					'title' => __('Utilisateurs', 'scg-helper'),
					'href' => $admin_url . 'users.php',
					'meta' => array(
						'class' => 'gest-users-list'
					)
				);
				if(current_user_can('list_users'))
					$wp_admin_bar->add_node($args);


				/*
				* Add Profile Management
				*/
				$args = array(
					'id' => 'gest-users-profile',
					'title' => __('Profil', 'scg-helper'),
					'href' => $admin_url . 'profile.php',
					'parent' => 'gest-users-list',
					'meta' => array(
						'class' => 'gest-users-profile'
					)
				);
				$wp_admin_bar->add_node($args);


				if(

					(
						current_user_can('edit_theme_options')

						&&

						empty(self::field('restrict_scg_tab'))
					)

					||

					(
						current_user_can('edit_theme_options')

						&&

						!empty(self::field('restrict_scg_tab'))

						&&

						in_array(wp_get_current_user()->ID, self::field('restrict_scg_tab'))
					)
				) {


					/*
					* Move SCG Menu
					*/
					$args = array(
						'id' => 'is-scg',
						'title' => __('SCG', 'scg-helper'),
						'meta' => array(
							'class' => 'is-scg'
						)
					);
					$wp_admin_bar->add_node($args);

					/*
					* Add General Management
					*/
					$args = array(
						'id' => 'is-scg-general',
						'title' => __('Configurations', 'scg-helper'),
						'href' => $admin_url . 'admin.php?page=scg-settings',
						'parent' => 'is-scg',
						'meta' => array(
							'class' => 'is-scg-general'
						)
					);
					$wp_admin_bar->add_node($args);


					/*
					* Add Themes Management
					*/
					$args = array(
						'id' => 'is-scg-themes',
						'title' => __('Thèmes', 'scg-helper'),
						'href' => $admin_url . 'themes.php',
						'parent' => 'is-scg',
						'meta' => array(
							'class' => 'is-scg-themes'
						)
					);
					if(current_user_can('switch_themes'))
						$wp_admin_bar->add_node($args);


					/*
					* Add Theme Editor Management
					*/
					$args = array(
						'id' => 'is-scg-themes-editor',
						'title' => __('Éditeur', 'scg-helper'),
						'href' => $admin_url . 'theme-editor.php',
						'parent' => 'is-scg-themes',
						'meta' => array(
							'class' => 'is-scg-themes-editor'
						)
					);
					if(current_user_can('edit_themes'))
						$wp_admin_bar->add_node($args);


					/*
					* Add Plugins Management
					*/
					$args = array(
						'id' => 'is-scg-plugins',
						'title' => __('Extensions', 'scg-helper'),
						'href' => $admin_url . 'plugins.php',
						'parent' => 'is-scg',
						'meta' => array(
							'class' => 'is-scg-plugins'
						)
					);
					if(current_user_can('activate_plugins'))
						$wp_admin_bar->add_node($args);


					/*
					* Add Plugin Editor Management
					*/
					$args = array(
						'id' => 'is-scg-plugin-editor',
						'title' => __('Éditeur', 'scg-helper'),
						'href' => $admin_url . 'plugin-editor.php',
						'parent' => 'is-scg-plugins',
						'meta' => array(
							'class' => 'is-scg-plugins-editor'
						)
					);
					if(current_user_can('edit_plugins'))
						$wp_admin_bar->add_node($args);


					/*
					* Add ACF PRO Management
					*/
					$args = array(
						'id' => 'is-scg-acf',
						'title' => __('ACF', 'scg-helper'),
						'href' => $admin_url . 'edit.php?post_type=acf-field-group',
						'parent' => 'is-scg',
						'meta' => array(
							'class' => 'is-scg-acf'
						)
					);
					$wp_admin_bar->add_node($args);


					/*
					* Add Import Management
					*/
					$args = array(
						'id' => 'is-scg-import',
						'title' => __('Importer', 'scg-helper'),
						'href' => $admin_url . 'import.php',
						'parent' => 'is-scg',
						'meta' => array(
							'class' => 'is-scg-import'
						)
					);
					if(current_user_can('import'))
						$wp_admin_bar->add_node($args);

					/*
					* Add Export Management
					*/
					$args = array(
						'id' => 'is-scg-export',
						'title' => __('Exporter', 'scg-helper'),
						'href' => $admin_url . 'export.php',
						'parent' => 'is-scg',
						'meta' => array(
							'class' => 'is-scg-export'
						)
					);
					if(current_user_can('export'))
						$wp_admin_bar->add_node($args);

				}

			}

			return;

		}, 99);


		/*
		* Clean Left Menus
		*/
		add_action('admin_menu', function(){

			if(self::field('change_display') === 'enable'){
				/*
				* Clean left menu
				*/
				remove_menu_page('tools.php');
				remove_menu_page('upload.php');
				remove_menu_page('themes.php');
				remove_menu_page('plugins.php');
				remove_menu_page('edit-comments.php');
				remove_menu_page('users.php');
				remove_menu_page('edit.php?post_type=acf-field-group');

				remove_submenu_page('options-general.php', 'options-privacy.php');
				remove_submenu_page('options-general.php', 'options-media.php');
				remove_submenu_page('options-general.php', 'options-writing.php');
				remove_submenu_page('options-general.php', 'options-discussion.php');

			}

			return;

		});

		/*
		* Admin Head
		*/
		add_action('admin_head', function(){
			
			if(self::field('change_display') === 'enable' || !empty(self::field('restrict_scg_tab')))
				echo '<style type="text/css">#toplevel_page_scg-settings{display: none !important;}</style>';

			
			return;

		});


		/*
		* Init
		*/
		add_action('init', function(){

			/*
			* Add Theme Management
			*
			* SCG Management
			*/
			acf_add_options_page([
				'page_title'    => __('Configurations', 'scg-helper'),
				'menu_title'    => __('SCG', 'scg-helper'),
				'menu_slug'     => 'scg-settings',
				'capability'    => 'edit_themes',
				'redirect'      => false
			]);

			/*
			* Theme Tab
			*/
			acf_add_local_field_group([
				'key' => 'group_637141e2601c7',
				'title' => __('Gestion du thème', 'scg-helper'),
				'fields' => [],
				'location' => [
					[
						[
							'param' => 'options_page',
							'operator' => '==',
							'value' => 'scg-settings',
						],
					],
				],
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'seamless',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
				'show_in_rest' => 0,
			]);

			acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_6371502242c21',
				'label' => __('Thème', 'scg-helper'),
				'name' => '',
				'aria-label' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '',
					'class' => '',
					'id' => '',
				],
				'placement' => 'top',
				'endpoint' => 0,
			]);

			acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_637141e24cb06',
				'label' => __('Maintenance', 'scg-helper'),
				'name' => 'maintenance',
				'aria-label' => '',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '33.3333333333',
					'class' => '',
					'id' => '',
				],
				'choices' => [
					'enable' => 'Enable',
					'disable' => 'Disable',
				],
				'default_value' => 'disable',
				'return_format' => 'value',
				'multiple' => 0,
				'allow_null' => 0,
				'ui' => 0,
				'ajax' => 0,
				'placeholder' => '',
			]);

			acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_637168726e495',
				'label' => __('Emplacements de thème', 'scg-helper'),
				'name' => 'register_nav_menus',
				'aria-label' => '',
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '',
					'class' => '',
					'id' => '',
				],
				'layout' => 'block',
				'pagination' => 0,
				'min' => 0,
				'max' => 0,
				'collapsed' => '',
				'button_label' => __('Ajouter un emplacement de thème', 'scg-helper'),
				'rows_per_page' => 20,
				'sub_fields' => [
					[
						'key' => 'field_637168da6e496',
						'label' => __('Nom', 'scg-helper'),
						'name' => 'name',
						'aria-label' => '',
						'type' => 'text',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '50',
							'class' => '',
							'id' => '',
						],
						'default_value' => '',
						'maxlength' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'parent_repeater' => 'field_637168726e495',
					],
					[
						'key' => 'field_637168e56e497',
						'label' => __('Slug', 'scg-helper'),
						'name' => 'slug',
						'aria-label' => '',
						'type' => 'text',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '50',
							'class' => '',
							'id' => '',
						],
						'default_value' => '',
						'maxlength' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'parent_repeater' => 'field_637168726e495',
					],
				],
			]);

			acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_637184da2d215',
				'label' => 'HTML Tags',
				'name' => 'html_tags',
				'aria-label' => '',
				'type' => 'group',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '',
					'class' => '',
					'id' => '',
				],
				'layout' => 'block',
				'sub_fields' => [
					[
						'key' => 'field_637185442d216',
						'label' => __('Après &lt;head&gt;', 'scg-helper'),
						'name' => 'after_open_head',
						'aria-label' => '',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '50',
							'class' => '',
							'id' => '',
						],
						'default_value' => '',
						'maxlength' => '',
						'rows' => 12,
						'placeholder' => '',
						'new_lines' => '',
					],
					[
						'key' => 'field_637185b12d217',
						'label' => __('Avant &lt;/head&gt;', 'scg-helper'),
						'name' => 'before_close_head',
						'aria-label' => '',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '50',
							'class' => '',
							'id' => '',
						],
						'default_value' => '',
						'maxlength' => '',
						'rows' => 12,
						'placeholder' => '',
						'new_lines' => '',
					],
					[
						'key' => 'field_637185f42d219',
						'label' => __('Après &lt;body&gt;', 'scg-helper'),
						'name' => 'after_open_body',
						'aria-label' => '',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '50',
							'class' => '',
							'id' => '',
						],
						'default_value' => '',
						'maxlength' => '',
						'rows' => 12,
						'placeholder' => '',
						'new_lines' => '',
					],
					[
						'key' => 'field_637185f02d218',
						'label' => __('Avant &lt;/body&gt;', 'scg-helper'),
						'name' => 'before_close_body',
						'aria-label' => '',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '50',
							'class' => '',
							'id' => '',
						],
						'default_value' => '',
						'maxlength' => '',
						'rows' => 12,
						'placeholder' => '',
						'new_lines' => '',
					],
				],
			]);

			acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_6371503842c22',
				'label' => __('Cleaner', 'scg-helper'),
				'name' => '',
				'aria-label' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '',
					'class' => '',
					'id' => '',
				],
				'placement' => 'top',
				'endpoint' => 0,
			]);

			acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_637192feggwxh',
				'label' => __('Notice', 'scg-helper'),
				'name' => '',
				'aria-label' => '',
				'type' => 'message',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '',
					'class' => '',
					'id' => '',
				],
				'message' => __('Vous avez besoin d\'enregistrer une fois pour que les configurations fonctionnent.', 'scg-helper'),
				'new_lines' => 'wpautop',
				'esc_html' => 0,
			]);
            

			acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_63715d8300da6',
				'label' => __('Restreindre l\'onglet SCG', 'scg-helper'),
				'name' => 'restrict_scg_tab',
				'aria-label' => '',
				'type' => 'user',
				'instructions' => __('Fonctionne seulement si vous avez configuré "Changer l\'apparence du panneau d\'admin" sur "Activer"', 'scg-helper'),
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '',
					'class' => '',
					'id' => '',
				],
				'role' => '',
				'return_format' => '',
				'multiple' => 1,
				'allow_null' => 1,
			]);

			acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_6371508a42c24',
				'label' => __('Nettoyer le tableau de bord', 'scg-helper'),
				'name' => 'clean_dashboard',
				'aria-label' => '',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '33.3333333333',
					'class' => '',
					'id' => '',
				],
				'choices' => [
					'enable' => __('Activer', 'scg-helper'),
					'disable' => __('Désactiver', 'scg-helper'),
				],
				'default_value' => 'disable',
				'return_format' => '',
				'multiple' => 0,
				'allow_null' => 0,
				'ui' => 0,
				'ajax' => 0,
				'placeholder' => '',
			]);

			acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_637152493e9be',
				'label' => __('Changer l\'apparence du panneau d\'admin', 'scg-helper'),
				'name' => 'change_display',
				'aria-label' => '',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '33.3333333333',
					'class' => '',
					'id' => '',
				],
				'choices' => [
					'enable' => __('Activer', 'scg-helper'),
					'disable' => __('Désactiver', 'scg-helper'),
				],
				'default_value' => 'disable',
				'return_format' => 'value',
				'multiple' => 0,
				'allow_null' => 0,
				'ui' => 0,
				'ajax' => 0,
				'placeholder' => '',
			]);

			acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_6371a10d4fd26',
				'label' => __('Gutenberg', 'scg-helper'),
				'name' => 'gutenberg',
				'aria-label' => '',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '33.3333333333',
					'class' => '',
					'id' => '',
				],
				'choices' => [
					'enable' => __('Activer', 'scg-helper'),
					'disable' => __('Désactiver', 'scg-helper'),
				],
				'default_value' => 'disable',
				'return_format' => 'value',
				'multiple' => 0,
				'allow_null' => 0,
				'ui' => 0,
				'ajax' => 0,
				'placeholder' => '',
			]);

			acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_6371a16c4fd27',
				'label' => 'Front-end Global Styles',
				'name' => 'global_styles',
				'aria-label' => '',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '33.3333333333',
					'class' => '',
					'id' => '',
				],
				'choices' => [
					'enable' => __('Activer', 'scg-helper'),
					'disable' => __('Désactiver', 'scg-helper'),
				],
				'default_value' => 'disable',
				'return_format' => 'value',
				'multiple' => 0,
				'allow_null' => 0,
				'ui' => 0,
				'ajax' => 0,
				'placeholder' => '',
			]);

			acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_6371a1ab4fd28',
				'label' => 'Front-end WP Block Library',
				'name' => 'wp_block_library',
				'aria-label' => '',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '33.3333333333',
					'class' => '',
					'id' => '',
				],
				'choices' => [
					'enable' => __('Activer', 'scg-helper'),
					'disable' => __('Désactiver', 'scg-helper'),
				],
				'default_value' => 'disable',
				'return_format' => 'value',
				'multiple' => 0,
				'allow_null' => 0,
				'ui' => 0,
				'ajax' => 0,
				'placeholder' => '',
			]);

			acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_6371a1c54fd29',
				'label' => 'Front-end Classic Theme Styles',
				'name' => 'classic_theme_styles',
				'aria-label' => '',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '33.3333333333',
					'class' => '',
					'id' => '',
				],
				'choices' => [
					'enable' => __('Activer', 'scg-helper'),
					'disable' => __('Désactiver', 'scg-helper'),
				],
				'default_value' => 'disable',
				'return_format' => 'value',
				'multiple' => 0,
				'allow_null' => 0,
				'ui' => 0,
				'ajax' => 0,
				'placeholder' => '',
			]);

			acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_6371508a42c24fr4',
				'label' => __('Gestion du SEO', 'scg-helper'),
				'name' => 'seo_management',
				'aria-label' => '',
				'type' => 'select',
				'instructions' => __('Désactiver cette option si vous voulez utiliser un plugin de SEO.', 'scg-helper'),
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '33.3333333333',
					'class' => '',
					'id' => '',
				],
				'choices' => [
					'enable' => __('Activer', 'scg-helper'),
					'disable' => __('Désactiver', 'scg-helper'),
				],
				'default_value' => 'enable',
				'return_format' => '',
				'multiple' => 0,
				'allow_null' => 0,
				'ui' => 0,
				'ajax' => 0,
				'placeholder' => ''
			]);

			acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_6371508kuhfrbmrt',
				'label' => __('Barre du haut', 'scg-helper'),
				'name' => 'top_bar',
				'aria-label' => '',
				'type' => 'select',
				'instructions' => __('Front-end seulement', 'scg-helper'),
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '33.3333333333',
					'class' => '',
					'id' => '',
				],
				'choices' => [
					'enable' => __('Activer', 'scg-helper'),
					'disable' => __('Désactiver', 'scg-helper'),
				],
				'default_value' => 'enable',
				'return_format' => '',
				'multiple' => 0,
				'allow_null' => 0,
				'ui' => 0,
				'ajax' => 0,
				'placeholder' => ''
			]);
            
            
            acf_add_local_field([
				'parent' => 'group_637141e2601c7',
				'key' => 'field_6371CACHING259',
				'label' => __('Version des fichiers', 'scg-helper'),
				'name' => 'files_versioning',
				'aria-label' => '',
				'type' => 'group',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => [
					'width' => '50%',
					'class' => '',
					'id' => '',
				],
				'layout' => 'table',
				'sub_fields' => [
					[
                        'key' => 'field_CACHING_STYLE_VERSION',
                        'label' => __('Style', 'scg-helper'),
                        'name' => 'style',
                        'aria-label' => '',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => [
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ],
                        'default_value' => '1.0',
                        'maxlength' => 10,
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                    ],
                    [
                        'key' => 'field_CACHING_JAVASCRIPT_VERSION',
                        'label' => __('JavaScript', 'scg-helper'),
                        'name' => 'javascript',
                        'aria-label' => '',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => [
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ],
                        'default_value' => '1.0',
                        'maxlength' => 10,
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                    ]
				],
			]);
            

			if(self::field('seo_management') !== 'disable') {

				/*
				* SEO Tab
				*/
				$post_types = get_post_types();

				//print_r($post_types);

				$unsets = [
					'post',
					'page',
					'attachment',
					'revision',
					'nav_menu_item',
					'custom_css',
					'customize_changeset',
					'oembed_cache',
					'user_request',
					'wp_block',
					'wp_template',
					'wp_template_part',
					'wp_global_styles',
					'wp_navigation',
					'acf-field',
					'acf-field-group',
					'acf-post-type',
					'acf-taxonomy',
					'acf-field',
				];

				foreach ($unsets as $unset) {
					unset($post_types[$unset]);
				}

				acf_add_local_field([
					'parent' => 'group_637141e2601c7',
					'key' => 'field_637192c94f715',
					'label' => 'SEO',
					'name' => '',
					'aria-label' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'placement' => 'top',
					'endpoint' => 0,
				]);

				acf_add_local_field([
					'parent' => 'group_637141e2601c7',
					'key' => 'field_6371ef026d947',
					'label' => 'Favicons',
					'name' => 'favicons_seo',
					'aria-label' => '',
					'type' => 'group',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'layout' => 'block',
					'sub_fields' => [
						[
							'key' => 'field_6371ef1c6d948',
							'label' => 'Internet Explorer.ico (32x32)',
							'name' => 'internet_explorer',
							'aria-label' => '',
							'type' => 'image',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '50',
								'class' => '',
								'id' => '',
							],
							'return_format' => 'url',
							'library' => 'all',
							'min_width' => 32,
							'min_height' => 32,
							'min_size' => '',
							'max_width' => 32,
							'max_height' => 32,
							'max_size' => '',
							'mime_types' => '.ico',
							'preview_size' => 'medium',
						],
						[
							'key' => 'field_6371ef9c6d949',
							'label' => 'Apple Touch (180x180)',
							'name' => 'apple_touch',
							'aria-label' => '',
							'type' => 'image',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '50',
								'class' => '',
								'id' => '',
							],
							'return_format' => 'url',
							'library' => 'all',
							'min_width' => 180,
							'min_height' => 180,
							'min_size' => '',
							'max_width' => 180,
							'max_height' => 180,
							'max_size' => '',
							'mime_types' => '.jpg,.jpeg,.png',
							'preview_size' => 'medium',
						],
						[
							'key' => 'field_6371f0646d94a',
							'label' => __('Tous les navigateurs et Android (192x192)', 'scg-helper'),
							'name' => 'all_browsers_and_android',
							'aria-label' => '',
							'type' => 'image',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '50',
								'class' => '',
								'id' => '',
							],
							'return_format' => 'url',
							'library' => 'all',
							'min_width' => 192,
							'min_height' => 192,
							'min_size' => '',
							'max_width' => 192,
							'max_height' => 192,
							'max_size' => '',
							'mime_types' => '.jpg,.jpeg,.png',
							'preview_size' => 'medium',
						],
						[
							'key' => 'field_6371f2d86d94b',
							'label' => 'msapplication-TileImage (270x270)',
							'name' => 'msapplication_tileimage',
							'aria-label' => '',
							'type' => 'image',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '50',
								'class' => '',
								'id' => '',
							],
							'return_format' => 'url',
							'library' => 'all',
							'min_width' => 270,
							'min_height' => 270,
							'min_size' => '',
							'max_width' => 270,
							'max_height' => 270,
							'max_size' => '',
							'mime_types' => '.jpg,.jpeg,.png',
							'preview_size' => 'medium',
						],
					],
				]);

				acf_add_local_field([
					'parent' => 'group_637141e2601c7',
					'key' => 'field_637192ff9b17c',
					'label' => __('Notice', 'scg-helper'),
					'name' => '',
					'aria-label' => '',
					'type' => 'message',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '66.6666666667',
						'class' => '',
						'id' => '',
					],
					'message' => __('Utiliser les boites HTML dans l\'onglet "Thème" pour ajouter des scripts d\'analyse.', 'scg-helper'),
					'new_lines' => 'wpautop',
					'esc_html' => 0,
				]);

				acf_add_local_field([
					'parent' => 'group_637141e2601c7',
					'key' => 'field_637195565c37f',
					'label' => __('Globale', 'scg-helper'),
					'name' => 'global_seo',
					'aria-label' => '',
					'type' => 'group',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'layout' => 'block',
					'sub_fields' => [
						[
							'key' => 'field_637195cb5c380',
							'label' => __('Moteurs de recherche', 'scg-helper'),
							'name' => '',
							'aria-label' => '',
							'type' => 'tab',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'placement' => 'top',
							'endpoint' => 0,
						],
						[
							'key' => 'field_637195e95c381',
							'label' => __('Titre', 'scg-helper'),
							'name' => 'title_se',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'default_value' => '',
							'maxlength' => 65,
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						],
						[
							'key' => 'field_637196a45c382',
							'label' => __('Description', 'scg-helper'),
							'name' => 'description_se',
							'aria-label' => '',
							'type' => 'textarea',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'default_value' => '',
							'maxlength' => 300,
							'rows' => '',
							'placeholder' => '',
							'new_lines' => '',
						],
						[
							'key' => 'field_637197225c383',
							'label' => __('Réseaux sociaux', 'scg-helper'),
							'name' => '',
							'aria-label' => '',
							'type' => 'tab',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'placement' => 'top',
							'endpoint' => 0,
						],
						[
							'key' => 'field_637197265c384',
							'label' => __('Titre', 'scg-helper'),
							'name' => 'title_sn',
							'aria-label' => '',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'default_value' => '',
							'maxlength' => 65,
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
						],
						[
							'key' => 'field_6371972d5c385',
							'label' => __('Description', 'scg-helper'),
							'name' => 'description_sn',
							'aria-label' => '',
							'type' => 'textarea',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'default_value' => '',
							'maxlength' => 300,
							'rows' => '',
							'placeholder' => '',
							'new_lines' => '',
						],
						[
							'key' => 'field_6371976e5c386',
							'label' => __('Image', 'scg-helper'),
							'name' => 'image_sn',
							'aria-label' => '',
							'type' => 'image',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => [
								'width' => '',
								'class' => '',
								'id' => '',
							],
							'return_format' => 'url',
							'library' => 'all',
							'min_width' => 1200,
							'min_height' => 630,
							'min_size' => '',
							'max_width' => 1200,
							'max_height' => 630,
							'max_size' => '',
							'mime_types' => '.jpg,.jpg',
							'preview_size' => 'full',
						],
					],
				]);

				acf_add_local_field([
					'parent' => 'group_637141e2601c7',
					'key' => 'field_6371949d5c37e',
					'label' => __('Types de publication', 'scg-helper'),
					'name' => 'post_types_seo',
					'aria-label' => '',
					'type' => 'checkbox',
					'instructions' => __('Ajouter le module de SEO sur ces types de publication. Page, Post, les "Terms" et les auteurs ont le module.', 'scg-helper'),
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'choices' => $post_types,
					'default_value' => [
					],
					'return_format' => 'value',
					'allow_custom' => 0,
					'layout' => 'horizontal',
					'toggle' => 0,
					'save_custom' => 0,
				]);


				/*
				* SEO Module Everywhere
				*/
				$post_types = !empty(self::field('post_types_seo')) ? self::field('post_types_seo') : [];

				$__post_types = null;
				if($post_types){
					foreach ($post_types as $pt) {
						$__post_types[] = [
							[
								'param' => 'post_type',
								'operator' => '==',
								'value' => $pt,
							]
						];
					}
				}

				$__post_types[] = [
					[
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'post',
					]
				];

				$__post_types[] = [
					[
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'page',
					]
				];

				$__post_types[] = [
					[
						'param' => 'taxonomy',
						'operator' => '==',
						'value' => 'all',
					]
				];

				$__post_types[] = [
					[
						'param' => 'user_form',
						'operator' => '==',
						'value' => 'all',
					]
				];

				acf_add_local_field_group([
					'key' => 'group_6371c77346f80',
					'title' => __('Gestion du SEO', 'scg-helper'),
					'fields' => [],
					'location' => $__post_types,
					'menu_order' => 0,
					'position' => 'normal',
					'style' => 'default',
					'label_placement' => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'active' => true,
					'description' => '',
					'show_in_rest' => 0,
				]);

				acf_add_local_field([
					'parent' => 'group_6371c77346f80',
					'key' => 'field_6371c773c2454',
					'label' => __('Moteurs de recherche', 'scg-helper'),
					'name' => '',
					'aria-label' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'placement' => 'top',
					'endpoint' => 0,
				]);

				acf_add_local_field([
					'parent' => 'group_6371c77346f80',
					'key' => 'field_6371c8e9c245e',
					'label' => __('Index', 'scg-helper'),
					'name' => 'index_se',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'message' => '',
					'default_value' => 1,
					'ui' => 0,
					'ui_on_text' => '',
					'ui_off_text' => '',
				]);

				acf_add_local_field([
					'parent' => 'group_6371c77346f80',
					'key' => 'field_6371c813c2458',
					'label' => __('Titre', 'scg-helper'),
					'name' => 'title_se',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'maxlength' => 65,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				]);

				acf_add_local_field([
					'parent' => 'group_6371c77346f80',
					'key' => 'field_6371c86fc2459',
					'label' => __('Description', 'scg-helper'),
					'name' => 'description_se',
					'aria-label' => '',
					'type' => 'textarea',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'maxlength' => 300,
					'rows' => '',
					'placeholder' => '',
					'new_lines' => '',
				]);

				acf_add_local_field([
					'parent' => 'group_6371c77346f80',
					'key' => 'field_6371c8acc245a',
					'label' => __('Réseaux sociaux', 'scg-helper'),
					'name' => '',
					'aria-label' => '',
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'placement' => 'top',
					'endpoint' => 0,
				]);

				acf_add_local_field([
					'parent' => 'group_6371c77346f80',
					'key' => 'field_6371c8afc245b',
					'label' => __('Titre', 'scg-helper'),
					'name' => 'title_sn',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'maxlength' => 65,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				]);

				acf_add_local_field([
					'parent' => 'group_6371c77346f80',
					'key' => 'field_6371c90ac245f',
					'label' => __('Description', 'scg-helper'),
					'name' => 'description_sn',
					'aria-label' => '',
					'type' => 'textarea',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'default_value' => '',
					'maxlength' => 300,
					'rows' => '',
					'placeholder' => '',
					'new_lines' => '',
				]);

				acf_add_local_field([
					'parent' => 'group_6371c77346f80',
					'key' => 'field_6371c8bec245d',
					'label' => __('Image', 'scg-helper'),
					'name' => 'image_sn',
					'aria-label' => '',
					'type' => 'image',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => [
						'width' => '',
						'class' => '',
						'id' => '',
					],
					'return_format' => 'url',
					'library' => 'all',
					'min_width' => 1200,
					'min_height' => 630,
					'min_size' => '',
					'max_width' => 1200,
					'max_height' => 630,
					'max_size' => '',
					'mime_types' => '.jpg,.png',
					'preview_size' => 'full',
				]);
			}


			/*
			* Register Menu Locations
			*/
			$menus = !empty(self::field('register_nav_menus')) ? self::field('register_nav_menus') : [];
			
			if($menus){
				$__menus = [];
				foreach ($menus as $menu) {
					$name = $menu['name'];
					$slug = $menu['slug'];
					$__menus[$slug] = $name;
				}
				register_nav_menus($__menus);
			}
		});

	}


	private function acf_json(){

		$this->acf_path = is_multisite() ? get_stylesheet_directory() . '/datas/acf/' . get_site()->blog_id : get_stylesheet_directory() . '/datas/acf';

		add_action('admin_init', function(){

			/*
			* If ACF JSON Directory not there, create it
			*/

			if(!file_exists($this->acf_path)){
				mkdir($this->acf_path, 0777, true);
				fopen($this->acf_path . '/index.php', 'w');
			}

		});


		/*
		* Save ACF JSON
		*/
		add_filter('acf/settings/save_json', function($path){

			return $this->acf_path;

		});

		add_filter('acf/settings/load_json', function($paths){

			// Remove original path
			unset( $paths[0] );

			// Append our new path
			$paths[] = $this->acf_path;

			return $paths;
		});
	}


	static function inc($file_path = null, $url = false){
		return self::template_directory('inc/' . $file_path, $url);
	}

	static function tp($file_path = null, $url = false){
		return self::template_directory('inc/template-parts/' . $file_path, $url);
	}

	static function assets($file_path = null, $url = false){
		return self::template_directory('assets/' . $file_path, $url);
	}

	static function template_directory($file_path = null, $url = false){
		$directory_path = new scg();
		
		$directory_path = (get_template_directory() === get_stylesheet_directory() ? get_template_directory() : get_stylesheet_directory()) . '/' . $file_path;

		if($url === true)
			$directory_path = (get_template_directory() === get_stylesheet_directory() ? get_template_directory_uri() : get_stylesheet_directory_uri()) . '/' . $file_path;

		return $directory_path;
	}

	static function field($field_slug = null, $id = null){
		if(!class_exists('ACF')) return;
		if($field_slug && $id)
			return get_field($field_slug, $id);

		elseif($field_slug)
			return !empty(get_field($field_slug, 'options')) ? get_field($field_slug, 'options') : get_field($field_slug);


		return;
	}

	static function cpt($post_type = 'post', $args = []){

		$parameters = array(
			'posts_per_page' => -1,
			'paged' => 1
		);

		if(!empty($args)){
			foreach($args as $arg_key => $arg){
				$parameters[$arg_key] = $arg;
			}
		}

		$parameters['post_type'] = $post_type;

		$result = new WP_Query($parameters);


		return $result;
	}

	static function menu($theme_location = null, $args = []){

		$parameters = array( 
			'menu' => '',
			'container' => false,
			'container_class' => '', 
			'container_id' => '', 
			'menu_class' => '',
			'menu_id' => '',
			'echo' => false, 
			'fallback_cb' => 'wp_page_menu', 
			'before' => '', 
			'after' => '', 
			'link_before' => '',
			'link_after' => '', 
			'items_wrap' => '<ul>%3$s</ul>', 
			'item_spacing' => 'preserve',
			'depth' => 0,
			'walker' => ''
		);

		if(!empty($args)){
			foreach($args as $arg_key => $arg){
				$parameters[$arg_key] = $arg;
			}
		}

		if(isset($parameters['add_mobile_bars']) && (int)$parameters['add_mobile_bars'] > 0){

			$html = '<div class="ham-menu">';
				$html .= '<div class="int">';
				for ($i=0; $i < (int)$parameters['add_mobile_bars']; $i++) { 
					$html .= '<span></span>';
				}
				$html .= '</div>';
			$html .= '</div>';

			$parameters['items_wrap'] = $parameters['items_wrap'] . $html;
		}


		$parameters['theme_location'] = $theme_location;


		$result = wp_nav_menu($parameters);


		return $result;
	}

	static function button($text = 'Aucun texte.', $args = []){

		$href = isset($args['href']) && $args['href'] ? $args['href'] : null;
		$class = isset($args['class']) && $args['class'] ? ' '. $args['class'] : null;
		$attr = isset($args['attr']) && $args['attr'] ? ' '. $args['attr'] : null;
		$before = isset($args['before']) && $args['before'] ? $args['before'] : null;
		$after = isset($args['after']) && $args['after'] ? $args['after'] : null;
		$text = $text ? '<span>'. $text .'</span>' : null;

		if($href){
			return '
				<a href="'. $href .'" class="btn'. $class .'"'. $attr .'>

				'. $before . $text . $after .'

				</a>
			';
		} else {
			return '
				<button class="btn'. $class .'"'. $attr .'>

				'. $before . $text . $after .'

				</button>
			';
		}
	}

	static function id($code_base = 'abcdefghijABCDEFGHIJ', $substr = [0, 4]){
		
		$shuffle_code = str_shuffle($code_base);
		$code = substr($shuffle_code, $substr[0], $substr[1]);


		return 'g_id-' . $code;
	}

}

new scg();

?>