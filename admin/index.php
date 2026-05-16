<?php
session_start();

if (empty($_SESSION['admin_id'])) {
  header('Location: login.php');
  exit;
}

require __DIR__ . '/../config.php';

/*
|--------------------------------------------------------------------------
| MESSAGE
|--------------------------------------------------------------------------
*/
$successMsg = '';
$errorMsg   = '';

/*
|--------------------------------------------------------------------------
| TAMBAH PRODUK
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {

  $name        = trim($_POST['name'] ?? '');
  $price       = (int)($_POST['price'] ?? 0);
  $price_old   = (int)($_POST['price_old'] ?? 0);
  $description = trim($_POST['description'] ?? '');
  $available   = isset($_POST['available']) ? 1 : 0;

  if (!$name || $price <= 0) {

    $errorMsg = 'Nama produk dan harga wajib diisi.';

  } else {

    $imgName = '';

    if (!empty($_FILES['img']['name'])) {

      $ext = strtolower(
        pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION)
      );

      $allowed = ['jpg', 'jpeg', 'png', 'webp'];

      if (!in_array($ext, $allowed)) {

        $errorMsg = 'Format gambar tidak didukung.';

      } else {

        // maksimal 2MB
        if ($_FILES['img']['size'] > 2 * 1024 * 1024) {

          $errorMsg = 'Ukuran gambar maksimal 2MB.';

        } else {

          $imgName = time() . '-' . uniqid() . '.' . $ext;

          move_uploaded_file(
            $_FILES['img']['tmp_name'],
            __DIR__ . '/../img/products/' . $imgName
          );
        }
      }
    }

    if (!$errorMsg) {

      $stmt = $pdo->prepare("
        INSERT INTO products
        (name, img, price, price_old, description, available)
        VALUES
        (:name, :img, :price, :price_old, :description, :available)
      ");

      $stmt->execute([
        ':name'        => $name,
        ':img'         => $imgName,
        ':price'       => $price,
        ':price_old'   => $price_old,
        ':description' => $description,
        ':available'   => $available,
      ]);

      $successMsg = 'Produk berhasil ditambahkan.';
    }
  }
}

/*
|--------------------------------------------------------------------------
| HAPUS PRODUK
|--------------------------------------------------------------------------
*/
if (isset($_GET['delete'])) {

  $id = (int)$_GET['delete'];

  // ambil data produk
  $stmt = $pdo->prepare("
    SELECT img
    FROM products
    WHERE id = :id
  ");

  $stmt->execute([
    ':id' => $id
  ]);

  $product = $stmt->fetch();

  if ($product) {

    // hapus file gambar
    if (!empty($product['img'])) {

      $file = __DIR__ . '/../img/products/' . $product['img'];

      if (file_exists($file)) {
        unlink($file);
      }
    }

    // hapus produk dari database
    $stmt = $pdo->prepare("
      DELETE FROM products
      WHERE id = :id
    ");

    $stmt->execute([
      ':id' => $id
    ]);

    $successMsg = 'Produk berhasil dihapus.';

  } else {

    $errorMsg = 'Produk tidak ditemukan.';
  }
}

/*
|--------------------------------------------------------------------------
| UPDATE PRODUK
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_products'])) {

    $prices       = $_POST['price'] ?? [];
    $names        = $_POST['name'] ?? [];        // TAMBAHKAN INI
    $descriptions = $_POST['description'] ?? []; // TAMBAHKAN INI
    $available    = $_POST['available'] ?? [];

    if (!is_array($prices) || !count($prices)) {
        $errorMsg = 'Tidak ada data produk yang dikirim.';
    } else {
        try {
            $pdo->beginTransaction();

            // UPDATE SQL (Ditambah name dan description)
            $stmt = $pdo->prepare("
                UPDATE products 
                SET 
                    name = :name,
                    description = :description,
                    price = :price,
                    available = :available
                WHERE id = :id
            ");

            foreach ($prices as $id => $price) {
                $id    = (int)$id;
                $price = (int)$price;
                $isAvailable = isset($available[$id]) ? 1 : 0;
                $name = $names[$id] ?? '';
                $description = $descriptions[$id] ?? '';

                $stmt->execute([
                    ':name'        => $name,
                    ':description' => $description,
                    ':price'       => $price,
                    ':available'   => $isAvailable,
                    ':id'          => $id
                ]);
            }

            $pdo->commit();
            header("Location: index.php?status=updated"); // Sesuaikan nama file jika bukan index.php
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errorMsg = 'Gagal memperbarui produk: ' . $e->getMessage();
        }
    }
}


/*
|--------------------------------------------------------------------------
| SUMMARY
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query("
  SELECT
    COUNT(*) AS total_orders,
    COALESCE(SUM(total),0) AS total_revenue
  FROM orders
");

$summary = $stmt->fetch();

$totalOrders  = $summary['total_orders'] ?? 0;
$totalRevenue = $summary['total_revenue'] ?? 0;

/*
|--------------------------------------------------------------------------
| ORDERS
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query("
  SELECT *
  FROM orders
  ORDER BY created_at DESC
  LIMIT 20
");

$orders = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| PRODUCTS
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query("
  SELECT *
  FROM products
  ORDER BY id DESC
");

$products = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| FORMAT RUPIAH
|--------------------------------------------------------------------------
*/
function formatRupiah($n)
{
  return 'Rp ' . number_format((int)$n, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

  <meta charset="UTF-8">

  <title>Dashboard Admin - Ps Astro</title>

  <link rel="stylesheet" href="../css/style.css">

  <style>

    body{
      background:#010101;
      color:#fff;
      font-family:"Poppins",sans-serif;
    }

    .admin-wrap{
      max-width:1200px;
      margin:7rem auto 3rem;
      padding:2rem;
      background:#111;
      border-radius:1rem;
      box-shadow:0 8px 20px rgba(0,0,0,.6);
    }

    .admin-header{
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-bottom:2rem;
      flex-wrap:wrap;
      gap:1rem;
    }

    .admin-header h1{
      font-size:2rem;
    }

    .logout-btn{
      padding:.8rem 1.5rem;
      background:#e74c3c;
      color:#fff;
      border-radius:999px;
      text-decoration:none;
      font-weight:600;
    }

    .admin-summary{
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
      gap:1rem;
      margin-bottom:2rem;
    }

    .admin-card{
      background:#222;
      padding:1.5rem;
      border-radius:1rem;
    }

    .admin-card-number{
      font-size:2rem;
      font-weight:bold;
      margin-top:.5rem;
    }

    h2{
      margin:2rem 0 1rem;
    }

    .admin-table-wrapper{
      overflow-x:auto;
    }

    table{
      width:100%;
      border-collapse:collapse;
    }

    th,
    td{
      border:1px solid #333;
      padding:1rem;
      text-align:left;
    }

    th{
      background:#222;
    }

    tr:nth-child(even){
      background:#181818;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    input[type="file"]{
      width:100%;
      padding:.8rem;
      border-radius:.5rem;
      border:1px solid #444;
      background:#000;
      color:#fff;
      margin-top:.3rem;
    }

    textarea{
      resize:vertical;
    }

    .admin-form{
      display:grid;
      gap:1rem;
    }

    .success-msg{
      background:#14532d;
      color:#bbf7d0;
      padding:1rem;
      border-radius:.5rem;
      margin-bottom:1rem;
    }

    .error-msg{
      background:#7f1d1d;
      color:#fecaca;
      padding:1rem;
      border-radius:.5rem;
      margin-bottom:1rem;
    }

    .switch-label{
      display:flex;
      align-items:center;
      gap:.5rem;
    }

    .product-thumb{
      width:80px;
      height:80px;
      object-fit:cover;
      border-radius:.5rem;
      border:1px solid #444;
    }

    .delete-btn{
      display:inline-block;
      padding:.6rem 1rem;
      background:#e74c3c;
      color:#fff;
      border-radius:.5rem;
      text-decoration:none;
      font-size:.9rem;
      font-weight:600;
      transition:.3s;
    }

    .delete-btn:hover{
      background:#c0392b;
    }

    /* ================= SAVE BUTTON ================= */

/* ================= BUTTON TAMBAH PRODUK ================= */

.add-product-btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:.7rem;

  width:fit-content;

  padding:1rem 2rem;

  border:none;
  border-radius:999px;

  background:linear-gradient(135deg,#16a34a,#15803d);

  color:#fff;

  font-size:1rem;
  font-weight:700;

  cursor:pointer;

  transition:all .3s ease;

  box-shadow:
    0 6px 16px rgba(22,163,74,.35),
    inset 0 1px 1px rgba(255,255,255,.15);
}

.add-product-btn:hover{
  transform:translateY(-3px);

  background:linear-gradient(135deg,#22c55e,#166534);

  box-shadow:
    0 10px 22px rgba(22,163,74,.45);
}

.add-product-btn:active{
  transform:scale(.96);
}

/* ================= BUTTON SIMPAN PERUBAHAN ================= */

.save-button-wrap{
  width:100%;
  display:flex;
  justify-content:flex-end;
  margin-top:2rem;
}

.save-btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:.7rem;

  padding:1rem 2.3rem;

  border:none;
  border-radius:999px;

  background:linear-gradient(135deg,#b6895b,#8a623e);

  color:#fff;

  font-size:1rem;
  font-weight:700;

  cursor:pointer;

  transition:all .3s ease;

  box-shadow:
    0 6px 16px rgba(182,137,91,.35),
    inset 0 1px 1px rgba(255,255,255,.12);
}

.save-btn:hover{
  transform:translateY(-3px);

  background:linear-gradient(135deg,#c89b6c,#9b7349);

  box-shadow:
    0 10px 22px rgba(182,137,91,.45);
}

.save-btn:active{
  transform:scale(.96);
}

/* ================= ICON ================= */

.btn-icon{
  font-size:1rem;
}
  </style>

</head>

<body>

<div class="admin-wrap">

  <div class="admin-header">

    <h1>Dashboard Admin Ps Astro</h1>

    <a href="logout.php" class="logout-btn">
      Logout (<?= htmlspecialchars($_SESSION['admin_username']) ?>)
    </a>

  </div>

  <div class="admin-summary">

    <div class="admin-card">
      <h3>Total Pesanan</h3>

      <p class="admin-card-number">
        <?= (int)$totalOrders ?>
      </p>
    </div>

    <div class="admin-card">
      <h3>Total Pendapatan</h3>

      <p class="admin-card-number">
        <?= formatRupiah($totalRevenue) ?>
      </p>
    </div>

  </div>

  <?php if ($successMsg): ?>
    <div class="success-msg">
      <?= htmlspecialchars($successMsg) ?>
    </div>
  <?php endif; ?>

  <?php if ($errorMsg): ?>
    <div class="error-msg">
      <?= htmlspecialchars($errorMsg) ?>
    </div>
  <?php endif; ?>

  <!-- ================= PESANAN ================= -->

  <h2>Pesanan Terakhir</h2>

  <div class="admin-table-wrapper">

    <?php if ($orders): ?>

      <table>

        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Nama</th>
            <th>No HP</th>
            <th>Total</th>
          </tr>
        </thead>

        <tbody>

        <?php foreach ($orders as $o): ?>

          <tr>

            <td>
              <?= htmlspecialchars($o['created_at']) ?>
            </td>

            <td>
              <?= htmlspecialchars($o['customer_name']) ?>
            </td>

            <td>
              <?= htmlspecialchars($o['phone']) ?>
            </td>

            <td>
              <?= formatRupiah($o['total']) ?>
            </td>

          </tr>

        <?php endforeach; ?>

        </tbody>

      </table>

    <?php else: ?>

      <p>Belum ada pesanan.</p>

    <?php endif; ?>

  </div>

  <!-- ================= TAMBAH PRODUK ================= -->

  <h2>Tambah Produk</h2>

  <form
    method="POST"
    enctype="multipart/form-data"
    class="admin-form"
  >

    <input type="hidden" name="add_product" value="1">

    <div>
      <label>Nama Produk</label>

      <input
        type="text"
        name="name"
        required
      >
    </div>

    <div>
      <label>Harga</label>

      <input
        type="number"
        name="price"
        min="0"
        required
      >
    </div>

    <div>
      <label>Harga Lama</label>

      <input
        type="number"
        name="price_old"
        min="0"
      >
    </div>

    <div>
      <label>Deskripsi</label>

      <textarea
        name="description"
        rows="4"
      ></textarea>
    </div>

    <div>
      <label>Upload Gambar</label>

      <input
        type="file"
        name="img"
        accept=".jpg,.jpeg,.png,.webp"
        required
      >
    </div>

    <div>

      <label class="switch-label">

        <input
          type="checkbox"
          name="available"
          value="1"
          checked
        >

        <span>Tersedia</span>

      </label>

    </div>

 <button
  type="submit"
  class="add-product-btn"
>
  <span class="btn-icon">➕</span>
  <span>Tambah Produk</span>
</button>

  </form>

 <!-- ================= KELOLA PRODUK ================= -->
<h2>Kelola Produk</h2>

<form method="POST">
  <input type="hidden" name="update_products" value="1">
  <div class="admin-table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Gambar</th>
          <th>Nama Produk</th>
          <th>Deskripsi</th> <!-- TAMBAHKAN INI -->
          <th>Harga</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><?= (int)$p['id'] ?></td>
          <td>
            <?php if (!empty($p['img'])): ?>
              <img src="../img/products/<?= htmlspecialchars($p['img']) ?>" class="product-thumb">
            <?php else: ?>
              Tidak ada gambar
            <?php endif; ?>
          </td>
          <td>
            <!-- UBAH JADI INPUT AGAR BISA DIEDIT -->
            <input type="text" name="name[<?= (int)$p['id'] ?>]" value="<?= htmlspecialchars($p['name']) ?>" style="width: 100%;">
          </td>
          <td>
            <!-- TAMBAHKAN TEXTAREA UNTUK DESKRIPSI -->
            <textarea name="description[<?= (int)$p['id'] ?>]" rows="3" style="width: 100%; min-width: 150px;"><?= htmlspecialchars($p['description']) ?></textarea>
          </td>
          <td>
            <input type="number" name="price[<?= (int)$p['id'] ?>]" value="<?= (int)$p['price'] ?>" min="0">
          </td>
          <td>
            <label class="switch-label">
              <input type="checkbox" name="available[<?= (int)$p['id'] ?>]" value="1" <?= $p['available'] ? 'checked' : '' ?>>
              <span><?= $p['available'] ? 'Tersedia' : 'Tidak' ?></span>
            </label>
          </td>
          <td>
            <a href="?delete=<?= (int)$p['id'] ?>" class="delete-btn" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="save-button-wrap">
    <button type="submit" class="save-btn">
      <span class="btn-icon">💾</span>
      <span>Simpan Perubahan</span>
    </button>
  </div>
</form>


</div>

</body>
</html>
