<?php

include dirname(__FILE__).'/../wp-config.php';
include 'bat_util.php';

//オーナーETH残高取得API
$getEthBalanceApi = 'https://api.etherscan.io/api?module=account&action=balance&address='.CONTRACT_ADDRESS.'&tag=latest&apikey='.API_TOKEN;

//オーナートークン残高取得API
$getBalanceApi = 'https://api.etherscan.io/api?module=account&action=tokenbalance&contractaddress='.CONTRACT_ADDRESS.'&address='.OWNER_ADDRESS.'&tag=latest&apikey='.API_TOKEN;

//トークン総発行数取得API
$getSupplyApi = 'https://api.etherscan.io/api?module=stats&action=tokensupply&contractaddress='.CONTRACT_ADDRESS.'&apikey='.API_TOKEN;

try{
    $pdo = new PDO('mysql:dbname='.DB_NAME.';host=localhost', DB_USER, DB_PASSWORD);

    $bat_util = new bat_util();

    //ETH取得
    $response = $bat_util->curl_post($getEthBalanceApi);
    $response = json_decode($response);
    $eth_balance = $response->result;//オーナーのETH残高

    //balance取得
    $response = $bat_util->curl_post($getBalanceApi);
	$response = json_decode($response);
    $own_balance = $response->result;//オーナー残高

    //supply取得
    $response = $bat_util->curl_post($getSupplyApi);
	$response = json_decode($response);
    $token_supply = $response->result;//トータルサプライ

    $percent = $bat_util->num2per($token_supply-$own_balance,$token_supply,12);

    //DB書き込み用データ作成
    $data[':id'] = 1;
    $data[':eth_balance'] = $eth_balance;
    $data[':balance'] = $own_balance;
    $data[':supply'] = $token_supply;
    $data[':percent'] = $percent;
    $data[':update_date'] = date('Y-m-d H:i:s');

    //DB書き込み
    $sql = 'update contract_info set balance = :balance, supply = :supply, percent = :percent, eth_balance = :eth_balance, update_date = :update_date where id = :id';
	$stmt = $pdo->prepare($sql);
	$stmt->execute($data);

}catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
}

$pdo = null;
