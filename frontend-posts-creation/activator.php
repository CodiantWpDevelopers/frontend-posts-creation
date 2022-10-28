<?php 
register_activation_hook( __FILE__, 'pagesCreation' ) ;
function pagesCreation(){
    $new_page_id = wp_insert_post( array(
        'post_title'     => 'pagee',
        'post_type'      => 'page',
        'post_name'      => 'pagee',
        'comment_status' => 'closed',
        'ping_status'    => 'closed',
        'post_content'   => '',
        'post_status'    => 'publish',
        'menu_order'     => 0,
        // Assign page template
        'page_template'  => 'user-dashboard.php'
    ) );
    update_post_meta( $new_page_id,'_wp_page_template','user-dashboard.php');
}
?>