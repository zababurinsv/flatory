<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
| https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
| $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
| $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
| $route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples: my-controller/index -> my_controller/index
|   my-controller/my-method -> my_controller/my_method
*/

$route['default_controller'] = "catalog";
$route['get'] = "api/item";
$route['get'] = "api/news";
$route['get'] = "api/testt";
$route['translate_uri_dashes'] = true;
///$route['objectcard/add_favorites/(:any)'] = "objectcard/add_favorites/$1";
// search objects
$route['catalog/search'] = "catalog/search";
// map
$route['catalog/map'] = "catalog/map";
// card of object
$route['catalog/(:any)'] = "catalog/objectcard/$1";
// news
$route['news'] = "news";
$route['news/preview/(:any)'] = "news/preview/$1";
$route['news/(:any)'] = "news/news_item/$1";
// articles
$route['articles'] = "articles";
$route['articles/preview/(:any)'] = "articles/preview/$1";
$route['articles/(:any)'] = "articles/articles_item/$1";
// reviews
$route['reviews'] = "reviews";
$route['reviews/preview/(:any)'] = "reviews/preview/$1";
$route['reviews/(:any)'] = "reviews/reviews_item/$1";
// tags
$route['tags/(:any)'] = "tags/index/$1";
// glossary
$route['kartoteka/organizations/(:any)'] = "kartoteka/organizations/$1";
$route['kartoteka/(:any)'] = "kartoteka/item/$1";
// etc
$route['sitemap'] = "sitemap";
$route['note/logout'] = "note/logout";
$route['note/save_description/(:any)'] = "note/save_description/$1";
$route['note/remove_note/(:any)'] = "note/remove_note/$1";
$route['note/(:any)'] = "note/index/$1";
$route['note'] = "note";
$route['reg/social_network'] = "reg/social_network";
$route['reg/registration'] = "reg/registration";



$route['admin'] = "admin/console/index";
$route['admin/cart/save_carts/(:any)'] = "admin/cart/save_carts/$1";
$route['admin/cart/(:any)'] = "admin/cart/index/$1";
$route['admin/test'] = "admin/objects/test";
$route['admin/contacts'] = "admin/contacts/index";
$route['admin/objects'] = "admin/objects/index";
$route['admin/glossary'] = "admin/glossary/index/";
$route['admin/organizations'] = "admin/organizations/index/";
$route['admin/handbk/register/tag'] = "admin/handbk/register/";

$route['admin/posts/edit/(:any)'] = "admin/posts/edit/$1";
$route['admin/posts/add/(:any)'] = "admin/posts/add/$1";
$route['admin/posts/(:any)'] = "admin/posts/type_items/$1";
$route['admin/posts'] = "admin/posts/index/";
// po-metro
$route["novostrojki-po-metro/(:any)"] = "catalog/metro_page/$1";
// geo_index
$route["novostrojki-po-okrugam-i-rajonam-moskvy"] = "catalog/geo_index/";
$route["novostrojki-po-rajonam-moskovskoj-oblasti"] = "catalog/geo_index/";
$route["novostrojki-po-nas-punktam-moskovskoj-oblasti"] = "catalog/geo_index/";
$route["novostrojki-po-metro"] = "catalog/geo_index/";
$route["(moskva|moskovskaya-oblast|novaya-moskva)"] = "catalog/geo/$1";
$route["(moskva|moskovskaya-oblast|novaya-moskva)/(:any)"] = "catalog/geo/$1/$2";
$route['404_override'] = '';