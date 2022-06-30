<?php

namespace App\Models;

use PDO;

class Course
	{
	private $conn;
	private $tableName = "courses";
	private $tableSubject = "course_subject";
	// Course properties
	public $id;
	public $name;
	public $places;
    public $subjects = [];
    // Filter properties
    public $byName;
	public $bySubjects;
	public $byPlaces;

	public function __construct($db, $data)
		{
		$this->conn = $db;
		$this->id = htmlspecialchars(strip_tags($_GET['id'] ?? null));
		$this->name = htmlspecialchars(strip_tags($data->name ?? null));
		$this->places = htmlspecialchars(strip_tags($data->places ?? null));
        $this->byName = htmlspecialchars(strip_tags($_GET['name'] ?? null));
		$this->bySubjects = htmlspecialchars(strip_tags($_GET['subjects'] ?? null));
		$this->byPlaces = htmlspecialchars(strip_tags($_GET['places'] ?? null));
        if (!empty($data->subjects)) {
            foreach ($data->subjects as $currentSubject) {
                array_push($this->subjects, htmlspecialchars(strip_tags($currentSubject)));
                }
            }
		}
    
    public function addSubjects()
        {
            if (empty($this->subjects)) {return true;}
            $query="INSERT IGNORE INTO ".$this->tableSubject." (course_id,subject_id) VALUES ";
            $numSub=count($this->subjects);
            for($i=0;$i<$numSub;$i++) {
                $query .= "($this->id, ?),";
            }
            $query = rtrim($query, ",");
            $stmt = $this->conn->prepare($query);
            for($i=0;$i<$numSub;$i++) {
                $stmt->bindParam($i+1, $this->subjects[$i]);  
            }
            return $stmt->execute();
        }

    public function deleteSubjects()
        {
            if (empty($this->subjects)) {
                $query= "DELETE FROM ".$this->tableSubject." WHERE course_id=?";
                $stmt->bindParam(1, $this->id);
            } else {
                $query= "DELETE FROM ".$this->tableSubject." WHERE course_id=? AND subject_id NOT IN (";
                $numSub=count($this->subjects);
                for($i=0;$i<$numSub;$i++) {
                    $query .= "?,";
                }
                $query = rtrim($query, ",");
                $query .= ")";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(1, $this->id);
                for($i=0;$i<$numSub;$i++) {
                    $stmt->bindParam($i+2, $this->subjects[$i]);  
                }
            }
            return $stmt->execute();
        }

	// CRUD functions
	public function readAll()
		{
            $subjectsArray = explode(",", $this->bySubjects);
            $numSubjects = !empty($this->bySubjects) ? count($subjectsArray) : 0;
            $sqlSubjectPar = "";
            if (!empty($this->bySubjects)) {
                for($i=0;$i<$numSubjects;$i++) {
                    $sqlSubjectPar .= "?,";
                }
                $sqlSubjectPar = rtrim($sqlSubjectPar, ",");
            } else {
                $sqlSubjectPar = "-1";
            }
            
            $query = "SELECT
                a.id, a.name, a.places, GROUP_CONCAT(b.subject_id) AS subjects,
                SUM(CASE WHEN b.subject_id IN (".$sqlSubjectPar.") THEN 1 ELSE 0 END) as subjectsMatched
                FROM
                " . $this->tableName . " a 
                LEFT JOIN ".$this->tableSubject." b
                ON a.id = b.course_id
                WHERE
                a.name LIKE ? AND
                a.places > ?
                GROUP BY a.id
                HAVING subjectsMatched = ?";

            $stmt = $this->conn->prepare($query);
            if($sqlSubjectPar != "-1") {
                for($i=0;$i<$numSubjects;$i++) {
                    $stmt->bindParam($i+1,$subjectsArray[$i]);
                }
            }
            $nameCond = !empty($this->byName) ? "%".$this->byName."%" : "%";
            $stmt->bindParam($numSubjects+1, $nameCond);
            $placesCond = !empty($this->byPlaces) ? $this->byPlaces : "-1";
            $stmt->bindParam($numSubjects+2, $placesCond);
            $stmt->bindParam($numSubjects+3, $numSubjects);
            $stmt->execute();
            $num = $stmt->rowCount();
            if($num>0){
                $coursesArr = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $courseItem = array(
                        "id" => $id,
                        "name" => $name,
                        "places" => $places,
                        "subjects" => $subjects
                    );
                    array_push($coursesArr, $courseItem);
                }
                return apiResponse(200, "Courses found: {$num}.", $coursesArr);
            }else{
                return apiResponse(404, "No courses found.", null);
            }
		}

	public function readSingle()
		{
            $query = "SELECT
                a.id, a.name, a.places, GROUP_CONCAT(b.subject_id) AS subjects
                FROM
                " . $this->tableName . " a
                LEFT JOIN ".$this->tableSubject." b
                ON a.id = b.course_id
                WHERE a.id=:id
                GROUP BY a.id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":id", $this->id);
            
            $stmt->execute();
            $num = $stmt->rowCount();
            if($num>0){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                extract($row);
                $courseItem = array(
                    "id" => $id,
                    "name" => $name,
                    "places" => $places,
                    "subjects" => $subjects
                );
                return apiResponse(200, "Course found.", $courseItem);
            }else{
                return apiResponse(404, "Course not found.", null);
            }
		}

	public function create()
		{
			$query = "INSERT INTO
					" . $this->tableName . "
					SET
						name=:name, places=:places";
			
			$stmt = $this->conn->prepare($query);

			$stmt->bindParam(":name", $this->name);
			$stmt->bindParam(":places", $this->places);
            $courseCreated = $stmt->execute();
            $this->id = $this->conn->lastInsertId();

			if($courseCreated&&$this->addSubjects()){
                $courseItem = array(
                    "id" => $this->id,
                    "name" => $this->name,
                    "places" => $this->places,
                    "subjects" => $this->subjects
                );
                return apiResponse(201, "Course created correctly.", $courseItem);
				}
            return apiResponse(503, "Service Unavailable: impossible to process the request.", null);
		}

	public function update()
		{
			$query = "UPDATE
				" . $this->tableName . "
				SET
					name = :name,
					places = :places
				WHERE
					id = :id";
	
			$stmt = $this->conn->prepare($query);

			$stmt->bindParam(":id", $this->id);
			$stmt->bindParam(":name", $this->name);
			$stmt->bindParam(":places", $this->places);

			if($stmt->execute()&&$this->deleteSubjects()&&$this->addSubjects()){
                $courseItem = array(
                    "id" => $this->id,
                    "name" => $this->name,
                    "places" => $this->places,
                    "subjects" => $this->subjects
                );
                return apiResponse(200, "Course updated correctly.", $courseItem);
				}
            return apiResponse(503, "Service Unavailable: impossible to process the request.", null);
		}

	public function delete()
		{
			$query = "DELETE FROM
				" . $this->tableName . "
				WHERE id = :id";

			$stmt = $this->conn->prepare($query);
		
			$stmt->bindParam(":id", $this->id);

			if($stmt->execute()){
                $courseItem = array(
                    "id" => $this->id
                );
                return apiResponse(200, "Course deleted correctly.", $courseItem);
				}
            return apiResponse(503, "Service Unavailable: impossible to process the request.", null);
		}
	}