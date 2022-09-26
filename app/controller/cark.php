<?php

$title .= $languageArray["cark.title"];

if( $_SESSION["userlogin"] != 1  || $user["client_type"] == 1  ){
  Header("Location:".site_url('logout'));
}

?>
