<?php
get_header();
pageBanner(array(
    'title' => "Our Campuses",
    'subtitle' => 'We have several conveniently located campuses.'
));
?>
<div class="container container--narrow page-section">
    <div class="acf-map">
        <?php
        while (have_posts()) {
            the_post();
            $mapLocation = get_field('location_map');
        ?>
            <div data-lat="<?php echo $mapLocation['lat']; ?>" data-lng="<?php echo $mapLocation['lng']; ?>" class="marker"></div>
        <?php }
        echo paginate_links();
        ?>
    </div>

</div>
<?php
get_footer();
