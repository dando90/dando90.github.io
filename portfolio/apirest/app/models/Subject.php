<?php

namespace App\Models;

use PDO;

class Subject
	{
	private $conn;
	private $tableName = "subjects";
	// Subject properties
	public $id;
	public $name;

	public function __construct($db, $data)
		{
		$this->conn = $db;
        $this->id = htmlspecialchars(strip_tags($_GET['id'] ?? null));
        $this->name = htmlspecialchars(strip_tags($data->name ?? null));
		}

    //CRUD functions        
	public function readAll()
		{
            $query = "SELECT
                a.id, a.name 
                FROM
                " . $this->tableName . " a ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $num = $stmt->rowCount();
            if($num>0){
                $subjectsArr = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $subjectItem = array(
                        "id" => $id,
                        "name" => $name
                    );
                    array_push($subjectsArr, $subjectItem);
                }
                return apiResponse(200, "Subjects found: {$num}.", $subjectsArr);
            }else{
                return apiResponse(404, "No subjects found.", null);
            }
		}

    public function readSingle()
		{
            $query = "SELECT
                a.id, a.name 
                FROM
                " . $this->tableName . " a
                WHERE id=:id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":id", $this->id);

            $stmt->execute();
            $num = $stmt->rowCount();
            if($num>0){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                extract($row);
                $subjectItem = array(
                    "id" => $id,
                    "name" => $name
                );
                return apiResponse(200, "Subject found.", $subjectItem);
            }else{
                return apiResponse(404, "Subject not found.", null);
            }
		}

    public function create()
        {
            $query = "INSERT INTO
                " . $this->tableName . "
                SET
                    name=:name";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":name", $this->name);

            $subjectCreated = $stmt->execute();
            $this->id = $this->conn->lastInsertId();
        
            if($subjectCreated){
                $subjectItem = array(
                    "id" => $this->id,
                    "name" => $this->name
                );
                return apiResponse(201, "Subject created correctly.", $subjectItem);
                }
            return apiResponse(503, "Service Unavailable: impossible to process the request.", null);
        }

    public function update()
        {
            $query = "UPDATE
                " . $this->tableName . "
                SET
                    name = :name
                WHERE
                    id = :id";
     
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":name", $this->name);

            if($stmt->execute()){
                $subjectItem = array(
                    "id" => $this->id,
                    "name" => $this->name
                );
                return apiResponse(200, "Subject updated correctly.", $subjectItem);
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
                $subjectItem = array(
                    "id" => $this->id
                );
                return apiResponse(200, "Subject deleted correctly.", $subjectItem);
                }
            return apiResponse(503, "Service Unavailable: impossible to process the request.", null);
        }
	}