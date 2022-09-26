<?php

$title .= $languageArray["addfunds.title"];

if ($_SESSION["userlogin"] != 1  || $user["client_type"] == 1) {
    Header("Location:" . site_url('logout'));
}

$paymentsList = $conn->prepare("SELECT * FROM payment_methods WHERE method_type=:type && id!=:id ORDER BY method_line ASC ");
$paymentsList->execute(array("type" => 2, "id" => 4));
$paymentsList = $paymentsList->fetchAll(PDO::FETCH_ASSOC);

foreach ($paymentsList as $index => $payment) {
    $extra = json_decode($payment["method_extras"], true);
    $methodList[$index]["method_name"] = $extra["name"];
    $methodList[$index]["id"] = $payment["id"];
}

$bankPayment  = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
$bankPayment->execute(array("id" => 4));
$bankPayment = $bankPayment->fetch(PDO::FETCH_ASSOC);

$bankList   = $conn->prepare("SELECT * FROM bank_accounts");
$bankList->execute(array());
$bankList   = $bankList->fetchAll(PDO::FETCH_ASSOC);

if ($_POST && $_POST["payment_bank"]) {

    foreach ($_POST as $key => $value) :
        $_SESSION["data"][$key]  = $value;
    endforeach;

    $bank     = $_POST["payment_bank"];
    $amount   = $_POST["payment_bank_amount"];
    $gonderen = $_POST["payment_gonderen"];
    $method_id = 4;
    $extras   = json_encode($_POST);

    if (open_bankpayment($user["client_id"]) == 2) {
        unset($_SESSION["data"]);
        $error    = 1;
        $errorText = $languageArray["error.addfunds.bank.limit"];
    } elseif (empty($bank)) {
        $error    = 1;
        $errorText = $languageArray["error.addfunds.bank.account"];
    } elseif (!is_numeric($amount)) {
        $error    = 1;
        $errorText =  $languageArray["error.addfunds.bank.amount"];
    } elseif (empty($gonderen)) {
        $error    = 1;
        $errorText =  $languageArray["error.addfunds.bank.sender"];
    } else {

        $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_method=:method, payment_create_date=:date, payment_ip=:ip, payment_extra=:extras, payment_bank=:bank ");
        $insert->execute(array("c_id" => $user["client_id"], "amount" => $amount, "method" => $method_id, "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extras" => $extras, "bank" => $bank));
        if ($insert) {
            unset($_SESSION["data"]);
            $success    = 1;
            $successText = $languageArray["error.addfunds.bank.success"];
            if ($settings["alert_newbankpayment"] == 2) :
                if ($settings["alert_type"] == 3) :   $sendmail = 1;
                    $sendsms  = 1;
                elseif ($settings["alert_type"] == 2) : $sendmail = 1;
                    $sendsms = 0;
                elseif ($settings["alert_type"] == 1) : $sendmail = 0;
                    $sendsms  = 1;
                endif;
                if ($sendsms) :
                    SMSUser($settings["admin_telephone"], "Websitenizde #" . $conn->lastInsertId() . " idli yeni bir ödeme talebi mevcut.");
                endif;
                if ($sendmail) :
                    sendMail(["subject" => "Yeni ödeme talebi mevcut.", "body" => "Websitenizde #" . $conn->lastInsertId() . " idli yeni bir ödeme talebi mevcut.", "mail" => $settings["admin_mail"]]);
                endif;
            endif;
        } else {
            $error    = 1;
            $errorText = $languageArray["error.addfunds.bank.fail"];
        }
    }
} else if ($_POST && $_POST["payment_type"]) {
    foreach ($_POST as $key => $value) :
        $_SESSION["data"][$key]  = $value;
    endforeach;

    $method_id = $_POST["payment_type"];
    $amount   = $_POST["payment_amount"];
    $extras   = json_encode($_POST);
    $method   = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
    $method->execute(array("id" => $method_id));
    $method   = $method->fetch(PDO::FETCH_ASSOC);
    $extra    = json_decode($method["method_extras"], true);
    $paymentCode  = time();
    $amount_fee   = ($amount + ($amount * $extra["fee"] / 100)); // Komisyonlu tutar

    if (empty($method_id)) {
        $error    = 1;
        $errorText = $languageArray["error.addfunds.online.method"];
    } elseif (!is_numeric($amount)) {
        $error    = 1;
        $errorText = $languageArray["error.addfunds.online.amount"];
    } elseif ($amount < $method["method_min"]) {
        $error    = 1;
        $errorText = str_replace("{min}", $method["method_min"], $languageArray["error.addfunds.online.min"]);
    } elseif ($amount > $method["method_max"] && $method["method_max"] != 0) {
        $error    = 1;
        $errorText = str_replace("{max}", $method["method_max"], $languageArray["error.addfunds.online.max"]);
    } else {
        if ($method_id == 1) :
            $merchant_id      = $extra["merchant_id"];
            $merchant_key     = $extra["merchant_key"];
            $merchant_salt    = $extra["merchant_salt"];
            $email            = $user["email"];
            $payment_amount   = $amount_fee * 100;
            $merchant_oid     = $paymentCode;
            $user_name        = $user["name"];
            $user_address     = "Belirtilmemiş";
            $user_phone       = $user["telephone"];
            $currency         = "TL";
            $merchant_ok_url  = URL;
            $merchant_fail_url = URL;
            $user_basket      = base64_encode(json_encode(array(array($amount . " " . $currency . " Bakiye", $amount_fee, 1))));
            $user_ip          = GetIP();
            $timeout_limit    = "360";
            $debug_on         = 1;
            $test_mode        = 0;
            $no_installment   = 0;
            $max_installment  = 0;
            $hash_str         = $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount . $user_basket . $no_installment . $max_installment . $currency . $test_mode;
            $paytr_token      = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));
            $post_vals = array(
                'merchant_id' => $merchant_id,
                'user_ip' => $user_ip,
                'merchant_oid' => $merchant_oid,
                'email' => $email,
                'payment_amount' => $payment_amount,
                'paytr_token' => $paytr_token,
                'user_basket' => $user_basket,
                'debug_on' => $debug_on,
                'no_installment' => $no_installment,
                'max_installment' => $max_installment,
                'user_name' => $user_name,
                'user_address' => $user_address,
                'user_phone' => $user_phone,
                'merchant_ok_url' => $merchant_ok_url,
                'merchant_fail_url' => $merchant_fail_url,
                'timeout_limit' => $timeout_limit,
                'currency' => $currency,
                'test_mode' => $test_mode
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            $result = @curl_exec($ch);
            if (curl_errno($ch))
                die("PAYTR IFRAME connection error. err:" . curl_error($ch));
            curl_close($ch);
            $result  = json_decode($result, 1);

            if ($result['status'] == 'success') :
                unset($_SESSION["data"]);
                $token  = $result['token'];
                $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip ");
                $insert->execute(array("c_id" => $user["client_id"], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Otomatik", "date" => date("Y.m.d H:i:s"), "ip" => GetIP()));
                $success    = 1;
                $successText = $languageArray["error.addfunds.online.success"];
                $payment_url = "https://www.paytr.com/odeme/guvenli/" . $token;
            else :
                $error    = 1;
                $errorText = $languageArray["error.addfunds.online.fail"] . " - " . $result['reason'];
            endif;
        elseif ($method_id == 2) :

            $payment_types  = "";
            foreach ($extra["payment_type"] as $i => $v) {
                $payment_types .= $v . ",";
            }
            $payment_types = substr($payment_types, 0, -1);
            $hashOlustur = base64_encode(hash_hmac('sha256', $user["email"] . "|" . $user["email"] . "|" . $user['client_id'] . $extra['apiKey'], $extra['apiSecret'], true));
            $postData = array(
                'apiKey' => $extra['apiKey'],
                'hash' => $hashOlustur,
                'returnData' => $user["email"],
                'userEmail' => $user["email"],
                'userIPAddress' => GetIP(),
                'userID' => $user["client_id"],
                'proApi' => TRUE,
                'productData' => [
                    "name" =>  $amount . " TL Tutarında Bakiye (" . $paymentCode . ")",
                    "amount" => $amount_fee * 100,
                    "extraData" => $paymentCode,
                    "paymentChannel" => $payment_types, // 1 Mobil Ödeme, 2 Kredi Kartı,3 Banka Havale/Eft/Atm,4 Türk Telekom Ödeme (TTNET),5 Mikrocard,6 CashU
                    "commissionType" => $extra["commissionType"] // 1 seçilirse komisyonu bizden al, 2 olursa komisyonu müşteri ödesin
                ]
            );
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://api.paywant.com/gateway.php",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($postData),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            if (!$err) :
                $jsonDecode = json_decode($response, false);
                $jsonDecode->Message = str_replace("https://", "", str_replace("http://", "", $jsonDecode->Message));
                $jsonDecode->Message = "https://" . $jsonDecode->Message;
                if ($jsonDecode->Status == 100) :
                    unset($_SESSION["data"]);
                    $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip ");
                    $insert->execute(array("c_id" => $user["client_id"], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Otomatik", "date" => date("Y.m.d H:i:s"), "ip" => GetIP()));
                    $success    = 1;
                    $successText = $languageArray["error.addfunds.online.success"];
                    $payment_url = $jsonDecode->Message;
                else :
                    //echo $response; // Dönen hatanın ne olduğunu bastır
                    $error    = 1;
                    $errorText = $languageArray["error.addfunds.online.fail"];
                endif;
            else :
                $error    = 1;
                $errorText = $languageArray["error.addfunds.online.fail"];
            endif;

        elseif ($method_id == 3) :

            $bp_amount      = str_replace('.', ',', $amount);
            $bp_amount_fee  = str_replace('.', ',', $amount_fee);

            $merchant_key           = $extra['merchant_key'];
            $merchant_salt          = $extra['merchant_salt'];
            $user_name              = $user['name'];
            $user_email             = $user['email'];
            $user_number            = ($user['telephone']=='') ? '08502410803' : $user['telephone'] ;;
            $user_address           = "Müşterinizin Sisteminizde Kayıtlı Adresi";
            $payment_amount         = $amount_fee * 100;
            $merchant_oid           = $paymentCode;
            $user_basket            = base64_encode(json_encode(array(
                array("Bakiye Yükleme Hizmeti", $amount, 1)
            )));
            $payment_currency       = 'TL';

            $user_ip            = GetIP();
            $hash               = base64_encode(hash_hmac('sha256','PAY|'.$merchant_key.'|'.$merchant_salt.'NISS|'.$payment_currency.'|'.$merchant_key,true));
            $post_vals          = array(
              'key'                 =>$merchant_key,    // MAĞAZA KEY
              'salt'                =>$merchant_salt,   // MAĞAZA SECRET GİZLİ ANAHTAR
              'user_name'           =>$user_name,       // MÜTERİ ADSOYAD
              'user_email'          =>$user_email,      // MÜŞTERİ EPOSTA ADRESİ
              'user_number'         =>$user_number,     // MÜŞTERİ GSM NUMARASI
              'user_address'        =>$user_address,    // MÜŞTERİ ADRES BİLGİSİ
              'user_basket'         =>$user_basket,     // MÜŞTERİ ÜRÜN BİLGİLERİ
              'user_ip'             =>$user_ip,         // MÜŞTERİ KULLANICI IP ADRESİ
              'payment_amount'      =>$payment_amount,  // MŞTERİ ÖDEME TUTARI
              'merchant_oid'        =>$merchant_oid,    // MÜŞTERİ SİPARİŞ NUMARASI
              'payment_currency'    =>$payment_currency,// PARA BİRİMİ
              'hash'                =>$hash             // PAYNISS GVENLİK ADIMI
            );

            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.payniss.com/token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1) ;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            $result = @curl_exec($ch);

            if(curl_errno($ch))
                die("PAYNISS PAYMENT BAGLANTI HATASI: ".curl_error($ch));
            curl_close($ch);
            $result = json_decode($result,1);

            if ($result['status']=='success') :
                unset($_SESSION["data"]);
                $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip ");
                $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Otomatik", "date" => date("Y.m.d H:i:s"), "ip" => GetIP()));
                $success    = 1;
                $successText = $languageArray["error.addfunds.online.success"];
                $payment_url  = $result['payment_url'].$result['token'];
            else :
                $error    = 1;
                $errorText = "PAYNISS PAYMENT BAGLANTIS HATASI. Mesaj: ".$result['error'] . ' Hata Kodu: '.$result['errorCode'];
            endif;
        elseif( $method_id == 7 ):
// shoplemo
        //public_html/app/controller/
      //  error_reporting(E_ALL);
        //ini_set("display_errors",1);
            require_once('vendor/shoplemosdk/Bootstrap.php');

            $payment_types  = ""; foreach ($extra["payment_type"] as $i => $v ) { $payment_types .= $v.",";  } $payment_types = substr($payment_types,0,-1);

            $config = new \Shoplemo\Config();
            $config->setAPIKey($extra['apiKey']); // API KEY
            $config->setSecretKey($extra['apiSecret']); // API SECRET
            $config->setServiceBaseUrl('https://payment.shoplemo.com');

            // credit - debit - prepaid card;
            $request = new \Shoplemo\Paywith\CreditCard($config);

            $request->setUserEmail($user["email"]); // customer email address
            $request->setCustomParams(json_encode(['client_id' => $user['client_id'],'payment_code' => $paymentCode])); // custom params, must be JSON

            //create a basket
            $basket = new \Shoplemo\Model\Basket;
            $basket->setTotalPrice($amount_fee * 100); // total price * 100

            //create a product
            $item1  = new \Shoplemo\Model\BasketItem;
            $item1->setName($amount." TL Tutarında Bakiye (".$paymentCode.")");
            $item1->setPrice($amount_fee * 100); // per price * 100
            $item1->setType(\Shoplemo\Model\BasketItem::DIGITAL); // DIGITAL product
            $item1->setQuantity(1); //quantity

            // add products to basket
            $basket->addItem($item1);


            $buyer = new \Shoplemo\Model\Buyer;
            $parcala = explode(' ',$user['name']);
            if(count($parcala) > 1){
                $buyer->setName($parcala[0]);
                $buyer->setSurname($parcala[1]);
            }else{
                 $buyer->setName($user['name']);
                 $buyer->setSurname('');
            }


            // set objects to Request
            $request->setBasket($basket);
            $request->setBuyer($buyer);

            if($request->execute()){
                 try{
                    $response = json_decode($request->getResponse(),true);
                    if($response['status'] == 'success'){

                        unset($_SESSION["data"]);
                        $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip ");
                        $insert-> execute(array("c_id"=>$user["client_id"],"amount"=>$amount,"code"=>$paymentCode,"method"=>$method_id,"mode"=>"Otomatik","date"=>date("Y.m.d H:i:s"),"ip"=>GetIP() ));
                        $success    = 1;
                        $successText = $languageArray["error.addfunds.online.success"];
                        $payment_url = $response['url'];
                    }
                }catch(Exception $ex){
                    $error    = 1;
                    $errorText= $ex->getMessage();
                }
            }else{
                $error    = 1;
                $errorText= $request->getError();
            }


        elseif ($method_id == 5) :
            if ($extra["processing_fee"]) :
                $amount_fee = $amount_fee + "0.49";
            endif;
            $form_data = [
                "website_index"   =>    $extra["website_index"],
                "apikey"            =>    $extra["apiKey"],
                "apisecret"          =>    $extra["apiSecret"],
                "item_name"       =>  "Bakiye Ekleme",
                "order_id"        =>  $paymentCode,
                "buyer_name"      =>  $user["name"],
                "buyer_surname"   =>  " ",
                "buyer_email"     =>  $user["email"],
                "buyer_phone"     =>  $user["telephone"],
                "city"            =>  "NA",
                "billing_address" =>  "NA",
                "ucret"           =>  $amount_fee
            ];
            print_r(generate_shopier_form(json_decode(json_encode($form_data))));
            if ($_SESSION["data"]["payment_shopier"] == true) :
                $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip ");
                $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Otomatik", "date" => date("Y.m.d H:i:s"), "ip" => GetIP()));
                $success    = 1;
                $successText = $languageArray["error.addfunds.online.success"];
                $payment_url  = $response;
                unset($_SESSION["data"]);
            else :
                $error    = 1;
                $errorText = $languageArray["error.addfunds.online.fail"];
            endif;

        elseif ($method_id == 6) :
			function getRealIpAddress()
			{
				$ipAddress = null;
				if (isset($_SERVER['HTTP_CLIENT_IP']))
					$ipAddress = $_SERVER['HTTP_CLIENT_IP'];
				else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
					$ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
				else if(isset($_SERVER['HTTP_X_FORWARDED']))
					$ipAddress = $_SERVER['HTTP_X_FORWARDED'];
				else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
					$ipAddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
				else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
					$ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
				else if(isset($_SERVER['HTTP_FORWARDED']))
					$ipAddress = $_SERVER['HTTP_FORWARDED'];
				else if(isset($_SERVER['REMOTE_ADDR']))
					$ipAddress = $_SERVER['REMOTE_ADDR'];
				else
					$ipAddress = 'UNKNOWN';
				return $ipAddress;
			}
			function APITokenCreate($apiKey, $secretKey)
			{
				$context = hash_init("sha256", HASH_HMAC, $secretKey);
				hash_update($context, $apiKey);
				$return = hash_final($context);
				$context2 = hash_init("md5", HASH_HMAC, $secretKey);
				hash_update($context2, $return);
				return hash_final($context2);
			}

            $apiKey      = $extra["api_key"];
            $apiSecret     = $extra["api_secret"];
            $APIToken = APITokenCreate($apiKey,$apiSecret);
			$userID = $user["client_id"];
			$userInfo = $user["email"];
			$userIP = getRealIpAddress();
			$callbackUrl = URL."/payment/payreks_cc";
			$productName = "$amount_fee TL Bakiye";
			$returnData = $amount_fee;
			$amount = $amount_fee;
			$payment = "1";

           $requestURL = "https://api.payreks.com/gateway/v2";
			$requestData = [
				"api_key" => $apiKey,
				"token" => $APIToken,
				"return_type" => "json",
				"return_url" => $callbackUrl,
				"product_name" => $productName,
				"return_data" => $returnData,
				"user_id" => $userID,
				"user_info" => $userInfo,
				"user_ip" => $userIP,
				"amount" => $amount,
				"payment" => $payment
			];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $requestURL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            $result = @curl_exec($ch);
            if (curl_errno($ch))
                die("Payreks API connection error. err:" . curl_error($ch));
            curl_close($ch);
            $result  = json_decode($result, 1);

            if ($result['status'] == '200') :
                unset($_SESSION["data"]);
                $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip ");
                $insert->execute(array("c_id" => $user["client_id"], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Otomatik", "date" => date("Y.m.d H:i:s"), "ip" => GetIP()));
                $success    = 1;
                $successText = $languageArray["error.addfunds.online.success"];
                $payment_url = $result["link"];
                $_POST = $result;
            else :
                $error    = 1;
                $errorText = $languageArray["error.addfunds.online.fail"];
            endif;

		elseif ($method_id == 8) :
			function getRealIpAddress()
			{
				$ipAddress = null;
				if (isset($_SERVER['HTTP_CLIENT_IP']))
					$ipAddress = $_SERVER['HTTP_CLIENT_IP'];
				else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
					$ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
				else if(isset($_SERVER['HTTP_X_FORWARDED']))
					$ipAddress = $_SERVER['HTTP_X_FORWARDED'];
				else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
					$ipAddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
				else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
					$ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
				else if(isset($_SERVER['HTTP_FORWARDED']))
					$ipAddress = $_SERVER['HTTP_FORWARDED'];
				else if(isset($_SERVER['REMOTE_ADDR']))
					$ipAddress = $_SERVER['REMOTE_ADDR'];
				else
					$ipAddress = 'UNKNOWN';
				return $ipAddress;
			}
			function APITokenCreate($apiKey, $secretKey)
			{
				$context = hash_init("sha256", HASH_HMAC, $secretKey);
				hash_update($context, $apiKey);
				$return = hash_final($context);
				$context2 = hash_init("md5", HASH_HMAC, $secretKey);
				hash_update($context2, $return);
				return hash_final($context2);
			}

			$apiKey      = $extra["api_key"];
			$apiSecret     = $extra["api_secret"];
			$APIToken = APITokenCreate($apiKey,$apiSecret);
			$userID = $user["client_id"];
			$userInfo = $user["email"];
			$userIP = getRealIpAddress();
			$callbackUrl = URL."/payment/payreks_mobile";
			$productName = "$amount_fee TL Bakiye";
			$returnData = $amount_fee;
			$amount = $amount_fee;
			$payment = "3";

			$requestURL = "https://api.payreks.com/gateway/v2";
			$requestData = [
				"api_key" => $apiKey,
				"token" => $APIToken,
				"return_type" => "json",
				"return_url" => $callbackUrl,
				"product_name" => $productName,
				"return_data" => $returnData,
				"user_id" => $userID,
				"user_info" => $userInfo,
				"user_ip" => $userIP,
				"amount" => $amount,
				"payment" => $payment
			];
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $requestURL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 20);
			$result = @curl_exec($ch);
			if (curl_errno($ch))
				die("Payreks API connection error. err:" . curl_error($ch));
			curl_close($ch);
			$result  = json_decode($result, 1);

			if ($result['status'] == '200') :
				unset($_SESSION["data"]);
				$insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip ");
				$insert->execute(array("c_id" => $user["client_id"], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Otomatik", "date" => date("Y.m.d H:i:s"), "ip" => GetIP()));
				$success    = 1;
				$successText = $languageArray["error.addfunds.online.success"];
				$payment_url = $result["link"];
				$_POST = $result;
			else :
				$error    = 1;
				$errorText = $languageArray["error.addfunds.online.fail"];
			endif;

        elseif ($method_id == 7) :
            $weepay = array();

            $weepay['Aut'] = array(
                'bayi-id' => $extra["bayi_id"],
                'api-key' => $extra["api-key"],
                'secret-key' => $extra["secret-key"]
            );

            $weepay['Data'] = array(
                'PaidPrice' => $amount_fee,
                'Currency' => "TL",
                'CardHolderName' => $_POST["namesurname"],
                'CardNumber' => $_POST["cardnumber"],
                'ExpireMonth' => $_POST["carddateay"],
                'ExpireYear' => $_POST["carddateyil"],
                'CvcNumber' => $_POST["cardccv"],
                'CartToken' => "",
                'InstallmentNumber' => "1",
                'ClientIP' => GetIP(),
                'CallBackType' => "",
                'CallBackUrl' => "https://hcateknoloji.com/payment/weepay",
                'OutSourceID' => "11111111",
                'PreAuth' => "",
                'Description' => "TRanK Technology"
            );

            $data = json_encode($weepay);

            $endPoinUrl = "https://api.weepay.co/Payment/RequestPaymentThreeD/";
            $ch = curl_init(); // initialize curl handle
            curl_setopt($ch, CURLOPT_URL, $endPoinUrl); // set url to post to
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); // times out after 4s
            curl_setopt($ch, CURLOPT_POST, 1); // set POST method
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // add POST fields
            if ($result = curl_exec($ch)) { // run the whole process
                curl_close($ch);
                $json = json_decode($result, true);

                if ($json['ResultCode'] == 'Success') {
                    unset($_SESSION["data"]);
                    $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip ");
                    $insert->execute(array("c_id" => $user["client_id"], "amount" => $amount, "code" => 'no-code', "method" => $method_id, "mode" => "Otomatik", "date" => date("Y.m.d H:i:s"), "ip" => GetIP()));
                    $success = 1;
                    Header("Location:" . $json['Data']);
                } else {
                    $error = 1;
                    $errorText = $languageArray["error.addfunds.online.fail"] . " - " . $result['errorMessage'];
                }
            }
        endif;
    }
}

if ($payment_url) :
    echo '<script>setInterval(function(){window.location="' . $payment_url . '"},2000)</script>';
endif;
