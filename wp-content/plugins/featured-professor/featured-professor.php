<?php

/*
  Plugin Name: Featured Professor Block Type
  Version: 1.0
  Author: Rusinner
  Author URI: https://www.udemy.com/user/bradschiff/
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once plugin_dir_path(__FILE__) . 'inc/generateProfessorHTML.php';

class FeaturedProfessor
{
  function __construct()
  {
    add_action('init', [$this, 'onInit']);
    //add custom endpoint to get data so i can use them in frontend at editor side
    add_action('rest_api_init', [$this, 'profHTML']);
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
    wp_register_script('featuredProfessorScript', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-blocks', 'wp-i18n', 'wp-editor'));
    wp_register_style('featuredProfessorStyle', plugin_dir_url(__FILE__) . 'build/index.css');

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
