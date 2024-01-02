<?php
session_start();
$loggedIn = isset($_SESSION['login_user']);
if ($loggedIn){
    $_SESSION['last_activity'] = time();
}
$pdo = new PDO('mysql:host=localhost;dbname=store', 'root', '165392');

$search = isset($_POST['search']) ? $_POST['search'] : '';
$category = isset($_POST['category']) ? $_POST['category'] : 'all';

$sql = "SELECT * FROM products WHERE 1=1";
$params = array();

if ($category !== 'all') {$sql .= " AND category = :category"; $params[':category'] = $category;}
if (!empty($search)) {$sql .= " AND name LIKE :search";$params[':search'] = "%$search%";}

$stmt = $pdo->prepare($sql);

if ($stmt->execute($params)) {
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $html = '';
    foreach ($products as $product) {
        $html .= '<div class="product" data-product-id="' . $product['id'] . '">';
        $html .= '<img src="' . $product['image'] . '" alt="' . $product['name'] . '">';
        $html .= '<h2>' . $product['name'] . '</h2>';
        $html .= '<p>' . $product['description'] . '</p>';
        $html .= '<p>Preço: R$ ' . $product['price'] . '</p><br>';
        $html .= '<button class="add-to-cart">Add to cart</button>';
        $html .= '</div>';
    }

    echo $html;
} else {echo "Erro na consulta SQL";}
?>
