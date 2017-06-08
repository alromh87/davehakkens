<?php

include dirname( __FILE__ ) . '/includes/meta-boxes.php';

add_theme_support( 'post-thumbnails' );
add_theme_support( 'post-formats', array( 'image', 'status', 'video', 'link' ) );

add_action( 'init', 'register_project_post_type' );
add_action( 'init', 'register_challenge_post_type' );

add_action( 'init', 'allow_origin' );

function allow_origin() {
    header("Access-Control-Allow-Origin: *");
}


//Exclude community category from main query
function exclude_category( $query ) {
	// EXCLUDE COMMUNITY CATEGORY POSTS FROM HOMEPAGE AND TAG ARCHVIES
    if ( $query->is_home() || $query->is_tag() ) {
        $query->set( 'cat', '-621' );
    }
}
add_action( 'pre_get_posts', 'exclude_category' );


function get_vine_thumbnail( $id ) {
  $vine = file_get_contents("http://vine.co/v/{$id}");
  preg_match('/property="og:image" content="(.*?)"/', $vine, $matches);

  return ( $matches[1] ) ? $matches[1] : false;
}

function dh_get_flag_by_location($country){
  if($country <> '' && !empty($country)){
  $country_filename = get_stylesheet_directory_uri() . '/img/flags/' . sanitize_file_name($country) . '.png';
  $country_path = get_stylesheet_directory() . '/img/flags/' . sanitize_file_name($country). '.png';
     if(file_exists($country_path)){
       $html = '<img src="' . $country_filename . '"/>';
     } else {
       $html = $country;
       echo '<!--' . get_stylesheet_directory_uri() . '/img/flags/' . sanitize_file_name($country) . '-->';
    }
  echo $html;
  }
}


function register_project_post_type(){

  register_post_type( 'projects', array(

    'labels' => array(
      'name' => __( 'Projects' ),
      'singular_name' => __( 'Project' )
    ),

    'public' => true,
    'has_archive' => false,
    'rewrite' => array(
      'slug' => 'project',
    ),

    'supports' => array(
      'title',
      'author',
      'excerpt',
      'editor',
      'thumbnail',
      'revisions',
      'custom-fields',
    )

  ));

}

$args = array(
  'name' => __( 'Forum sidebar' ),
  'id' => "forum-sidebar",
  'description' => '',
  'class' => '',
  'before_widget' => '<li id="%1$s" class="widget %2$s">',
  'after_widget' => "</li>\n",
  'before_title' => '<h2 class="widgettitle">',
  'after_title' => "</h2>\n",
);

register_sidebar( $args );

add_post_type_support( 'forum', array( 'thumbnail' ) );
add_post_type_support( 'topic', array( 'thumbnail' ) );

function custom_bbp_show_lead_topic( $show_lead ) {
  $show_lead[] = 'true';
  return $show_lead;
}

add_filter( 'bbp_show_lead_topic', 'custom_bbp_show_lead_topic' );
add_action( 'wp_login_failed', 'my_front_end_login_fail' );

function my_front_end_login_fail( $username ) {

  $referrer = $_SERVER['HTTP_REFERER'];

  if ( !empty( $referrer ) && !strstr( $referrer, 'wp-login' ) && !strstr( $referrer, 'wp-admin' ) ) {
    wp_redirect( $referrer . '?login=failed' );
    exit;
  }

}

function wcs_post_thumbnails_in_feeds( $content ) {

  global $post;

  if( has_post_thumbnail( $post->ID ) ) {
    $content = '<p>' . get_the_post_thumbnail( $post->ID ) . '</p>' . get_the_content();
  }

  return $content;

}

add_filter( 'the_excerpt_rss', 'wcs_post_thumbnails_in_feeds' );
add_filter( 'the_content_feed', 'wcs_post_thumbnails_in_feeds' );

register_nav_menus(array(
  'grid_filter' => 'Filter for post grid',
));
/*
add_filter( 'wp_mail', 'my_wp_mail_filter' );

function my_wp_mail_filter( $args ) {

  $header = file_get_contents( dirname(__FILE__) . '/emailtemplate/header.html' );
  $footer = file_get_contents( dirname(__FILE__) . '/emailtemplate/footer.html' );

  $new_wp_mail = [
    'to' => $args['to'],
    'subject' => $args['subject'],
    'message' => $header . nl2br( $args['message'] ) . $footer,
    'headers' => $args['headers'],
    'attachments' => $args['attachments']
  ];

  return $new_wp_mail;

}

add_filter( 'wp_mail_content_type', 'set_content_type' );

function set_content_type( $content_type ) {
  return 'text/html';
}
*/
function dave_hakkens_scripts() {
  wp_enqueue_script( 'jcrop', '/wp-includes/js/jcrop/jquery.Jcrop.min.js', array( 'jquery' ) );
  wp_enqueue_script( 'fancybox', get_bloginfo( 'template_url' ) . '/js/vendor/fancybox/jquery.fancybox.pack.js', array( 'jquery' ) );
  wp_enqueue_script( 'isotope', get_bloginfo( 'template_url' ) . '/js/vendor/isotope.pkgd.min.js', array( 'jquery' ) );
  wp_enqueue_script( 'scroll_to', get_bloginfo( 'template_url' ) . '/js/vendor/jquery.scroll_to.js', array( 'jquery' ) );
  wp_enqueue_script( 'mousewheel', get_bloginfo( 'template_url' ) . '/js/vendor/jquery.mousewheel.min.js', array( 'jquery' ) );
  wp_enqueue_script( 'fullpage-js', get_bloginfo( 'template_url' ) . '/js/vendor/fullpage.min.js', array( 'jquery' ) );
  wp_enqueue_script( 'dh_plugins', get_bloginfo( 'template_url' ) . '/js/plugins.js', array( 'jquery' ) );
  wp_enqueue_script( 'dh_main_js', get_bloginfo( 'template_url' ) . '/js/main.js', array( 'jquery', 'fancybox', 'isotope', 'scroll_to', 'mousewheel', 'dh_plugins' ), rand(999,2500) );
}

add_action( 'wp_enqueue_scripts', 'dave_hakkens_scripts' );


//Remove "Billing Details" for all gateways give plugin
function give_remove_billing_fields(){
	remove_action( 'give_after_cc_fields', 'give_default_cc_address_fields' );
}

add_action('init', 'give_remove_billing_fields');


//Shorter title instagram imports
add_filter( 'dsgnwrks_instagram_pre_save', 'dsgnwrks_qa_make_title_excerpted' );
function dsgnwrks_qa_make_title_excerpted( $import ) {
	if ( isset( $import['post_title'] ) ) {
		// feel free to edit these 2 values
		$number_of_words = 5;
		$more = '...';
		$import['post_title'] = wp_trim_words( $import['post_title'], $number_of_words, $more );
	}
	return $import;
}

/*
//chnage logo login page
function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/img/dave_community-logo.svg);
            width: 150px;
            height: 150px;
            background-size: 150px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );


//chnage URL login page
function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'Your Site Name and Info';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

*/


//change more.. on homepage
function modify_read_more_link() {
    return '<a class="more-link" href="' . get_permalink() . '">keep going..</a>';
}
add_filter( 'the_content_more_link', 'modify_read_more_link' );

/*

//Visual editor without tiny mc
function bbp_enable_visual_editor( $args = array() ) {
    $args['tinymce'] = true;
    return $args;
}
add_filter( 'bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor' );

//Visual editor paste clean text
function bbp_tinymce_paste_plain_text( $plugins = array() ) {
    $plugins[] = 'paste';
    return $plugins;
}
add_filter( 'bbp_get_tiny_mce_plugins', 'bbp_tinymce_paste_plain_text' );

*/

//set max topic title to 50
add_filter ('bbp_get_title_max_length','rkk_change_title') ;

Function rkk_change_title ($default) {
$default=50 ;
return $default ;
}


//Hide admin bar
add_filter('show_admin_bar', '__return_false');


/*
//Goto forums after login
function my_login_redirect( $url, $request, $user ){
if( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
if( $user->has_cap( 'administrator' ) ) {
$url = admin_url();
} else {
$url = home_url()/community/forums;
}
}
return $url;
}
add_filter('login_redirect', 'my_login_redirect', 10, 3 );


//Change name user roles
add_filter( 'bbp_get_dynamic_roles', 'ntwb_bbpress_custom_role_names' );

function ntwb_bbpress_custom_role_names() {
	return array(

		// Keymaster
		bbp_get_keymaster_role() => array(
			'name'         => 'Team',
			'capabilities' => bbp_get_caps_for_role( bbp_get_keymaster_role() )
		),

		// Moderator
		bbp_get_moderator_role() => array(
			'name'         => 'Moderator',
			'capabilities' => bbp_get_caps_for_role( bbp_get_moderator_role() )
		),

		// Participant
		bbp_get_participant_role() => array(
			'name'         => '.',
			'capabilities' => bbp_get_caps_for_role( bbp_get_participant_role() )
		),

		// Spectator
		bbp_get_spectator_role() => array(
			'name'         => '.',
			'capabilities' => bbp_get_caps_for_role( bbp_get_spectator_role() )
		),

		// Blocked
		bbp_get_blocked_role() => array(
			'name'         => '.',
			'capabilities' => bbp_get_caps_for_role( bbp_get_blocked_role() )
		)
	);
}
*/
//Remove user info
add_filter('user_contactmethods','hide_profile_fields',10,1);
 function hide_profile_fields( $contactmethods ) {
 unset($contactmethods['aim']);
 unset($contactmethods['jabber']);
 unset($contactmethods['yim']);
 return $contactmethods;
 }
//Remove color scheme admin panel
 remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );


//Make frehness shorter in bbpress
 function short_freshness_time( $output, $older_date, $newer_date ) {
  $output = preg_replace( '/, .*[^ago]/', ' ', $output );
  return $output;
}
add_filter( 'bbp_get_time_since', 'short_freshness_time' );


//Add extra class for topic lead
add_filter( 'bbp_show_lead_topic', '__return_true' );


//Custom css moderator
add_filter('bbp_before_get_reply_author_role_parse_args', 'ntwb_bbpress_reply_css_role' );
function ntwb_bbpress_reply_css_role() {

	$role = strtolower( bbp_get_user_display_role( bbp_get_reply_author_id( $reply_id ) ) );
	$args['class']  = 'bbp-author-role bbp-author-role-' . $role;
	$args['before'] = '';
	$args['after']  = '';

	return $args;
}

add_filter('bbp_before_get_topic_author_role_parse_args', 'ntwb_bbpress_topic_css_role' );
function ntwb_bbpress_topic_css_role() {

	$role = strtolower( bbp_get_user_display_role( bbp_get_topic_author_id( $topic_id ) ) );
	$args['class']  = 'bbp-author-role bbp-author-role-' . $role;
	$args['before'] = '';
	$args['after']  = '';

	return $args;
}


//*************************************//

function get_pp_pins( $data ) {
  global $wpdb;
  $query = "SELECT * FROM pp_pins";
  if (isset($data['id'])){
    $query .= " WHERE id=" . intval($data['id']);
  }
  $query .= ";";
  $pins = $wpdb->get_results($query);
  foreach ($pins as &$pin){
    $query = "SELECT service FROM pp_pins_services_pin WHERE pin =". $pin->id .";";
    $services = $wpdb->get_col($query);
    foreach($services as &$service)$service=intval($service);
    $pin->services = $services;
    $query = "SELECT tag FROM pp_pins_tags_pin WHERE pin =". $pin->id .";";
    $tags = $wpdb->get_col($query);
    foreach($tags as &$tag)$tag=intval($tag);
    $pin->tags = $tags;
  }
  if ( empty($pins) ) {
    return array("error"=>"not found");
  }
  return $pins;
}

function add_pin_extras($wpdb, $pinID, $table, $label, $data){
  foreach($data as $in){
    $dataDB = array(
      'pin'	=> $pinID,
      $label	=> $in
    );
    $format = array(
      '%d',
      '%d'
    );
    $insert = $wpdb->insert($table, $dataDB, $format);
    if(!$insert){
      return false;
    }
  }
  return true;
}
function create_pp_pin( $data ) {
  global $wpdb;
  $db = 'pp_pins';
  $dataDB = array(
    'owner'		=> get_current_user_id(),
    'lat'		=> $data['lat'],
    'long'		=> $data['long'],
    'name'		=> $data['name'],
    'description'	=> $data['description'],
    'status'		=> $data['status'],
    'url'		=> $data['url']
  );
//  print_r($dataDB);
  $format = array(
    '%d',
    '%f',
    '%f',
    '%s',
    '%s',
    '%d',
    '%s'
  );
  $wpdb->query( 'BEGIN' );
  $pin = $wpdb->insert($db, $dataDB, $format);
  if ($pin){
    $pinID = $wpdb->insert_id;
    $services = add_pin_extras($wpdb, $pinID, 'pp_pins_services_pin', 'service', $data['services']);
    $tags = add_pin_extras($wpdb, $pinID, 'pp_pins_tags_pin', 'tag', $data['tags']);
    if($services && $tags){
      $wpdb->query( 'COMMIT' );
      $dataDB['id'] = $pinID;
      $dataDB['services'] = $data[services];
      $dataDB['tags'] = $data[tags];
      return $dataDB;
    }
    $wpdb->query( 'ROLLBACK' );
    return array('error'=>'Could not insert pin');
  }else{
    return array('error'=>'Could not insert pin');
  }
}

function delete_pp_pin( $data ){
  global $wpdb;
  $format = array(
    '%d'
  );
  $dataDB = array(
    'pin' => $data['id']
  );
  $db = "pp_pins_services_pin";
  $wpdb->delete($db, $dataDB, $format);
  $db = "pp_pins_tags_pin";
  $wpdb->delete($db, $dataDB, $format);
  $db = "pp_pins";
  $dataDB = array(
    'id' => $data['id']
  );
  $pin = $wpdb->delete($db, $dataDB, $format);
  return array("deleted" => $pin?true:false);
}

function get_pp_pins_services( $data ) {
  global $wpdb;
  $query = "SELECT * FROM pp_pins_services";
  if (isset($data['id'])){
    $query .= " WHERE id=" . intval($data['id']);
  }
  $query .= ";";
  $result = $wpdb->get_results($query);
  if ( empty($result) ) {
    return array("error"=>"not found");
  }
  return $result;
}

function get_pp_pins_tags( $data ) {
  global $wpdb;
  $query = "SELECT * FROM pp_pins_tags";
  if (isset($data['id'])){
    $query .= " WHERE id=" . intval($data['id']);
  }
  $query .= ";";
  $result = $wpdb->get_results($query);
  if ( empty($result) ) {
    return array("error"=>"not found");
  }
  return $result;
}

function pp_pins_check_login() {
  if (!is_user_logged_in()){
    return new WP_Error( 'rest_forbidden', esc_html__( 'Unauthorized', 'my-text-domain' ), array( 'status' => 401 ) );
  }
//  if (!current_user_can('edit_posts')){
//    return new WP_Error( 'rest_forbidden', esc_html__( 'OMG you can not view private data.', 'my-text-domain' ), array( 'status' => 401 ) );
//  }
  return true;
}

add_action( 'rest_api_init', function () {
  $namespace	= 'pp_pins/v1';
  $route	= 'pins';
  register_rest_route( $namespace, $route, array(
    array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => 'get_pp_pins',
    ),
    array(
      'methods' => WP_REST_Server::CREATABLE,
//      'methods' => WP_REST_Server::EDITABLE,
//      'methods' => WP_REST_Server::DELETABLE,
      'callback' => 'create_pp_pin',
      'permission_callback' => 'pp_pins_check_login',
      'args' => array(
        'lat' => array('required' => true),
        'long' => array('required' => true),
        'name' => array('required' => true),
        'description' => array('required' => true),
        'status' => array('required' => true),
        'services' => array('required' => true),
        'tags' => array('required' => true),
        'url' => array('default' => '')
      ),
    ),
    array(
      'methods' => WP_REST_Server::DELETABLE,
      'callback' => 'delete_pp_pin',
      'permission_callback' => 'pp_pins_check_login',
      'args' => array(
        'id' => array('required' => true),
      ),
    )
  ) );

  register_rest_route( $namespace, $route.'/(?P<id>\d+)', array(
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'get_pp_pins',
  ) );

  $route	= 'services';
  register_rest_route( $namespace, $route, array(
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'get_pp_pins_services',
  ) );

  $route	= 'tags';
  register_rest_route( $namespace, $route, array(
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'get_pp_pins_tags',
  ) );

} );

/*
add_action( 'rest_api_init', function() {
  remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
  add_filter( 'rest_pre_serve_request', function( $value ) {
    header( 'Access-Control-Allow-Origin: http://localhost' );
    header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
    header( 'Access-Control-Allow-Credentials: true' );
    header( 'Access-Control-Allow-Headers: X-WP-Nonce' );
    return $value;
  });
}, 15 );
*/

//*************************************//


//ad sidebar
if ( is_active_sidebar( 'primary' ) ) : ?>

    <div id="primary" class="sidebar aside">

        <?php dynamic_sidebar( 'primary' ); ?>

    </div><!-- #primary .aside -->

<?php endif; ?>
