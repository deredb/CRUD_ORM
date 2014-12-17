<?php

require_once ("database.php");
/**
 * Category class represent Category table, and extends
 * basic CRUD operations from Common class.
 */
class Category extends Common {

    //array of table names
    public $db_fields = array('id', 'category_name');
    
    //table names
    public $id;
    public $category_name;
    
    //the name of the table-similar to the class name
    protected static $tableName = "category";

    /**
     * default constructor instantiate class properties
     */
    function __construct() {
        $this->id = NULL;
        $this->category_name = "cat name";
    }

    /**
     * 
     * @return string representation of the object properties-table field values
     */
    public function __toString() {
        $output = $this->id;
        $output.= $this->category_name;
        return $output;
    }

}

?>