// js/script.js

document.addEventListener("DOMContentLoaded", function () {
  // ===============================
  // NAVBAR, SEARCH, CART
  // ===============================

  const navbarNav = document.querySelector(".navbar-nav");
  const hm = document.querySelector("#hamburger-menu");

  if (navbarNav && hm) {
    hm.addEventListener("click", function (e) {
      e.preventDefault();
      navbarNav.classList.toggle("active");
    });
  }

  // Search form
  const searchForm = document.querySelector(".search-form");
  const searchBox = document.querySelector("#search-box");
  const sb = document.querySelector("#search-button");

  if (sb && searchForm && searchBox) {
    sb.addEventListener("click", function (e) {
      e.preventDefault(); // cegah naik ke atas karena href="#"
      searchForm.classList.toggle("active");
      searchBox.focus();
    });
  }

  // Shopping cart
  const shoppingCart = document.querySelector(".shopping-cart");
  const sc = document.querySelector("#shopping-cart-button");

  if (sc && shoppingCart) {
    sc.addEventListener("click", function (e) {
      e.preventDefault(); // cegah naik ke atas
      shoppingCart.classList.toggle("active");
    });
  }

  // Klik di luar elemen -> tutup menu/search/cart
  document.addEventListener("click", function (e) {
    const target = e.target;

    // Tutup navbar
    if (
      hm &&
      navbarNav &&
      !hm.contains(target) &&
      !navbarNav.contains(target)
    ) {
      navbarNav.classList.remove("active");
    }

    // Tutup search form
    if (
      sb &&
      searchForm &&
      !sb.contains(target) &&
      !searchForm.contains(target)
    ) {
      searchForm.classList.remove("active");
    }

    // Tutup shopping cart
    if (
      sc &&
      shoppingCart &&
      !sc.contains(target) &&
      !shoppingCart.contains(target)
    ) {
      shoppingCart.classList.remove("active");
    }
  });

  // ===============================
  // MODAL BOX DETAIL PRODUK
  // ===============================

  const itemDetailModal = document.querySelector("#item-detail-modal");
  const itemDetailButtons = document.querySelectorAll(".item-detail-button");

  if (itemDetailModal && itemDetailButtons.length) {
    itemDetailButtons.forEach((btn) => {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        itemDetailModal.style.display = "flex";
      });
    });

    const closeIcon = document.querySelector(".modal .close-icon");
    if (closeIcon) {
      closeIcon.addEventListener("click", function (e) {
        e.preventDefault();
        itemDetailModal.style.display = "none";
      });
    }

    window.addEventListener("click", function (e) {
      if (e.target === itemDetailModal) {
        itemDetailModal.style.display = "none";
      }
    });
  }

  // ===============================
  // CHECKOUT FORM -> WHATSAPP + SIMPAN KE DB
  // ===============================

  const checkoutForm = document.querySelector("#checkoutForm");
  if (checkoutForm) {
    checkoutForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // Ambil cart dari Alpine store
      const cart = window.Alpine && Alpine.store ? Alpine.store("cart") : null;
      const items = cart ? cart.items : [];
      const total = cart ? cart.total : 0;

      // Data pelanggan
      const name = document.querySelector("#name").value.trim();
      const email = document.querySelector("#email").value.trim();
      const phone = document.querySelector("#phone").value.trim();

      if (!items.length) {
        alert("Keranjang masih kosong.");
        return;
      }
      if (!name || !email || !phone) {
        alert("Mohon isi Nama, Email, dan Phone.");
        return;
      }

      const lines = items
        .map((it, i) => {
          const subtotal = (it.quantity * it.price).toLocaleString("id-ID");
          return `${i + 1}. ${it.name} x ${it.quantity} = Rp ${subtotal}`;
        })
        .join("\n");

      const totalStr = total.toLocaleString("id-ID");
      const message = `Halo Ps Astro, saya ${name}.
Email: ${email}
No. HP: ${phone}

Pesanan:
${lines}

TOTAL: Rp ${totalStr}

Mohon konfirmasi ketersediaan. Terima kasih!`;

      const waNumber = "6285223185593"; // nomor WA tujuan
      const url = `https://wa.me/${waNumber}?text=${encodeURIComponent(
        message
      )}`;

      // Kirim data ke PHP (save_order.php)
      fetch("save_order.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          name,
          email,
          phone,
          items,
          total,
        }),
      })
        .catch((err) => {
          console.error("Gagal simpan ke server:", err);
        })
        .finally(() => {
          // Buka WhatsApp
          window.open(url, "_blank");

          // Reset form & cart
          cart && cart.clear();
          checkoutForm.reset();
          const shoppingCartEl = document.querySelector(".shopping-cart");
          shoppingCartEl && shoppingCartEl.classList.remove("active");
        });
    });
  }

  // ===============================
  // CONTACT FORM -> WHATSAPP
  // ===============================

  const contactForm = document.querySelector("#contactForm");
  if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const name = document.querySelector("#contactName").value.trim();
      const email = document.querySelector("#contactEmail").value.trim();
      const phone = document.querySelector("#contactPhone").value.trim();

      if (!name || !email || !phone) {
        alert("Mohon isi Nama, Email, dan No. HP.");
        return;
      }

      const message = `Halo Ps Astro, saya ${name}.
Email: ${email}
No. HP: ${phone}

Saya ingin bertanya mengenai layanan PS Astro. 
Mohon info lebih lanjut. Terima kasih!`;

      const waNumber = "6285223185593";
      const url = `https://wa.me/${waNumber}?text=${encodeURIComponent(
        message
      )}`;

      window.open(url, "_blank");

      contactForm.reset();
    });
  }
});
