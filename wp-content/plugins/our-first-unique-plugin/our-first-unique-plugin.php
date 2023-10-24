<?php

// Plugin Name: Our Test Plugin 
// Description: A truly amazing plugin
// Version:1.0 
// Author: rusinner 
// Author URI: https://github.com/rusinner



// ussually instead of function name we are going to use classes because the name has to be unique and it is difficult to find unique for all cases not to conflict
add_filter('the_content', 'addToEndOfPost');

function addToEndOfPost($content)
{
    if (is_single() && is_main_query()) {
        return $content . '<p>My name is Rusinner</p>';
    }
    return $content;
}
