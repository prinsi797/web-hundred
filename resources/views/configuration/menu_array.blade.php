<?php

$defaultIcon = 'fa fa-circle-o';
$menu = [
  [
    'name' => 'Dashboard',
    'icon' => 'fa fa-dashboard',
    'is_external' => false,
    'dropdown' => false,
    'route' => 'admin.dashboard',
    'dropdown_items' => [],
  ],

  [
    'name' => 'Admins',
    'icon' => 'fa fa-user',
    'is_external' => false,
    'dropdown' => false,
    'route' => 'admin.users.index',
    'dropdown_items' => [],
  ],


  [
    'name' => 'App Users',
    'icon' => 'fa fa-users',
    'is_external' => false,
    'dropdown' => false,
    'route' => 'admin.app_users.index',
    'dropdown_items' => [],
  ],

  // [
  //   'name' => 'User Roles',
  //   'icon' => 'fa fa-tasks',
  //   'is_external' => false,
  //   'dropdown' => false,
  //   'route' => 'admin.roles.index',
  //   'dropdown_items' => [],
  // ],

  // [
  //   'name' => 'Stats',
  //   'icon' => 'fa fa-tasks',
  //   'is_external' => false,
  //   'dropdown' => false,
  //   'route' => 'admin.stats.index',
  //   'dropdown_items' => [],
  // ],

  [
    'name' => 'App User Stats',
    'icon' => 'fa fa-bar-chart',
    'is_external' => false,
    'dropdown' => false,
    'route' => 'admin.stats_users.index',
    'dropdown_items' => [],
  ],

  [
    'name' => 'App User Benchpress',
    'icon' => 'fa fa-bar-chart',
    'is_external' => false,
    'dropdown' => false,
    'route' => 'admin.benchpress.index',
    'dropdown_items' => [],
  ],

  [
    'name' => 'App User Deadlifts',
    'icon' => 'fa fa-arrow-up',
    'is_external' => false,
    'dropdown' => false,
    'route' => 'admin.deadlifts.index',
    'dropdown_items' => [],
  ],

  [
    'name' => 'App User Powercleans',
    'icon' => 'fa fa-bolt',
    'is_external' => false,
    'dropdown' => false,
    'route' => 'admin.powercleans.index',
    'dropdown_items' => [],
  ],

  [
    'name' => 'App User Squats',
    'icon' => 'fa fa-bar-chart',
    'is_external' => false,
    'dropdown' => false,
    'route' => 'admin.squats.index',
    'dropdown_items' => [],
  ],

  [
    'name' => 'App User Heights',
    'icon' => 'fa fa-arrows-v',
    'is_external' => false,
    'dropdown' => false,
    'route' => 'admin.app_user_heights.index',
    'dropdown_items' => [],
  ],

  [
    'name' => 'App User Weights',
    'icon' => 'fa fa-balance-scale',
    'is_external' => false,
    'dropdown' => false,
    'route' => 'admin.app_user_weights.index',
    'dropdown_items' => [],
  ],
  
  [
    'name' => 'Schools',
    'icon' => 'fa fa-university',
    'is_external' => false,
    'dropdown' => false,
    'route' => 'admin.schools.index',
    'dropdown_items' => [],
  ],

  [
    'name' => 'Feedbacks',
    'icon' => 'fa fa-comment',
    'is_external' => false,
    'dropdown' => false,
    'route' => 'admin.app_user_feedbacks.index',
    'dropdown_items' => [],
  ]

];

// Set default icons for dropdown items

foreach ($menu as &$menuItem) {
  if ($menuItem['dropdown']) {
    foreach ($menuItem['dropdown_items'] as &$dropdownItem) {
      $dropdownItem['icon'] = $defaultIcon;
    }
  }
}

unset($menuItem, $dropdownItem);

if (auth()->user()->isUser) {
    foreach ($menu as $key => $item) {
        if ($item['name'] === 'User Roles' || $item['name'] === 'Users') {
            unset($menu[$key]);
        }
    }
}
