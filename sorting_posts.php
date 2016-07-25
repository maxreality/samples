<?php

function truncate_with_ellipses($str, $len) {
    return strlen($str) > $len ? substr($str,0,$len)."..." : $str;
}

function sort_down($a, $b)
{
    if ($a['views'] == $b['views']) {
        return 0;
    }
    return ($a['views'] > $b['views']) ? -1 : 1;
}

function sorting_posts_sprout($current_cat_id = '', $difficulty = '', $how_to_sort = 'recent', $duration = '', $paged = '1', $type = 'initial_load')
{
    require_once(TEMPLATEPATH . '/version-2/includes/Mobile_Detect.php');
    $device = '';
    $detect = new Mobile_Detect;
    $post_per_page_initial = 6;
    if ($detect->isMobile()) {
        $post_per_page_initial = 6;
        $device = 'mobile';
    }

    if ($detect->isTablet()) {
        $post_per_page_initial = 6;
        $device = 'tablet';
    }
    else {
        $post_per_page = $post_per_page_initial - 1;
    }
    $current_cat_name = single_cat_title("", 0);
    $sub_meta_query = array(
        'relation' => 'AND',
    );
    $meta_query = array(
        'relation' => 'AND',
    );

    switch ($difficulty) {
        case 'diff1':
            $sub_meta_query[] = array(
                'key' => 'project_difficulty',
                'value' => 'Easy',
                'compare' => '=',
            );
            break;
        case 'diff2':
            $sub_meta_query[] = array(
                'key' => 'project_difficulty',
                'value' => 'Moderate',
                'compare' => '=',
            );
            break;
        case 'diff3':
            $sub_meta_query[] = array(
                'key' => 'project_difficulty',
                'value' => 'Hard',
                'compare' => '=',
            );
            break;

    }
    $meta_query[] = $sub_meta_query;
    switch ($duration) {

        case 'dur1':
            $sub_meta_query[] = array(
                'key' => 'project_duration',
                'value' => '1–3 Hours ',
                'compare' => '=',
            );
            break;
        case 'dur2':
            $sub_meta_query[] = array(
                'key' => 'project_duration',
                'value' => '3-8 Hours',
                'compare' => '=',
            );
            break;
        case 'dur3':
            $sub_meta_query[] = array(
                'key' => 'project_duration',
                'value' => '8–16 Hours (A Weekend)',
                'compare' => '=',
            );
            break;
        case 'dur4':
            $sub_meta_query[] = array(
                'key' => 'project_duration',
                'value' => '>16 Hours',
                'compare' => '=',
            );
            break;
    }

    $top_ids = '';
    $ordered = 'date';
    if ($how_to_sort === 'popular') {
        $top_posts = stats_get_csv('postviews', array('days' => 90, 'limit' => -1));
        $top_ids = array();
        usort($top_posts, 'sort_down');
        foreach ($top_posts as $top_post) {
            $top_ids[] = $top_post['post_id'];
        }
        $ordered = 'post__in';
    }
    $meta_query[] = $sub_meta_query;
    $offset = ( $paged - 1 ) * $post_per_page;

    $args=array(
        'tag' => 'sprout-by-hp',
        'posts_per_page' => $post_per_page,
        'paged' => $paged,
    );
    if (!empty($current_cat_id)) $args['cat'] = $current_cat_id;
    $query = new WP_Query($args);
    $counter = 0;
    $ads_counter = 0;
    $cat_link = '';
    $child_cat_length = -1;
    $max_num_pages = $query->max_num_pages;
    $count_posts = $query->post_count;
    $output = '';
    if ($type !== 'load_more') {
        $output .= '<ul class="selected-posts-list" data-max_num_pages="' . $max_num_pages . '">';
    }
    // Add leadboard for additional pages.
    if (isset($paged) && $paged > 1 && $post_per_page > 12) {
        $output .= '<li class="row post_rows"><div class="js-ad" data-size=\'[[728,90],[940,250],[970,90],[970,250],[320,50]]\' data-size-map=\'[[[1000,0],[[728,90],[940,250],[970,90],[970,250]]],[[800,0],[[728,90]]],[[0,0],[[320,50]]]]\' data-pos=\'"btf"\'></div></li>';
    }
    if ($query->have_posts()) {
        while ($query->have_posts())  : $query->the_post();
            $child_cat = array();
            $parent_cat = array();
            $parent_id = array();
            $red_cat_name = '';
            if ($counter == 0) {
                $output .= '<li class="row post_rows"> <ul>';
            }
            $counter++;

            $output .= '<li class="post col-lg-4 col-md-4 col-sm-6 col-xs-12';
            if (( ( $ads_counter + 1 ) == $count_posts) and ( $count_posts > 2 )) {
                $output .= ' before-ads';
            }
            $post_id = get_the_ID();
            $output .= '">';
            $output .= '<div class="gradient-wrapper"><div class="gradient_animation"><a href="';
            $link = get_the_permalink();
            $output .= $link;
            $output .= '">';
            $output .= '</a></div>';
            $output .= '<div class="final_gradient"><a href="';
            $link = get_the_permalink();
            $output .= $link;
            $output .= '">';
            $output .= '</a></div>';
            $output .= '<a href="';
            $link = get_the_permalink();
            $output .= $link;
            $output .= '">';
            $args = array(
                'resize' => '370,240',
            );
            $url = wp_get_attachment_image(get_post_thumbnail_id($post_id), 'project-thumb');
            $re = "/^(.*? src=\")(.*?)(\".*)$/m";
            preg_match_all($re, $url, $matches);
            $str = $matches[2][0];
            $photon = jetpack_photon_url($str, $args);
            $output .= '<img src="' . $photon . '" alt="thumbnail">';
            $output .= '</a>';
            $post_duration = get_post_meta($post_id, 'project_duration');
            $post_difficulty = get_post_meta($post_id, 'project_difficulty');
            $post_video = get_post_meta($post_id, 'ga_youtube_embed');
            $post_flag = get_post_meta($post_id, 'flag_taxonomy');

            $red_cat_name = '';
            $cat_link = '';
            if ('post' == get_post_type()) {
                $post_display_category = get_post_meta($post_id, 'display_category');

                if (!empty($post_display_category[0])) {
                    $red_cat_name = get_tag(intval($post_display_category[0]))->name;
                    $cat_link = get_tag_link($post_display_category[0]);
                } else {
                    if ($the_tags = get_the_tags()) {
                        $the_tag = $the_tags[0]; //TODO: be smarter here.  Should probably get the tag with most things
                        $red_cat_name = $the_tag->name;
                        $cat_link = get_tag_link($the_tag->term_id);
                    }
                }
            } elseif (!empty($post_flag[0])) {
                $red_cat_name = get_cat_name(intval($post_flag[0]));
                $cat_link = get_category_link($post_flag[0]) . '?post_type=projects';
            } else {
                $post_categories = get_the_category();
                foreach ($post_categories as $post_category) {
                    if (!empty($current_cat_id)) {
                        if ($post_category->parent == $current_cat_id) {
                            $child_cat[] = $post_category->name;
                        }
                    } else {
                        if ($post_category->category_parent == 0) {
                            $parent_cat[] = $post_category->name;
                            $parent_id[] = $post_category->term_id;
                        }
                    }
                }

                if (!empty($current_cat_id)) {
                    $child_cat_length = count($child_cat);
                    $child_cat_length--;
                    $check_parrent = get_category_parents($current_cat_id, false);
                    $check_parrent_counter = substr_count($check_parrent, '/');
                    if ($child_cat_length > 0) {
                        $red_cat_name = $child_cat[0];
                    } elseif ($check_parrent_counter > 1) {
                        $red_cat_name = '';
                    } else {
                        $find_sub_cats = get_the_category($post_id);
                        foreach ($find_sub_cats as $find_sub_cat) {
                            if ($find_sub_cat->parent != 0) {
                                $child_cat[] = $find_sub_cat->name;
                                $child_id[] = $find_sub_cat->term_id;
                            }
                        }
                        $child_cat_length = count($child_cat);
                        $child_cat_length--;
                        if ($child_cat_length > 0) {
                            $red_cat_name = $child_cat[0];
                        } else {
                            foreach ($find_sub_cats as $find_sub_cat) {
                                $child_cat[] = $find_sub_cat->name;
                                $child_id[] = $find_sub_cat->term_id;
                            }
                            $child_cat_length = count($child_cat);
                            $child_cat_length--;
                            $red_cat_name = $child_cat[0];
                        }
                    }
                    $cat_link = get_category_link($child_id[0]) . '';
                } else {
                    $parent_cat_length = count($parent_cat);
                    $parent_cat_length--;
                    $red_cat_name = $parent_cat[0];
                    $cat_link = get_category_link($parent_id[0]) . '';
                }
            }
            if (empty($red_cat_name)) {
                $red_cat_name = $post_category->name;
            }
            $red_car_id = get_cat_ID($red_cat_name);
            $red_cat_name = htmlspecialchars_decode($red_cat_name);
            $cat_length = iconv_strlen($red_cat_name, 'UTF-8');
            if ($cat_length > 13) {
                $red_cat_name = substr($red_cat_name, 0, 13) . '...';
            }
            $output .= '<div class="filter-display-wrapper">';
            if (!empty($red_cat_name)) {
                $output .= '<div class="red-box-category">';
                $output .= '<p><a href="';
                $output .= $cat_link;
                if ('post' == get_post_type()) {
                    $output .= '">#';
                } else {
                    $output .= '"><span class="fa fa-wrench"></span>';
                }
                $output .= $red_cat_name;
                $output .= '</a></p>';
            }
            if (!empty($post_video[0])) {
                $output .= '<div class="videoblock"><a href="';
                $link = get_the_permalink();
                $output .= $link;
                $output .= '">';
                $output .= '';
                $output .= '<span class="video fa fa-video-camera"></span>';
                $output .= '</a></div>';
            }
            $output .= '</div>';
            $difficulty_counter = 0;
            $duration_counter = 0;
            if (!empty($post_difficulty[0])) {
                switch ($post_difficulty[0]) {
                    case 'Easy':
                        $difficulty_counter = 1;
                        break;
                    case 'Moderate':
                        $difficulty_counter = 2;
                        break;
                    case 'Hard':
                        $difficulty_counter = 3;
                        break;
                }
            }
            if (!empty($post_duration[0])) {
                switch ($post_duration[0]) {
                    case '1–3 Hours ':
                        $duration_counter = 1;
                        break;
                    case '3-8 Hours':
                        $duration_counter = 2;
                        break;
                    case '8–16 Hours (A Weekend)':
                        $duration_counter = 3;
                        break;
                    case '>16 Hours':
                        $duration_counter = 4;
                        break;
                }
            }

            $output .= '<div class="difficulty-lvl">';
            while ($difficulty_counter > 0) {
                $output .= '<span class="difficulty-level-image fa fa-wrench"></span>';
                $difficulty_counter--;
            }
            $output .= '</div>';
            $output .= '<div class="duration-lvl">';

            while ($duration_counter > 0) {
                $output .= '<span class="duration-level-image fa fa-clock-o"></span>';
                $duration_counter--;
            }
            $output .= '</div>';
            $output .= '</div>';
            $excerpt = get_the_excerpt();
            if (!has_excerpt($post_id)) {
                $excerpt = winwar_first_sentence($excerpt);
            }
            $output .= '<p class="excerpt trans"><a href="';
            $link = get_the_permalink();
            $output .= $link;
            $output .= '">';
            /** strip_shortcodes did not seem to be working here for contextly - maybe it isn't r
            egistered as a shortcode at this point.  I am duplicating the strip_shortcode call
            here from WPSEO_Utils.  We should really figure out how to make strip_shortcodes work.
             */
            $output .= truncate_with_ellipses(preg_replace( '`\[[^\]]+\]`s', '', $excerpt ), 240);
            $output .= '</a>';
            $output .= '</p>';
            $output .= '</div>';
            $output .= '<h2><a href="';
            $link = get_the_permalink();
            $output .= $link;
            $output .= '">';
            $post_title = get_the_title();
            $output .= truncate_with_ellipses($post_title, 90);
            $output .= '</a></h2>';
            $output .= '</li>';

            if (($counter == 3) and ($device != 'tablet') and ($device != 'mobile')) {
                $output .= '</ul> </li>';
                $counter = 0;
            }
            if (($counter == 2) and ($device == 'tablet')) {
                $output .= '</ul> </li>';
                $counter = 0;
            }
            if (($counter == 1) and ($device == 'mobile')) {
                $output .= '</ul> </li>';
                $counter = 0;
            }
            if ( ( $ads_counter == 1 ) and ( $post_per_page == $post_per_page_initial - 1 ) and ( $paged == 1 ) ) {
                if (($counter == 0) and ( ($device == 'mobile') or ($device == 'tablet') )) {
                    $output .= '<li class="row post_rows"> <ul>';
                }
                $output .= '<li class="post col-lg-4 col-md-4 col-sm-6 col-xs-12 own_ads';
                if ( $count_posts <= 2 ) {
                    $output .= ' before-ads';
                }
                $output .= '">';
                $output .= '<div class="own">';
                $output .= '<p id="ads-title">Advertisement</p>';
                $output .= '<div class="home-ads">';
                $output .= '<div class="js-ad" data-size=\'[[300,250]]\' data-pos=\'"btf"\'></div>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</li>';
                $counter++;
                if (($counter == 3) and ($device != 'tablet') and ($device != 'mobile')) {
                    $output .= '</ul> </li>';
                    $counter = 0;
                }
                if (($counter == 2) and ($device == 'tablet')) {
                    $output .= '</ul> </li>';
                    $counter = 0;
                }
                if (($counter == 1) and ($device == 'mobile')) {
                    $output .= '</ul> </li>';
                    $counter = 0;
                }
            }
            $ads_counter++;
        endwhile;
        if (($counter == 1) and ($device != 'mobile')) {
            $output .= '</ul> </li>';
        }
        if (($ads_counter == $post_per_page) and ($ads_counter == ($post_per_page_initial - 1))) {
            $output .= '</ul> </li>';
        }
        do_action('custom_page_hook', $query);
        wp_reset_query();
    } else {
        if (!empty($current_cat_id)) {
            $current_cat_name = get_cat_name($current_cat_id);
        } else {
            $current_cat_name = "All projects";
        }
        $output .= '<div class="error_message">';
        $output .= '<p>Darn: we haven\'t created any projects like this for <span class="current_cat_name">' . $current_cat_name . '</span> (yet).</p>';
        $output .= '<span>But keep browsing!</span>';
        $output .= '</div>';
    }
    if ($type !== 'load_more') {
        $output .= '</ul>';
    }

    if ($max_num_pages > 1 && $type !== 'load_more') {
        //$output .= '<p id="pbd-alp-load-posts" class="row"><a href="javascript:void(0);">More</a><i class="fa fa-spinner fa-pulse more-button-spinner"></i></p>';
    }

    echo $output;
}

function get_sproutgrid_with_ajax()
{
    $current_cat_id = $_POST['cat'];
    $difficulty = $_POST['diff'];
    $how_to_sort = $_POST['sort'];
    $duration = $_POST['dur'];
    $type = $_POST['type'];
    $paged = !empty($_POST['paged']) ? $_POST['paged'] : 1;

    sorting_posts_sprout($current_cat_id, $difficulty, $how_to_sort, $duration, $paged, $type);

    die();
}

add_action('wp_ajax_sorting_posts_sprout', 'get_sproutgrid_with_ajax');
add_action('wp_ajax_nopriv_sorting_posts_sprout', 'get_sproutgrid_with_ajax');

function sorting_posts_home($current_cat_id = '', $difficulty = '', $how_to_sort = 'recent', $duration = '', $paged = '1', $type = 'initial_load')
{ 
    require_once(TEMPLATEPATH . '/version-2/includes/Mobile_Detect.php');
    $device = '';
    $detect = new Mobile_Detect;
    $post_per_page_initial = 18;
    if ($detect->isMobile()) {
        $post_per_page_initial = 21;
        $device = 'mobile';
        $post_per_page = $post_per_page_initial;
    }

    if ($detect->isTablet()) {
        $post_per_page_initial = 12;
        $device = 'tablet';
        $post_per_page = $post_per_page_initial - 1;
    }
    else {
        $post_per_page = $post_per_page_initial - 1;
    }
    $current_cat_name = single_cat_title("", 0);
    $sub_meta_query = array(
        'relation' => 'AND',
    );
    $meta_query = array(
        'relation' => 'AND',
    );

    switch ($difficulty) {
        case 'diff1':
            $sub_meta_query[] = array(
                'key' => 'project_difficulty',
                'value' => 'Easy',
                'compare' => '=',
            );
            break;
        case 'diff2':
            $sub_meta_query[] = array(
                'key' => 'project_difficulty',
                'value' => 'Moderate',
                'compare' => '=',
            );
            break;
        case 'diff3':
            $sub_meta_query[] = array(
                'key' => 'project_difficulty',
                'value' => 'Hard',
                'compare' => '=',
            );
            break;

    }
    $meta_query[] = $sub_meta_query;
    switch ($duration) {

        case 'dur1':
            $sub_meta_query[] = array(
                'key' => 'project_duration',
                'value' => '1–3 Hours ',
                'compare' => '=',
            );
            break;
        case 'dur2':
            $sub_meta_query[] = array(
                'key' => 'project_duration',
                'value' => '3-8 Hours',
                'compare' => '=',
            );
            break;
        case 'dur3':
            $sub_meta_query[] = array(
                'key' => 'project_duration',
                'value' => '8–16 Hours (A Weekend)',
                'compare' => '=',
            );
            break;
        case 'dur4':
            $sub_meta_query[] = array(
                'key' => 'project_duration',
                'value' => '>16 Hours',
                'compare' => '=',
            );
            break;
    }

    $top_ids = '';
    $ordered = 'date';
    if ($how_to_sort === 'popular') {
        $top_posts = stats_get_csv('postviews', array('days' => 90, 'limit' => -1));
        $top_ids = array();
        usort($top_posts, 'sort_down');
        foreach ($top_posts as $top_post) {
            $top_ids[] = $top_post['post_id'];
        }
        $ordered = 'post__in';
    }
    $meta_query[] = $sub_meta_query;
    $offset = ( $paged - 1 ) * $post_per_page;

    $args = array(
        'post_type' => array('post', 'projects',),
        'meta_query' => $meta_query,
        'posts_per_page' => $post_per_page,
        'page' => $paged,
        'offset'     =>  $offset,
        'orderby' => $ordered,
        'post__in' => $top_ids,
        'post_status' => 'publish',
        'tag__not_in' => '4172',
    );
    if (!empty($current_cat_id)) $args['cat'] = $current_cat_id;
    $query = new WP_Query($args);
    $counter = 0;
    $ads_counter = 0;
    $cat_link = '';
    $child_cat_length = -1;
    $max_num_pages = $query->max_num_pages;
    $count_posts = $query->post_count;
    $output = '';
    if ($type !== 'load_more') {
        $output .= '<ul class="selected-posts-list" data-max_num_pages="' . $max_num_pages . '">';
    }
    // Add leadboard for additional pages.
    if (isset($paged) && $paged > 1) {
        $output .= '<li class="row post_rows"><div class="js-ad" data-size=\'[[728,90],[940,250],[970,90],[970,250],[320,50]]\' data-size-map=\'[[[1000,0],[[728,90],[940,250],[970,90],[970,250]]],[[728,0],[[728,90]]],[[0,0],[[320,50]]]]\' data-pos=\'"btf"\'></div></li>';
    }
    $output .= '<li class="row post_rows"> <ul>';
    if ($query->have_posts()) {
        while ($query->have_posts())  : $query->the_post();
            $child_cat = array();
            $parent_cat = array();
            $parent_id = array();
            $red_cat_name = '';
            $counter++;

            $output .= '<li class="post col-lg-4 col-md-4 col-sm-6 col-xs-12';
            if (( ( $ads_counter + 1 ) == $count_posts) and ( $count_posts > 2 )) {
                $output .= ' before-ads';
            }
            $post_id = get_the_ID();
            $output .= '">';
            $output .= '<div class="gradient-wrapper"><div class="gradient_animation"><a href="';
            $link = get_the_permalink();
            $output .= $link;
            $output .= '">';
            $output .= '</a></div>';
            $output .= '<div class="final_gradient"><a href="';
            $link = get_the_permalink();
            $output .= $link;
            $output .= '">';
            $output .= '</a></div>';
            $output .= '<a href="';
            $link = get_the_permalink();
            $output .= $link;
            $output .= '">';
            $args = array(
                'resize' => '370,240',
            );
            $url = wp_get_attachment_image(get_post_thumbnail_id($post_id), 'project-thumb');
            $re = "/^(.*? src=\")(.*?)(\".*)$/m";
            preg_match_all($re, $url, $matches);
            $str = $matches[2][0];
            $photon = jetpack_photon_url($str, $args);
            $output .= '<img src="' . $photon . '" alt="thumbnail">';
            $output .= '</a>';
            $post_duration = get_post_meta($post_id, 'project_duration');
            $post_difficulty = get_post_meta($post_id, 'project_difficulty');
            $post_video = get_post_meta($post_id, 'ga_youtube_embed');
            $post_flag = get_post_meta($post_id, 'flag_taxonomy');

            $red_cat_name = '';
            $cat_link = '';
            if ('post' == get_post_type()) {
                $post_display_category = get_post_meta($post_id, 'display_category');

                if (!empty($post_display_category[0])) {
                    $red_cat_name = get_tag(intval($post_display_category[0]))->name;
                    $cat_link = get_tag_link($post_display_category[0]);
                } else {
                    if ($the_tags = get_the_tags()) {
                        $the_tag = $the_tags[0]; //TODO: be smarter here.  Should probably get the tag with most things
                        $red_cat_name = $the_tag->name;
                        $cat_link = get_tag_link($the_tag->term_id);
                    }
                }
            } elseif (!empty($post_flag[0])) {
                $red_cat_name = get_cat_name(intval($post_flag[0]));
                $cat_link = get_category_link($post_flag[0]) . '?post_type=projects';
            } else {
                $post_categories = get_the_category();
                foreach ($post_categories as $post_category) {
                    if (!empty($current_cat_id)) {
                        if ($post_category->parent == $current_cat_id) {
                            $child_cat[] = $post_category->name;
                        }
                    } else {
                        if ($post_category->category_parent == 0) {
                            $parent_cat[] = $post_category->name;
                            $parent_id[] = $post_category->term_id;
                        }
                    }
                }

                if (!empty($current_cat_id)) {
                    $child_cat_length = count($child_cat);
                    $child_cat_length--;
                    $check_parrent = get_category_parents($current_cat_id, false);
                    $check_parrent_counter = substr_count($check_parrent, '/');
                    if ($child_cat_length > 0) {
                        $red_cat_name = $child_cat[0];
                    } elseif ($check_parrent_counter > 1) {
                        $red_cat_name = '';
                    } else {
                        $find_sub_cats = get_the_category($post_id);
                        foreach ($find_sub_cats as $find_sub_cat) {
                            if ($find_sub_cat->parent != 0) {
                                $child_cat[] = $find_sub_cat->name;
                                $child_id[] = $find_sub_cat->term_id;
                            }
                        }
                        $child_cat_length = count($child_cat);
                        $child_cat_length--;
                        if ($child_cat_length > 0) {
                            $red_cat_name = $child_cat[0];
                        } else {
                            foreach ($find_sub_cats as $find_sub_cat) {
                                $child_cat[] = $find_sub_cat->name;
                                $child_id[] = $find_sub_cat->term_id;
                            }
                            $child_cat_length = count($child_cat);
                            $child_cat_length--;
                            $red_cat_name = $child_cat[0];
                        }
                    }
                    $cat_link = get_category_link($child_id[0]) . '';
                } else {
                    $parent_cat_length = count($parent_cat);
                    $parent_cat_length--;
                    $red_cat_name = $parent_cat[0];
                    $cat_link = get_category_link($parent_id[0]) . '';
                }
            }
            if (empty($red_cat_name)) {
                $red_cat_name = $post_category->name;
            }
            $red_car_id = get_cat_ID($red_cat_name);
            $red_cat_name = htmlspecialchars_decode($red_cat_name);
            $cat_length = iconv_strlen($red_cat_name, 'UTF-8');
            if ($cat_length > 13) {
                $red_cat_name = substr($red_cat_name, 0, 13) . '...';
            }
            $output .= '<div class="filter-display-wrapper">';
            if (!empty($red_cat_name)) {
                $output .= '<div class="red-box-category">';
                $output .= '<p><a href="';
                $output .= $cat_link;
                if ('post' == get_post_type()) {
                    $output .= '">#';
                } else {
                    $output .= '"><span class="fa fa-wrench"></span>';
                }
                $output .= $red_cat_name;
                $output .= '</a></p>';
            }
            if (!empty($post_video[0])) {
                $output .= '<div class="videoblock"><a href="';
                $link = get_the_permalink();
                $output .= $link;
                $output .= '">';
                $output .= '';
                $output .= '<span class="video fa fa-video-camera"></span>';
                $output .= '</a></div>';
            }
            $output .= '</div>';
            $difficulty_counter = 0;
            $duration_counter = 0;
            if (!empty($post_difficulty[0])) {
                switch ($post_difficulty[0]) {
                    case 'Easy':
                        $difficulty_counter = 1;
                        break;
                    case 'Moderate':
                        $difficulty_counter = 2;
                        break;
                    case 'Hard':
                        $difficulty_counter = 3;
                        break;
                }
            }
            if (!empty($post_duration[0])) {
                switch ($post_duration[0]) {
                    case '1–3 Hours ':
                        $duration_counter = 1;
                        break;
                    case '3-8 Hours':
                        $duration_counter = 2;
                        break;
                    case '8–16 Hours (A Weekend)':
                        $duration_counter = 3;
                        break;
                    case '>16 Hours':
                        $duration_counter = 4;
                        break;
                }
            }

            $output .= '<div class="difficulty-lvl">';
            while ($difficulty_counter > 0) {
                $output .= '<span class="difficulty-level-image fa fa-wrench"></span>';
                $difficulty_counter--;
            }
            $output .= '</div>';
            $output .= '<div class="duration-lvl">';

            while ($duration_counter > 0) {
                $output .= '<span class="duration-level-image fa fa-clock-o"></span>';
                $duration_counter--;
            }
            $output .= '</div>';
            $output .= '</div>';
            $excerpt = get_the_excerpt();
            if (!has_excerpt($post_id)) {
                $excerpt = winwar_first_sentence($excerpt);
            }
            $output .= '<p class="excerpt trans"><a href="';
            $link = get_the_permalink();
            $output .= $link;
            $output .= '">';
            /** strip_shortcodes did not seem to be working here for contextly - maybe it isn't r
            egistered as a shortcode at this point.  I am duplicating the strip_shortcode call
            here from WPSEO_Utils.  We should really figure out how to make strip_shortcodes work.
             */
            $output .= truncate_with_ellipses(preg_replace( '`\[[^\]]+\]`s', '', $excerpt ), 240);
            $output .= '</a>';
            $output .= '</p>';
            $output .= '</div>';

            if(get_field("sponsored_content_label")) {
              $output .= '<div class="home-post-card">';
              $output .= '<span class="home-post-card-sponsor">SPONSORED BY ' . get_field("sponsored_content_label") . '</span>';
              $output .= '<h2><a href="';
              $link = get_the_permalink();
              $output .= $link;
              $output .= '">';
              $post_title = get_the_title();
              $output .= truncate_with_ellipses($post_title, 90);
              $output .= '</a></h2></div>';
            } else {
              $output .= '<h2><a href="';
              $link = get_the_permalink();
              $output .= $link;
              $output .= '">';
              $post_title = get_the_title();
              $output .= truncate_with_ellipses($post_title, 90);
              $output .= '</a></h2>';
            }
            $output .= '</li>';
            if ( ( $ads_counter == 1 && $device != 'tablet') || ($ads_counter == 0 && $device == 'tablet') and ( $post_per_page == $post_per_page_initial - 1 ) ) {
                $output .= '<li class="post col-lg-4 col-md-4 col-sm-6 col-xs-12 own_ads';
                if ( $count_posts <= 2 ) {
                    $output .= ' before-ads';
                }
                $output .= '">';
                $output .= '<div class="own">';
                $output .= '<p id="ads-title">Advertisement</p>';
                $output .= '<div class="home-ads">';
                $output .= '<div class="js-ad" data-size=\'[[300,250]]\' data-pos=\'"btf"\'></div>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</li>';
                $counter++;
            }
            $ads_counter++;
        endwhile;
        // Output MAKER SHED PANEL
        $output .= '<li class="ads shed-row-li"><div class="shed-row">';
        $output .= make_shopify_featured_products_slider_home( 'row-fluid' );
        $output .= '</div></li>';
        // End Projects list
        $output .= '</ul> </li>';
        do_action('custom_page_hook', $query);
        wp_reset_query();
    } else {
        if (!empty($current_cat_id)) {
            $current_cat_name = get_cat_name($current_cat_id);
        } else {
            $current_cat_name = "All projects";
        }
        $output .= '<div class="error_message">';
        $output .= '<p>Darn: we haven\'t created any projects like this for <span class="current_cat_name">' . $current_cat_name . '</span> (yet).</p>';
        $output .= '<span>But keep browsing!</span>';
        $output .= '</div>';
    }
    if ($type !== 'load_more') {
        $output .= '</ul>';
    }
    if ($max_num_pages > 1 && $type !== 'load_more') {
        $output .= '<p id="pbd-alp-load-posts" class="row"><a href="javascript:void(0);">More</a><i class="fa fa-spinner fa-pulse more-button-spinner"></i></p>';
    }

    echo $output;
}

function get_homegrid_with_ajax()
{
    $current_cat_id = $_POST['cat'];
    $difficulty = $_POST['diff'];
    $how_to_sort = $_POST['sort'];
    $duration = $_POST['dur'];
    $type = $_POST['type'];
    $paged = !empty($_POST['paged']) ? $_POST['paged'] : 1;

    sorting_posts_home($current_cat_id, $difficulty, $how_to_sort, $duration, $paged, $type);

    die();
}

add_action('wp_ajax_sorting_posts_home', 'get_homegrid_with_ajax');
add_action('wp_ajax_nopriv_sorting_posts_home', 'get_homegrid_with_ajax');

function sorting_posts($current_cat_id = '', $difficulty = '', $how_to_sort = 'recent', $duration = '', $paged = '1', $type = 'initial_load')
{
    require_once(TEMPLATEPATH . '/version-2/includes/Mobile_Detect.php');
    $device = '';
    $detect = new Mobile_Detect;
    $post_per_page_initial = 18;
    if ($detect->isMobile()) {
        $post_per_page_initial = 18;
        $device = 'mobile';
        $post_per_page = $post_per_page_initial;
    }

    if ($detect->isTablet()) {
        $post_per_page_initial = 12;
        $device = 'tablet';
        $post_per_page = $post_per_page_initial - 1;
    }
    else {
        $post_per_page = $post_per_page_initial - 1;
    }
    $current_cat_name = single_cat_title("", 0);
    $sub_meta_query = array(
        'relation' => 'AND',
    );
    $meta_query = array(
        'relation' => 'AND',
    );

    switch ($difficulty) {
        case 'diff1':
            $sub_meta_query[] = array(
                'key' => 'project_difficulty',
                'value' => 'Easy',
                'compare' => '=',
            );
            break;
        case 'diff2':
            $sub_meta_query[] = array(
                'key' => 'project_difficulty',
                'value' => 'Moderate',
                'compare' => '=',
            );
            break;
        case 'diff3':
            $sub_meta_query[] = array(
                'key' => 'project_difficulty',
                'value' => 'Hard',
                'compare' => '=',
            );
            break;

    }
    $meta_query[] = $sub_meta_query;
    switch ($duration) {

        case 'dur1':
            $sub_meta_query[] = array(
                'key' => 'project_duration',
                'value' => '1–3 Hours ',
                'compare' => '=',
            );
            break;
        case 'dur2':
            $sub_meta_query[] = array(
                'key' => 'project_duration',
                'value' => '3-8 Hours',
                'compare' => '=',
            );
            break;
        case 'dur3':
            $sub_meta_query[] = array(
                'key' => 'project_duration',
                'value' => '8–16 Hours (A Weekend)',
                'compare' => '=',
            );
            break;
        case 'dur4':
            $sub_meta_query[] = array(
                'key' => 'project_duration',
                'value' => '>16 Hours',
                'compare' => '=',
            );
            break;
    }

    $top_ids = '';
    $ordered = 'date';
    if ($how_to_sort === 'popular') {
        $top_posts = stats_get_csv('postviews', array('days' => 90, 'limit' => -1));
        $top_ids = array();
        usort($top_posts, 'sort_down');
        foreach ($top_posts as $top_post) {
            $top_ids[] = $top_post['post_id'];
        }
        $ordered = 'post__in';
    }
    $meta_query[] = $sub_meta_query;
    $offset = ( $paged - 1 ) * $post_per_page;

    $args = array(
        'post_type' => 'projects',
        'meta_query' => $meta_query,
        'posts_per_page' => $post_per_page,
        'page' => $paged,
        'offset'     =>  $offset,
        'orderby' => $ordered,
        'post__in' => $top_ids,
        'post_status' => 'publish',
        'category__not_in' => array(25624, 12, 8, 24794, 13, 1),
    );
    if (!empty($current_cat_id)) $args['cat'] = $current_cat_id;
    $query = new WP_Query($args);
    $counter = 0;
    $ads_counter = 0;
    $cat_link = '';
    $child_cat_length = -1;
    $max_num_pages = $query->max_num_pages;
    $count_posts = $query->post_count;
    $output = '';
    if ($type !== 'load_more') {
        $output .= '<ul class="selected-posts-list" data-max_num_pages="' . $max_num_pages . '">';
    }
    if ($query->have_posts()) {
        while ($query->have_posts())  : $query->the_post();
            $child_cat = array();
            $parent_cat = array();
            $parent_id = array();
            $red_cat_name = '';
            // Add leadboard for additional pages.
            if (isset($paged) && $paged > 1  && $ads_counter == 0) {
                $output .= '<li class="row post_rows ad"><div class="js-ad" data-size=\'[[728,90],[940,250],[970,90],[970,250],[320,50]]\' data-size-map=\'[[[1000,0],[[728,90],[940,250],[970,90],[970,250]]],[[800,0],[[728,90]]],[[0,0],[[320,50]]]]\' data-pos=\'"btf"\'></div></li>';
            }
            if ($counter == 0) {
                $output .= '<li class="row post_rows"> <ul>';
            }
            $counter++;

            $output .= '<li class="post col-lg-4 col-md-4 col-sm-6 col-xs-12';
            if (( ( $ads_counter + 1 ) == $count_posts) and ( $count_posts > 2 )) {
                $output .= ' before-ads';
            }
            $post_id = get_the_ID();
            $output .= '">';
            $output .= '<div class="gradient-wrapper"><div class="gradient_animation"><a href="';
            $link = get_the_permalink();
            $output .= $link;
            $output .= '">';
            $output .= '</a></div>';
            $output .= '<div class="final_gradient"><a href="';
            $link = get_the_permalink();
            $output .= $link;
            $output .= '">';
            $output .= '</a></div>';
            $output .= '<a href="';
            $link = get_the_permalink();
            $output .= $link;
            $output .= '">';
            $args = array(
                'resize' => '370,240',
            );
            $url = wp_get_attachment_image(get_post_thumbnail_id($post_id), 'project-thumb');
            $re = "/^(.*? src=\")(.*?)(\".*)$/m";
            preg_match_all($re, $url, $matches);
            $str = $matches[2][0];
            $photon = jetpack_photon_url($str, $args);
            $output .= '<img src="' . $photon . '" alt="thumbnail">';
            $output .= '</a>';
            $post_duration = get_post_meta($post_id, 'project_duration');
            $post_difficulty = get_post_meta($post_id, 'project_difficulty');
            $post_video = get_post_meta($post_id, 'ga_youtube_embed');
            $post_flag = get_post_meta($post_id, 'flag_taxonomy');

            if (!empty($post_flag[0])) {
                $red_cat_name = get_cat_name(intval($post_flag[0]));
                $cat_link = get_category_link($post_flag[0]);
            } else {
                $post_categories = get_the_category();
                foreach ($post_categories as $post_category) {
                    if (!empty($current_cat_id)) {

                        if ($post_category->parent == $current_cat_id) {
                            $child_cat[] = $post_category->name;
                            $child_id[] = $post_category->term_id;
                        }
                    } else {
                        if ($post_category->category_parent == 0) {
                            $parent_cat[] = $post_category->name;
                            $parent_id[] = $post_category->term_id;
                        }
                    }
                }

                if (!empty($current_cat_id)) {
                    $child_cat_length = count($child_cat);
                    $child_cat_length--;
                    $check_parrent = get_category_parents($current_cat_id, false);
                    $check_parrent_counter = substr_count($check_parrent, '/');
                    if ($child_cat_length > 0) {
                        $red_cat_name = $child_cat[0];
                    } elseif ($check_parrent_counter > 1) {
                        $red_cat_name = '';
                    } else {
                        $find_sub_cats = get_the_category($post_id);
                        foreach ($find_sub_cats as $find_sub_cat) {
                            if ($find_sub_cat->parent != 0) {
                                $child_cat[] = $find_sub_cat->name;
                                $child_id[] = $find_sub_cat->term_id;
                            }
                        }
                        $child_cat_length = count($child_cat);
                        $child_cat_length--;
                        if ($child_cat_length > 0) {
                            $red_cat_name = $child_cat[0];
                        } else {
                            foreach ($find_sub_cats as $find_sub_cat) {
                                $child_cat[] = $find_sub_cat->name;
                                $child_id[] = $find_sub_cat->term_id;
                            }
                            $child_cat_length = count($child_cat);
                            $child_cat_length--;
                            $red_cat_name = $child_cat[0];
                        }
                    }
                    $cat_link = get_category_link($child_id[0]);
                } else {
                    $parent_cat_length = count($parent_cat);
                    $parent_cat_length--;
                    $red_cat_name = $parent_cat[0];
                    $cat_link = get_category_link($parent_id[0]);
                }
            }
            if (empty($red_cat_name)) {
                $red_cat_name = $post_category->name;
            }
            $red_car_id = get_cat_ID($red_cat_name);
            $cat_link = get_category_link($red_car_id);
            $red_cat_name = htmlspecialchars_decode($red_cat_name);
            $cat_length = iconv_strlen($red_cat_name, 'UTF-8');
            if ($cat_length > 13) {
                $red_cat_name = substr($red_cat_name, 0, 13) . '...';
            }
            $output .= '<div class="filter-display-wrapper">';
            if (!empty($red_cat_name)) {
                $output .= '<div class="red-box-category">';
                $output .= '<p><a href="';
                $output .= $cat_link;
                $output .= '"><span class="fa fa-wrench"></span>';
                $output .= $red_cat_name;
                $output .= '</a></p>';
                $output .= '</div>';
            }
            if (!empty($post_video[0])) {
                $output .= '<div class="videoblock"><a href="';
                $link = get_the_permalink();
                $output .= $link;
                $output .= '">';
                $output .= '';
                $output .= '<span class="video fa fa-video-camera"></span>';
                $output .= '</a></div>';
            }
            $difficulty_counter = 0;
            $duration_counter = 0;
            if (!empty($post_difficulty[0])) {
                switch ($post_difficulty[0]) {
                    case 'Easy':
                        $difficulty_counter = 1;
                        break;
                    case 'Moderate':
                        $difficulty_counter = 2;
                        break;
                    case 'Hard':
                        $difficulty_counter = 3;
                        break;
                }
            }
            if (!empty($post_duration[0])) {
                switch ($post_duration[0]) {
                    case '1–3 Hours ':
                        $duration_counter = 1;
                        break;
                    case '3-8 Hours':
                        $duration_counter = 2;
                        break;
                    case '8–16 Hours (A Weekend)':
                        $duration_counter = 3;
                        break;
                    case '>16 Hours':
                        $duration_counter = 4;
                        break;
                }
            }

            $output .= '<div class="difficulty-lvl">';
            while ($difficulty_counter > 0) {
                $output .= '<span class="difficulty-level-image fa fa-wrench"></span>';
                $difficulty_counter--;
            }
            $output .= '</div>';
            $output .= '<div class="duration-lvl">';

            while ($duration_counter > 0) {
                $output .= '<span class="duration-level-image fa fa-clock-o"></span>';
                $duration_counter--;
            }
            $output .= '</div>';
            $output .= '</div>';
            $excerpt = get_the_excerpt();
            if (!has_excerpt($post_id)) {
                $excerpt = winwar_first_sentence($excerpt);
            }
            $output .= '<p class="excerpt trans"><a href="';
            $link = get_the_permalink();
            $output .= $link;
            $output .= '">';
            /** strip_shortcodes did not seem to be working here for contextly - maybe it isn't r
            egistered as a shortcode at this point.  I am duplicating the strip_shortcode call
            here from WPSEO_Utils.  We should really figure out how to make strip_shortcodes work.
             */
            $output .= truncate_with_ellipses(preg_replace( '`\[[^\]]+\]`s', '', $excerpt ), 240);
            $output .= '</a>';
            $output .= '</p>';
            $output .= '</div>';
            $output .= '<h2><a href="';
            $link = get_the_permalink();
            $output .= $link;
            $output .= '">';
            $post_title = get_the_title();
            $output .= truncate_with_ellipses($post_title, 90);
            $output .= '</a></h2>';
            $output .= '</li>';

            if (($counter == 3) and ($device != 'tablet') and ($device != 'mobile')) {
                $output .= '</ul> </li>';
                $counter = 0;
            }
            if (($counter == 2) and ($device == 'tablet')) {
                $output .= '</ul> </li>';
                $counter = 0;
            }
            if (($counter == 1) and ($device == 'mobile')) {
                $output .= '</ul> </li>';
                $counter = 0;
            }
            if ( ( $ads_counter == 1 && $device != 'tablet') || ($ads_counter == 0 && $device == 'tablet') and ( $post_per_page == $post_per_page_initial - 1 ) ) {
                if (($counter == 0) and ($device == 'mobile')) {
                    $output .= '<li class="row post_rows mobile-only-class"> <ul>';
                }
                $output .= '<li class="post col-lg-4 col-md-4 col-sm-6 col-xs-12 own_ads';
                if ( $count_posts <= 2 ) {
                    $output .= ' before-ads';
                }
                $output .= '">';
                $output .= '<div class="own">';
                $output .= '<p id="ads-title">Advertisement</p>';
                $output .= '<div class="home-ads">';
                $output .= '<div class="js-ad" data-size=\'[[300,250]]\' data-pos=\'"btf"\'></div>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</li>';
                $counter++;
                if (($counter == 3) and ($device != 'tablet') and ($device != 'mobile')) {
                    $output .= '</ul> </li>';
                    $counter = 0;
                }
                if (($counter == 2) and ($device == 'tablet')) {
                    $output .= '</ul> </li>';
                    $counter = 0;
                }
                if (($counter == 1) and ($device == 'mobile')) {
                    $output .= '</ul> </li>';
                    $counter = 0;
                }
            }

            $ads_counter++;
        endwhile;
        if (($counter == 1) and ($device != 'mobile')) {
            $output .= '</ul> </li>';
        }
        if (($ads_counter == $post_per_page) and ($ads_counter == ($post_per_page_initial - 1))) {
            $output .= '</ul> </li>';
        }
        do_action('custom_page_hook', $query);
        wp_reset_query();
    } else {
        if (!empty($current_cat_id)) {
            $current_cat_name = get_cat_name($current_cat_id);
        } else {
            $current_cat_name = "All projects";
        }
        $output .= '<div class="error_message">';
        $output .= '<p>Darn: we haven\'t created any projects like this for <span class="current_cat_name">' . $current_cat_name . '</span> (yet).</p>';
        $output .= '<span>But keep browsing!</span>';
        $output .= '</div>';
    }
    if ($type !== 'load_more') {
        $output .= '</ul>';
    }
    if ($max_num_pages > 1 && $type !== 'load_more') {
        $output .= '<p id="pbd-alp-load-posts" class="row"><a href="javascript:void(0);">More</a><i class="fa fa-spinner fa-pulse more-button-spinner"></i></p>';
    }

    echo $output;
}

function get_projects_with_ajax()
{
    $current_cat_id = $_POST['cat'];
    $difficulty = $_POST['diff'];
    $how_to_sort = $_POST['sort'];
    $duration = $_POST['dur'];
    $type = $_POST['type'];
    $paged = !empty($_POST['paged']) ? $_POST['paged'] : 1;

    sorting_posts($current_cat_id, $difficulty, $how_to_sort, $duration, $paged, $type);

    die();
}
add_action('wp_ajax_sorting_posts', 'get_projects_with_ajax');
add_action('wp_ajax_nopriv_sorting_posts', 'get_projects_with_ajax');
