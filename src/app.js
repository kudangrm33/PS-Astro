// ===== Formatter rupiah global =====
window.rupiah = (number) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number(number || 0));

// ===== Alpine: Produk, Cart, UI, Auth =====
document.addEventListener("alpine:init", () => {
  // === Data Produk: langsung dari DB (window.PRODUCTS_FROM_DB) ===
  Alpine.data("products", () => ({
    items: (window.PRODUCTS_FROM_DB || []).map((p) => {
      const old = p.price_old;

      return {
        id: Number(p.id),
        name: p.name,
        img: p.img || `${p.id}.jpg`, // fallback kalau kolom img kosong
        price: Number(p.price),
        priceOld: old != null ? Number(old) : null,
        description: p.description || "Produk unggulan PS Astro.",
        available: Number(p.available || 0), // <--- TAMBAHKAN BARIS INI
      };
    }),
  }));

  // === Store Keranjang: TANPA localStorage (selalu fresh) ===
  Alpine.store("cart", {
    items: [],

    get quantity() {
      return this.items.reduce((s, i) => s + i.quantity, 0);
    },

    get total() {
      return this.items.reduce((s, i) => s + i.total, 0);
    },

        add(newItem) {
      if (newItem.available !== undefined && newItem.available <= 0) {
        alert("Maaf, stok produk habis atau sedang dipesan.");
        return;
      }

      const found = this.items.find((i) => i.id === newItem.id);
      if (found) {
        if (newItem.available !== undefined && found.quantity >= newItem.available) {
          alert("Maaf, stok tidak mencukupi untuk menambah pesanan.");
          return;
        }
        found.quantity += 1;
        found.total = found.quantity * found.price;
      } else {
        this.items.push({
          id: newItem.id,
          name: newItem.name,
          img: newItem.img,
          price: Number(newItem.price),
          quantity: 1,
          total: Number(newItem.price),
          available: newItem.available,
        });
      }
    },


    remove(id) {
      const idx = this.items.findIndex((i) => i.id === id);
      if (idx === -1) return;
      const item = this.items[idx];
      if (item.quantity > 1) {
        item.quantity -= 1;
        item.total = item.quantity * item.price;
      } else {
        this.items.splice(idx, 1);
      }
    },

    clear() {
      this.items = [];
    },

    // TIDAK ada localStorage lagi supaya tidak nyangkut harga lama
    persist() {},
    load() {
      this.items = [];
    },

    init() {
      this.load();
    },
  });

  // === Store UI umum (untuk modal login, dll) ===
  Alpine.store("ui", {
    showLogin: false,
  });

  // === Store AUTH (login admin di halaman yang sama) ===
  Alpine.store("auth", {
    isLoggedIn: false,
    username: "",
    error: "",

    login(form) {
      const user = form.username.value.trim();
      const pass = form.password.value.trim();

      // >>> GANTI di sini username & password admin sesuai keinginan
      if (user === "admin" && pass === "12345") {
        this.isLoggedIn = true;
        this.username = user;
        this.error = "";
      } else {
        this.error = "Username atau password salah.";
      }

      form.password.value = "";
    },

    logout() {
      this.isLoggedIn = false;
      this.username = "";
      this.error = "";
    },
  });
});
