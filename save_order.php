<?php
// save_order.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
  http_response_code(400);
  echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
  exit;
}

$name  = $data['name']   ?? '';
$email = $data['email']  ?? '';
$phone = $data['phone']  ?? '';
$items = $data['items']  ?? [];
$total = (int)($data['total'] ?? 0);

if (!$name || !$email || !$phone || !$items || $total <= 0) {
  http_response_code(400);
  echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
  exit;
}

require __DIR__ . '/config.php';

try {
  $pdo->beginTransaction();

  // simpan ke tabel orders
  $stmt = $pdo->prepare("
    INSERT INTO orders (customer_name, email, phone, total)
    VALUES (:name, :email, :phone, :total)
  ");
  $stmt->execute([
    ':name'  => $name,
    ':email' => $email,
    ':phone' => $phone,
    ':total' => $total,
  ]);
  $orderId = $pdo->lastInsertId();

    // simpan item
  $stmtItem = $pdo->prepare("
    INSERT INTO order_items (order_id, product_id, product_name, quantity, price, total)
    VALUES (:order_id, :product_id, :product_name, :quantity, :price, :total)
  ");

  // PERSIAPKAN QUERY POTONG STOK
  $stmtUpdateStock = $pdo->prepare("
    UPDATE products SET available = available - :quantity WHERE id = :product_id
  ");

  foreach ($items as $it) {
    $qty = (int)($it['quantity'] ?? 0);
    $prodId = $it['id'] ?? null;

    $stmtItem->execute([
      ':order_id'     => $orderId,
      ':product_id'   => $prodId,
      ':product_name' => $it['name'] ?? '',
      ':quantity'     => $qty,
      ':price'        => (int)($it['price'] ?? 0),
      ':total'        => (int)($it['total'] ?? 0),
    ]);

    // JALANKAN POTONG STOK
    if ($prodId) {
      $stmtUpdateStock->execute([
        ':quantity'   => $qty,
        ':product_id' => $prodId,
      ]);
    }
  }


  $pdo->commit();

  echo json_encode(['status' => 'ok', 'order_id' => $orderId]);
} catch (Exception $e) {
  if ($pdo->inTransaction()) {
    $pdo->rollBack();
  }
  http_response_code(500);
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
