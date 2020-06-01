<?php
require_once 'vendor/autoload.php';
require_once 'Class/Database.php';

$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates');
$twig = new Twig_Environment($loader, [
    //'cache' => __DIR__ . '/tmp' //For final version
    'cache' => false
        ]);

$db = new Database('qianli','root','root');
$db->exec("SET CHARACTER SET utf8");

$page = 'home';
$twig->addGlobal('current_page', $page);


$id_product=null;

if(isset($_GET['page'])){
    $page=$_GET['page'];
}

if($page === 'product'){
    if(isset($_GET['id'])){
        $id_product=$_GET['id'];
    }
    else if(isset($_GET['name'])){
        $ret = $db->query("SELECT id FROM products WHERE Name LIKE '%".$_GET['name']."%' ");
        if(isset($ret[0]))
            $id_product= intval($ret[0]->id);
    }
}


//PAGES INFORMATIONS / META TAGS
$title = "QianLi";
$author = "QianLi";
$description = "QianLi is the brand of excellence in iPhone and phone repair tools";

//CARDS OF DATABASE
/* --For future improvement of navbar that expands to reveal products 
require_once('Class/Category.php');
$products = $db->query("SELECT * FROM products");
$catdb =  $db->query("SELECT * FROM categories");

$categories9 = array();
foreach ($catdb as $cat) {
    $newc = new Category($cat->table,$cat->name,$db,'LIMIT 9');
    array_push($categories9, $newc );
}
$twig->addGlobal('categories9Nav', $categories9);
*/
//Make sure to activate 'product_list' on your pages


function renderCategoryPage($table, $name){
    global $db;
    global $twig;
    global $title, $author, $description;

    require_once('Class/Category.php');
    $cat_array = array( new Category($table,$name,$db));
    echo $twig->render("allproducts.twig",
    [
        'meta' => [
            'title' => $title.' : '.$name,
            'author' => $author,
            'description' => $description
        ],
        'categories' => $cat_array,
        'search' => false,
        'product_list' => false
    ]
    );
}

switch ($page) {
    case 'home':
        echo $twig->render("index.twig",
        [
            'meta' => [
                'title' => $title,
                'author' => $author,
                'description' => $description
            ],
            'product_list' => false
        ]
        );
        break;
    
    case 'about':
        //For the results : order by ASC or DESC
        $events = $db->query("SELECT * FROM timeline ORDER BY id ASC");
        echo $twig->render("about.twig",
        [
            'meta' => [
                'title' => $title,
                'author' => $author,
                'description' => $description
            ],
            'product_list' => false,
            'timeline' => $events
        ]
        );
        break;
    
    case 'buying':
    case 'distributors':
        echo $twig->render("distributors.twig",
        [
            'meta' => [
                'title' => $title,
                'author' => $author,
                'description' => $description
            ],
            'product_list' => false
        ]
        );
        break;
    
    case 'product':
        if(is_null($id_product) || $id_product <= 0){
            not_found:
            echo $twig->render("product-base.twig",
            [
                'meta' => [
                    'title' => $title,
                    'author' => $author,
                    'description' => $description,
                ],
                'product' => [
                    'title' => 'Product not found',
                    'description' => 'The product you are looking for is not registered. Enjoy our placeholder content !',
                    'buy_link' => 'https://qianlispace.en.alibaba.com/',
                    'video_link' => '#',
                ],
                'index_link' => 'index.php',
                'product_list' => false
            ]
            );
        }
        else{
            //We get the product in the database
            $products = $db->query("SELECT * FROM products WHERE ID=".$id_product);
            if (array_key_exists(0,$products)==false) {
                goto not_found;
            }
            else{
                $product = $products[0];
                $img_folder = "resources/products/".$product->Image_folder;
                //For simplicity sake, $folder is our image array;
                $folder = array();
                if( is_dir($img_folder)){
                    $folder_base = scandir($img_folder,0);
                    for ($i=2; $i < sizeof($folder_base); $i++) {
                        if($folder_base[$i] != 'main.png')
                        array_push($folder,"resources/products/".$product->Image_folder."/".$folder_base[$i]);
                    }
                }
                echo $twig->render("product.twig",
                [
                    'meta' => [
                        'title' => $title,
                        'author' => $author,
                        'description' => $description
                    ],
                    'product' => [
                        'title' => $product->Name,
                        'description' => $product->Description,
                        'buy_link' => 'https://qianlispace.en.alibaba.com/',
                        'video_link' => '#',
                        'mainPic' => 'resources/products/'.$product->Image_folder.'/main.png',
                        'folder' => $folder
                    ],
                    'index_link' => 'index.php',
                    'product_list' => false
                ]
                );
            }
        }
        break;
    
    case 'allproducts':
        require_once('Class/Category.php');
        $products = $db->query("SELECT * FROM products");
        $categories =  $db->query("SELECT * FROM categories");

        $cat_array = array();
        $registered_products = array();
        foreach ($categories as $cat) {
            $newc = new Category($cat->table,$cat->name,$db);
            array_push($cat_array, $newc );
            foreach ($newc->items as $item) {
                array_push($registered_products,$item->product_id);
            }
        }
        $unregistered_products = array();
        foreach ($products as $prod) {
            $in = false;
            foreach ($registered_products as $registered) {
               if ($prod->ID == $registered) {
                    $in = true;
               }
            }
            if($in == false){
                array_push($unregistered_products,$prod);
            }
        }
        echo $twig->render("allproducts.twig",
        [
            'meta' => [
                'title' => $title,
                'author' => $author,
                'description' => $description
            ],
            'all' => $products,
            'categories' => $cat_array,
            'others' => $unregistered_products,
            'search' => true,
            'product_list' => false
        ]
        );
        break;


    case 'stencils':
        renderCategoryPage('cat_stencils',"Stencils");
    break;
    case 'removers':
        renderCategoryPage('cat_removers',"Blades and glue removal");
    break;
    case 'prying_tools':
        renderCategoryPage('cat_pryers',"Prying tools");
    break;
    case 'welding_tools':
        renderCategoryPage('cat_welding',"Welding and Soldering supplies");
    break;
    
    
    
    case 'downloads':
        $downloads = $db->query("SELECT * FROM downloads ORDER BY id DESC");
        echo $twig->render("downloads.twig",
        [
            'meta' => [
                'title' => $title,
                'author' => $author,
                'description' => $description
            ],
            'downloads' => $downloads,
            'product_list' => false
        ]
        );
        break;

    case 'contact':
        echo $twig->render("contact.twig",
        [
            'meta' => [
                'title' => $title,
                'author' => $author,
                'description' => $description
            ],
            'product_list' => false
        ]
        );
        break;
    
    case 'insertproduct':
        $products = $db->query("SELECT * FROM products");
        echo $twig->render("insertproduct.twig",
        [
            'meta' => [
                'title' => $title,
                'author' => $author,
                'description' => $description
            ],
            'products' => $products,
            'product_list' => false
        ]
        );
        break;
    
    case 'assigncategory':
        $products = $db->query("SELECT * FROM products");
        $categories =  $db->query("SELECT * FROM categories");
        echo $twig->render("assigncategory.twig",
        [
            'meta' => [
                'title' => $title,
                'author' => $author,
                'description' => $description
            ],
            'products' => $products,
            'categories' => $categories,
            'product_list' => false
        ]
        );
        break;
    
    default:
        echo $twig->render("404.twig",
        [
            'meta' => [
                'title' => $title,
                'author' => $author,
                'description' => $description
            ],
            'product_list' => false
        ]
        );
        break;
}


?>