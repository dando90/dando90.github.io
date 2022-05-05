<?php
class Subject
	{
	private $conn;
	private $table_name = "subjects";
	// Subject properties
	public $id;
	public $name;

	public function __construct($db,$data)
		{
		$this->conn = $db;
        $this->id = htmlspecialchars(strip_tags($data->id ?? null));
        $this->name = htmlspecialchars(strip_tags($data->name ?? null));
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
                    if(!empty($this->name)){
                        return $this->create();
                    } else {
                        return getError(1);
                    }
                    break;
                case "PUT":
                    if(!empty($this->id)&&!empty($this->name)){
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
    //CRUD functions        
	public function readAll()
		{
            $query = "SELECT
                a.id, a.name 
                FROM
                " . $this->table_name . " a ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $num = $stmt->rowCount();
            if($num>0){
                $subjects_arr = array();
                $subjects_arr["records"] = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $subject_item = array(
                        "id" => $id,
                        "name" => $name
                    );
                    array_push($subjects_arr["records"], $subject_item);
                }
                return [
                    "res_code" => 200,
                    "res_message" => $subjects_arr
                ];
            }else{
                return [
                    "res_code" => 404,
                    "res_message" => array("message" => "No subjects found.")
                ];
            }
		}

    public function readSingle()
		{
            $query = "SELECT
                a.id, a.name 
                FROM
                " . $this->table_name . " a
                WHERE id=:id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":id", $this->id);

            $stmt->execute();
            $num = $stmt->rowCount();
            if($num>0){
                $subjects_arr = array();
                $subjects_arr["records"] = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $subject_item = array(
                        "id" => $id,
                        "name" => $name
                    );
                    array_push($subjects_arr["records"], $subject_item);
                }
                return [
                    "res_code" => 200,
                    "res_message" => $subjects_arr
                ];
            }else{
                return [
                    "res_code" => 404,
                    "res_message" => array("message" => "Subject not found.")
                ];
            }
		}

    public function create()
        {
            $query = "INSERT INTO
                " . $this->table_name . "
                SET
                    name=:name";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":name", $this->name);
        
            if($stmt->execute()){
                return [
                    "res_code" => 201,
                    "res_message" => array("message" => "Subject created correctly")
                ];
                }
            return getError(3);     
        }

    public function update()
        {
            $query = "UPDATE
                " . $this->table_name . "
                SET
                    name = :name
                WHERE
                    id = :id";
     
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":name", $this->name);

            if($stmt->execute()){
                    return [
                        "res_code" => 200,
                        "res_message" => array("message" => "Subject updated correctly")
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
                    "res_message" => array("message" => "Subject deleted correctly")
                ];
                }
            return getError(3);
        }
	}