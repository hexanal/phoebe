<?php
require 'phoebe-core.php';

/**
 * ————————————————————————————————————————————————————————————————
 * Get a neat array of all the menu elements of the given menu slug
 * ————————————————————————————————————————————————————————————————
 *
 * @param string $slug The Wordpress menu slug of the menu you want to display.
 * @return array a hierarchical array of a WP menu
 */
function phoebe_get_menu($slug) {
  $wp_menu = wp_get_nav_menu_object($slug);

  if( !$wp_menu ) {
    return null;
  }

  $menu_items = wp_get_nav_menu_items( $wp_menu->term_id );
  $parents = array_filter($menu_items, function($item) {
    return $item->menu_item_parent == 0;
  });

  return phoebe_get_menu_elements($parents, $menu_items);
}

/**
 * ———————————————————————————————————————————————————————
 * Given a page ID, returns an array of its children pages
 * ———————————————————————————————————————————————————————
 *
 * @param int $parentId the page ID of the parent page
 * @return array an array containing the children pages along with some attributes
 */
function phoebe_get_children_pages($parentId) {
  global $post;

  $pages = get_posts([
    'posts_per_page' => -1,
    'post_type' => 'page',
    'post_parent' => $parentId,
    'orderby' => 'menu_order',
    'order' => 'asc'
  ]);

  return array_map(function($navItem) use ($post) {
    return [
      'active' => $navItem->ID === $post->ID,
      'label' => $navItem->post_title,
      'href' => get_permalink($navItem->ID)
    ];
  }, $pages);
}

/**
 * ———————————————————————————————————————————————————————
 * Given a page ID, returns an array of its siblings pages
 * ———————————————————————————————————————————————————————
 *
 * @param int (optional) the ID of the sibling page, defaults to the current page
 * @return array an array containing the siblings pages along with some attributes
 */
function phoebe_get_sibling_pages($id = null) {
  if (!$id) {
    global $post;
    $id = $post->ID;
  }

  $parentId = wp_get_post_parent_id($id);
  $parent = get_post($parentId);
  $parentItem = [
    [
      'active' => false,
      'label' => $parent->post_title,
      'href' => get_permalink($parentId)
    ]
  ];
  $siblings = phoebe_get_children_pages($parentId);

  return array_merge($parentItem, $siblings);
}
