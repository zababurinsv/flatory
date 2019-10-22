<?php
global $site_root, $version, $site_root, $server_site_root, $file_site_root;

$site_root = "sport";

//$file_site_root = $_SERVER['DOCUMENT_ROOT']."/".$site_root;
$file_site_root = $_SERVER['DOCUMENT_ROOT'];
$server_site_root = "http://".$_SERVER["HTTP_HOST"];

ini_set("include_dir", ini_get("include_dir").":".$file_site_root);

$version = "0.10";
?>