<?php

$menu = array(
  0 =>
  array(
    'name' => 'Dashboard',
    'icon' => 'fa fa-dashboard',
    'is_external' => false,
    'dropdown' => false,
    'route' => 'admin.dashboard',
    'dropdown_items' =>
    array(),
  ),
);


// $report_menu =  [
//   'name' => 'Reports',
//   'icon' => 'fa fa-file',
//   'dropdown' => false,
//   'route_list' => [
//     'admin.reports.preview'
//   ],
//   'route' => 'admin.reports.index',
//   'dropdown_items' =>
//   array(),
// ];

// if (auth()->user()->hasActiveSubscription()) {
//   // $menu[] = $report_menu;
// }
// $menu[] = [
//   'name' => 'Edit Profile',
//   'icon' => 'fa fa-edit',
//   'is_external' => false,
//   'dropdown' => false,
//   'route' => 'admin.settings.edit_profile',
//   'dropdown_items' =>
//   array(),
// ];


