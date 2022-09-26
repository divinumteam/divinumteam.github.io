<?php

$method_name  = route(1);

if( !countRow(["table"=>"payment_methods","where"=>["method_get"=>$method_name] ]) ):
    header("Location:".site_url());
    exit();
endif;

$method       = $conn->prepare("SELECT * FROM payment_methods WHERE method_get=:get ");
$method       ->execute(array("get"=>$method_name ));
$method       = $method->fetch(PDO::FETCH_ASSOC);
$extras       = json_decode($method["method_extras"],true);


if( $method_name == "shopier" ):
    ## Shopier başla ##
    $post           = $_POST;
    $order_id       = $post['platform_order_id'];
    $status         = $post['status'];
    $payment_id     = $post['payment_id'];
    $installment    = $post['installment'];
    $random_nr      = $post['random_nr'];
    $signature      = base64_decode($_POST["signature"]);
    $expected       = hash_hmac('SHA256', $random_nr.$order_id, $extras["apiSecret"], true);
    if( $signature != $expected ):
        header("Location:".site_url());
    endif;
    if( $status == 'success' ):
        if( countRow(["table"=>"payments","where"=>["payment_privatecode"=>$order_id,"payment_delivery"=>1 ] ]) ):
            $payment        = $conn->prepare("SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_privatecode=:orderid ");
            $payment      ->execute(array("orderid"=>$order_id));
            $payment        = $payment->fetch(PDO::FETCH_ASSOC);

            $payment_bonus  = $conn->prepare("SELECT * FROM payments_bonus WHERE bonus_method=:method && bonus_from<=:from ORDER BY bonus_from DESC LIMIT 1 ");
            $payment_bonus  -> execute(array("method"=>$method["id"],"from"=>$payment["payment_amount"]));
            $payment_bonus  = $payment_bonus->fetch(PDO::FETCH_ASSOC);
            if( $payment_bonus ):
                $amount     = ($payment["payment_amount"]+($payment["payment_amount"]*$payment_bonus["bonus_amount"]/100));
            else:
                $amount     = $payment["payment_amount"];
            endif;
            $extra    = ($_POST);
            $extra    = json_encode($extra);
            $conn->beginTransaction();
            $update   = $conn->prepare("UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery, payment_extra=:extra WHERE payment_id=:id ");
            $update   = $update->execute(array("balance"=>$payment["balance"],"status"=>3,"delivery"=>2,"extra"=>$extra,"id"=>$payment["payment_id"]));
            $balance  = $conn->prepare("UPDATE clients SET balance=:balance WHERE client_id=:id ");
            $balance  = $balance->execute(array("id"=>$payment["client_id"],"balance"=>$payment["balance"]+$amount));
            $insert= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
            if( $payment_bonus ):
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla %".$payment_bonus["bonus_amount"]." bonus dahil ".$amount." TL tutarında bakiye yüklendi","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            else:
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla ".$amount." TL tutarında bakiye yüklendi","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            endif;
            if( $update && $balance ):
                $conn->commit();
            else:
                $conn->rollBack();
            endif;
        else:
        endif;
    else:
        $update   = $conn->prepare("UPDATE payments SET payment_status=:status, payment_delivery=:delivery WHERE payment_privatecode=:code  ");
        $update   = $update->execute(array("status"=>2,"delivery"=>1,"code"=>$order_id));
    endif;
    ## shopier bitti ##
    header("Location:".site_url());
elseif( $method_name == "paytr" ):
    ## paytr başla ##
    $post           = $_POST;
    $order_id       = $post['merchant_oid'];
    $payment        = $conn->prepare("SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_privatecode=:orderid ");
    $payment      ->execute(array("orderid"=>$order_id));
    $payment        = $payment->fetch(PDO::FETCH_ASSOC);
    $method       = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
    $method       ->execute(array("id"=>$payment["payment_method"] ));
    $method       = $method->fetch(PDO::FETCH_ASSOC);
    $extras       = json_decode($method["method_extras"],true);
    $merchant_key   = $extras["merchant_key"];
    $merchant_salt  = $extras["merchant_salt"];
    $hash           = base64_encode(hash_hmac('sha256', $post['merchant_oid'].$merchant_salt.$post['status'].$post['total_amount'], $merchant_key, true) );

        $dr = $post['status']; // çalışmıcak ki boş gelicek yine ver
        if($dr == 'success'){
            echo "OK";
        }

    if( $hash != $post['hash'] ):
        die('HASH Hatalı' . count($post));
        exit();
    endif;
    if( $post['status'] == 'success' ):
        if( countRow(["table"=>"payments","where"=>["payment_privatecode"=>$order_id,"payment_delivery"=>1 ] ]) ):
            $payment_bonus  = $conn->prepare("SELECT * FROM payments_bonus WHERE bonus_method=:method && bonus_from<=:from ORDER BY bonus_from DESC LIMIT 1 ");
            $payment_bonus  -> execute(array("method"=>$method["id"],"from"=>$payment["payment_amount"]));
            $payment_bonus  = $payment_bonus->fetch(PDO::FETCH_ASSOC);
            if( $payment_bonus ):
                $amount     = ($payment["payment_amount"]+($payment["payment_amount"]*$payment_bonus["bonus_amount"]/100));
            else:
                $amount     = $payment["payment_amount"];
            endif;
            $extra    = ($_POST);
            $extra    = json_encode($extra);
            $conn->beginTransaction();
            $update   = $conn->prepare("UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery, payment_extra=:extra WHERE payment_id=:id ");
            $update   = $update->execute(array("balance"=>$payment["balance"],"status"=>3,"delivery"=>2,"extra"=>$extra,"id"=>$payment["payment_id"]));
            $balance  = $conn->prepare("UPDATE clients SET balance=:balance WHERE client_id=:id ");
            $balance  = $balance->execute(array("id"=>$payment["client_id"],"balance"=>$payment["balance"]+$amount));
            $insert= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
            if( $payment_bonus ):
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla %".$payment_bonus["bonus_amount"]." bonus dahil ".$amount." TL tutarında bakiye yüklendi","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            else:
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla ".$amount." TL tutarında bakiye yüklendi","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            endif;
            if( $update && $balance ):
                $conn->commit();
                echo "OK";
            else:
                $conn->rollBack();
                echo "NO";
            endif;
        else:
        endif;
    else:
        $update   = $conn->prepare("UPDATE payments SET payment_status=:status, payment_delivery=:delivery WHERE payment_privatecode=:code  ");
        $update   = $update->execute(array("status"=>2,"delivery"=>1,"code"=>$order_id));
    endif;
## paytr bitti ##
elseif( $method_name == "paywant" ):
    ## paytr başla ##
    $apiSecret    = $extras["apiSecret"];
    $SiparisID    = $_POST["SiparisID"];
    $ExtraData    = $_POST["ExtraData"];
    $UserID       = $_POST["UserID"];
    $ReturnData   = $_POST["ReturnData"];
    $Status       = $_POST["Status"];
    $OdemeKanali  = $_POST["OdemeKanali"];
    $OdemeTutari  = $_POST["OdemeTutari"];
    $NetKazanc    = $_POST["NetKazanc"];
    $Hash         = $_POST["Hash"];
    $order_id     = $_POST["ExtraData"];
    $hashKontrol = base64_encode(hash_hmac('sha256',"$SiparisID|$ExtraData|$UserID|$ReturnData|$Status|$OdemeKanali|$OdemeTutari|$NetKazanc" . $apiKey, $apiSecret, true));
    if( $Status == 100 ):
        if( countRow(["table"=>"payments","where"=>["payment_privatecode"=>$order_id,"payment_delivery"=>1 ] ]) ):
            $payment        = $conn->prepare("SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_privatecode=:orderid ");
            $payment      ->execute(array("orderid"=>$order_id));
            $payment        = $payment->fetch(PDO::FETCH_ASSOC);

            $payment_bonus  = $conn->prepare("SELECT * FROM payments_bonus WHERE bonus_method=:method && bonus_from<=:from ORDER BY bonus_from DESC LIMIT 1 ");
            $payment_bonus  -> execute(array("method"=>$method["id"],"from"=>$payment["payment_amount"]));
            $payment_bonus  = $payment_bonus->fetch(PDO::FETCH_ASSOC);
            if( $payment_bonus ):
                $amount     = ($payment["payment_amount"]+($payment["payment_amount"]*$payment_bonus["bonus_amount"]/100));
            else:
                $amount     = $payment["payment_amount"];
            endif;
            $extra    = ($_POST);
            $extra    = json_encode($extra);
            $conn->beginTransaction();
            $update   = $conn->prepare("UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery, payment_extra=:extra WHERE payment_id=:id ");
            $update   = $update->execute(array("balance"=>$payment["balance"],"status"=>3,"delivery"=>2,"extra"=>$extra,"id"=>$payment["payment_id"]));
            $balance  = $conn->prepare("UPDATE clients SET balance=:balance WHERE client_id=:id ");
            $balance  = $balance->execute(array("id"=>$payment["client_id"],"balance"=>$payment["balance"]+$amount));
            $insert= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
            if( $payment_bonus ):
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla %".$payment_bonus["bonus_amount"]." bonus dahil ".$amount." TL tutarında bakiye yüklendi","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            else:
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla ".$amount." TL tutarında bakiye yüklendi","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            endif;
            if( $update && $balance ):
                $conn->commit();
                echo "OK";
            else:
                $conn->rollBack();
                echo "NO";
            endif;
        else:
            echo "NOO";
        endif;
    else:
        $update   = $conn->prepare("UPDATE payments SET payment_status=:status, payment_delivery=:delivery WHERE payment_privatecode=:code  ");
        $update   = $update->execute(array("status"=>2,"delivery"=>1,"code"=>$order_id));
        echo "NOOO";
    endif;
## paywant bitti ##

elseif( $method_name == "payreks_cc" ):
    //Payreks Filter Function
    function payreksFilter($string)
	{
		$escapes = ["--",";","/*","*/","//","#","||","<","|","&",">","'",")","(","*","\""];
		$filterString = $string;
		foreach ($escapes as $row)
			$filterString = str_replace($row,"",$filterString);
		return $filterString;
	}

    //Payreks Hash Control Function
    function payreksHashControl($type, $data, $key)
	{
		$context = hash_init($type, HASH_HMAC, $key);
		hash_update($context, $data);
		return hash_final($context);
	}

	if (!$_POST)
		die("do not have post values");

	$orderID = isset($_POST['order_id']) ? payreksFilter($_POST['order_id']) : null;
	$credit = isset($_POST['credit']) ? payreksFilter($_POST['credit']) : null;
	$userID = isset($_POST['user_id']) ? payreksFilter($_POST['user_id']) : null;
	$userInfo = isset($_POST['user_info']) ? payreksFilter($_POST['user_info']) : null;
	$payLabel = isset($_POST['pay_label']) ? payreksFilter($_POST['pay_label']) : null;
	$totalPrice = isset($_POST['total_price']) ? payreksFilter($_POST['total_price']) : null;
	$netPrice = isset($_POST['net_price']) ? payreksFilter($_POST['net_price']) : null;
	$hash = isset($_POST['hash']) ? $_POST['hash'] : null;

	if ($orderID === null || $credit === null || $userID === null || $userInfo === null || $payLabel === null ||
		$totalPrice === null || $netPrice === null || $hash === null)
		die("missing value");

	//Control Post Values
	if ($orderID === "" || $credit === "" || $userID === "" || $userInfo === "" || $payLabel === "" ||
		$totalPrice === "" || $netPrice === "" || $hash === "")
		die("empty value");

	$apiKey = $extras["api_key"]; //Mağaza api key
	$secretKey = $extras["api_secret"]; //Mağaza secret key

	$hashData = md5($orderID . $credit . $userID . $userInfo . $payLabel . $totalPrice . $netPrice . $apiKey);
	$hashCreate = payreksHashControl('sha256', $hashData, $secretKey);

	//Hash Control
	if ($hash !== $hashCreate)
		die("wrong hash");

	if (countRow(["table"=>"payments","where"=>["payment_privatecode"=>$orderID,"payment_delivery"=>1 ] ]))
		die("order already have");

	$controlUser = $conn->prepare("SELECT * FROM clients WHERE email = ? && client_id = ?");
	$controlUser->execute(array($userInfo, $userID));

	//User Control
	if ($controlUser->rowCount() <= 0)
		die("user not found");

	$userInfo = $controlUser->fetchAll(PDO::FETCH_ASSOC)[0];

	$newBalance = $userInfo["balance"] + $credit;
	$balance  = $conn->prepare("UPDATE clients SET balance=:balance WHERE client_id=:id ");
	$balance  = $balance->execute(array("id"=>$userID,"balance"=>$newBalance));
	$insert= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
	$insert= $insert->execute(array("c_id"=>$userID,"action"=>$method["method_name"]." API aracılığıyla ".$totalPrice." TL tutarında bakiye yüklendi","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));

	die("OK");
## payreks_cc bitti ##

elseif( $method_name == "payreks_mobile" ):
	//Payreks Filter Function
	function payreksFilter($string)
	{
		$escapes = ["--",";","/*","*/","//","#","||","<","|","&",">","'",")","(","*","\""];
		$filterString = $string;
		foreach ($escapes as $row)
			$filterString = str_replace($row,"",$filterString);
		return $filterString;
	}

	//Payreks Hash Control Function
	function payreksHashControl($type, $data, $key)
	{
		$context = hash_init($type, HASH_HMAC, $key);
		hash_update($context, $data);
		return hash_final($context);
	}

	if (!$_POST)
		die("do not have post values");

	$orderID = isset($_POST['order_id']) ? payreksFilter($_POST['order_id']) : null;
	$credit = isset($_POST['credit']) ? payreksFilter($_POST['credit']) : null;
	$userID = isset($_POST['user_id']) ? payreksFilter($_POST['user_id']) : null;
	$userInfo = isset($_POST['user_info']) ? payreksFilter($_POST['user_info']) : null;
	$payLabel = isset($_POST['pay_label']) ? payreksFilter($_POST['pay_label']) : null;
	$totalPrice = isset($_POST['total_price']) ? payreksFilter($_POST['total_price']) : null;
	$netPrice = isset($_POST['net_price']) ? payreksFilter($_POST['net_price']) : null;
	$hash = isset($_POST['hash']) ? $_POST['hash'] : null;

	if ($orderID === null || $credit === null || $userID === null || $userInfo === null || $payLabel === null ||
		$totalPrice === null || $netPrice === null || $hash === null)
		die("missing value");

	//Control Post Values
	if ($orderID === "" || $credit === "" || $userID === "" || $userInfo === "" || $payLabel === "" ||
		$totalPrice === "" || $netPrice === "" || $hash === "")
		die("empty value");

	$apiKey = $extras["api_key"]; //Mağaza api key
	$secretKey = $extras["api_secret"]; //Mağaza secret key

	$hashData = md5($orderID . $credit . $userID . $userInfo . $payLabel . $totalPrice . $netPrice . $apiKey);
	$hashCreate = payreksHashControl('sha256', $hashData, $secretKey);

	//Hash Control
	if ($hash !== $hashCreate)
		die("wrong hash");

	if (countRow(["table"=>"payments","where"=>["payment_privatecode"=>$orderID,"payment_delivery"=>1 ] ]))
		die("order already have");

	$controlUser = $conn->prepare("SELECT * FROM clients WHERE email = ? && client_id = ?");
	$controlUser->execute(array($userInfo, $userID));

	//User Control
	if ($controlUser->rowCount() <= 0)
		die("user not found");

	$userInfo = $controlUser->fetchAll(PDO::FETCH_ASSOC)[0];

	$newBalance = $userInfo["balance"] + $credit;
	$balance  = $conn->prepare("UPDATE clients SET balance=:balance WHERE client_id=:id ");
	$balance  = $balance->execute(array("id"=>$userID,"balance"=>$newBalance));
	$insert= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
	$insert= $insert->execute(array("c_id"=>$userID,"action"=>$method["method_name"]." API aracılığıyla ".$totalPrice." TL tutarında bakiye yüklendi","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));

	die("OK");
## payreks_mobile bitti ##

elseif( $method_name == "shoplemo" ):
## shoplemo başladı ##

    if (!$_POST || $_POST['status'] != 'success') {
        die('Shoplemo.com');
    }

    //print_r($extras);

    $_data = json_decode($_POST['data'], true);
    $hash = base64_encode(hash_hmac('sha256', $_data['progress_id'] . implode('|', $_data['payment']) . $extras["apiKey"], $extras["apiSecret"], true));

   // print_r($_data);
    if ($hash != $_data['hash']) {
        die('Shoplemo: Calculated hashes doesn\'t match!');
    }

    if($_data['custom_params'] != false):
          $custom_params = json_decode($_data['custom_params']);
          $order_id = $custom_params->payment_code;

          if($_data['payment']['payment_status'] == 'COMPLETED'){
                if( countRow(["table"=>"payments","where"=>["payment_privatecode"=>$order_id,"payment_delivery"=>1 ] ])){
                        $payment        = $conn->prepare("SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_privatecode=:orderid ");
                        $payment      ->execute(array("orderid"=>$order_id));
                        $payment        = $payment->fetch(PDO::FETCH_ASSOC);

                        $payment_bonus  = $conn->prepare("SELECT * FROM payments_bonus WHERE bonus_method=:method && bonus_from<=:from ORDER BY bonus_from DESC LIMIT 1 ");
                        $payment_bonus  -> execute(array("method"=>$method["id"],"from"=>$payment["payment_amount"]));
                        $payment_bonus  = $payment_bonus->fetch(PDO::FETCH_ASSOC);
                        if( $payment_bonus ):
                            $amount     = ($payment["payment_amount"]+($payment["payment_amount"]*$payment_bonus["bonus_amount"]/100));
                        else:
                            $amount     = $payment["payment_amount"];
                        endif;
                        $extra    = ($_POST);
                        $extra    = json_encode($extra);
                        $conn->beginTransaction();
                        $update   = $conn->prepare("UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery, payment_extra=:extra WHERE payment_id=:id ");
                        $update   = $update->execute(array("balance"=>$payment["balance"],"status"=>3,"delivery"=>2,"extra"=>$extra,"id"=>$payment["payment_id"]));
                        $balance  = $conn->prepare("UPDATE clients SET balance=:balance WHERE client_id=:id ");
                        $balance  = $balance->execute(array("id"=>$payment["client_id"],"balance"=>$payment["balance"]+$amount));
                        $insert= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
                        if( $payment_bonus ):
                            $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla %".$payment_bonus["bonus_amount"]." bonus dahil ".$amount." TL tutarında bakiye yüklendi","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
                        else:
                            $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla ".$amount." TL tutarında bakiye yüklendi","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
                        endif;
                        if( $update && $balance ):
                            $conn->commit();
                            exit("OK");
                        else:
                            $conn->rollBack();
                            echo "NO";
                        endif;
                    }else{
                        exit("OK");
                    }
          }else{
                $update   = $conn->prepare("UPDATE payments SET payment_status=:status, payment_delivery=:delivery WHERE payment_privatecode=:code  ");
                $update   = $update->execute(array("status"=>2,"delivery"=>1,"code"=>$order_id));
          }
    endif;
 ##shoplemo bitti ##

elseif( $method_name == "buypayer" ):
    ## buypayer başla ##
    $order_id    = $_POST["sipid"];
    if( !$_POST ):
        die('Post Yok');
        exit();
    endif;
    if( $extras["magaza_secret"] ==  $_POST["sifre"] && $_POST["opr"] == "success" ):
        if( countRow(["table"=>"payments","where"=>["payment_privatecode"=>$order_id,"payment_delivery"=>1 ] ]) ):
            $payment        = $conn->prepare("SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_privatecode=:orderid ");
            $payment      ->execute(array("orderid"=>$order_id));
            $payment        = $payment->fetch(PDO::FETCH_ASSOC);

            $payment_bonus  = $conn->prepare("SELECT * FROM payments_bonus WHERE bonus_method=:method && bonus_from<=:from ORDER BY bonus_from DESC LIMIT 1 ");
            $payment_bonus  -> execute(array("method"=>$method["id"],"from"=>$payment["payment_amount"]));
            $payment_bonus  = $payment_bonus->fetch(PDO::FETCH_ASSOC);
            if( $payment_bonus ):
                $amount     = ($payment["payment_amount"]+($payment["payment_amount"]*$payment_bonus["bonus_amount"]/100));
            else:
                $amount     = $payment["payment_amount"];
            endif;
            $extra    = ($_POST);
            $extra    = json_encode($extra);
            $conn->beginTransaction();
            $update   = $conn->prepare("UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery, payment_extra=:extra WHERE payment_id=:id ");
            $update   = $update->execute(array("balance"=>$payment["balance"],"status"=>3,"delivery"=>2,"extra"=>$extra,"id"=>$payment["payment_id"]));
            $balance  = $conn->prepare("UPDATE clients SET balance=:balance WHERE client_id=:id ");
            $balance  = $balance->execute(array("id"=>$payment["client_id"],"balance"=>$payment["balance"]+$amount));
            $insert= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
            if( $payment_bonus ):
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla %".$payment_bonus["bonus_amount"]." bonus dahil ".$amount." TL tutarında bakiye yüklendi","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            else:
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla ".$amount." TL tutarında bakiye yüklendi","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            endif;
            if( $update && $balance ):
                $conn->commit();
                echo "OK";
            else:
                $conn->rollBack();
                echo "NO";
            endif;
        else:
        endif;
    else:
        $update   = $conn->prepare("UPDATE payments SET payment_status=:status, payment_delivery=:delivery WHERE payment_privatecode=:code  ");
        $update   = $update->execute(array("status"=>2,"delivery"=>1,"code"=>$order_id));
    endif;
    ## buypayer bitti ##
endif;
