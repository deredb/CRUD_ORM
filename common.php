<?php

require_once("database.php");
/*
 * common class holds common database actions (CRUD)
 * It provides a simple Object Relational Model
 */
class Common {

    /**
     * 
     * @global type $db
     * @return array of objects that represent records of the query 
     */
    public static function findAll() {
        global $db;

        $sql = "SELECT* FROM " . static::$tableName;
        $result = $db->query($sql);
        $ObjectArray = array();
        
        //commenting this will give us an 
        //array of result set from table but not
        //objects representing each rows of the result set
        $result->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class());
      
        while ($record = $result->fetch()) {
            $ObjectArray[] = $record;
        }

        return $ObjectArray;
    }

    /**
     * 
     * @global type $db
     * @param $id table id the search is made
     * @return mixed 
     */
    public static function findById($id = 0) {
        global $db;
        $resultObj = array();
        $sql = "SELECT* FROM " . static::$tableName;
        $sql .=" WHERE id=:id ";
        $sql .=" LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        //prevents from getting default values,
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class());
        $row = $stmt->fetch();

        if ($row) {
            do {
                $resultObj = $row;
            } while ($row = $stmt->fetch());
        } else {

            return FALSE;
        }

        return $resultObj;
    }

    /**
     * 
     * @global type $db
     * @param $sql represents SQL query
     * @return array of objects representing each row of the result set
     */
    public static function findBySql($sql = "") {

        global $db;
        $result = $db->query($sql);
        $ObjectArray = array();



        $result->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class());
        while ($record = $result->fetch()) {
            $ObjectArray[] = $record;
        }

        return $ObjectArray;
    }



    /**
     * Makes insert to the table using an instance of class-database table
     * 
     * @global type $db
     * @param an object instance
     * @return mixed- number of rows affected or throws exception
     */
    public static function create($obj) {
        global $db;

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbAttributes = $obj->db_fields;
        
        $columns = array_map(function ($col) {
            return "`" . $col . "`";
        }, array_values($dbAttributes));
        
        $paramPlaceholders = array_map(function ($col) {
            return ":" . $col;
        }, array_values($dbAttributes));
        $paramValues = array_intersect_key(get_object_vars($obj), array_flip($dbAttributes));

        $sql = "INSERT INTO `" . $obj::$tableName . "` (" . implode(",", $columns) . ")"
                . " VALUES (" . implode(",", $paramPlaceholders) . ")";

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($paramValues);

            return $numRowsAffected = $stmt->rowCount();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

   /**
    * 
    * @global type $db the database connection
    * @param $id identify the row for deletion
    * @return mixed
    */
    public static function delete($id) {
        global $db;
        try {
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "DELETE FROM " . static::$tableName;
            $sql .=" WHERE `id`=:id";
            $sql .=" LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $errorInfo = $stmt->errorInfo();
            if (isset($errorInfo[2])) {
                //dont want to show this in live site
                //echo "Deletion is not complete";
                //echo $error = $errorInfo[2];
            }
        } catch (Exception $e) {
          
            echo "Deletion is not complete";
        }
        $numRowsAffected = $stmt->rowCount();

        if ($numRowsAffected >= 1) {
            
            return $numRowsAffected;
        } else {
            return FALSE;
        }
    }

    /**
     * Updates the tabe in the database 
     * 
     * @global type $db
     * @param instance of the class-database table
     * @return number of rows affected with the update
     */
    public static function update($obj) {
        global $db;
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbAttributes = $obj->db_fields;
        $columns = array_map(function($col) {
            return "`" . $col . "`";
        }, array_values($dbAttributes));
        $paramPlaceholders = array_map(function($col) {
            return ":" . $col;
        }, array_values($dbAttributes));
        $columnValuePairs = array();
        foreach ($columns as $key => $value) {
            $columnValuePairs[] = "{$value}={$paramPlaceholders[$key]}";
        }

        //get values of table fields
        $paramValues = array_intersect_key(get_object_vars($obj), array_flip($dbAttributes));
        $sql = "UPDATE " . static::$tableName . " SET ";
        $sql.=implode(",", $columnValuePairs);
        $sql.=" WHERE id=" . $obj->id;

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($paramValues);
            return $numRowsAffected = $stmt->rowCount();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

}

?>