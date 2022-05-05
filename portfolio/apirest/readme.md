# API Rest Project - Start2Impact
## Table of Contents
1. [General Info](#general-info)
2. [Technologies](#technologies)
3. [Preparation for testing](#installation)
4. [API documentation](#collaboration)
## General Info
***
This project is an implementation of an API Rest service to manage subjects and courses for a school
## Technologies
***
A list of technologies used within the project:
* [Apache]: Version 2.4.52
* [PHP]: Version 8.1.2 
* [MariaDB]: Version 10.4.22

## Preparation for testing
***
A little intro about testing.
```
1. Install apache php and mysql on your pc
2. Download and copy files in apirest directory on your own server
3. Import the database using the migration.sql file in 'database' folder
4. Change parameters in config/database.php file accordingly to your host name and MySql's username and password
5. Install Postman, a program to easily make GET, POST, PUT and DELETE requests
6. Follow the instructions about API usage
```
## API documentation
***
### Manage Subjects
***
#### CREATE Requests
Endpoint: apirest/api/subjects/
Make a POST request to the this endpoint to create a new subject.
Request body:
{
    "name": "New subject"
}
#### READ Requests
Endpoint: apirest/api/subjects/
Make a GET request to the this endpoint to get the list of all subjects.
***
Endpoint: apirest/api/subjects/{id}
Make a GET request to the this endpoint to get the subject with the the specified id.
#### UPDATE Requests
Endpoint: apirest/api/subjects/{id}
Make a PUT request to the this endpoint to update the subject with the specified id.
Request body:
{
    "name": "Updated subject"
}
#### DELETE Requests
Endpoint: apirest/api/subjects/{id}
Make a DELETE request to the this endpoint to delete the subject with the specified id.
***
### Manage Courses
***
#### CREATE Requests
Endpoint: apirest/api/courses/
Make a POST request to the this endpoint to create a new course.
Request body:
{
    "name": "New course",
    "places": "{number}",
    "subjects": ["{id1}", "{id2 }", ... , "{idn}"]
}
where {id1}, {id2}, ... , {idn} are the id corresponding to the created subjects.
#### READ Requests
Endpoint: apirest/api/courses/
Make a GET request to the this endpoint to get the list of all courses.

It's possible to filter the results by adding the following parameters to the endpoint, like this
api/courses/?name="{name}"&subjects="{1,2,...,n}"=&places="{number}"

{name} is part of the name of the course
{1,2,...,n} are the subject id that must be present in the course
{number} is the minimum number of places that must be available in the course
***
Endpoint: apirest/api/courses/{id}
Make a GET request to the this endpoint to get the course with the the specified id.
#### UPDATE Requests
Endpoint: apirest/api/courses/{id}
Make a PUT request to the this endpoint to update the course with the specified id.
Request body:
{
    "name": "Updated course",
    "places": "{number}",
    "subjects": ["{id1}", "{id2 }", ... , "{idn}"]
}
where {id1}, {id2}, ... , {idn} are the id corresponding to the created subjects.
#### DELETE Requests
Endpoint: apirest/api/courses/{id}
Make a DELETE request to the this endpoint to delete the course with the specified id.
