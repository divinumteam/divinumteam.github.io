<?php

  unset($_SESSION["userid"]);
  unset($_SESSION["userpass"]);
  unset($_SESSION["userlogin"]);
  setcookie("u_id", $user["client_id"], time()-(60*60*24*7), '/', null, null, true );
  setcookie("u_password", $user["password"], time()-(60*60*24*7), '/', null, null, true );
  setcookie("u_login", 'ok', time()-(60*60*24*7), '/', null, null, true );
  session_destroy();
  Header("Location:".site_url(''));
