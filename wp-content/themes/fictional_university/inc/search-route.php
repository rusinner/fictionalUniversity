<?php


//hook the fuction that creates custom rest api response
add_action('rest_api_init', 'universityRegisterSearch');


//create the function
function universityRegisterSearch()
{
    //v1 in url is the version number it is part of the namespace and it is good practice to include one 
    //especially when you make big changes at the json
    register_rest_route('university/v1', 'search', array(
        'methods' => WP_REST_SERVER::READABLE, // normally the methods name could be just 'GET' but it is a safest way to work GET method at any browser
        'callback' => 'universitySearchResults'
    ));
}


function universitySearchResults($data)
{
    $mainQuery = new WP_Query(
        array(
            'post_type' => array('post', 'page', 'professor', 'event', 'program', 'campus'),
            //s is for search and sanitize_text_field is generic wordpress to prevent malicious code in searchline
            's' => sanitize_text_field($data['term'])
        )
    );


    //choose only data i want from professors array
    $results = array(
        'generalInfo'  => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array(),
        'campuses' => array()
    );

    while ($mainQuery->have_posts()) {
        $mainQuery->the_post();
        if (get_post_type() == 'post' or get_post_type() == 'page') {
            array_push($results['generalInfo'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type(),
                'authorName' => get_the_author()

            ));
        }
        if (get_post_type() == 'professor') {
            array_push($results['professors'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type()

            ));
        }
        if (get_post_type() == 'program') {
            array_push($results['programs'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type()

            ));
        }
        if (get_post_type() == 'campus') {
            array_push($results['campuses'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type()

            ));
        }
        if (get_post_type() == 'event') {
            array_push($results['events'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type(),


            ));
        }
    }

    return
        $results;
}
