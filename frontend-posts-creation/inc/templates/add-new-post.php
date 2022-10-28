<?php  /* Template Name: Add New Post */  
get_header();  
if ( !is_user_logged_in() ) {
    wp_redirect( get_site_url() ); 
    exit;
}
?>
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <?php  do_action( 'user_left_sidebar'); ?>  
        </div>
            <div class="col-md-8">
            <?php do_action('user-account'); ?>
            </div>
    </div>
</div>
<?php 
get_footer();
?>