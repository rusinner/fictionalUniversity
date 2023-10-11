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
                'postType' => get_post_type(),
                'image' => get_the_post_thumbnail_url(0, 'professorLandscape')

            ));
        }
        if (get_post_type() == 'program') {
            array_push($results['programs'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type(),
                'id' => get_the_id()

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
            $event_date = new DateTime(get_field('event_date'));
            $description = null;
            if (has_excerpt()) {
                $description = get_the_excerpt();
            } else {
                $description = wp_trim_words(get_the_content(), 6);
            }
            array_push($results['events'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type(),
                'month' => $event_date->format('M'),
                'day' => $event_date->format('d'),
                'description' => $description


            ));
        }
    }


    //query about finding relations between terms and not the exact search term.For example: if you search for a program title
    //the query appearing also the professor teaches it without the word itself exists in the text. 
    //Also the if statement is only beacause if my search term is nonsense the WP Query returns all professors so i check if there are programs before returning
    if ($results['programs']) {
        $programsMetaQuery = array('relation' => 'OR');

        foreach ($results['programs'] as $item) {
            array_push($programsMetaQuery, array(
                'key' => 'related_programs',
                'compare' => 'LIKE',
                'value' => '"' . $item['id'] . '"'
            ));
        }

        $programRelationshipQuery = new WP_Query(array(
            'post_type' => 'professor',
            'meta_query' => $programsMetaQuery
        ));

        while ($programRelationshipQuery->have_posts()) {
            $programRelationshipQuery->the_post();

            if (get_post_type() == 'professor') {
                array_push($results['professors'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'postType' => get_post_type(),
                    'image' => get_the_post_thumbnail_url(0, 'professorLandscape')

                ));
            }
        }

        //remove duplicate results from the two already existing queries
        //array unique deletes duplicate values but creates index.So array_values deletes that index we don't want
        $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
    }


    return $results;
}
