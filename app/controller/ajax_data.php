<?php


$action = $_POST["action"];

  if( $action ==  "services_list" ):
    $category = $_POST["category"];
    $services = $conn->prepare("SELECT * FROM services WHERE category_id=:c_id && service_type=:type ORDER BY service_line ");
    $services->execute(array('c_id'=>$category,'type'=>2));
    $services = $services->fetchAll(PDO::FETCH_ASSOC);
    if( $services ):
      $serviceList  ="";
    else:
      $serviceList  ="<option value='0'>".$languageArray["neworder.no.service"]."</option>";
    endif;
      foreach ($services as $service) {
        $search = $conn->prepare("SELECT * FROM clients_service WHERE service_id=:service && client_id=:c_id ");
        $search->execute(array("service"=>$service["service_id"],"c_id"=>$user["client_id"]));
        if( $service["service_secret"] == 2 || $search->rowCount()  ):
          $multiName   =  json_decode($service["name_lang"],true);
            if( $multiName[$user["lang"]] ):
              $name = $multiName[$user["lang"]];
            else:
              $name = $service["service_name"];
            endif;
          $serviceList.=  "<option value='".$service['service_id']."' ";
            if( $_SESSION["data"]["services"] == $service['service_id'] ):
              $serviceList.=  "selected";
            endif;
          $serviceList.=">".$name." - ".priceFormat(service_price($service["service_id"]))."&#8378;</option>";
        endif;
      }
    echo json_encode(['services'=>$serviceList]);
  elseif( $action ==  "service_detail" ):
    $s_id     = $_POST["service"];
    $service  = $conn->prepare("SELECT * FROM services WHERE service_id=:s_id");
    $service->execute(array('s_id'=>$s_id));
    $service  = $service->fetch(PDO::FETCH_ASSOC);
    $service["service_price"] = service_price($service["service_id"]);
    $serviceDetails = "";
      $multiDesc  =  json_decode($service["description_lang"],true);
        if( $multiDesc[$user["lang"]] ):
          $desc = $multiDesc[$user["lang"]];
        else:
          $desc = $service["service_description"];
        endif;
      if( $desc ):
        $description    = str_replace("\n","<br />",$service["service_description"]);
        $serviceDetails.= '<div class="form-group fields" id="description">
              <label for="service_description" class="control-label">'.$languageArray["neworder.description"].'</label>
              <div class="panel-body border-solid border-rounded" id="service_description">
              '.$description.'
              </div>
            </div>';
      endif;
      if( $service["service_package"] == 1 || $service["service_package"] == 2 || $service["service_package"] == 3 || $service["service_package"] == 4 ):
        if( $service["want_username"] == 2 ):
          $link_type  = $languageArray["neworder.username"];
        else:
          $link_type  = $languageArray["neworder.url"];
        endif;
        $serviceDetails.='<div class="form-group fields" id="order_link">
                <label class="control-label" for="field-orderform-fields-link">'.$link_type.'</label>
                <input class="form-control" name="link" value="'.$_SESSION["data"]["link"].'" type="text" id="field-orderform-fields-link">
              </div>';
      endif;
      if( $service["service_package"] == 1 ):
        $serviceDetails.='<div class="form-group fields" id="order_quantity">
                  <label class="control-label" for="field-orderform-fields-quantity">'.$languageArray["neworder.quantity"].'</label>
                  <input class="form-control" name="quantity" value="'.$_SESSION["data"]["quantity"].'" type="text" id="neworder_quantity">
              </div>
              <small class="help-block min-max">Min: '.$service["service_min"].' - Maks: '.$service["service_max"].'</small>
              ';
      endif;
      if( $service["service_package"] == 11 || $service["service_package"] == 12 || $service["service_package"] == 13 ||  $service["service_package"] == 14 || $service["service_package"] == 15   ):
        $serviceDetails.='<div class="form-group fields" id="order_link">
                <label class="control-label" for="field-orderform-fields-link">'.$languageArray["neworder.username"].'</label>
                <input class="form-control" name="username" value="'.$_SESSION["data"]["username"].'" type="text" id="field-orderform-fields-link">
              </div>';
      endif;
      if( $service["service_package"] == 3 ):
        $serviceDetails.='<div class="form-group fields" id="order_quantity">
              <label class="control-label" for="field-orderform-fields-quantity">'.$languageArray["neworder.quantity"].'</label>
              <input class="form-control" name="quantity" value="" type="text" id="neworder_quantity" disabled="">
          </div>
          <small class="help-block min-max">Min: '.$service["service_min"].' - Maks: '.$service["service_max"].'</small>
          ';
      endif;
      if( $service["service_package"] == 11 || $service["service_package"] == 12 || $service["service_package"] == 13  ):
        $serviceDetails.='<div class="form-group fields" id="order_link">
                <label class="control-label" for="field-orderform-fields-link">'.$languageArray["neworder.posts"].'</label>
                <input class="form-control" name="posts" value="'.$_SESSION["data"]["posts"].'" type="text" id="field-orderform-fields-link">
              </div>';
        $serviceDetails.='<div class="form-group fields" id="order_min">
              <label class="control-label" for="order_count">'.$languageArray["neworder.quantity"].'</label>
              <div class="row">
                  <div class="col-xs-6">
                      <input type="text" class="form-control" id="order_count" name="min" value="'.$_SESSION["data"]["min"].'" placeholder="Minimum">
                  </div>
                  <div class="col-xs-6">
                      <input type="text" class="form-control" id="order_count" name="max" value="'.$_SESSION["data"]["max"].'" placeholder="Maksimum">
                  </div>
              </div>
              <small class="help-block min-max">Min: '.$service["service_min"].' - Maks: '.$service["service_max"].'</small>
          </div>
          <div class="form-group fields" id="order_delay">
              <div class="row">
                  <div class="col-xs-6">
                      <label class="control-label" for="field-orderform-fields-delay">'.$languageArray["neworder.delay"].'</label>
                      <select class="form-control" name="delay" id="field-orderform-fields-delay">
                          <option value="0" ';
            if( $_SESSION["data"]["delay"] == 0 ):
              $serviceDetails.=' selected';
            endif;
          $serviceDetails.='>'.$languageArray["neworder.no.delay"].'</option>
                          <option value="300" ';
            if( $_SESSION["data"]["delay"] == 300 ):
              $serviceDetails.=' selected';
            endif;
          $serviceDetails.='>5 '.$languageArray["neworder.minute"].'</option>
                          <option value="600" ';
            if( $_SESSION["data"]["delay"] == 600 ):
              $serviceDetails.=' selected';
            endif;
          $serviceDetails.='>10 '.$languageArray["neworder.minute"].'</option>
                          <option value="900" ';
            if( $_SESSION["data"]["delay"] == 900 ):
              $serviceDetails.=' selected';
            endif;
          $serviceDetails.='>15 '.$languageArray["neworder.minute"].'</option>
                          <option value="1800" ';
            if( $_SESSION["data"]["delay"] == 1800 ):
              $serviceDetails.=' selected';
            endif;
          $serviceDetails.='>30 '.$languageArray["neworder.minute"].'</option>
                          <option value="3600" ';
            if( $_SESSION["data"]["delay"] == 3600 ):
              $serviceDetails.=' selected';
            endif;
          $serviceDetails.='>60 '.$languageArray["neworder.minute"].'</option>
                          <option value="5400" ';
            if( $_SESSION["data"]["delay"] == 5400 ):
              $serviceDetails.=' selected';
            endif;
          $serviceDetails.='>90 dakika</option>
                      </select>
                  </div>
                  <div class="col-xs-6">
                      <label for="field-orderform-fields-expiry">'.$languageArray["neworder.expiry"].'</label>
                      <div class="input-group" style="padding:10px;" id="datetimepicker">
					  <i class="fa fa-calendar" style="font-size: 25px;margin-top: 10px;padding: 5px;"></i>
                          <input style="300px" class="form-control datetime" name="expiry" id="expiryDate" value="'.$_SESSION["data"]["expiry"].'" type="date" autocomplete="off">
                          <span class="input-group-btn">
                              <button class="btn btn-default clear-datetime" id="clearExpiry" type="button"> <span class="fa fa-trash-o"></span></button>
                          </span>
                      </div>
                  </div>
              </div>
          </div>';
      endif;
      if( $service["service_package"] == 3 || $service["service_package"] == 4  ):
        $serviceDetails.='<div class="form-group fields" id="order_comment">
              <label class="control-label">'.$languageArray["neworder.comments"].'</label>
              <textarea class="form-control counter" name="comments" id="neworder_comment" cols="30" rows="10" data-related="quantity">'.$_SESSION["data"]["comments"].'</textarea>
          </div>';
      endif;
      if( $service["service_dripfeed"] == 2 ):
          if( $_SESSION["data"]["check"] ): $check = "checked"; endif;
        $serviceDetails.= '<div id="dripfeed">
                <div class="form-group fields" id="order_check">
                    <label class="control-label has-depends " for="dripfeedcheckbox">
                        <input name="check" value="1" type="checkbox" '.$check.' id="dripfeedcheckbox">
                        '.$languageArray["neworder.dripfeed.title"].'
                    </label>
                    <div class="hidden" id="dripfeed-options">
                        <div class="form-group">
                            <label class="control-label" for="dripfeed-runs">'.$languageArray["neworder.runs.title"].'</label>
                            <input class="form-control" name="runs" value="'.$_SESSION["data"]["runs"].'" type="text" id="dripfeed-runs">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="dripfeed-interval">'.$languageArray["neworder.interval.title"].'</label>
                            <input class="form-control" name="interval" value="'.$_SESSION["data"]["interval"].'" type="text" id="dripfeed-interval">
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="dripfeed-totalquantity">'.$languageArray["neworder.totalquantity.title"].'</label>
                            <input class="form-control" name="total_quantity" value="'.$_SESSION["data"]["total_quantity"].'" type="text" id="dripfeed-totalquantity" readonly="">
                        </div>
                    </div>
                </div>
            </div>
            ';
      endif;
      $runs     = $_POST["runs"];  if( !$runs ): $runs = 1; endif;
      $dripfeed = $_POST["dripfeed"];
      $quantity = $_POST["quantity"];
      if( $s_id != 0 && $dripfeed == "bos"  ):
        $price =  $quantity*$service["service_price"]/1000;
        $data = ['details'=>$serviceDetails,'price'=>priceFormat($price).' TL'];
      elseif( $s_id != 0 && $dripfeed == "var" ):
        $price =  $runs*$quantity*$service["service_price"]/1000;
        $data = ['details'=>$serviceDetails,'price'=>priceFormat($price).' TL'];
      elseif( $s_id != 0 && !isset($dripfeed)  ):
        $price  = $service["service_price"];
        $data = ['details'=>$serviceDetails,'price'=>priceFormat($price).' TL'];
      else:
        $data = ['empty'=>1];
      endif;
        if( $service["service_package"] == 11 || $service["service_package"] == 12 || $service["service_package"] == 13 ):
          $data["sub"]  = 1;
        endif;
      echo json_encode($data);
    unset($_SESSION["data"]);
  elseif( $action ==  "service_price" ):
    $service  = $_POST["service"];
    $quantity = $_POST["quantity"];
    $comments = $_POST["comments"];
    $dripfeed = $_POST["dripfeed"];
    $runs     = $_POST["runs"]; if( !$runs ): $runs = 1; endif;
    $price    = service_price($service)/1000;
      if( $comments ): $quantity = count(explode("\n",$comments)); endif;

      if( $quantity == 0 ){
        $totalPrice = service_price($service)." TL";
      }elseif( $dripfeed == "var" ){
        $totalPrice = priceFormat($price*$quantity*$runs);
        $totalPrice.=" TL";
      }else{
        $totalPrice = priceFormat($price*$quantity);
        $totalPrice.=" TL";
      }
      echo json_encode(['price'=>$totalPrice,'commentsCount'=>$quantity,'totalQuantity'=>$runs*$quantity ]);
  endif;
