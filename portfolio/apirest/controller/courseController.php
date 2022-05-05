<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once '../config/database.php';
include_once '../models/course.php';

$data = json_decode(file_get_contents("php://input")) ?? new stdClass();
$data->id = $_GET['id'] ?? null;
$data->byName = $_GET['name'] ?? null;
$data->bySubjects = $_GET['subjects'] ?? null;
$data->byPlaces = $_GET['places'] ?? null;
$database = new Database();
$db = $database->Connect();
$course = new Course($db,$data);

$res = $course->callAction($_SERVER['REQUEST_METHOD']);

http_response_code($res['res_code']);
echo json_encode($res['res_message']);