<?php

/*
  Plugin Name: Featured Professor Block Type
  Version: 1.0
  Author: Rusinner
  Author URI: https://www.udemy.com/user/bradschiff/
  Text Domain:featured-professor
  Domain Path: /languages
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once plugin_dir_path(__FILE__) . 'inc/generateProfessorHTML.php';
require_once plugin_dir_path(__FILE__) . 'inc/relatedPostsHTML.php';


class FeaturedProfessor
{
  function __construct()
  {
    add_action('init', [$this, 'onInit']);
    //add custom endpoint to get data so i can use them in frontend at editor side
    add_action('rest_api_init', [$this, 'profHTML']);

    add_filter('the_content', array($this, 'addRelatedPosts'));
  }


  //add posts that the specific professor is being mentioned at the end pf content
  function addRelatedPosts($content)
  {
    if (is_singular('professor') && in_the_loop() && is_main_query()) {
      return $content . relatedPostsHTML(get_the_id());
    }
    return $content;
  }

  function profHTML()
  {
    register_rest_route('featuredProfessor/v1', 'getHTML', array(
      'methods' => WP_REST_SERVER::READABLE,
      'callback' => [$this, 'getProfHTML']
    ));
  }

  function getProfHTML($data)
  {
    return generateProfessorHTML($data['profId']);
  }

  function onInit()
  {
    load_plugin_textdomain('featured-professor', false, dirname(plugin_basename(__FILE__)) . '/languages');

    register_meta('post', 'featuredprofessor', array(
      'show_in_rest' => true,
      'type' => 'number',
      'single' => false

    ));


    wp_register_script('featuredProfessorScript', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-blocks', 'wp-i18n', 'wp-editor'));
    wp_register_style('featuredProfessorStyle', plugin_dir_url(__FILE__) . 'build/index.css');
    // this line is necessary for translations to work in js files
    wp_set_script_translations('featuredProfessorScript', 'featured-professor', plugin_dir_path(__FILE__) . '/languages');

    register_block_type('ourplugin/featured-professor', array(
      'render_callback' => [$this, 'renderCallback'],
      'editor_script' => 'featuredProfessorScript',
      'editor_style' => 'featuredProfessorStyle'
    ));
  }

  function renderCallback($attributes)
  {
    if ($attributes['profId']) {
      wp_enqueue_style('featuredProfessorStyle');
      return generateProfessorHTMl($attributes['profId']);
    } else {
      return null;
    }
  }
}

$featuredProfessor = new FeaturedProfessor();
