<?php

namespace App\Controllers;

use Core\App;
use App\Models\Course;
use stdClass;

class CoursesController
{
    private $course;

    public function __construct()
    {
        $data = json_decode(file_get_contents("php://input")) ?? new stdClass();
        $this->course = new Course(App::get('database'), $data);
    }

    public function get()
    {
        if (empty($_GET['id'])) {
            $response = $this->course->readAll();
        } else {
            $response = $this->course->readSingle();
        }
        return view('json', compact('response')); 
    }
    public function create()
    {
        if(!empty($this->course->name)&&!empty($this->course->places)){
            $response = $this->course->create();
        } else {
            $response = apiResponse(400, "Bad request: missing data.");
        }
        return view('json', compact('response'));
    }
    public function update()
    {
        if(!empty($this->course->id)&&!empty($this->course->name)&&!empty($this->course->places)){
            $response = $this->course->update();
        } else {
            $response = apiResponse(400, "Bad request: missing data.");
        }      
        return view('json', compact('response'));
    }
    public function delete()
    {
        if(!empty($this->course->id)){
            $response = $this->course->delete();
        } else {
            $response = apiResponse(400, "Bad request: missing data.");
        }
        return view('json', compact('response'));
    }
}