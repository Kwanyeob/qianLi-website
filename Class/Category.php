<?php
require_once 'Class/Database.php';

class Category{
    public $name;
    public $table;
    public $items;
    private $option = "";

    public function getItems($db){
        $items = $db->query("SELECT * FROM ".$this->table." ".$this->option);
        foreach ($items as $product) {
            $p = $db->query("SELECT Name,Image_folder FROM products WHERE ID=".$product->product_id);
            $product->{'name'} = $p[0]->Name;
            $product->{'picture'} = $p[0]->Image_folder."/main.png";
        }
        return $items;
    }

    public function __construct($table, $name, $db,$option=''){
        $this->name=$name;
        $this->table = $table;
        $this->items= $this->getItems($db);
        $this->option = $option;
    }

    
}


?>