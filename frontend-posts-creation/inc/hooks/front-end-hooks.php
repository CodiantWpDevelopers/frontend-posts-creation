<?php 
add_action( 'wp_enqueue_scripts', 'frontend_scripts' );
add_action('wp_login', 'redirect_after_loggedin', 10, 2);
add_filter( 'single_template', 'myplugin_single_template' );
add_filter( 'archive_template', 'myplugin_archive_template' );
add_shortcode( 'create_frontend_posts', 'frontend_post_form' );
add_action( 'user_left_sidebar', 'sidebar_section');
add_action('user-dashboard', 'userDashboard');
add_action('user-account', 'userAccount');
add_action ('edit-profile', 'editUser');
add_action('show_profile','editProfileDetails');
add_action('add_posts', 'newPostCreation');
add_action('view_posts', 'viewAllPosts');
add_action( 'wp_ajax_showPostTypeTerms', 'showPostTypeTerms' );
/***************************************************************************************************************
*                                           Front End Scripts
***************************************************************************************************************/
function frontend_scripts(){
	global $wpdb,$post;  
	$currentPostId = $post->ID;
	$tableName = $wpdb->prefix."postmeta";
	$countsql = $wpdb->prepare("select count(*) as totalcount from $tableName where meta_key = 'showjscss' and meta_value = '$currentPostId' ");  
	$countresult = $wpdb->get_var( $countsql );
	if( !empty( $countresult ) && $countresult >=1 ){
		
		wp_register_script('bootstrap-bundle-min-js',  FRONTEND_LOCATION_URL . '/assets/front-end/js/bootstrap.bundle.min.js', array('jquery'), '1.0.5',true); 
		wp_enqueue_script('bootstrap-bundle-min-js');
		
		wp_register_script('fontawesome-js',  FRONTEND_LOCATION_URL . '/assets/front-end/js/fontawesome.js', array('jquery'), '1.0.5',true); 
		wp_enqueue_script('fontawesome-js');
		
		wp_register_script('bootbox-min-js',  FRONTEND_LOCATION_URL . '/assets/front-end/js/bootbox.min.js', array('jquery'), '1.0.5',true); 
		wp_enqueue_script('bootbox-min-js');
		
		wp_register_script('jquery-validate-min-js',  FRONTEND_LOCATION_URL . '/assets/front-end/js/jquery.validate.min.js', array('jquery'), '1.0.5',true); 
		wp_enqueue_script('jquery-validate-min-js');
		
		wp_register_script('jquery-min-js',  FRONTEND_LOCATION_URL . '/assets/front-end/js/jquery-3.6.0.min.js', array('jquery'), '1.0.5',false); 
		wp_enqueue_script('jquery-min-js');
		
		wp_register_script('dataTables-bootstrap5-min-js',  FRONTEND_LOCATION_URL . '/assets/front-end/js/dataTables.bootstrap5.min.js', array('jquery'), '1.0.5',true); 
		wp_enqueue_script('dataTables-bootstrap5-min-js');

        wp_register_script('jquery-dataTables-min-js',  FRONTEND_LOCATION_URL . '/assets/front-end/js/jquery.dataTables.min.js', array('jquery'), '1.0.5',true); 
		wp_enqueue_script('jquery-dataTables-min-js');

        wp_register_script('frontend-js',  FRONTEND_LOCATION_URL . '/assets/front-end/js/frontend.js', array('jquery'), '1.0.5',true); 
		wp_enqueue_script('frontend-js');
		
		wp_localize_script('frontend-js','plugin_ajax_object', array('ajaxurl'=>admin_url('admin-ajax.php' ), 'nonce' => wp_create_nonce('field_ajax_nonce') ) );
		
		wp_register_style( 'bootstrap-min-css', FRONTEND_LOCATION_URL. '/assets/front-end/css/bootstrap.min.css',false,'3.1','all');
		wp_enqueue_style('bootstrap-min-css');

        wp_register_style( 'dataTables-bootstrap5-min-css', FRONTEND_LOCATION_URL. '/assets/front-end/css/dataTables.bootstrap5.min.css',false,'3.1','all');
		wp_enqueue_style('dataTables-bootstrap5-min-css');
		
		wp_register_style( 'self-css', FRONTEND_LOCATION_URL. '/assets/front-end/css/self.css',false,'3.1','all');
		wp_enqueue_style('self-css');
		
	}
    if(is_archive()){
        wp_register_script('bootstrap-bundle-min-js',  FRONTEND_LOCATION_URL . '/assets/front-end/js/bootstrap.bundle.min.js', array('jquery'), '1.0.5',true); 
		wp_enqueue_script('bootstrap-bundle-min-js');
		
		wp_register_script('fontawesome-js',  FRONTEND_LOCATION_URL . '/assets/front-end/js/fontawesome.js', array('jquery'), '1.0.5',true); 
		wp_enqueue_script('fontawesome-js');
		
		wp_register_script('jquery-min-js',  FRONTEND_LOCATION_URL . '/assets/front-end/js/jquery-3.6.0.min.js', array('jquery'), '1.0.5',false); 
		wp_enqueue_script('jquery-min-js');

        wp_register_script('archive-js',  FRONTEND_LOCATION_URL . '/assets/front-end/js/archive.js', array('jquery'), '1.0.5',false); 
		wp_enqueue_script('archive-js');
		
		wp_localize_script('frontend-js','plugin_ajax_object', array('ajaxurl'=>admin_url('admin-ajax.php' ), 'nonce' => wp_create_nonce('field_ajax_nonce') ) );
		
		wp_register_style( 'bootstrap-min-css', FRONTEND_LOCATION_URL. '/assets/front-end/css/bootstrap.min.css',false,'3.1','all');
		wp_enqueue_style('bootstrap-min-css');
		
		wp_register_style( 'self-css', FRONTEND_LOCATION_URL. '/assets/front-end/css/self.css',false,'3.1','all');
		wp_enqueue_style('self-css'); 
    }
}

/**********************************************************************************************************
                                    Redirect after logged in
**********************************************************************************************************/
function redirect_after_loggedin( $user_login, $user ){
    if( $user->roles[0] == 'author'){
        wp_redirect(get_site_url."/dashboard/");
        exit();
    }
}

/***************************************************************************************************************
                                    Theme Template for post types and post type archive
****************************************************************************************************************/
function myplugin_single_template( $template ) {
    static $using_null = array();

    // Adjust with your custom post types.
    $post_types = array( 'event', );

    if ( is_single() || is_archive() ) { 
        $template_basename = basename( $template ); 
        // This check can be removed.
        if ( $template == '' || substr( $template_basename, 0, 4 ) == 'sing' || substr( $template_basename, 0, 4 ) == 'arch' ) { 
            $post_type = get_post_type();
            $slug = is_archive() ? 'archive' : 'single';
            if ( in_array( $post_type, $post_types ) ) { 
                // Allow user to override.
                if ( $single_template = myplugin_get_template( $slug, $post_type ) ) {
                    $template = $single_template;
                } else {
                    // If haven't gone through all this before...
                    if ( empty( $using_null[$slug][$post_type] ) ) {
                        if ( $template && ( $content_template = myplugin_get_template( 'content-' . $slug, $post_type ) ) ) {
                            $tpl_str = file_get_contents( $template );
                            // You'll have to adjust these regexs to your own case - good luck!
                            if ( preg_match( '/get_template_part\s*\(\s*\'content\'\s*,\s*\'' . $slug . '\'\s*\)/', $tpl_str, $matches, PREG_OFFSET_CAPTURE )
                            || preg_match( '/get_template_part\s*\(\s*\'content\'\s*,\s*get_post_format\s*\(\s*\)\s*\)/', $tpl_str, $matches, PREG_OFFSET_CAPTURE )
                            || preg_match( '/get_template_part\s*\(\s*\'content\'\s*\)/', $tpl_str, $matches, PREG_OFFSET_CAPTURE )
                            || preg_match( '/get_template_part\s*\(\s*\'[^\']+\'\s*,\s*\'' . $slug . '\'\s*\)/', $tpl_str, $matches, PREG_OFFSET_CAPTURE ) ) {
                                $using_null[$slug][$post_type] = true;
                                $tpl_str = substr( $tpl_str, 0, $matches[0][1] ) . 'include \'' . $content_template . '\'' . substr( $tpl_str, $matches[0][1] + strlen( $matches[0][0] ) );
                                // This trick includes the $tpl_str.
                                eval( '?>' . $tpl_str );
                            }
                        }
                    }
                    if ( empty( $using_null[$slug][$post_type] ) ) {
                        // Failed to parse - look for fall back template.
                        if ( file_exists( FRONTEND_LOCATION . '/inc/templates/' . $slug . '.php' ) ) {
                            $template = FRONTEND_LOCATION . '/inc/templates/' . $slug . '.php';
                        }
                    } else {
                        // Success! "null.php" is just a blank zero-byte file.
                        $template = FRONTEND_LOCATION . '/inc/templates/null.php';
                    }
                }
            }
        }
    }
    return $template;
}

function myplugin_archive_template( $template ) {
    return myplugin_single_template( $template );
}

function myplugin_get_template( $slug, $part = '' ) { 
     $template = $slug . ( $part ? '-' . $part : '' ) . '.php'; 

    $dirs = array();

    if ( is_child_theme() ) {
        $child_dir = FRONTEND_LOCATION . '/';
        $dirs[] = $child_dir .'/';
        $dirs[] = $child_dir;
    }

    $template_dir = FRONTEND_LOCATION . '/inc//templates';
    $dirs[] = $template_dir. '/';
    $dirs[] = $template_dir;
    $dirs[] = FRONTEND_LOCATION . '/inc/templates/';

    foreach ( $dirs as $dir ) {
        if ( file_exists( $dir . $template ) ) { 
            return $dir . $template;
        }
    }
    return false;
}

function post_form(){
	global $current_user;
	$user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);
	if( $user_role == 'author' || $user_role == 'administrator' ){
		$user_ID = get_current_user_id(); 
		if(isset( $_POST['postname'] )){ 
			if( !empty( $_POST['postname'] ) ){  
				$postName 			= trim( $_POST['postname'] );
				$frontend_editor 	= $_POST['frontend_editor'];
				$postType 			= $_POST['selectposttype'];
				$eventcategoryid 	= $_POST['eventfirstlevelchild']; 
				$taxonomyName 		= $_POST['hiddeneventcategory'];
				$locationId 		= $_POST['locationfirstlevelchild'];
				$locTaxName 		= $_POST['hiddenlocationcategory'];   
				$categoryIds 		= array( $_POST['postcategoryid']);
				$hiddenpostcategory	= array($_POST['hiddenpostcategory']);
				if( $hiddenpostcategory == 'category' ){ 
					$my_post = array(
					  'post_title'    => wp_strip_all_tags( $postName ),
					  'post_content'  => $frontend_editor,
					  'post_status'   => 'publish',
					  'post_author'   => $user_ID,
					  'post_type'	  => 'post',
					  'post_category' => $categoryIds
					);
					// Insert the post into the database
					wp_insert_post( $my_post );
				}
				else{ 
					if(!empty($taxonomyName)){  
						if( trim( $taxonomyName ) == 'events' ){ 
							$eventargs = array(
								'post_title'    => wp_strip_all_tags( $postName ),
								'post_content'  => $frontend_editor,
								'post_status'   => 'publish',
								'post_author'   => $user_ID,
								'post_type'		=> $postType
							); 
							$eventPostId = wp_insert_post( $eventargs );
							wp_set_post_terms( $eventPostId, $eventcategoryid, 'events',false);
							wp_set_post_terms( $eventPostId, $locationId, 'locations',false);
						}
                        else{ 
                        }
					}
                    else{
                        $withoutcategorypost = array(
                            'post_title'    => wp_strip_all_tags( $postName ),
                            'post_content'  => $frontend_editor,
                            'post_status'   => 'publish',
                            'post_author'   => $user_ID,
                            'post_type'	  => 'post',
                        ); 
                        wp_insert_post( $withoutcategorypost );
                    }
				}
			}
			echo '
			<script>
				if ( window.history.replaceState ) {
					window.history.replaceState( null, null, window.location.href );
				}
			</script>
			';
		}
		echo '
			<div class="container">
				<div class="row">
					<form method="POST" name="postForm">
					  <div class="form-outline mb-12 mt-4">
						<label class="form-label" for="posttitle">Post Title</label>
						<input type="text" id="posttitle" class="form-control posttitle" name="postname" required />
					  </div>

					  <!-- Password input -->
					<div class="form-outline mb-12 mt-4">
						<label class="form-label" for="form1Example2">Content</label>';
						$content = '';
						$editor_id = 'frontend_editor';
						$settings =   array(
							'wpautop' => true, // use wpautop?
							'media_buttons' => true, // show insert/upload button(s)
							'textarea_name' => $editor_id, // set the textarea name to something different, square brackets [] can be used here
							'textarea_rows' => get_option('default_post_edit_rows', 10), // rows="..."
							'tabindex' => '',
							'editor_css' => '', //  extra styles for both visual and HTML editors buttons, 
							'editor_class' => '', // add extra class(es) to the editor textarea
							'teeny' => false, // output the minimal editor config used in Press This
							'dfw' => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
							'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
							'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
						);
						echo wp_editor( $content, $editor_id, $settings);
					echo '
					</div> 
					<div class="form-outline mb-12 mt-4">
					<label class="form-label px-2" for="posttype">Select Post Type</label>';
						$args = array(
						'public'   => true,
						'_builtin' => false
						);

						$output = 'names'; // 'names' or 'objects' (default: 'names')
						$operator = 'or'; // 'and' or 'or' (default: 'and')

						$post_types = get_post_types( $args, $output, $operator );

						if ( $post_types ) {
							echo '<select name="selectposttype" class="select selectPostTypes" data-mdb-placeholder="Select Post Type">';
							echo '<option value="select">Select Post Type </option>';
							foreach ( $post_types  as $post_type ) {
								if($post_type == 'attachment'){}else
								{
									echo '<option value="'.$post_type.'">'.ucwords( $post_type ).'</option>';
								}
							}
							echo '</select>';
						}
					echo '
					</div>
					
					<div class="form-outline mb-12 mt-4 hide">';
					echo '
					</div>
					<input type="hidden" name="eventcategories" id="eventcategories" value="" />
					
					<button type="submit" class="btn btn-primary btn-block publishposts">Publish</button>
					</form>
				</div>
			</div>
		';
	}
}
function custom_post_function(){
	post_form();
}
/****************************************************************************************************
                                Add post form shortcode
/***************************************************************************************************/
function frontend_post_form() {
    ob_start();
    custom_post_function();
    return ob_get_clean();
}

/****************************************************************************************************
                                hook for user dashboard sidebar
/***************************************************************************************************/
function sidebar_section(){
    $parentPageId = get_option('pages_lists');
            if(!empty( $parentPageId ) ){
                $parentqueryargs = array(
                    'post_type'     => 'page',
                    'post_status'   => 'publish',
                    'post__in'   	=> array( $parentPageId )	
                );
                $parentQuery = new WP_Query( $parentqueryargs ); 
                if( $parentQuery->have_posts() ){  ?>
                    <ul class="list-group">
                        <?php
                        while( $parentQuery->have_posts()): $parentQuery->the_post();
                        $parentId = get_the_ID();
                        ?>
                        <li class="list-group-item" aria-current="true"><a href="<?php echo get_the_permalink($parentId); ?> " class="active"><?php echo get_the_title( $parentId ); ?></a>
                            <?php 
                            $childArgs =  array(
                                'post_type'     => 'page',
                                'post_status'   => 'publish',
                                'post_parent'   => $parentId,
								'orderby'		=> 'ID',
								'order'			=> 'ASC'
                            );
                            $childquery = new WP_Query( $childArgs ); 
                            if( $childquery->have_posts()){?>
                                <ul>
                                    <?php 
                                    while( $childquery->have_posts()): $childquery->the_post(); 
                                    $childId = get_the_ID();?>
                                        <li class="list-group"><a href="<?php echo get_the_permalink( $childId ); ?>" class="child"><?php echo get_the_title( $childId ); ?></a></li>
                                    <?php 
                                    endwhile; wp_reset_query(); ?>
                                </ul>
                        </li>
                        <li class="list-group-item"><a href="<?php echo wp_logout_url(get_site_url());?>">Log Out</a></li>
                        <?php
                        
                        }
                    
                        endwhile;wp_reset_query();  ?>
                    </ul>
                <?php 
                }
            } 
}

/****************************************************************************************************
                                        Get Logged In User Details 
****************************************************************************************************/
function get_user_details(){
    $current_user = wp_get_current_user();
    return $current_user;
}
/****************************************************************************************************
                                        User Dashboard Hook
****************************************************************************************************/
function userDashboard(){
    if(function_exists('get_user_details') ){
        $userLists =  get_user_details();
        $userId = $userLists->ID;
        $userName = $userLists->user_login;
        $userRole = $userLists->roles[0];
        if( $userRole == 'author' ){ 
            echo '<p>Welcome ' . ucwords($userName). ' . <a href="'.wp_logout_url(get_site_url()).'" class="btn btn-info">Click here to logged out  </a></p>';
        }
    }    
}

/****************************************************************************************************
                                        User Account Page 
****************************************************************************************************/
function userAccount(){
    if(function_exists('get_user_details') ){
        $userLists 		=  get_user_details();
        $userId 		= $userLists->ID;
        $userName 		= $userLists->user_login;
        $userRole 		= $userLists->roles[0];
		$userEmail		= $userLists->user_email;
		$userFirstName 	= '';
		$userLastName 	= '';
		$phoneNumber 	= '';
		$address 		= '';
		$description	= '';
		if( !empty( get_user_meta( $userId, 'first_name', true) ) )
		{
			$userFirstName = get_user_meta( $userId, 'first_name', true);
		}
		if( !empty( get_user_meta( $userId, 'last_name', true) ) )
		{
			$userLastName = get_user_meta( $userId, 'last_name', true);
		}
		if( !empty( get_user_meta( $userId, 'phone_number', true) ) )
		{
			$phoneNumber = get_user_meta( $userId, 'phone_number', true);
		}
		if( !empty( get_user_meta( $userId, 'address', true) ) )
		{
			$address = get_user_meta( $userId, 'address', true);
		}
		if( !empty( get_user_meta( $userId, 'description', true) ) )
		{
			$description = get_user_meta( $userId, 'description', true);
		}

		do_action('edit-profile');
		
        if( $userRole == 'author' ){ ?>
            <div class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-headingOne">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#userprofile" aria-expanded="false" aria-controls="userprofile">
							Profile Settings
						</button>
                    </h2>
                    <div id="userprofile" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                    	<div class="accordion-body">
							<?php do_action('show_profile');?>
							
						</div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-headingThree">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#allposts" aria-expanded="false" aria-controls="allposts">
                            View All Posts
						</button>
                    </h2>
                    <div id="allposts" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#allposts">
						<div class="accordion-body">
                        <?php do_action('view_posts');?>
						</div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-headingTwo">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#addpost" aria-expanded="false" aria-controls="addpost">
                            Add New Post
						</button>
                    </h2>
                    <div id="addpost" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#addpost">
						<div class="accordion-body">
							<?php do_action('add_posts'); ?>
						</div>
                    </div>
                </div>
                
            </div>
        <?php  
        }
    } 
}

/****************************************************************************************************************************
                                        Edit user section
*****************************************************************************************************************************/
function editUser(){
	global $wpdb;
	if(function_exists('get_user_details') ){
        $userLists 		=  get_user_details();
        $userId 		= $userLists->ID;
		if(isset($_POST['editprofile'] ) ){ 
			if(!empty($_POST['firstname'] ) ){
				update_user_meta( $userId, 'first_name', $_POST['firstname']);
			}
			if(!empty($_POST['lastname'] ) ){
				update_user_meta( $userId, 'last_name', $_POST['lastname']);
			}
			if(!empty($_POST['phonenumber'] ) ){
				update_user_meta( $userId, 'phone_number', $_POST['phonenumber']);
			}
			if(!empty($_POST['address'] ) ){
				update_user_meta( $userId, 'address', $_POST['address']);
			}
			if(!empty($_POST['emailid'] ) ){
				wp_update_user( array( 'ID' => $userId, 'user_email' => ($_POST['emailid'] ) ) );
			}
			if(!empty($_POST['biographicalinfo'] ) ){
				update_user_meta( $userId, 'description', $_POST['biographicalinfo']);
			}
		}
	}
}
/****************************************************************************************
 							Edit Profile Html 
****************************************************************************************/
function editProfileDetails(){ 
	if(function_exists('get_user_details') ){
        $userLists 		=  get_user_details();
        $userId 		= $userLists->ID;
        $userName 		= $userLists->user_login;
        $userRole 		= $userLists->roles[0];
		$userEmail		= $userLists->user_email;
		$userFirstName 	= '';
		$userLastName 	= '';
		$phoneNumber 	= '';
		$address 		= '';
		$description	= '';
		if( !empty( get_user_meta( $userId, 'first_name', true) ) )
		{
			$userFirstName = get_user_meta( $userId, 'first_name', true);
		}
		if( !empty( get_user_meta( $userId, 'last_name', true) ) )
		{
			$userLastName = get_user_meta( $userId, 'last_name', true);
		}
		if( !empty( get_user_meta( $userId, 'phone_number', true) ) )
		{
			$phoneNumber = get_user_meta( $userId, 'phone_number', true);
		}
		if( !empty( get_user_meta( $userId, 'address', true) ) )
		{
			$address = get_user_meta( $userId, 'address', true);
		}
		if( !empty( get_user_meta( $userId, 'description', true) ) )
		{
			$description = get_user_meta( $userId, 'description', true);
		}
        ?>
		<div class="container rounded bg-white mt-5 mb-5"> 
			<div class="row"> 
				<div class="col-md-6 border-right"> 
					<div class="d-flex flex-column align-items-center text-center p-3 py-5">
						<img class="rounded-circle mt-5" src="https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcQF2psCzfbB611rnUhxgMi-lc2oB78ykqDGYb4v83xQ1pAbhPiB&usqp=CAU">
						<span class="font-weight-bold"><?php echo $userName;?></span>
						<span class="text-black-50"><?php echo $userEmail;?></span>
						<span> </span>
					</div> 
				</div> 
				<div class="col-md-6 border-right"> 
					<form method="POST" action="">
						<div class="p-3 py-6"> 
							<div class="d-flex justify-content-between align-items-center mb-3"> 
								<h4 class="text-right">Profile Settings</h4> 
							</div> 
							<div class="row mt-2"> 
								<div class="col-md-6">
									<label class="labels">First Name</label>
									<?php 
									if( !empty($_POST['firstname'] ) ){
										$userFirstName = $_POST['firstname'];
									}else{
										$userFirstName = $userFirstName ;
									}
									?>
									<input type="text" class="form-control" placeholder="first name" name="firstname" value="<?php echo $userFirstName;?>">
								</div> 
								<div class="col-md-6">
									<label class="labels">Last Name</label>
									<?php 
									if( !empty($_POST['lastname'] ) ){
										$userLastName = $_POST['lastname'];
									}else{
										$userLastName = $userLastName ;
									}
									?>
									<input type="text" class="form-control" value="<?php echo $userLastName;?>" name="lastname" placeholder="surname">
								</div> 
							</div> 
							<div class="row mt-3"> 
								<div class="col-md-12">
									<label class="labels">PhoneNumber</label>
									<?php 
									if( !empty($_POST['phonenumber'] ) ){
										$phoneNumber = $_POST['phonenumber'];
									}else{
										$phoneNumber = $phoneNumber ;
									}
									?>
									<input type="text" class="form-control mb-3" placeholder="enter phone number" name="phonenumber" value="<?php echo $phoneNumber;?>">
								</div> 
								<div class="col-md-12">
									<label class="labels">Address</label>
									<?php 
									if( !empty($_POST['address'] ) ){
										$address = $_POST['address'];
									}else{
										$address = $address ;
									}
									?>
									<textarea class="form-control mb-3" name="address" id="address" rows="3"><?php echo $address;?></textarea>
								</div> 
								<div class="col-md-12">
									<label class="labels requiredclass">Email ID</label>
									<?php 
									if( !empty($_POST['emailid'] ) ){
										$userEmail = $_POST['emailid'];
									}else{
										$userEmail = $userEmail ;
									}
									?>
									<input type="text" class="form-control mb-3" name="emailid" placeholder="enter email id" value="<?php echo $userEmail;?>" required>
								</div> 
							</div> 
							<div class="row"> 
								<div class="col-md-12">
									<label class="labels">Biographical Info</label>
									<?php 
									if( !empty($_POST['biographicalinfo'] ) ){
										$description = $_POST['biographicalinfo'];
									}else{
										$description = $description ;
									}
									?>
									<textarea class="form-control mb-3" name="biographicalinfo" id="biographicalinfo" rows="3"><?php echo $description;?></textarea>
								</div>
							</div> 
							<div class="mt-5 text-center">
								<input type="submit" class="btn btn-primary profile-button" value="Edit Profile" name="editprofile">
							</div> 
						</div> 
					</form>
				</div> 
			</div>
		</div>
<?php
	} 
}
/********************************************************************************************************************************
                                Ajax call back function to get post types categories lists 
********************************************************************************************************************************/
function showPostTypeTerms(){
	if ( ! wp_verify_nonce( $_POST['nonce'], 'field_ajax_nonce' ) ) {
		die( __( 'You are not allowed to edit', 'notifications' ) ); 
	}else {
		$postType = trim($_POST['postType'] );
		if($postType == 'post'){
			$taxonomy = 'category';
			$parent_ID = 0;
			$exclude = array(1);			
        
			//-level_one_clilds-
			$level_one_clilds = get_terms( array(
				'taxonomy'   => $taxonomy,
				'parent'     => $parent_ID ,
				'exclude'  => $exclude,
				'depth'      => 3,
				'hide_empty' => false
			) );
			if( !empty( $level_one_clilds ) ){
				echo '<label class="form-label px-2" for="posttypecategory">Select Categories</label>';
				echo '<ul class="list-group postcategories">';
				foreach( $level_one_clilds as $level_one_clild ):
					
					echo '<li class="list-group-item border-0 level-first"><input class="form-check-input" name="postcategoryid[]" type="checkbox" value="'.$level_one_clild->term_id.'" id="firstchild"> <label class="form-check-label" for="firstlevelchild">'.$level_one_clild->name.'</label>';
					
					//--level_tow_clilds--
					$level_tow_clilds = get_terms( array(
					  'taxonomy'    => $taxonomy,
					  'parent'      => $level_one_clild->term_id, 
					  'depth'       => 1,
					  'hide_empty'  => false
					));
					if( !empty( $level_tow_clilds ) ){
						echo '<ul class="list-group">';
							foreach( $level_tow_clilds as $level_tow_clild ): 
								echo '<li class="list-group-item border-0 level-second"><input class="form-check-input" name="postcategoryid[]" type="checkbox" value="'.$level_tow_clild->term_id.'" id="secondlevelchild"> <label class="form-check-label" for="secondlevelchild">'.$level_tow_clild->name.'</label>';
							  
							   //---level_three_clild ---
							   $level_three_clilds = get_terms( array(
								'taxonomy'    => $taxonomy,
								'parent'      => $level_tow_clild->term_id, 
								'depth'       => 1,
								'hide_empty'  => false
							  ));
								if( !empty( $level_three_clilds ) && !is_wp_error( $level_three_clilds ) ){
									echo '<ul class="list-group">';
										foreach( $level_three_clilds as $level_three_clild ):
											echo '<li class="list-group-item border-0 level-second"><input class="form-check-input" name="postcategoryid[]" type="checkbox" value="'.$level_three_clild->term_id.'" id="thirdlevelchild"> <label class="form-check-label" for="thirdlevelchild">'.$level_three_clild->name.'</label>';
											
											//---level_four_clild ---
											$level_four_clilds = get_terms( array(
												'taxonomy'    => $taxonomy,
												'parent'      => $level_three_clild->term_id, 
												'depth'       => 1,
												'hide_empty'  => false
											));
											if( !empty( $level_four_clilds ) && !is_wp_error( $level_four_clilds )){
												echo '<ul class="list-group">';
												foreach( $level_four_clilds as $level_four_clild ):
													echo '<li class="list-group-item border-0 level-second"><input class="form-check-input" name="postcategoryid[]" type="checkbox" value="'.$level_four_clild->term_id.'" id="fourthlevelchild"> <label class="form-check-label" for="fourthlevelchild">'.$level_four_clild->name.'</label></li>';
												endforeach ; //level_four_clild
												echo '</ul>';
											}
										endforeach ; //level_three_clild
									echo '</ul>';
								}
							endforeach ;  //level_tow_clild
						echo '</li>
						</li>
						</ul></li>';
					}
					
				endforeach ; //level_one_clilds
				echo '</ul>';
			}
			echo '<input type="hidden" name="hiddenpostcategory" value="category">';
			
		}
		else{
			$taxonomy = 'events';
			$parent_ID = 0;
			$exclude = array(1);			
        
			//-level_one_clilds-
			$level_one_clilds = get_terms( array(
				'taxonomy'   => $taxonomy,
				'parent'     => $parent_ID ,
				'depth'      => 3,
				'hide_empty' => false
			) );
			echo '<label class="form-label px-2" for="posttypecategory">Select Categories</label>';
			if( !empty( $level_one_clilds ) ){
				echo '
				<div class="eventcategory">
				<h6>Event Category</h6>';
					echo '<ul class="list-group postcategories">';
					foreach( $level_one_clilds as $level_one_clild ):
						
						echo '<li class="list-group-item border-0 level-first"><input class="form-check-input" name="eventfirstlevelchild[]" type="checkbox" value="'.$level_one_clild->term_id.'" id="firstchild"> <label class="form-check-label" for="firstlevelchild">'.$level_one_clild->name.'</label>';
						
						//--level_tow_clilds--
						$level_tow_clilds = get_terms( array(
						  'taxonomy'    => $taxonomy,
						  'parent'      => $level_one_clild->term_id, 
						  'depth'       => 1,
						  'hide_empty'  => false
						));
						if( !empty( $level_tow_clilds ) ){
							echo '<ul class="list-group">';
								foreach( $level_tow_clilds as $level_tow_clild ): 
									echo '<li class="list-group-item border-0 level-second"><input class="form-check-input" name="eventfirstlevelchild[]" type="checkbox" value="'.$level_tow_clild->term_id.'" id="secondlevelchild"> <label class="form-check-label" for="secondlevelchild">'.$level_tow_clild->name.'</label>';
								  
								   //---level_three_clild ---
								   $level_three_clilds = get_terms( array(
									'taxonomy'    => $taxonomy,
									'parent'      => $level_tow_clild->term_id, 
									'depth'       => 1,
									'hide_empty'  => false
								  ));
									if( !empty( $level_three_clilds ) && !is_wp_error( $level_three_clilds ) ){
										echo '<ul class="list-group">';
											foreach( $level_three_clilds as $level_three_clild ):
												echo '<li class="list-group-item border-0 level-second"><input class="form-check-input" name="eventfirstlevelchild[]" type="checkbox" value="'.$level_three_clild->term_id.'" id="thirdlevelchild"> <label class="form-check-label" for="thirdlevelchild">'.$level_three_clild->name.'</label>';
												
												//---level_four_clild ---
												$level_four_clilds = get_terms( array(
													'taxonomy'    => $taxonomy,
													'parent'      => $level_three_clild->term_id, 
													'depth'       => 1,
													'hide_empty'  => false
												));
												if( !empty( $level_four_clilds ) && !is_wp_error( $level_four_clilds )){
													echo '<ul class="list-group">';
													foreach( $level_four_clilds as $level_four_clild ):
														echo '<li class="list-group-item border-0 level-second"><input class="form-check-input" name="eventfirstlevelchild" type="checkbox" value="'.$level_four_clild->term_id.'" id="fourthlevelchild"> <label class="form-check-label" for="fourthlevelchild">'.$level_four_clild->name.'</label></li>';
													endforeach ; //level_four_clild
													echo '</ul>';
												}
											endforeach ; //level_three_clild
										echo '</ul>';
									}
								endforeach ;  //level_tow_clild
							echo '</li>
							</li>
							</ul></li>';
						}
						
					endforeach ; //level_one_clilds
					echo '</ul>';
				echo '
				<input type="hidden" name="hiddeneventcategory" value="events">
				</div>';
								
			}
			
			
			$taxonomy = 'locations';
			$parent_ID = 0;
			$exclude = array(1);			
        
			//-level_one_clilds-
			$level_one_clilds = get_terms( array(
				'taxonomy'   => $taxonomy,
				'parent'     => $parent_ID ,
				'depth'      => 3,
				'hide_empty' => false
			) );
			
			if( !empty( $level_one_clilds ) ){
				echo '
				<div class="locationcategory">
					<h6>Location Category</h6>';
					echo '<ul class="list-group postcategories">';
					foreach( $level_one_clilds as $level_one_clild ):
						
						echo '<li class="list-group-item border-0 level-first"><input class="form-check-input" name="locationfirstlevelchild[]" type="checkbox" value="'.$level_one_clild->term_id.'" id="firstchild"> <label class="form-check-label" for="firstlevelchild">'.$level_one_clild->name.'</label>';
						
						//--level_tow_clilds--
						$level_tow_clilds = get_terms( array(
						  'taxonomy'    => $taxonomy,
						  'parent'      => $level_one_clild->term_id, 
						  'depth'       => 1,
						  'hide_empty'  => false
						));
						if( !empty( $level_tow_clilds ) ){
							echo '<ul class="list-group">';
								foreach( $level_tow_clilds as $level_tow_clild ): 
									echo '<li class="list-group-item border-0 level-second"><input class="form-check-input" name="locationfirstlevelchild[]" type="checkbox" value="'.$level_tow_clild->term_id.'" id="secondlevelchild"> <label class="form-check-label" for="secondlevelchild">'.$level_tow_clild->name.'</label>';
								  
								   //---level_three_clild ---
								   $level_three_clilds = get_terms( array(
									'taxonomy'    => $taxonomy,
									'parent'      => $level_tow_clild->term_id, 
									'depth'       => 1,
									'hide_empty'  => false
								  ));
									if( !empty( $level_three_clilds ) && !is_wp_error( $level_three_clilds ) ){
										echo '<ul class="list-group">';
											foreach( $level_three_clilds as $level_three_clild ):
												echo '<li class="list-group-item border-0 level-second"><input class="form-check-input" name="locationfirstlevelchild[]" type="checkbox" value="'.$level_three_clild->term_id.'" id="thirdlevelchild"> <label class="form-check-label" for="thirdlevelchild">'.$level_three_clild->name.'</label>';
												
												//---level_four_clild ---
												$level_four_clilds = get_terms( array(
													'taxonomy'    => $taxonomy,
													'parent'      => $level_three_clild->term_id, 
													'depth'       => 1,
													'hide_empty'  => false
												));
												if( !empty( $level_four_clilds ) && !is_wp_error( $level_four_clilds )){
													echo '<ul class="list-group">';
													foreach( $level_four_clilds as $level_four_clild ):
														echo '<li class="list-group-item border-0 level-second"><input class="form-check-input" name="locationfirstlevelchild[]" type="checkbox" value="'.$level_four_clild->term_id.'" id="fourthlevelchild"> <label class="form-check-label" for="fourthlevelchild">'.$level_four_clild->name.'</label></li>';
													endforeach ; //level_four_clild
													echo '</ul>';
												}
											endforeach ; //level_three_clild
										echo '</ul>';
									}
								endforeach ;  //level_tow_clild
							echo '</li>
							</li>
							</ul></li>';
						}
						
					endforeach ; //level_one_clilds
					echo '</ul>';
				echo '
				<input type="hidden" name="hiddenlocationcategory" value="locations">
				</div>';
			}
		}
		
	}
	exit();
}

/*****************************************************************************************
 								Add New Post Hook
******************************************************************************************/
function newPostCreation(){
	if( !empty( get_option('shortcode_page_id') ) ){
		$postid = get_option('shortcode_page_id');
		$query = new WP_Query(
			array(
				'post_type'			=> 'page',
				'post_status'		=> 'publish',
				'post__in'			=> array($postid)
			)
		);
		if( $query->have_posts()): 
			while($query->have_posts()): $query->the_post();
			$pageid = get_the_ID();
			$content = get_the_content($pageid);
			echo do_shortcode($content);
			endwhile;
			wp_reset_query();
		else:

		endif;
	}
}

/********************************************************************************************************
                            View all posts section
********************************************************************************************************/
function viewAllPosts(){
    if(function_exists('get_user_details') ){
        $userLists 		=  get_user_details();
        $userId 		= $userLists->ID;
        $args           = array(
            'post_type'         => array('post','event'),
            'post_status'       => 'publish',
            'posts_per_page'    => -1,
            'author'            => $userId,
            'orderby'           => 'ID',
            'order'             => 'ASC'
        );
        
        $query = new WP_Query( $args ); 
       if( $query->have_posts()):?>
            <table id="example" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th><input type="checkbox" name="allchecked" value="all" /></th>
                        <th>Name</th>
                        <th>Content</th>
                        <th>Owner</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date</th>
						<th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while( $query->have_posts()): $query->the_post();
                        $post_id = get_the_ID();
                        $authorId = get_post_field( 'post_author', $post_id ); 
                        $author_obj = get_user_by('id', $authorId); 
                        $postType = get_post_type( $post_id);

                    ?>
                        <tr>
                            <td><input type="checkbox" name="postids" value="<?php echo $post_id;?>" /></td>
                            <td><a href="<?php echo get_the_permalink( get_the_ID());?>"><?php echo get_the_title($post_id);?></a></td>
                            <td><?php echo get_the_excerpt();"..." ?></td>
                            <td><?php echo $author_obj->user_login;?></td>
                            <td><?php echo  $postType;?></td>
                            <td> <?php echo get_post_status( get_the_ID());?></td>
                            <td><?php echo $post_date = get_the_date( 'l dS M Y', get_the_ID() );?></td>
							<td> 
								<ul class="list-inline m-0">
									<li class="list-inline-item">
										<button class="btn btn-success btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
									</li>
									<li class="list-inline-item">
										<button class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button>
									</li>
								</ul>
							</td>
                        </tr>
                    <?php endwhile; 
                    wp_reset_query(); ?>
                    
                </tbody>
                <tfoot>
                    <tr>
                        <th><input type="checkbox" name="allchecked" value="all" /></th>
                        <th>Name</th>
                        <th>Content</th>
                        <th>Owner</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date</th>
						<th>Date</th>
                    </tr>
                </tfoot>
        </table>
        <?php 
       else:
        echo '<span class="alert d-block alert-danger">No property found.</span>';
       endif;
    }
}




/********************************************************************************************************
                            Child category waler to extend HTML section
*********************************************************************************************************/
class My_Category_Walker extends Walker_Category {
    function start_lvl(&$output, $depth=1, $args=array()) {
        $output .= "\n<div class=\"product_cats\">\n";
    }

    function end_lvl(&$output, $depth=0, $args=array()) {
        $output .= "</div>\n";
    }
    function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        extract($args);
        $cat_name = esc_attr( $category->name );
        $cat_name = apply_filters( 'list_cats', $cat_name, $category );
        $termchildren = get_term_children( $category->term_id, $category->taxonomy );
        if($category->count >0 ){
            $aclass =  ' class="cat_has_posts" ';
        }
        else
        $aclass =  ' class="cat_has_no_posts" ';
        if($category->parent != 0)
            $link = '&nbsp;&nbsp;<a '.$aclass.' href="' . esc_url( get_term_link($category) ) . '" ';
        else
            $link = '<a '.$aclass.' href="' . esc_url( get_term_link($category) ) . '" ';
        if ( $use_desc_for_title == 0 || empty($category->description) )
        $link .= 'title="' . esc_attr( sprintf(__( 'View all posts filed under %s' ), $cat_name) ) . '"';
        else
        $link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
        $link .= '>';
        $link .= $cat_name . '</a>';
        if ( !empty($show_count) )
        $link .= ' (' . intval($category->count) . ')';
        if ( 'list' == $args['style'] ) {
            $output .= "\t<div";
            $class = 'cat-item cat-item-' . $category->term_id;
            if ( !empty($current_category) ) {
                $_current_category = get_term( $current_category, $category->taxonomy );
                if ( $category->term_id == $current_category )
                $class .=  ' current-cat';
                elseif ( $category->term_id == $_current_category->parent )
                $class .=  ' current-cat-parent';
            }
            $output .=  ' class="' . $class . '"';
            $output .= ">$link\n";
        } else {
            $output .= "\t$link<br />\n";
        }
    }
    function end_el(&$output, $item, $depth=0, $args=array()) {
        $output .= "</div>\n";
    }
}
?>