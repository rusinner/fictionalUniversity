<?php

// Plugin Name: Our Test Plugin 
// Description: A truly amazing plugin
// Version:1.0 
// Author: rusinner 
// Author URI: https://github.com/rusinner
// Text Domain: wcpdomain
// Domain Path: /languages


class WordCountAndTimePlugin
{
    function __construct()
    {
        //menu content
        add_action('admin_menu', array($this, 'adminPage'));
        add_action('admin_init', array($this, 'settings'));
        add_filter('the_content', array($this, 'ifWrap'));
        add_action('init', array($this, 'languages'));
    }

    //loads the existing languages.middle argument deprecatd so false
    function languages()
    {
        load_plugin_textdomain('wcpdomain', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }


    // function that checks if it is a blog post and if the plugin checkboxes are checked and calls the fucntion that returns the content with the counters
    function ifWrap($content)
    {
        if (
            is_main_query() and is_single() and
            (get_option('wcp_wordcount', '1') or
                get_option('wcp_charactercount', '1') or
                get_option('wcp_readtime', 1))
        ) {
            return $this->createHTML($content);
        }
        return $content;
    }

    // function that actually projecting the counters on HTML
    function createHTML($content)
    {
        $html = '<h3>' . esc_html(get_option('wcp_headline', ' Post Statistics')) . '</h3><p>';

        //get word count once because both wordcount and read time will need it.
        if (get_option('wcp_wordcount', '1') or get_option('wcp_readtime', '1')) {
            $wordCount = str_word_count(strip_tags($content));
        }
        //__() function is for translate purposes
        if (get_option('wcp_wordcount', '1')) {
            $html .= esc_html__('This post has', 'wcpdomain') . ' ' . $wordCount . ' ' . esc_html__('words', 'wcpdomain') . '<br>';
        }
        if (get_option('wcp_charactercount', '1')) {
            $html .= 'This post has ' . strlen(strip_tags($content)) . ' characters.<br>';
        }
        if (get_option('wcp_readtime', '1')) {
            $html .= round($wordCount / 225) < 1 ? 'This post will take less than 1 minute to read.' : 'This post will take about ' . round($wordCount / 225) . ' minute(s) to read.<br>';
        }

        $html .= '</p>';

        if (get_option('wcp_location', '0') == '0') {
            return $html . $content;
        }
        return $content . $html;
    }

    function settings()
    {
        //add html section
        add_settings_section('wcp_first_section', null, null, 'word-count-settings-page');
        // build html input field
        add_settings_field('wcp_location', 'Display Location', array($this, 'locationHTML'), 'word-count-settings-page', 'wcp_first_section');
        // use register setting function one for each menu option in plugin settings here instead of sanitize_text_field a added an array for extra protection 
        //the values must be specifically 0 or 1 so i created a custom sanitize function
        register_setting('wordcountplugin', 'wcp_location', array('sanitize_callback' => array($this, 'sanitizeLocation'), 'default' => '0'));


        add_settings_field('wcp_headline', 'Headline Text', array($this, 'headlineHTML'), 'word-count-settings-page', 'wcp_first_section');
        register_setting('wordcountplugin', 'wcp_headline', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics'));


        // in these three a added one more argument because i want to be part of the reusable function checkboxHTML
        add_settings_field('wcp_wordcount', 'Word Count', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_first_section', array('theName' => 'wcp_wordcount'));
        register_setting('wordcountplugin', 'wcp_wordcount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

        add_settings_field('wcp_charactercount', 'Character Count', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_first_section', array('theName' => 'wcp_charactercount'));
        register_setting('wordcountplugin', 'wcp_charactercount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

        add_settings_field('wcp_readtime', 'Read Time', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_first_section', array('theName' => 'wcp_readtime'));
        register_setting('wordcountplugin', 'wcp_readtime', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
    }

    function sanitizeLocation($input)
    {
        if ($input != '0' and $input != '1') {
            add_settings_error('wcp_location', 'wcp_location_error', 'Dispaly location must be either beginning or end');
            return get_option('wcp_location');
        }
        return $input;
    }



    function checkboxHTML($args)
    { ?>
        <input type="checkbox" name="<?php echo $args['theName']; ?>" value="1" <?php checked(get_option($args['theName']), '1'); ?>>
    <?php }


    function headlineHTML()
    { ?>
        <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')); ?>">
    <?php }

    function locationHTML()
    { ?>
        <select name="wcp_location">
            <option value="0" <?php selected(get_option('wcp_location'), '0'); ?>>Beginning of post</option>
            <option value="1" <?php selected(get_option('wcp_location'), '1'); ?>>End of post</option>
        </select>
    <?php }

    // arguments are:Page Title name,Menu name,permissions(now is only for admin),slug (must be unique),function that generates the html
    function adminPage()
    {
        add_options_page('Word Count Settings', __('Word Count', 'wcpdomain'), 'manage_options', 'word-count-settings-page', array($this, 'ourHTML'));
    }

    function ourHTML()
    { ?>
        <div class="wrap">
            <h1>Word Count Settings</h1>
            <form action="options.php" method="POST">
                <?php
                settings_fields('wordcountplugin');
                do_settings_sections('word-count-settings-page');
                submit_button();
                ?>
            </form>
        </div>
<?php }
}

$newWordAndTimePlugin = new WordCountAndTimePlugin();
