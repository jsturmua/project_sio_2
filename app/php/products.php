<?php 
$pdo = new PDO('mysql:host=localhost;dbname=store', 'root', '165392');

$search = $_POST['search'];
$category = $_POST['category'];

$sql = "SELECT * FROM products WHERE 1=1";

if ($category !== 'all') {$sql .= " AND category = '$category'";}
if (!empty($search)) {$search = "%$search%";$sql .= " AND name LIKE '$search'";}

$result = $pdo->query($sql);

if ($result !== false) {
    $products = $result->fetchAll(PDO::FETCH_ASSOC);

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
} else {
    echo "Erro na consulta SQL";
}
?>
