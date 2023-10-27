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
        $mainPageHook = add_menu_page('Words To Filter', 'Word Filter', 'manage_options', 'ourwordfilter', array($this, 'wordFilterPage'), 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xMCAyMEMxNS41MjI5IDIwIDIwIDE1LjUyMjkgMjAgMTBDMjAgNC40NzcxNCAxNS41MjI5IDAgMTAgMEM0LjQ3NzE0IDAgMCA0LjQ3NzE0IDAgMTBDMCAxNS41MjI5IDQuNDc3MTQgMjAgMTAgMjBaTTExLjk5IDcuNDQ2NjZMMTAuMDc4MSAxLjU2MjVMOC4xNjYyNiA3LjQ0NjY2SDEuOTc5MjhMNi45ODQ2NSAxMS4wODMzTDUuMDcyNzUgMTYuOTY3NEwxMC4wNzgxIDEzLjMzMDhMMTUuMDgzNSAxNi45Njc0TDEzLjE3MTYgMTEuMDgzM0wxOC4xNzcgNy40NDY2NkgxMS45OVoiIGZpbGw9IiNGRkRGOEQiLz4KPC9zdmc+Cg==', 100);
        //the main menu and the firts submenu line lead to the exact same page.I just added submenu to have different text.
        add_submenu_page('ourwordfilter', 'Words To Filter', 'Words List', 'manage_options', 'ourwordfilter', array($this, 'wordFilterPage'));
        add_submenu_page('ourwordfilter', 'Word Filter Options', 'Options', 'manage_options', 'word-filter-options', array($this, 'optionsSubPage'));
        //load sutom css just for admin subpage
        add_action("load-{$mainPageHook}", array($this, 'mainPageAssets'));
    }


    function mainPageAssets()
    {
        //load css hook
        wp_enqueue_style('filterAdminCss', plugin_dir_url(__FILE__) . 'styles.css');
    }

    function handleForm()
    {
        if (wp_verify_nonce($_POST['ourNonce'], 'saveFilterWords') and current_user_can('manage_options')) {
            update_option('plugin_words_to_filter', sanitize_text_field($_POST['plugin_words_to_filter'])); ?>
            <div class="updated">
                <p>Your filtered words were saved.</p>
            </div>
        <?php
        } else { ?>
            <div class="error">
                <p>Sorry you do not have the permission to perform that action.</p>
            </div>
        <?php }
    }

    function wordFilterPage()
    { ?>
        <div class="wrap">
            <h1>Word Filter</h1>
            <?php
            if (isset($_POST['justsubmitted']) == 'true') {
                $this->handleForm();
            }
            ?>
            <form method="POST">
                <input type="hidden" name="justsubmitted" value="true">
                <!-- this line is to prevent csrf attacks in submitted form -->
                <?php wp_nonce_field('saveFilterWords', 'ourNonce'); ?>
                <label for="plugin_words_to_filter">
                    <p>Enter a <strong>comma separated</strong> list of words to filter from your site's content.</p>
                </label>
                <div class="word-filter__flex-container">
                    <textarea name="plugin_words_to_filter" id="plugin_words_to_filter" placeholder="bad, mean, awful, horrible"><?php echo esc_textarea(get_option('plugin_words_to_filter')); ?>
                    </textarea>
                </div>
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
            </form>
        </div>
    <?php }

    function optionsSubPage()
    { ?>
        Hello from the options page
<?php }
}

$ourWordFilterPlugin = new OurWordFilterPlugin();
