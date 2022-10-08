<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/*
 * SEARCH FOR A CLIENT EXPERIENCE - shortcode
 */
//test--start
function pmpro_getMembershipLevelForUser(){
	//don't put any code up here
	
	//test for PMPro
	if ( ! defined( 'PMPRO_VERSION' ) ) {
	return;
	}
	
	//put your edits below here
	}
//

function search_experience_shortcode_by_premium_page_scripts_styles()
{
    // wp_enqueue_script('search_experience_shortcode_by_premium-script', plugin_dir_url(__FILE__) . 'assets/js/add_experience_page.js', array('jquery'), time());


    wp_enqueue_style('search_experience_shortcode_by_premium_style', plugin_dir_url(__FILE__) . 'assets/css/search_experience_shortcode_by_premium.css', array(), time());
}

add_action('wp_print_styles', 'search_experience_shortcode_by_premium_page_scripts_styles');
add_action('wp_print_scripts', 'search_experience_shortcode_by_premium_page_scripts_styles');

// SEARCH: function that runs when shortcode is called
add_shortcode('lcms_premium_search', 'lcms_premium_search_shortcode');
function lcms_premium_search_shortcode($atts)
{
    wp_print_styles();
    wp_print_scripts();

    // Things that you want to do.
    $a = shortcode_atts(array(
        'category' => 'uncategorized',
        'category'    => isset($_GET['category']) ? sanitize_key($_GET['category']) : 'uncategorized',
    ), $atts);
	
    // *** COMMON INCLUDES FOR THE SHORTCODES ***
    require_once(plugin_dir_path(__FILE__) . 'includes' . DIRECTORY_SEPARATOR . 'shortcode-common-functions.php');

    global $wpdb;

    //var_dump($_POST);

    $html = '';    // clear html variable before building it
	$html = '<div class="loader-container"><div class="loader"></div></div>';

    if ($a['category'] == 'uncategorized' && !isset($_POST['place_categories'])) {
        $html = '<div style="margin: 2rem 0;">
						<h1 style="font-size:1.75rem;margin-bottom:20px;color: var(--ast-global-color-2); font-weight: 500;">' . __('Premium Member Club', 'lcms-geodirectory-custom-functions') . '</h1>

						<form method="post">
							<div class="form-group row mt-2 mb-2">
								<div class="col-sm-4">
									<input type="text" name="search_term" placeholder="' . __('Enter Search', 'lcms-geodirectory-custom-functions') . '" class="form-control">
								</div>
								<div class="col-sm-8">
									<button class="btn bsui btn-primary mt-1" name="submit_button" value="search">' . __('Search', 'lcms-geodirectory-custom-functions') . '</button>
								</div>
							</div>
						</form>

						<p style="font-size:0.8rem;">' . __('You can Search by using a zip code and city/state.', 'lcms-geodirectory-custom-functions') . '</p>

				</div>
				';
    }
//Test Start
//     $user_query = new WP_User_Query(array('meta_query' => array(
//         'relation' => 'OR',
//         array(
//             'key'     => 'Zip_Code',
//             'value'   => '4017',
//             'compare' => 'LIKE'
//         ),
//         array(
//             'key'     => 'cityState',
//             'value'   => 'chittagong',
//             'compare' => 'LIKE'
//         ),
//     )));

//     $user_data = $user_query->get_results();
//     foreach($user_data as $data){
//         var_dump($data);
//     }
//Test end

    // IF SEARCH FORM IS SUBMITTED
    if (isset($_GET['submit_button']) && $_POST['search_term'] && !isset($_POST['place_categories'])) {
        $search_term = sanitize_text_field($_POST['search_term']);

////Test start
//         $tableprefix = $wpdb->prefix;
//         $sql = $wpdb->prepare(
//             '
//                 SELECT
//                  post_id,
//                  post_title,
//                  _search_title,
//                  member_name,
//                  business_name,
//                  website,
//                  clients_phone_number,
//                  clients_email,
//                  clients_zip_code,
//                  clients_experience
//                 FROM ' . $tableprefix . 'geodir_gd_place_detail
//                 WHERE post_status = %s AND (
//                  post_title LIKE %s
//                  OR _search_title LIKE %s
//                  OR member_name LIKE %s
//                  OR business_name LIKE %s
//                  OR website LIKE %s
//                  OR clients_phone_number LIKE %s
//                  OR clients_email LIKE %s
//                  OR clients_zip_code LIKE %s
//                  OR clients_experience LIKE %s
//                  )
//               	ORDER BY post_title, business_name, member_name
//             ',
//             'publish',
//             '%' . $wpdb->esc_like($search_term) . '%',
//             '%' . $wpdb->esc_like($search_term) . '%',
//             '%' . $wpdb->esc_like($search_term) . '%',
//             '%' . $wpdb->esc_like($search_term) . '%',
//             '%' . $wpdb->esc_like($search_term) . '%',
//             '%' . $wpdb->esc_like($search_term) . '%',
//             '%' . $wpdb->esc_like($search_term) . '%',
//             '%' . $wpdb->esc_like($search_term) . '%',
//             '%' . $wpdb->esc_like($search_term) . '%'
//         );
//         $search_results = $wpdb->get_results($sql);
//test end
        $user_query = new WP_User_Query(array('meta_query' => array(
            'relation' => 'OR',
            array(
                'key'     => 'Zip_Code',
                'value'   => "" . $search_term . "",
                'compare' => 'LIKE'
            ),
            array(
                'key'     => 'cityState',
                'value'   => "" . $search_term . "",
                'compare' => 'LIKE'
            ),
        )));

        $search_results = $user_query->get_results();


        // var_dump($search_results);


        $html .= '<div style="margin: 1rem 0;">';
        if (!empty($search_results)) {
            $html .= '<p>' . __('Your search results for keyword', 'lcms-geodirectory-custom-functions') . ' <strong>"' . $search_term . '"</strong>:</p>';
            foreach ($search_results as $search_row) {
                //$html.= '<li><a href="'.get_home_url().'/client-experience/?experience_slug='.str_replace(' ', '-', $search_row->_search_title).'">'.$search_row->post_title.' - '.__('Complaint by:','lcms-geodirectory-custom-functions').' '.$search_row->business_name.'</a></li>';

                $membership_level = pmpro_getMembershipLevelForUser($search_row->ID);
                // var_dump($membership_level);
                if ($membership_level) {
                    if ($membership_level->ID == 3) {
                        $html .= '<div class="busiess_div_premium">

                                    <div class="business_details_div_parent">
                                    
                                        <div class="business_details_div_flex">
                                            <div class="business_details_div">
                                                <p class="business_name">' . $search_row->display_name . '</p>
                                                <p class="business_category">' . get_the_category_by_ID($search_row->member_business_category) . '</p>
                                            </div>
                                        </div>

                                        <div class="business_brand_div_parent">
                                            <div class="business_brand_logo">
                                                <img class="business_brand_logo_img" src="' . plugin_dir_url(__FILE__) . 'assets/lying_client_1.png">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="business_contact_info_div">
                                        <div class="business_contact_info_div_flex">
                                            <div class="business_logo">
                                                        <img class="business_logo_img" src="' . plugin_dir_url(__FILE__) . 'assets/lying_client.png">
                                            </div>
                                            <div class="business_contact_info">
                                                <p class="business_phone">' . $search_row->phone_number . '</p>
                                                <p class="business_location">' . $search_row->cityState . ' (' . $search_row->Zip_Code . ')</p>
                                            </div>
                                        </div>
                                        <div class="business_website_btn_div">
                                            <a class="business_website_btn" href="' .  $search_row->user_url . '" target="_blank" rel="nofollow" >Visit Website</a>
                                        </div>
                                    </div>

                                  </div>';
                    }
                }
            }
        } else {
            $html .= '<p>' . __('No results found. Please try again.', 'lcms-geodirectory-custom-functions') . '</p>';
        }
        $html .= '</div>';
    }
	
    // IF CATEGORY IS SPECIFIED THROUGH SHORTCODE FROM A URL (USED IN SHORTCODE'S ATTRIBUTE)
	
    if (isset($_POST['place_categories'])) {

        $search_term = $_POST['place_categories']; // Searched or Selected Category
		
        $html .= '<div id="premium-members-club" style="margin: 1rem 0;">';

		$html .= '<h1 style="font-size:1.6rem;margin-bottom:20px;">' . __('Business Category', 'lcms-geodirectory-custom-functions') . ': <strong data-cat-id="'. $search_term .'">"' . __(get_term($search_term)->name, 'lcms-geodirectory-custom-functions') . '"</strong></h1>';
		
		$html .= '<div class="premium-members-container animate-bottom"></div>'; // AND THIS IS WHERE @WEBOZZA WILL INPUT JSON DATA USING JS

        $html .= '</div>';
    }


    $wpdb->flush();    // clear the results cache

    //return "foo = {$a['foo']}";
    //return "<p>User count is {$user_count}</p>";
    return $html;
}

// DROPDOWN CATEGORY: - function that runs when shortcode is called
add_shortcode('lcms_category_premium_dropdown', 'lcms_category_premium_dropdown_shortcode');

function lcms_category_premium_dropdown_shortcode($atts)
{
    // Things that you want to do. 
    $a = shortcode_atts(array(
        'category' => 'uncategorized',
        //'category'    => isset($_GET['category']) ? sanitize_key($_GET['category']) : 'uncategorized',
    ), $atts);

    // *** COMMON INCLUDES FOR THE SHORTCODES ***
    require_once(plugin_dir_path(__FILE__) . 'includes' . DIRECTORY_SEPARATOR . 'shortcode-common-functions.php');


    // IF CATEGORY SUBMIT BUTTON WAS PRESSED, THEN GO TO RELEVANT CATEGORY PLACE PAGE
    // if ($_POST['submit_button_category'] && $_POST['place_categories']) {
    //     $redirect_url = esc_url(home_url('/premium-member-club-2/?category=' . sanitize_text_field($_POST['place_categories'])));
    //     wp_safe_redirect($redirect_url);
    //     exit;
    // }

    // Get the member categories
    $categories_results = lcms_get_categories();
    // echo '<pre>'; var_dump($categories_results);
    //exit();
    // var_dump(get_taxonomy( 'wpcode_location' ));


    // Build the html
    $html = '';    // clear html variable before building it

    if (!isset($_POST['place_categories'])) {
        $html .= '<div style="margin: 1rem 0 3rem 0;" id="premium-members-club">';
        //if ($wpdb->num_rows > 0) {
        $html .= '<h5 style="margin-bottom:20px;">' . _e('OR select a Business Category', 'lcms-geodirectory-custom-functions') . '</h5>';
        //} else {
        //	$html.= '<h4 style="font-size:1.6rem;margin-bottom:20px;">'._e('Business Category','lcms-geodirectory-custom-functions').'</h4>';
        //	$html.= '<p>'._e('No place categories found.','lcms-geodirectory-custom-functions').'</p>';
        //}

        $html .= '<form method="post">';
        $html .= '		<select name="place_categories" id="place_categories">';
        $html .= '  		<option value="0" selected disabled>' . __('Select', 'lcms-geodirectory-custom-functions') . '</option>';
        foreach ($categories_results as $category_row) {
            $html .= '  		<option value="' . $category_row->term_id . '">' . __($category_row->name, 'lcms-geodirectory-custom-functions') . '</option>';
        }
        $html .= '		</select>';
        $html .= '		<button class="btn bsui btn-primary" name="submit_button_category" value="search">' . __('Go!', 'lcms-geodirectory-custom-functions') . '</button>';
        $html .= '</form>';



        $html .= '</div>';
    }



    //return "foo = {$a['foo']}";
    //return "<p>User count is {$user_count}</p>";

    return $html;
}
