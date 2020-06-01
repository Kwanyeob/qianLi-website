<?php

class Database{
    
    private $db_name;
    private $db_user;
    private $db_pass;
    private $db_host;
    private $pdo;
    
    //Initialize database connexion
    public function __construct($db_name,$db_user='root',$db_pass='root',$db_host='localhost'){
        
        $this->db_name=$db_name;
        $this->db_user=$db_user;
        $this->db_pass=$db_pass;
        $this->db_host=$db_host;
        
        
    }
    //returns the pdo object - private
    private function getPDO(){
        if($this->pdo === null){
            $pdo =new PDO('mysql:dbname='.$this->db_name.';host='.$this->db_host,$this->db_user,$this->db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $this->pdo=$pdo;
        }
        return $this->pdo;
    }
    //returns an array of results(read)
    public function query($statement){
        $req= $this->getPDO()->query($statement);
        $datas= $req->fetchALL(PDO::FETCH_OBJ);
        return $datas;
    }
    //execute an instruction and returns the result (insert,update,delete)
    public function exec($statement){
        $datas= $this->getPDO()->exec($statement);
        return $datas;
    }
    
}

?>
