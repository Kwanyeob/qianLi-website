<?php

if (    isset($_POST['Name']) && isset($_POST['Description']) ){
    $name = $_POST['Name'];
    $imgfolder = $_POST['imgFolder'];
    $description = $_POST['Description'];

    require '../Class/Database.php';
    $db= new Database('qianli','root','','localhost');

    $sqlrequest = 'INSERT INTO products SET Name="'.$name.'",Image_folder="'.$imgfolder.'",Description="'.$description.'"';
    try{
        $db->exec($sqlrequest);
        echo "Product added ✔️";
    }
    catch (Exception $ex){
        echo  "Error : Product not added ❌";
    }
}
