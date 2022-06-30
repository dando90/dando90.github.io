<?php

namespace App\Controllers;

use Core\App;
use App\Models\Subject;
use stdClass;

class SubjectsController
{
    public function __construct()
    {
        $data = json_decode(file_get_contents("php://input")) ?? new stdClass();
        $this->subject = new Subject(App::get('database'), $data);
    }
    public function get()
    {
        if (empty($_GET['id'])) {
            $response = $this->subject->readAll();
        } else {
            $response = $this->subject->readSingle();
        }
        return view('json', compact('response')); 
    }
    public function create()
    {
        if(!empty($this->subject->name)){
            $response = $this->subject->create();
        } else {
            $response = apiResponse(400, "Bad request: missing data.");
        }
        return view('json', compact('response'));
    }
    public function update()
    {
        if(!empty($this->subject->id)&&!empty($this->subject->name)){
            $response = $this->subject->update();
        } else {
            $response = apiResponse(400, "Bad request: missing data.");
        }
        return view('json', compact('response'));
    }
    public function delete()
    {
        if(!empty($this->subject->id)){
            $response = $this->subject->delete();
        } else {
            $response = apiResponse(400, "Bad request: missing data.");
        }
        return view('json', compact('response'));
    }
}