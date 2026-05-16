<?php
require __DIR__ . '/config.php';

// Ambil produk dari DB (lengkap)
$stmt = $pdo->query("
  SELECT 
    id,
    NAME AS name,
    img,
    price,
    price_old,
    description,
    available
  FROM products
  ORDER BY id
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ps Astro</title>

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />

       <!-- feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>

    <!-- style -->
    <link rel="stylesheet" href="css/style.css" />

    <!-- Kirim data produk dari PHP ke JavaScript -->
    <script>
      window.PRODUCTS_FROM_DB = <?= json_encode(
        $products,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
      ); ?>;
    </script>

    <!-- App: PASTIKAN defer, BUKAN async, dan DILETAKKAN SEBELUM Alpine -->
    <script src="src/app.js" defer></script>

    <!-- AlpineJS: LETAKKAN SETELAH app.js -->
    <script
      defer
      src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"
    ></script>

    <style>
  [x-cloak] {
    display: none !important;
  }
</style>



  </head>
  

  <body>

            <!-- Navbar Start -->
    <nav class="navbar" x-data>
      <a href="#home" class="navbar-logo">Ps<span>Astro</span>.</a>

      <div class="navbar-nav">
        <a href="#home">Home</a>
        <a href="#about">Tentang Kami</a>
        <a href="#menu">Fasilitas</a>
        <a href="#products">Produk</a>
        <a href="#contact">Kontak</a>
      </div>

          <div class="navbar-extra">
  <!-- Search -->
  <a href="#" id="search-button" title="Cari">
    <i data-feather="search"></i>
  </a>

  <!-- Cart -->
  <a href="#" id="shopping-cart-button" title="Keranjang">
    <i data-feather="shopping-cart"></i>
    <span
      class="quantity-badge"
      x-show="$store.cart.quantity"
      x-text="$store.cart.quantity"
    ></span>
  </a>

  <!-- Login Admin -->
  <a href="/admin/login.php" id="admin-login-button" title="Login Admin">
    <i data-feather="user"></i>
  </a>

  <!-- Hamburger (mobile) -->
  <a href="#" id="hamburger-menu">
    <i data-feather="menu"></i>
  </a>
</div>



      <!-- search form start -->
      <div class="search-form">
        <input type="search" id="search-box" placeholder="cari disini ..." />
        <label for="search-box"><i data-feather="search"></i></label>
      </div>
      <!-- search form end -->

      <!-- Shopping Cart Start -->
      <div class="shopping-cart">
        <template x-for="(item, index) in $store.cart.items" x-key="index">
          <div class="cart-item">
            <img :src="`img/products/${item.img}`" :alt="item.name" />
            <div class="item-detail">
              <h3 x-text="item.name"></h3>
              <div class="item-price">
                <span x-text="rupiah(item.price)"></span> &times;
                <button id="remove" @click="$store.cart.remove(item.id)">
                  &minus;
                </button>
                <span x-text="item.quantity"></span>
                <button id="add" @click="$store.cart.add(item)">
                  &plus;
                </button>
                &equals;
                <span x-text="rupiah(item.total)"></span>
              </div>
            </div>
          </div>
        </template>

        <h4 x-show="!$store.cart.items.length" style="margin-top: 1rem">
          Cart is Empty
        </h4>
        <h4 x-show="$store.cart.items.length">
          Total : <span x-text="rupiah($store.cart.total)"></span>
        </h4>

        <div class="form-container" x-show="$store.cart.items.length">
          <form id="checkoutForm">
            <input
              type="hidden"
              name="items"
              :value="JSON.stringify($store.cart.items)"
            />
            <input type="hidden" name="total" :value="$store.cart.total" />
            <h5>Customer Detail</h5>

            <label for="name">
              <span>Nama</span>
              <input type="text" name="name" id="name" required />
            </label>

            <label for="email">
              <span>Email</span>
              <input type="email" name="email" id="email" required />
            </label>

            <label for="phone">
              <span>Phone</span>
              <input
                type="tel"
                name="phone"
                id="phone"
                autocomplete="off"
                required
              />
            </label>

            <button class="checkout-button" type="submit" id="checkout-button">
              Checkout
            </button>
          </form>
        </div>
      </div>
      <!-- Shopping Cart End -->
    </nav>
    <!-- Navbar end -->


    <!-- Hero section start-->
    <section class="hero" id="home">
      <main class="content">
        <h1><span>PlayStation:</span> Mainkan Game, Ciptakan Kenangan.</h1>
        <p>
          Main bareng teman, seru-seruan tanpa ribet. Yuk, nongkrong sambil
          nge-game di PS Astro!.
        </p>
        <!-- <a href="#" class="cta">Beli Sekarang</a> -->
      </main>
    </section>
    <!-- Hero section end-->

    <!-- About section start -->
    <section id="about" class="about">
      <h2>Tentang <span>Kami</span></h2>

      <div class="row">
        <div class="about-img">
          <img src="img/Tentang-kami.jpg" alt="Tentang Kami" />
        </div>
        <div class="content">
          <h3>Kenapa Astro Ps?</h3>
          <p>
            Astro Ps hadir dengan harga hemat, layanan cepat, dan pengalaman
            bermain yang bikin ketagihan!.Astro Ps menyediakan layanan permainan
            interaktif dengan teknologi terkini. Didukung fasilitas modern dan
            suasana kondusif, kami siap memberikan pengalaman bermain yang aman
            dan menyenangkan.
          </p>
          <p>
            Tempat bermain profesional dengan pelayanan terbaik dan fasilitas
            yang menunjang kenyamanan seluruh pelanggan.
          </p>
        </div>
      </div>
    </section>
    <!-- About section end -->

    <!-- Menu section start -->
    <section id="menu" class="menu">
      <h2>Fasilitas <span>Kami</span></h2>
      <p>
        Nikmati ruang bermain nyaman dengan sofa empuk, kipas sejuk, layar
        besar, koleksi game terbaru, serta pilihan snack dan minuman. Cocok
        untuk mabar santai, turnamen kecil, atau quality time bersama teman.
      </p>
      <div class="row">
        <div class="menu-card">
          <img src="img/menu/2.jpg" alt="Paket 1" class="menu-card-img" />
          <h3 class="menu-card-title">- Tempat -</h3>
          <p class="menu-card-price">
            Ruangan nyaman dengan pencahayaan yang pas, membuat pengalaman
            bermain lebih seru dan betah berlama-lama.
          </p>
        </div>
        <div class="menu-card">
          <img src="img/menu/3.jpg" alt="Paket 1" class="menu-card-img" />
          <h3 class="menu-card-title">- Sofa -</h3>
          <p class="menu-card-price">
            Sofa empuk dan bersih yang dirancang untuk kenyamanan maksimal saat
            bermain game bersama teman.
          </p>
        </div>
        <div class="menu-card">
          <img src="img/menu/4.jpg" alt="Paket 1" class="menu-card-img" />
          <h3 class="menu-card-title">- Kipas -</h3>
          <p class="menu-card-price">
            Ruangan dilengkapi kipas angin untuk menjaga suasana tetap sejuk dan
            nyaman selama bermain.
          </p>
        </div>
        <div class="menu-card">
          <img src="img/menu/5.jpg" alt="Paket 1" class="menu-card-img" />
          <h3 class="menu-card-title">- Minuman -</h3>
          <p class="menu-card-price">
            Tersedia berbagai pilihan minuman segar untuk menemani waktu bermain
            Anda di PS Astro.
          </p>
        </div>
        <div class="menu-card">
          <img src="img/menu/6.jpg" alt="Paket 1" class="menu-card-img" />
          <h3 class="menu-card-title">- Makanan -</h3>
          <p class="menu-card-price">
            Nikmati aneka camilan ringan seperti keripik dan snack untuk
            menambah keseruan saat nge-game.
          </p>
        </div>
        <div class="menu-card">
          <img src="img/menu/7.jpg" alt="Paket 1" class="menu-card-img" />
          <h3 class="menu-card-title">- Daftar Game -</h3>
          <p class="menu-card-price">
            Koleksi game terbaru dan terpopuler siap dimainkan, mulai dari
            action, adventure, hingga sport.
          </p>
        </div>
      </div>
    </section>
    <!-- Menu section end -->

    <!-- Products Section start -->
    <section class="products" id="products" x-data="products">
      <h2><span>Produk Unggulan</span> Kami</h2>
      <p>
        Kami memiliki beberapa produk unggulan yang bisa dinikmati dengan
        layanan dan harga yang sangat menyenangkan untuk kalian.
      </p>

      <div class="row">
        <template x-for="(item, index) in items" x-key="index">
          <div class="product-card">
            <div class="product-icons">
              <a href="#" @click.prevent="$store.cart.add(item)">
                <svg
                  width="24"
                  height="24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <use href="img/feather-sprite.svg#shopping-cart" />
                </svg>
              </a>
              <!-- Eye button: tambahkan data-* agar isi modal -->
              <a
                href="#"
                class="item-detail-button"
                data-modal-target="#item-detail-modal"
                :data-id="item.id"
                :data-title="item.name"
                :data-image="`img/products/${item.img}`"
                :data-price="item.price"
                :data-price-old="item.priceOld || ''"
                :data-description="item.description || 'Produk unggulan PS Astro.'"
              >
                <svg
                  width="24"
                  height="24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <use href="img/feather-sprite.svg#eye" />
                </svg>
              </a>
            </div>
            <div class="product-image">
  <img :src="`img/products/${item.img}`" :alt="item.name" />
</div>
            <div class="product-content">
              <h3 x-text="item.name"></h3>
              <div class="product-stars">
                <svg
                  width="24"
                  height="24"
                  fill="currentColor"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <use href="img/feather-sprite.svg#star" />
                </svg>
                <svg
                  width="24"
                  height="24"
                  fill="currentColor"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <use href="img/feather-sprite.svg#star" />
                </svg>
                <svg
                  width="24"
                  height="24"
                  fill="currentColor"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <use href="img/feather-sprite.svg#star" />
                </svg>
                <svg
                  width="24"
                  height="24"
                  fill="currentColor"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <use href="img/feather-sprite.svg#star" />
                </svg>
                <svg
                  width="24"
                  height="24"
                  fill="currentColor"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <use href="img/feather-sprite.svg#star" />
                </svg>
              </div>
              <div class="product-price">
                <span x-text="rupiah(item.price)"></span>
              </div>
            </div>
          </div>
        </template>
      </div>
    </section>
    <!-- Products Section end -->

    <!-- Contact section start -->
    <section id="contact" class="contact">
      <h2>Kontak <span>Kami</span></h2>
      <p>
        Ada yang ingin ditanyakan? Langsung aja hubungi kami, ya! Bisa lewat WA,
        DM IG, atau mampir langsung ke tempat. Tim Astro Ps siap bantu dengan
        ramah!
      </p>

      <div class="row">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d989.1132442055759!2d109.27234156945038!3d-7.415001299537208!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e655ec823a64e91%3A0x7220010327deac51!2sJl.%20Tegal%20Mulya%20No.6%2C%20Tegalmulya%2C%20Ledug%2C%20Kec.%20Kembaran%2C%20Kabupaten%20Banyumas%2C%20Jawa%20Tengah%2053182!5e0!3m2!1sid!2sid!4v1752122432426!5m2!1sid!2sid"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          class="map"
        ></iframe>

        <form id="contactForm">
          <div class="input-group">
            <i data-feather="user"></i>
            <input
              type="text"
              placeholder="nama"
              id="contactName"
              name="name"
              required
            />
          </div>
          <div class="input-group">
            <i data-feather="mail"></i>
            <input
              type="email"
              placeholder="email"
              id="contactEmail"
              name="email"
              required
            />
          </div>
          <div class="input-group">
            <i data-feather="phone"></i>
            <input
              type="tel"
              placeholder="no.hp"
              id="contactPhone"
              name="phone"
              required
            />
          </div>
          <button type="submit" class="btn">kirim pesan</button>
        </form>
      </div>
    </section>
    <!-- Contact section end -->

    <!-- Footer start -->
    <footer>
      <div class="socials">
        <a href="#"><i data-feather="instagram"></i></a>
        <a href="#"><i data-feather="twitter"></i></a>
        <a href="#"><i data-feather="facebook"></i></a>
      </div>
      <div class="links">
        <a href="#home">Home</a>
        <a href="#about">Tentang Kami</a>
        <a href="#menu">Fasilitas</a>
        <a href="#products">Produk</a>
        <a href="#contact">Kontak</a>
      </div>

      <div class="credit">
        <p>Created By <a href="">KudangRakhmanMuliana</a>. | &copy; 2025.</p>
      </div>
    </footer>
    <!-- Footer end -->

            <!-- Modal Login Admin start -->
    <div
      class="modal-login"
      x-cloak
      x-show="$store.ui.showLogin"
      x-transition
      style="
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
      "
      @click.self="$store.ui.showLogin = false"
    >
      <div
        class="modal-login-box"
        style="
          background: #fff;
          padding: 1.5rem;
          border-radius: 0.75rem;
          width: 100%;
          max-width: 360px;
        "
      >
        <h2 style="margin-top:0; margin-bottom:1rem; text-align:center;">
          Login Admin PS Astro
        </h2>

        <form
          @submit.prevent="$store.auth.login($event.target); if($store.auth.isLoggedIn){ $store.ui.showLogin = false }"
        >
          <label style="display:block; margin-bottom:0.75rem;">
            <span>Username</span>
            <input
              type="text"
              name="username"
              required
              style="width:100%; padding:0.5rem; margin-top:0.25rem;"
            />
          </label>

          <label style="display:block; margin-bottom:0.75rem;">
            <span>Password</span>
            <input
              type="password"
              name="password"
              required
              style="width:100%; padding:0.5rem; margin-top:0.25rem;"
            />
          </label>

          <p
            x-show="$store.auth.error"
            x-text="$store.auth.error"
            style="color:red; font-size:0.875rem; margin-bottom:0.5rem;"
          ></p>

          <div
            style="display:flex; justify-content:flex-end; gap:0.5rem; margin-top:1rem;"
          >
            <button
              type="button"
              @click="$store.ui.showLogin = false"
              style="padding:0.5rem 0.75rem;"
            >
              Batal
            </button>
            <button
              type="submit"
              style="padding:0.5rem 0.75rem; font-weight:bold;"
            >
              Login
            </button>
          </div>
        </form>
      </div>
    </div>
    <!-- Modal Login Admin end -->

    
    <!-- Modal box Item Detail start -->
    <div
      class="modal"
      id="item-detail-modal"
      role="dialog"
      aria-modal="true"
      aria-labelledby="item-detail-title"
      aria-describedby="item-detail-desc"
      aria-hidden="true"
    >
      <div class="modal-container" role="document">
        <button
          type="button"
          class="close-icon"
          data-modal-close
          aria-label="Tutup"
        >
          <i data-feather="x"></i>
        </button>
        <div class="modal-content">
          <img
            id="item-detail-image"
            src="img/products/p1.jpg"
            alt="Gambar produk"
            loading="lazy"
          />
          <div class="product-content">
            <h3 id="item-detail-title">Product 1</h3>
            <p id="item-detail-desc">Deskripsi singkat produk.</p>
            <div class="product-stars" aria-label="Rating 5 dari 5">
              <i data-feather="star"></i>
              <i data-feather="star"></i>
              <i data-feather="star"></i>
              <i data-feather="star"></i>
              <i data-feather="star"></i>
            </div>
            <div class="product-price">
              <span id="item-price">Rp 70.000</span>
              <span id="item-price-old" class="price-old">Rp 90.000</span>
            </div>
            <a href="#products" class="btn-buy" data-modal-close>
  <i data-feather="shopping-cart"></i> <!-- Ikon Keranjang -->
  <span>Rp </span>
  <span id="item-price-plain">70.000</span>
</a>

          </div>
        </div>
      </div>
    </div>

       
    <!-- Modal box Item Detail end -->

    <!-- Feather Icons -->
    <script>
      feather.replace();
    </script>

    <!-- Modal logic: buka/tutup + isi konten + trap fokus -->
    <script>
  (() => {
    const modal = document.getElementById("item-detail-modal");
    const docBody = document.body;
    let currentItem = null; // Untuk menyimpan data produk yang sedang dibuka

    const sel = {
      img: "#item-detail-image",
      title: "#item-detail-title",
      desc: "#item-detail-desc",
      price: "#item-price",
      priceOld: "#item-price-old",
      pricePlain: "#item-price-plain",
    };

    const formatRupiah = (n) =>
      new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
      }).format(Number(n || 0));

    function openModal(trigger) {
      // Ambil semua data dari dataset tombol mata
      const { id, title, description, price, priceOld, image } = trigger.dataset;

      // 1. Simpan ke variabel currentItem agar bisa dibeli
      currentItem = {
        id: id,
        name: title,
        price: price,
        img: image.split('/').pop()
      };

      // 2. Update Tampilan Modal secara Dinamis
      if (title) modal.querySelector(sel.title).textContent = title;
      if (description) modal.querySelector(sel.desc).textContent = description;
      if (image) {
        const imgEl = modal.querySelector(sel.img);
        imgEl.src = image;
        imgEl.alt = title || "Gambar produk";
      }

      if (price) {
        modal.querySelector(sel.price).textContent = formatRupiah(price);
        modal.querySelector(sel.pricePlain).textContent =
          new Intl.NumberFormat("id-ID").format(Number(price));
      }

      const oldEl = modal.querySelector(sel.priceOld);
      if (priceOld) {
        oldEl.textContent = formatRupiah(priceOld);
        oldEl.style.display = "";
      } else {
        oldEl.style.display = "none";
      }

      modal.setAttribute("aria-hidden", "false");
      docBody.style.overflow = "hidden";

      if (window.feather) feather.replace();
    }

    function closeModal() {
      modal.setAttribute("aria-hidden", "true");
      docBody.style.overflow = "";
    }

    // Event Klik Tombol Cokelat (Beli)
    document.querySelector('.btn-buy').addEventListener('click', (e) => {
      e.preventDefault();
      if (currentItem && window.Alpine) {
        Alpine.store('cart').add(currentItem);
        closeModal();
      }
    });

    // Delegasi klik: buka + tutup
    document.addEventListener("click", (e) => {
      const btn = e.target.closest('[data-modal-target="#item-detail-modal"]');
      if (btn) {
        e.preventDefault();
        openModal(btn);
        return;
      }
      if (e.target.closest("[data-modal-close]")) {
        e.preventDefault();
        closeModal();
      }
    });

    modal.addEventListener("mousedown", (e) => {
      if (e.target === modal) closeModal();
    });

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") closeModal();
    });
  })();
</script>


    <!-- My Javascript -->
    <script src="js/script.js" defer></script>
  </body>
</html>
