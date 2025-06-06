<?php
session_start();
include '../../../koneksi.php'; // Sesuaikan path jika perlu

// Kode pengecekan yang BENAR untuk halaman kasir
// Memastikan user sudah login DAN jabatannya adalah 'kasir'
if (!isset($_SESSION['user']) || $_SESSION['user']['jabatan'] !== 'kasir') {
    // Jika tidak, tendang kembali ke halaman login
    header('Location: ../../../login.php');
    exit;
}


// ===================================================================================
// PHP: PROSES PESANAN SAAT FORM DISUBMIT
// ===================================================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cart_data'])) {

    // Ambil data dari form dan JSON keranjang
    $id_karyawan_session = $_SESSION['user']['id']; // Ambil ID dari sesi
    $nama_pemesan = $_POST['nama_pemesan'] ?: 'Walk-in'; // Jika kosong, isi 'Walk-in'
    $jenis_pesanan = $_POST['jenis_pesanan'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $total_amount = filter_var($_POST['total_amount'], FILTER_VALIDATE_FLOAT);

    // Anda bisa menambahkan input 'catatan' di form jika perlu
    $catatan = $_POST['catatan'] ?? '';

    // Decode JSON data dari keranjang menjadi array PHP
    $cart_data = json_decode($_POST['cart_data'], true);

    if (json_last_error() !== JSON_ERROR_NONE || empty($cart_data)) {
        $pesan_error = "Terjadi kesalahan data keranjang. Silakan coba lagi.";
    } else {
        // Mulai TRANSACTION untuk memastikan integritas data
        mysqli_begin_transaction($koneksi);

        try {
            // 1. Siapkan statement untuk insert ke tabel pesanan_kasir
            $id_pesanan_baru = "PSK-" . date("YmdHis"); // PSK = Pesanan Kasir
            $tgl_pesanan = date("Y-m-d H:i:s");

            // PERBAIKAN: Menggunakan nama tabel dan kolom yang benar sesuai skema Anda
            $stmt_transaksi = mysqli_prepare($koneksi, "INSERT INTO pesanan_kasir (id_pesanan, nama_pemesan, tgl_pesanan, total_hargaall, catatan, jenis_pesanan, metode_pembayaran, id_karyawan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_transaksi, "sssdsssi", $id_pesanan_baru, $nama_pemesan, $tgl_pesanan, $total_amount, $catatan, $jenis_pesanan, $metode_pembayaran, $id_karyawan_session);
            mysqli_stmt_execute($stmt_transaksi);

            // 2. Loop setiap item di keranjang (bagian ini sebagian besar sudah benar)
            $stmt_detail = mysqli_prepare($koneksi, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_produk, sub_total) VALUES (?, ?, ?, ?, ?)");
            $stmt_update_stok = mysqli_prepare($koneksi, "UPDATE stok_harian SET stok = stok - ? WHERE id_produk = ?");

            foreach ($cart_data as $productId => $item) {
                $jumlah_dipesan = $item['quantity'];
                $harga_saat_transaksi = $item['price'];
                $sub_total = $jumlah_dipesan * $harga_saat_transaksi;

                // Validasi stok langsung di database untuk data terbaru
                $check_stok_query = mysqli_query($koneksi, "SELECT stok FROM stok_harian WHERE id_produk = '$productId' AND tanggal = CURDATE()");
                $stok_data = mysqli_fetch_assoc($check_stok_query);

                if (!$stok_data || $stok_data['stok'] < $jumlah_dipesan) {
                    $sisa_stok = $stok_data['stok'] ?? 0;
                    throw new Exception("Stok untuk produk '{$item['name']}' tidak mencukupi (sisa: {$sisa_stok}).");
                }

                // Insert ke detail_pesanan
                // PERBAIKAN: Menggunakan $id_pesanan_baru
                mysqli_stmt_bind_param($stmt_detail, "ssidd", $id_pesanan_baru, $productId, $jumlah_dipesan, $harga_saat_transaksi, $sub_total);
                mysqli_stmt_execute($stmt_detail);

                // Update stok di stok_harian
                mysqli_stmt_bind_param($stmt_update_stok, "is", $jumlah_dipesan, $productId);
                mysqli_stmt_execute($stmt_update_stok);
            }

            // 3. PERBAIKAN: Hapus query UPDATE status karena kolomnya tidak ada di tabel pesanan_kasir
            // mysqli_query($koneksi, "UPDATE transaksi SET status = '$status_final' WHERE id_transaksi = '$id_transaksi'");

            mysqli_commit($koneksi);

            // Set pesan sukses dan siapkan JavaScript untuk membersihkan keranjang
            // ... setelah mysqli_commit($koneksi);

            // Set pesan sukses dengan tambahan link/tombol Cetak Struk
            $pesan_sukses = "Transaksi berhasil disimpan dengan ID: $id_pesanan_baru. 
                 <a href='../detail_pesanan.php?id=$id_pesanan_baru' target='_blank' class='btn btn-sm btn-info ms-2 fw-bold'>
                     <i class='fas fa-print'></i> Cetak Struk
                 </a>";

            $clear_cart_js = "<script>localStorage.removeItem('kasir_cart');</script>";
        } catch (Exception $e) {
            // Jika ada error, batalkan semua query (rollback)
            mysqli_rollback($koneksi);
            $pesan_error = "Transaksi Gagal: " . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Input Pesanan - Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../../../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .product-card {
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .product-card .card-img-top {
            object-fit: cover;
            height: 120px;
        }

        .cart-item-row .form-control {
            height: calc(1.5em + .5rem + 2px);
            padding: .25rem .5rem;
            font-size: .875rem;
        }

        .cart-item-row .btn {
            padding: .25rem .5rem;
            font-size: .875rem;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <?php include "../inc/navbar.php"; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <?php include "../inc/sidebar.php"; ?>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <div class="row">
                        <div class="col-xl-12 col-md-12">
                            <h1 class="mt-4">Daftar Menu</h1>
                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Input Pesanan</li>
                            </ol>
                            <?php if (isset($pesan_sukses)): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $pesan_sukses;
                                    // Ganti baris $pesan_sukses Anda dengan ini

                                    $pesan_sukses = "Transaksi berhasil disimpan dengan ID: $id_pesanan_baru.
                                    <a href='../detail_pesanan.php?id=$id_pesanan_baru' target='_blank' class='btn btn-sm btn-info ms-2 fw-bold'>
                                        <i class='fas fa-print'></i> Cetak Struk
                                    </a>";

                                    $clear_cart_js = "<script>
                                        localStorage.removeItem('kasir_cart');
                                    </script>"; ?>
                                    <br>    
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($pesan_error)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $pesan_error; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <?php
                                    $query_produk = "SELECT p.id_produk, p.nama_produk, p.harga_produk, p.poto_produk, p.kategori, sh.stok 
                                                 FROM produk p
                                                 LEFT JOIN stok_harian sh ON p.id_produk = sh.id_produk
                                                 ORDER BY p.kategori, p.nama_produk";
                                    $result_produk = $koneksi->query($query_produk);

                                    if ($result_produk->num_rows > 0) {
                                        $current_kategori = '';
                                        while ($row = $result_produk->fetch_assoc()) {
                                            if ($row['kategori'] != $current_kategori) {
                                                echo '<div class="col-12 mt-4"><h4>' . htmlspecialchars(strtoupper($row['kategori'])) . '</h4></div>';
                                                $current_kategori = $row['kategori'];
                                            }
                                    ?>
                                            <div class="col-lg-3 col-md-4 mb-4">
                                                <div class="card product-card h-100"
                                                    data-product-id="<?php echo $row['id_produk']; ?>"
                                                    data-product-name="<?php echo htmlspecialchars($row['nama_produk']); ?>"
                                                    data-product-price="<?php echo $row['harga_produk']; ?>"
                                                    data-product-stock="<?php echo $row['stok'] ?? 0; ?>">
                                                    <img src="../../../assets/img/produk/<?php echo $row['poto_produk']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                                                    <div class="card-body text-center d-flex flex-column">
                                                        <h6 class="card-title flex-grow-1"><?php echo htmlspecialchars($row['nama_produk']); ?></h6>
                                                        <p class="card-text mb-2"><strong>Rp <?php echo number_format($row['harga_produk'], 0, ',', '.'); ?></strong></p>
                                                        <p class="card-text small text-muted">Stok: <?php echo $row['stok'] ?? 0; ?></p>
                                                        <?php if (($row['stok'] ?? 0) > 0): ?>
                                                            <button class="btn btn-primary btn-sm add-to-cart-btn mt-auto">Pilih</button>
                                                        <?php else: ?>
                                                            <button class="btn btn-secondary btn-sm mt-auto" disabled>Stok Habis</button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php
                                        }
                                    } else {
                                        echo '<div class="col-12"><p>Belum ada produk terdaftar.</p></div>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card sticky-top" style="top: 20px;">
                                    <div class="card-header">
                                        <h4 class="mb-0">Detail Pesanan</h4>
                                    </div>
                                    <div class="card-body">
                                        <form id="orderForm" action="input_pesanan.php" method="POST">
                                            <div id="cart-items" style="max-height: 300px; overflow-y: auto;">
                                                <p class="text-muted text-center" id="empty-cart-message">Keranjang kosong.</p>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h4>Total:</h4>
                                                <h4 id="total-display">Rp 0</h4>
                                            </div>

                                            <input type="hidden" name="cart_data" id="cart-data-input">
                                            <input type="hidden" name="total_amount" id="total-amount-input">

                                            <div class="form-group mb-3">
                                                <label for="nama_pemesan" class="form-label">Nama Pemesan (Opsional):</label>
                                                <input type="text" class="form-control" id="nama_pemesan" name="nama_pemesan" placeholder="Nama Pelanggan">
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="jenis_pesanan" class="form-label">Jenis Pesanan:</label>
                                                <select class="form-select" id="jenis_pesanan" name="jenis_pesanan" required>
                                                    <option value="take_away">Dine In</option>
                                                    <option value="dine_in">Take Away</option>
                                                </select>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="metode_pembayaran" class="form-label">Metode Pembayaran:</label>
                                                <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                                                    <option value="tunai">Tunai</option>
                                                    <option value="qris">QRIS</option>
                                                    <option value="debit">Debit</option>
                                                </select>
                                            </div>

                                            <div class="d-grid gap-2">
                                                <button type="submit" class="btn btn-success" id="charge-btn" disabled>Bayar</button>
                                                <button type="button" class="btn btn-danger" id="clear-cart-btn">Kosongkan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; KueBalok 2025</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cart = {};

            const cartItemsDiv = document.getElementById('cart-items');
            const subtotalDisplay = document.getElementById('subtotal-display'); // mungkin tidak ada lagi
            const totalDisplay = document.getElementById('total-display');
            const chargeBtn = document.getElementById('charge-btn');
            const cartDataInput = document.getElementById('cart-data-input');
            const totalAmountInput = document.getElementById('total-amount-input');
            const emptyCartMessage = document.getElementById('empty-cart-message');

            function numberFormat(amount) {
                return new Intl.NumberFormat('id-ID').format(amount);
            }

            function saveCartToLocalStorage() {
                localStorage.setItem('kasir_cart', JSON.stringify(cart));
            }

            function loadCartFromLocalStorage() {
                const storedCart = localStorage.getItem('kasir_cart');
                if (storedCart) {
                    cart = JSON.parse(storedCart);
                }
            }

            function updateCartDisplay() {
                cartItemsDiv.innerHTML = ''; // Kosongkan
                let total = 0;
                const hasItems = Object.keys(cart).length > 0;

                if (hasItems) {
                    emptyCartMessage.style.display = 'none'; // Sembunyikan pesan keranjang kosong
                    for (const productId in cart) {
                        const item = cart[productId];
                        const itemSubtotal = item.price * item.quantity;
                        total += itemSubtotal;

                        const itemHtml = `
                        <div class="d-flex justify-content-between align-items-center mb-2 cart-item-row" data-product-id="${productId}">
                            <div>
                                <strong class="d-block">${item.name}</strong>
                                <div class="input-group input-group-sm mt-1" style="width: 120px;">
                                    <button class="btn btn-outline-secondary minus-btn" type="button">-</button>
                                    <input type="text" class="form-control text-center quantity-input" value="${item.quantity}" readonly>
                                    <button class="btn btn-outline-secondary plus-btn" type="button">+</button>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="d-block">Rp ${numberFormat(itemSubtotal)}</span>
                                <button class="btn btn-sm btn-link text-danger remove-item-btn p-0">Hapus</button>
                            </div>
                        </div>`;
                        cartItemsDiv.innerHTML += itemHtml;
                    }
                } else {
                    cartItemsDiv.appendChild(emptyCartMessage); // Tampilkan kembali
                    emptyCartMessage.style.display = 'block';
                }

                totalDisplay.textContent = 'Rp ' + numberFormat(total);
                chargeBtn.disabled = !hasItems; // Aktifkan/nonaktifkan tombol bayar

                // Siapkan data untuk form
                cartDataInput.value = JSON.stringify(cart);
                totalAmountInput.value = total;

                saveCartToLocalStorage();
            }

            function addToCart(card) {
                const productId = card.dataset.productId;
                const stock = parseInt(card.dataset.productStock);

                if (cart[productId]) { // Jika produk sudah ada di keranjang
                    if (cart[productId].quantity < stock) {
                        cart[productId].quantity++;
                    } else {
                        alert('Stok tidak mencukupi!');
                    }
                } else { // Jika produk baru
                    if (stock > 0) {
                        cart[productId] = {
                            name: card.dataset.productName,
                            price: parseInt(card.dataset.productPrice),
                            quantity: 1,
                            stock: stock
                        };
                    } else {
                        alert('Stok produk ini habis!');
                    }
                }
                updateCartDisplay();
            }

            // Event listener untuk klik pada card produk
            document.querySelectorAll('.product-card').forEach(card => {
                card.addEventListener('click', function() {
                    addToCart(this);
                });
            });

            // Event listener untuk tombol plus, minus, hapus (delegasi)
            cartItemsDiv.addEventListener('click', function(e) {
                const row = e.target.closest('.cart-item-row');
                if (!row) return;

                const productId = row.dataset.productId;

                if (e.target.classList.contains('plus-btn')) {
                    if (cart[productId].quantity < cart[productId].stock) {
                        cart[productId].quantity++;
                    } else {
                        alert('Stok tidak mencukupi!');
                    }
                } else if (e.target.classList.contains('minus-btn')) {
                    cart[productId].quantity--;
                    if (cart[productId].quantity <= 0) {
                        delete cart[productId];
                    }
                } else if (e.target.classList.contains('remove-item-btn')) {
                    delete cart[productId];
                }
                updateCartDisplay();
            });

            // Event listener untuk tombol Kosongkan
            document.getElementById('clear-cart-btn').addEventListener('click', function() {
                if (Object.keys(cart).length > 0 && confirm('Anda yakin ingin mengosongkan keranjang?')) {
                    cart = {};
                    updateCartDisplay();
                }
            });

            // Event listener untuk submit form
            document.getElementById('orderForm').addEventListener('submit', function(e) {
                if (Object.keys(cart).length === 0) {
                    e.preventDefault();
                    alert('Keranjang pesanan masih kosong!');
                }
            });

            // Inisialisasi
            loadCartFromLocalStorage();
            updateCartDisplay();
        });
    </script>

    <?php
    if (isset($clear_cart_js)) {
        echo $clear_cart_js;
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../../../js/scripts.js"></script>
</body>

</html>