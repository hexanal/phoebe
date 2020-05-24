# Phoebe

> Phoebe is another name for the goddess that the Greeks called Artemis and the Romans called Diana. She was the youthful goddess of Earth's Moon, forests, wild animals, and hunting. Sworn to chastity and independence, she never married and was closely identified with her brother Apollo.​

A rather lightweight drop-in helper for Wordpress themes that aims to helps with
fetching and displaying navigation menus. Here's what it does:

- uses the default Wordpress Menus system
- fetches all the items of a menu and spits out a regular old array
- the active state of the items will be given by a `is-active` key
- a parent item will be flagged as being active if one of its children is active

## How to use

### `phoebe_get_menu('menu-slug')`

Fetches all the items from a menu and formats it into an array.

Example:

```php
// provided you have setup a menu with slug 'main-nav' in the WP admin:

$menu = phoebe_get_menu('main-nav');
```

This would give you something like:

```php
[
  [
    'id' => 15, // the ID of the `nav_menu_item` WP_Post object
    'page-id' => 2, // the ID of the actual object (usually a page) the menu item points to
    'label' => 'Homepage', // the menu item label (as defined in Appearance > Menus)
    'is-active' => true, // boolean value is true if the page is active (or one of its children is)
    'slug' => 'homepage', // this is a kebab-case version of the label (can be used to slap on a class or a data attribute on a link)
    'attr' => [ // the attributes
      'url' => 'http://phoebe.local/', // the link destination
      'target' => '', // the item '_target' attribute config (as defined in the menu)
      'title' => '', // the item title attribute
      'classes' => '', // the item 'CSS class' config (as defined in the menu)
    ],
    'children' => [] // no children for this one 😥
  ],
  [
    'id' => 13,
    'page-id' => 8,
    'label' => 'Another page in the wall',
    'is-active' => false,
    'slug' => 'another-page-in-the-wall',
    'attr' => [
      'url' => 'http://phoebe.local/another-page-in-the-wall/',
      'target' => '',
      'title' => '',
      'classes' => '',
    ],
    'children' => [ // childrens will have the same exact kind of array structure
      [
        'id' => 14,
        'page-id' => 10,
        'label' => 'Child page',
        'is-active' => false,
        'slug' => 'child-page',
        'attr' => [
          'url' => 'http://phoebe.local/another-page-in-the-wall/child-page/',
          'target' => '',
          'title' => '',
          'classes' => '',
        ],
        'children' => []
      ]
    ],
  ]
```

***

### `phoebe_get_children_pages(8)`

> This (and the following function) is based on the actual **pages** found in the Wordpress admin, and **not** on navigation menus.

Lists the children pages of the current page. That comes in handy when you want
to build a quick contextual menu for a given page and show its children.

Based on the previous example structure, running this function would return:

```php
[
  'active' => false,
  'label' => 'Child page',
  'href' => 'http://phoebe.local/another-page-in-the-wall/child-page/'
];
```

Notice we're using the page ID here (`page-id` in the array returned by the `phoebe_get_menu` function)

***

### `phoebe_get_siblings_pages(8, false)`

Lists the *siblings* pages of the current page. That comes in handy if you wish
to build a quick sidebar menu for a given page and show the pages of the same
depth/hierarchy level.

Based on my previous example, running this function would return the homepage,
which is the only sibling of page ID **13**.

The second argument determines whether we should display the currently active
page item or not (you can always decide to exclude it when looping through the
items, just by looking at the `active` flag)

```php
[
  'active' => true,
  'label' => 'Homepage',
  'href' => 'http://phoebe.local/'
];
```
