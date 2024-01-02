<?php
session_start();
$loggedIn = isset($_SESSION['login_user']);
if ($loggedIn){
    $_SESSION['last_activity'] = time();
}

$pdo = new PDO('mysql:host=localhost;dbname=store', 'root', '165392');

$productId = $_POST['id'];
 
$sql = "SELECT * FROM products WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $productId);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

$html = '<div class="product-details">';
$html .= '<img src="' . $product['image'] . '" alt="' . $product['name'] . '">';
$html .= '<h2>' . $product['name'] . '</h2>';
$html .= '<p>' . $product['description'] . '</p>';
$html .= '<p>Preço: R$ ' . $product['price'] . '</p><br>';
$html .= '<button class="add-to-cart">Add to cart</button>';
$html .= '</div>';

echo $html;
?>