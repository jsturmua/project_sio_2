<?php
$pdo = new PDO('mysql:host=localhost;dbname=store', 'root', '165392');

// Obtenha o ID do produto dos dados POST
$productId = $_POST['id'];

// Insira o produto na tabela "cart"
$sql = "INSERT INTO cart (product_id) VALUES (:product_id)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':product_id', $productId);

// Execute a consulta
if ($stmt->execute()) {echo 'success';} else {echo 'error';}
?>
