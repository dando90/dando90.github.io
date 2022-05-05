<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once '../config/database.php';
include_once '../models/subject.php';

$data = json_decode(file_get_contents("php://input")) ?? new stdClass();
$data->id = $_GET['id'] ?? null;
$database = new Database();
$db = $database->Connect();
$subject = new Subject($db,$data);

$res = $subject->callAction($_SERVER['REQUEST_METHOD']);

http_response_code($res['res_code']);
echo json_encode($res['res_message']);