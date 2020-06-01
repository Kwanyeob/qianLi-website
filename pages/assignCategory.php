<?php

if (    isset($_POST['categoryId']) && isset($_POST['products']) ){
    $products = $_POST['products'];
    $categoryId = $_POST['categoryId'];
    $productsarray = explode(',',$products);


    require '../Class/Database.php';
    $db= new Database('qianli','root','root','localhost');

    $cat_table = $db->query("SELECT * FROM categories WHERE id=".$categoryId)[0]->table;
    try{
        foreach ($productsarray as $prod) {
            if(!isset($db->query('SELECT * FROM '.$cat_table.' WHERE product_id='.$prod)[0])){
                $sqlrequest = 'INSERT INTO '.$cat_table.' SET product_id="'.$prod.'"';
                $db->exec($sqlrequest);
            }
            else{
                echo "<p>Product '".$prod."' already existing ❌</p>";
            }
        }
        echo "Finished ✔️";
    }
    catch (Exception $ex){
        echo  "Error : Product not added ❌";
    }
}
