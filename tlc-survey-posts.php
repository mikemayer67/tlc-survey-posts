<?php

/**
 * Plugin Name: TLC Survey Posts
 * Plugin URI: https://github.com/mikemayer67/tlc-survey-posts
 * Description: Plugin to understand how custom post types work
 * Version: 0.0.1
 * Author: Michael A. Mayer
 * Requires PHP: 5.3.0
 * License: GPLv3
 * License URL: https://www.gnu.org/licenses/gpl-3.0.html
 */

if( ! defined('WPINC') ) { die; }

function tlc_register_post_type()
{
  $labels = array(
   'name' => 'Duck Posts',
   'singular_name' => 'Duck',
   'add_new' => 'New Duck',
   'add_new_item' => 'Add New Duck',
   'edit_item' => 'Edit Duck',
   'new_item' => 'New Ducky',
   'view_item' => 'View Duckies',
   'search_items' => 'Search Ducks',
   'not_found' =>  'No Ducks Found',
   'not_found_in_trash' => 'No Ducks found in Trash',
  );

  $args = array(
   'labels' => $labels,
   'has_archive' => false,
   'public' => false,
   'show_ui' => true,
   'show_in_rest' => false,
  );


  register_post_type('tlc_duck',$args);
}

function tlc_handle_shortcode()
{
  ob_start();

  $form_uri = $_SERVER['REQUEST_URI'];

?>
<form method=post action='<?=$form_uri?>'>
  <input type=hidden name=action value=add_duck>
  <input type=submit value="Add a duck">
</form>

<?php
  $html = ob_get_contents();
  ob_end_clean();
  return $html;
}

function tlc_add_duck()
{
  error_log("Add a duck");
  $duck_args = array(
    'post_content' => 'test duck',
    'post_title' => 'ducky duck',
    'post_type' => 'tlc_duck',
  );
  error_log(print_r($duck_args,true));
  $post_id = wp_insert_post($duck_args,true);
  error_log("added duck: $post_id");

  $qargs = array(
    'post_type' => 'tlc_duck',
  );

  $duck_posts = new WP_Query($qargs);
  if($duck_posts->have_posts())
  {
    while($duck_posts->have_posts())
    {
      $duck = $duck_posts->the_post();
      error_log(print_r($duck,true));
    }
  }
  else
  {
    error_log("No ducks");
  }
}

add_action('init','tlc_register_post_type');
add_shortcode('duck-posts','tlc_handle_shortcode');

$action = $_POST['action'] ?? "";
if( $action == 'add_duck')
{
  error_log("Need to add a duck");
  add_action('init','tlc_add_duck');
}

