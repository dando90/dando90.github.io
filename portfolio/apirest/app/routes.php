<?php

$router->get('api/subjects', 'SubjectsController@get');
$router->post('api/subjects', 'SubjectsController@create');
$router->put('api/subjects', 'SubjectsController@update');
$router->delete('api/subjects', 'SubjectsController@delete');


$router->get('api/courses', 'CoursesController@get');
$router->post('api/courses', 'CoursesController@create');
$router->put('api/courses', 'CoursesController@update');
$router->delete('api/courses', 'CoursesController@delete');