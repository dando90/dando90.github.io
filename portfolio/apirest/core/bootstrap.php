<?php

use Core\App;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

App::bind('config', require 'config.php');

App::bind('database', Connection::make(App::get('config')['database']));

function view($name, $data = [])
{
    extract($data);
    return require "app/views/{$name}.view.php";
}

function apiError($code, $message)
{
    return [
        "res_code" => $code,
        "res_message" => array("message" => $message)
    ];
}

function apiResponse($code, $message, $data=null)
{
    $response = array();
    $response["code"] = $code;
    $response["json"] = array();
    $response["json"]["message"] = $message;
    if(!empty($data)) {$response["json"]["data"] = $data;}
    return $response;
}