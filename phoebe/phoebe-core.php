<?php

/**
 * Recursively(-ish) fetch the info for $item
 *
 * @param WP_Term $item a object of the currentlyr-looked-into menu item
 * @param array $menu_items a list of menu items (fetched with `wp_get_nav_menu_items`)
 * @return array neat array containing the menu item, its attributes, an "active" flag, and its children
 */
function phoebe_get_menu_item_info($item, $menu_items) {
  $item_id = $item->ID;
  $children_items = phoebe_get_menu_item_children($item_id, $menu_items);
  $is_active = phoebe_is_menu_item_active($item) || phoebe_are_children_items_active($children_items);

  /**
   * Here's what you get:
   */
  return array(
    'id' => $item_id,
    'page-id' => $item->object_id, // the actual object ID the item points to
    'label' => $item->title,
    'is-active' => $is_active,
    'slug' => sanitize_title($item->title),
    'attr' => array(
      'url' => $item->url,
      'target' => $item->target,
      'title' => $item->attr_title,
      'classes' => implode(' ', $item->classes),
    ),
    'children' => phoebe_get_menu_elements($children_items, $menu_items),
  );
}

/**
 * Loop through the items in the given menu
 *
 * @param array $level the menu items of the currently-looked-into depth level
 * @param array $menu_items a list of menu items (fetched with `wp_get_nav_menu_items`)
 * @return array the request menu elements
 */
function phoebe_get_menu_elements($level, $menu_items) {
  $menu = array();

  if( $level ) {
    foreach( $level as $item ) {
      $menu[] = phoebe_get_menu_item_info($item, $menu_items);
    }
  }

  return $menu;
}

/**
 * Test whether the given menu elements are active
 *
 * @param array $children a list of menu items
 * @return boolean true if a child of the currently-looked-into element has active children
 */
function phoebe_are_children_items_active($children) {
  if($children) {
    $actives = array_map('phoebe_is_menu_item_active', $children);
    return in_array(true, $actives);
  } else {
    return false;
  }
}

/**
 * Returns true if the current page being viewed is the menu item $item.
 *
 * @param WP_Term a menu item
 * @return boolean true if $item is the currently displayed page
 */
function phoebe_is_menu_item_active($item) {
  $item_object_id = $item->object_id;
  $current_object_id = get_queried_object_id();

  return $item_object_id == $current_object_id;
}

/**
 * Get the given item $id's children items
 *
 * @param string $id the ID of the currently-looked-into menu item
 * @param array $items the items to look through for possible children
 * @return array|false an array containing the children if any are found, otherwise false
 */
function phoebe_get_menu_item_children($id, $items) {
  $children = array();

  foreach($items as $item) {
    if( $item->menu_item_parent == $id ) {
      $children[] = $item;
    }
  }

  if( count($children) == 0 ) {
    return false;
  }

  return $children;
}
