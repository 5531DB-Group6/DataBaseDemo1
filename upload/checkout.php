<?php

require './paypal/start.php';

use \PayPal\Api\Payer;
use \PayPal\Api\Item;
use \PayPal\Api\ItemList;
use \PayPal\Api\Details;
use \PayPal\Api\Amount;
use \PayPal\Api\Transaction;
use \PayPal\Api\RedirectUrls;
use \PayPal\Api\Payment;


if(!isset($_POST['amount'])){
    die();
}
$product = "membership";
$option = $_POST['amount'];
$shipping = 2.00;
if($option==0){
    $price=20.00;
    $description = 'one year membership';
}
elseif($option==1) {
    $price=10.00;
    $description = 'half year membership';
}
else{
    $price=0.00;
    $description ='';
}
$total = $price+$shipping;

$payer=new Payer();
$payer->setPaymentMethod('paypal');

$item=new Item();
$item->setName($product)
    ->setCurrency('USD')
    ->setQuantity(1)
    ->setPrice($price);

$itemList = new ItemList();
$itemList ->setItems([$item]);

$details =new Details();
$details->setShipping($shipping)
    ->setSubtotal($price);

$amount = new Amount();
$amount ->setCurrency('USD')
    ->setTotal($total)
    ->setDetails($details);

$transaction = new Transaction();
$transaction ->setAmount($amount)
    ->setItemList($itemList)
    ->setDescription($description)
    ->setInvoiceNumber(uniqid());


$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl(SITE_URL.'/pay.php?success=true')
    ->setCancelUrl(SITE_URL.'/pay.php?success=false');

$payment = new Payment();
$payment ->setIntent('sale')
    ->setPayer($payer)
    ->setRedirectUrls($redirectUrls)
    ->setTransactions([$transaction]);

try {
    $payment->create($paypal);
}
catch (Exception $e){
    die($e);
}
echo $approvalUrl = $payment ->getApprovalLink();

//
header("Location: {$approvalUrl}");

return $payment;
