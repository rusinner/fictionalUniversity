<?php

// Plugin Name: Are You Paying Attention Plugin 
// Description: Give your readers a multiple choice question.
// Version:1.0 
// Author: rusinner 
// Author URI: https://github.com/rusinner


if (!defined('ABSPATH')) exit; //exit if accessed directly

class AreYouPayingAttention
{
    function __construct()
    {
        add_action('enqueue_block_editor_assets', array($this, 'adminAssets'));
    }

    function adminAssets()
    {
        wp_enqueue_script('ournewblocktype', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-blocks', 'wp-element'));
    }
}

$areYouPayingAttention = new AreYouPayingAttention();
