<?php

function university_post_types()
{
    // event post type
    register_post_type('event', array(
        'show_in_rest' => true,
        'capability_type' => 'event',
        'map_meta_cap' => true,
        'supports' => array('title', 'editor', 'excerpt'),
        'rewrite' => array('slug' => 'events'),
        'has_archive' => true,
        'public' => true,
        'show_in_rest' => true,
        'labels' => array(
            'name' => 'Events',
            'add_new_item' => 'Add New Event',
            'edit_item' => 'Edit Event',
            'all_items' => 'All Events',
            'singular_name' => "Event"
        ),
        'menu_icon' => 'dashicons-calendar-alt'
    ));

    //program post type
    register_post_type('program', array(
        'show_in_rest' => true,
        //removed editor next to title below in the supports array beacause i don't want in the programs dashboard exist two block of text
        //i added one custom so i do not need the default one
        'supports' => array('title'),
        'rewrite' => array('slug' => 'programs'),
        'has_archive' => true,
        'public' => true,
        'show_in_rest' => true,
        'labels' => array(
            'name' => 'Programs',
            'add_new_item' => 'Add New Program',
            'edit_item' => 'Edit Programs',
            'all_items' => 'All Programs',
            'singular_name' => "Program"
        ),
        'menu_icon' => 'dashicons-awards'
    ));
    //professors post type
    register_post_type(
        'professor',
        array(
            'show_in_rest' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
            'public' => true,
            'show_in_rest' => true,
            'labels' => array(
                'name' => 'Professors',
                'add_new_item' => 'Add New Professor',
                'edit_item' => 'Edit Professors',
                'all_items' => 'All Professors',
                'singular_name' => "Professor"
            ),
            'menu_icon' => 'dashicons-welcome-learn-more'
        )
    );

    // campus post type
    register_post_type('campus', array(
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'excerpt'),
        'rewrite' => array('slug' => 'campuses'),
        'has_archive' => true,
        'public' => true,
        'show_in_rest' => true,
        'labels' => array(
            'name' => 'Campuses',
            'add_new_item' => 'Add New Campus',
            'edit_item' => 'Edit Campus',
            'all_items' => 'All Campuses',
            'singular_name' => "Campus"
        ),
        'menu_icon' => 'dashicons-location-alt'
    ));
}

add_action('init', 'university_post_types');
