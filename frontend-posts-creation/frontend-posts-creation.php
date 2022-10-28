<?php 
/* 
Plugin Name: Frontend Post Creation
Plugin URI: https://codiant.com/wordpress/pluins/frontend-post
Description: Creating posts and categories from front end. 
Version: 2.0
license: GPL-2.0
Author : Bijay
Author URI: https://codiant.com/wordpress/author/bijay112
Text Domain: frontend-posts-creation
*/

if (!defined( 'ABSPATH')) exit; 
define( 'FRONTEND_LOCATION', dirname( __FILE__) );
define( 'FRONTEND_LOCATION_URL', plugins_url('', __FILE__) );
define( 'FRONTEND_BASENAME', basename( FRONTEND_LOCATION ) );
require_once( FRONTEND_LOCATION."/admin/activator.php");
register_activation_hook( __FILE__, 'pagesCreation' ) ;
require_once( FRONTEND_LOCATION."/admin/plugin-hooks.php");
require_once( FRONTEND_LOCATION."/inc/class-pagetemplater.php"); 
require_once( FRONTEND_LOCATION."/inc/hooks/front-end-hooks.php");
require_once( FRONTEND_LOCATION."/inc/class-slug-custom-Route.php");
?>