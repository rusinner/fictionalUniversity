<?php

// Plugin Name: Our Word Filter Plugin 
// Description: Replaces a list of words.
// Version:1.0 
// Author: rusinner 
// Author URI: https://github.com/rusinner


if (!defined('ABSPATH')) exit; //exit if accessed directly


class OurWordFilterPlugin
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'ourMenu'));
    }

    function ourMenu()
    {
        //the number at the end is th appearing order so i just put a large one to be at the bottom of the menu
        add_menu_page('Words To Filter', 'Word Filter', 'manage_options', 'ourwordfilter', array($this, 'wordFilterPage'), 'dashicons-smiley', 100);
        //the main menu and the firts submenu line lead to the exact same page.I just added submenu to have different text.
        add_submenu_page('ourwordfilter', 'Words To Filter', 'Words List', 'manage_options', 'ourwordfilter', array($this, 'wordFilterPage'));
        add_submenu_page('ourwordfilter', 'Word Filter Options', 'Options', 'manage_options', 'word-filter-options', array($this, 'optionsSubPage'));
    }

    function wordFilterPage()
    { ?>
        Hello
    <?php }

    function optionsSubPage()
    { ?>
        Hello from the options page
<?php }
}

$ourWordFilterPlugin = new OurWordFilterPlugin();
