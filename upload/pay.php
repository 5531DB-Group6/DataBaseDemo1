<?php

use \PayPal\Api\Payment;
use \PayPal\Api\PaymentExecution;
use \PayPal\Api\Amount;
use \PayPal\Api\Details;
use \PayPal\Api\Transaction;

include './common/common.php';
require './paypal/start.php';


$apiContext = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(
        'AUcxCZB_3P1ouAUqB0jXSKwXTPeL4XxQB88e0bxLwQrYyo-KK1YfwNVKcOr2Xr23Nh_UbyO3qGrnpwMo',     // ClientID
        'EIorESkw7ih7XiJoIlkI8gmcIQ0ir-iu0Ll8Y4iGw6lYQl1psLIyc-3ACG7BMInj1Dsh8-CZAHC7_mwz'      // ClientSecret
    )
);


if(!isset($_GET['success'], $_GET['paymentId'], $_GET['PayerID'])){
    die();
}

if((bool)$_GET['success']== false){
    die();
}

$paymentId = $_GET['paymentId'];
$payerId =$_GET['PayerID'];

//
//try {
//    $payment = Payment::get($payerId, $apiContext);
//} catch (PayPal\Exception\PayPalConnectionException $ex) {
//    echo $ex->getCode(); // Prints the Error Code
//    echo $ex->getData(); // Prints the detailed error message
//    die($ex);
//} catch (Exception $ex) {
//    die($ex);
//}
//
//
//$execute = new PaymentExecution();
//$execute ->setPayerId($_GET['PayerID']);
//
//
////new code
//$transaction = new Transaction();
//$amount = new Amount();
//$details = new Details();
//
//$details->setShipping(2)
//    ->setSubtotal(20);
//
//$amount->setCurrency('USD');
//$amount->setTotal(22);
//$amount->setDetails($details);
//$transaction->setAmount($amount);
//
//$execute->addTransaction($transaction);
//// new code
//
//try{
//    $result = $payment->execute($execute,$apiContext);
//    echo $result;
//}catch (Exception $e){
//    $data = json_decode($e->getData());
//    var_dump($data);
//    echo $data->message;
//    die();
//}
//
//$invoice= $result->getTransactions()[0]->invoice_number;
//$amount = $result->getTransactions()[0]->getAmount()->total;

$invoice= $paymentId;
$amount = 22.0;

$uid = $_COOKIE['uid'];

$user  = dbSelect('user','*','uid='.$_COOKIE['uid'].'');

if($user[0]['expiretime']>time()){
    $expireDate = strtotime('+1 year',$user[0]['expiretime']);
}
else{
    $expireDate = strtotime('+1 year',time());
}
dbInsert('bill','uid, invoice, paydate, amount',''.$_COOKIE['uid'].',"'.$invoice.'",'.time().','.$amount.'');
dbUpdate('user','expiretime='.$expireDate.', status=0','uid='.$_COOKIE['uid'].'');

setcookie('username', $user[0]['username'], time() + 2592000);
setcookie('udertype', $user[0]['udertype'], $longTime);
setcookie('picture', $user[0]['picture'], $longTime);


    $msg = '<font color=green><b>Transaction succeed</b></font>';
    $url = 'home_membership.php';
    $style = 'alert_right';
    include 'notice.php';



