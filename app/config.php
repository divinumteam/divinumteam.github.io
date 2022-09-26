<?php

define('PATH', realpath('.'));
define('SUBFOLDER', false);
define('URL', 'https://onlinesmmbayi.com');
define('STYLESHEETS_URL', '//onlinesmmbayi.com');

error_reporting(0);
date_default_timezone_set('Europe/Istanbul');

return [
  'db' => [
    'name'    =>  'onlinesmmbayi_fayujsiker',   //// Database Adı
    'host'    =>  'localhost',       //// Buna Elleme (varsayılan localhost)
    'user'    =>  'onlinesmmbayi_fayujsiker',   //// Database Kullanıcı
    'pass'    =>  'coderfayujx.', //// Database Kullanıcı Şifresi
    'charset' =>  'utf8mb4' 
  ]
];


