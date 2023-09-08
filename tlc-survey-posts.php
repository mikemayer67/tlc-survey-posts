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
   'taxonomies' => array('category'),
  );


  register_post_type('tlc_duck',$args);
}

function tlc_activate() {
  error_log("Activate Ducks");
  tlc_register_post_type();
  flush_rewrite_rules();
}

function tlc_deactivate() {
  error_log("Deactivate Ducks");
  unregister_post_type('tlc_duck');
}


function tlc_uninstall() {
  error_log("Uninstall Ducks");
}

add_action('init','tlc_register_post_type');
register_activation_hook(__FILE__,'tlc_activate');
register_deactivation_hook(__FILE__,'tlc_deactivate');
register_uninstall_hook(__FILE__,'tlc_uninstall');


function tlc_handle_shortcode()
{
  ob_start();

  $form_uri = $_SERVER['REQUEST_URI'];

  $duck_ids = get_posts(
    array(
      'post_type' => 'tlc_duck',
      'numberposts' => -1,
      'meta_key' => 'bio',
      'fields' => 'ids',
    )
  );

  $ids = implode(", ",$duck_ids);
  echo "<h2>$ids</h2>";

  $ducks = get_posts(
    array(
      'post_type' => 'tlc_duck',
      'numberposts' => -1,
    )
  );
?>
<table>
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Content</th>
    <th>Bio</th>
  </tr>
<?php foreach( $ducks as $duck ) { 
  $id = $duck->ID;
  $bio = get_post_meta($id,'bio',true);
?>
  <tr>
    <td><?=$duck->ID?></td>
    <td><?=$duck->post_title?></td>
    <td><?=$duck->post_content?></td>
    <td><?=$bio?></td>
  </tr>
<?php } ?>
</table>
<form method=post action='<?=$form_uri?>'>
  <input type=hidden name=add_duck value=1>
  <input type=submit value="Add a duck">
</form>

<form method=post action='<?=$form_uri?>'>
  <input type=hidden name=remove_ducks value=1>
  <input type=submit value="Remove all ducks">
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
    'post_title' => 'darkwing duck',
    'post_type' => 'tlc_duck',
    'post_status' => 'publish',

  );
  $post_id = wp_insert_post($duck_args,true);
  error_log("added duck: $post_id");

  $bio_id = update_post_meta($post_id,'bio',"well hello there");
  error_log("added bio metadata: $bio_id");
}

function tlc_remove_ducks()
{
  error_log("Remove all ducks");
  $duck_ids = get_posts(
    array(
    'post_type' => 'tlc_duck',
    'numberposts' => -1,
    'fields' => 'ids',
    )
  );
  foreach( $duck_ids as $id )
  {
    error_log("Removing duck $id");
    wp_delete_post($id,true);
  }
}

add_shortcode('duck-posts','tlc_handle_shortcode');

if( array_key_exists('add_duck',$_POST) )
{
  error_log("Need to add a duck");
  add_action('init','tlc_add_duck');
}

if( array_key_exists('remove_ducks',$_POST) )
{
  error_log("Need to remove all ducks");
  add_action('init','tlc_remove_ducks');
}

