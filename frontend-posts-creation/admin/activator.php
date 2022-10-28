<?php 
function pagesCreation(){
    $pageslists = get_option( 'pages_lists' );
    if( empty( $pageslists ) ){
        $new_page_id = wp_insert_post( array(
            'post_title'     => 'Dashboard',
            'post_type'      => 'page',
            'post_name'      => 'dashboard',
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
            'post_content'   => '',
            'post_status'    => 'publish',
            'menu_order'     => 0,
            // Assign page template
            'page_template'  => 'user-dashboard.php'
        ) );
        update_post_meta( $new_page_id,'_wp_page_template','user-dashboard.php');
        update_post_meta( $new_page_id,'showjscss',$new_page_id);

        $my_account_id = wp_insert_post( array(
            'post_title'     => 'My Account',
            'post_type'      => 'page',
            'post_name'      => 'my-account',
            'post_parent'    => $new_page_id,
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
            'post_content'   => '',
            'post_status'    => 'publish',
            'menu_order'     => 0,
            // Assign page template
            'page_template'  => 'my-account.php'
        ) );
        update_post_meta( $my_account_id,'_wp_page_template','my-account.php');
        update_post_meta( $my_account_id,'showjscss',$my_account_id);

        $my_profile_id = wp_insert_post( array(
            'post_title'     => 'My Profile',
            'post_type'      => 'page',
            'post_name'      => 'my-profile',
            'post_parent'    => $new_page_id,
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
            'post_content'   => '',
            'post_status'    => 'publish',
            'menu_order'     => 0,
            // Assign page template
            'page_template'  => 'my-profile.php'
        ) );
        update_post_meta( $my_profile_id,'_wp_page_template','my-profile.php');
        update_post_meta( $my_profile_id,'showjscss',$my_profile_id);

        $add_new_post_id = wp_insert_post( array(
            'post_title'     => 'Add New Post',
            'post_type'      => 'page',
            'post_name'      => 'add-new-post',
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
            'post_content'   => '[create_frontend_posts]',
            'post_status'    => 'publish',
            'menu_order'     => 0,
            // Assign page template
            'page_template'  => 'add-new-post.php'
        ) );
        update_post_meta( $add_new_post_id,'_wp_page_template','add-new-post.php');
        update_post_meta( $add_new_post_id,'showjscss',$add_new_post_id);
        add_option( 'shortcode_page_id', $add_new_post_id , '', 'yes' );

        add_option( 'pages_lists', $new_page_id , '', 'yes' );
    }

}

?>