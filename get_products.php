<?php
// get_products.php
require __DIR__ . '/config.php';

$stmt = $pdo->query('SELECT * FROM products ORDER BY id');
$rows = $stmt->fetchAll();

$items = [];
foreach ($rows as $row) {
  

  $items[] = [
    'id'          => (int)$row['id'],
    'name'        => $row['name'],
    'img'         => $row['img'],          // simpan di DB sebagai "1.jpg", "2.jpg", dst
    'price'       => (int)$row['price'],
    'priceOld'    => isset($row['price_old']) ? (int)$row['price_old'] : null,
    'description' => $row['description'] ?? '',
    'available'   => (int)$row['available'], // <--- TAMBAHKAN BARIS INI
  ];
}

header('Content-Type: application/json');
echo json_encode($items);
