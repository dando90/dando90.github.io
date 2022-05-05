<?php
class Course
	{
	private $conn;
	private $table_name = "courses";
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

	public function __construct($db,$data)
		{
		$this->conn = $db;
		$this->id = htmlspecialchars(strip_tags($data->id ?? null));
		$this->name = htmlspecialchars(strip_tags($data->name ?? null));
		$this->places = htmlspecialchars(strip_tags($data->places ?? null));
        $this->byName = htmlspecialchars(strip_tags($data->byName ?? null));
		$this->bySubjects = htmlspecialchars(strip_tags($data->bySubjects ?? null));
		$this->byPlaces = htmlspecialchars(strip_tags($data->byPlaces ?? null));
        if (!empty($data->subjects)) {
            foreach ($data->subjects as $currentSubject) {
                array_push($this->subjects, htmlspecialchars(strip_tags($currentSubject)));
                }
            }
		}
	
	public function getError($errorType)
		{
            switch($errorType) {
                case 1: //Missing Data
                    return [
                        "res_code" => 400,
                        "res_message" => array("message" => "Bad request: missing data.")
                    ];
                    break;
                case 2: //Method not allowed
                    return [
                        "res_code" => 405,
                        "res_message" => array("message" => "Bad request: method not allowed.")
                    ];
                    break;
                case 3: //Service Unavailable
                    return [
                        "res_code" => 503,
                        "res_message" => array("message" => "Service Unavailable: impossible to process the request")
                    ];
                    break;
            }
		}
	
	public function callAction($requestType)
		{
            switch ($requestType) {
                case "GET":
                    if(empty($this->id)){
                        return $this->readAll();
                    } else {
                        return $this->readSingle();
                    }
                    break;
                case "POST":
                    if(!empty($this->name)&&!empty($this->places)){
                        return $this->create();
                    } else {
                        return getError(1);
                    }
                    break;
                case "PUT":
                    if(!empty($this->id)&&!empty($this->name)&&!empty($this->places)){
                        return $this->update();
                    } else {
                        return getError(1);
                    }
                    break;
                case "DELETE":
                    if(!empty($this->id)){
                        return $this->delete();
                    } else {
                        return getError(1);
                    }
                    break;
                default:
                    return getError(2);
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
                " . $this->table_name . " a 
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
                $courses_arr = array();
                $courses_arr["records"] = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $course_item = array(
                        "id" => $id,
                        "name" => $name,
                        "places" => $places,
                        "subjects" => $subjects
                    );
                    array_push($courses_arr["records"], $course_item);
                }
                return [
                    "res_code" => 200,
                    "res_message" => $courses_arr
                ];
            }else{
                return [
                    "res_code" => 404,
                    "res_message" => array("message" => "No courses found.")
                ];
            }
		}

	public function readSingle()
		{
            $query = "SELECT
                a.id, a.name, a.places, GROUP_CONCAT(b.subject_id) AS subjects
                FROM
                " . $this->table_name . " a
                LEFT JOIN ".$this->tableSubject." b
                ON a.id = b.course_id
                WHERE a.id=:id
                GROUP BY a.id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":id", $this->id);
            
            $stmt->execute();
            $num = $stmt->rowCount();
            if($num>0){
                $courses_arr = array();
                $courses_arr["records"] = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $course_item = array(
                        "id" => $id,
                        "name" => $name,
                        "places" => $places,
                        "subjects" => $subjects
                    );
                    array_push($courses_arr["records"], $course_item);
                }
                return [
                    "res_code" => 200,
                    "res_message" => $courses_arr
                ];
            }else{
                return [
                    "res_code" => 404,
                    "res_message" => array("message" => "Course not found.")
                ];
            }
		}

	public function create()
		{
			$query = "INSERT INTO
					" . $this->table_name . "
					SET
						name=:name, places=:places";
			
			$stmt = $this->conn->prepare($query);

			$stmt->bindParam(":name", $this->name);
			$stmt->bindParam(":places", $this->places);
            $courseCreated = $stmt->execute();
            $this->id = $this->conn->lastInsertId();
			
			if($courseCreated&&$this->addSubjects()){
				return [
                    "res_code" => 201,
                    "res_message" => array("message" => "Course created correctly")
                ];
				}
			return getError(3);
		}

	public function update()
		{
			$query = "UPDATE
				" . $this->table_name . "
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
				return [
                    "res_code" => 200,
                    "res_message" => array("message" => "Course updated correctly")
                ];
				}
			return getError(3);
		}

	public function delete()
		{
			$query = "DELETE FROM
				" . $this->table_name . "
				WHERE id = :id";

			$stmt = $this->conn->prepare($query);
		
			$stmt->bindParam(":id", $this->id);

			if($stmt->execute()){
				return [
                    "res_code" => 200,
                    "res_message" => array("message" => "Course deleted correctly")
                ];
				}
			return getError(3);
		}
	}