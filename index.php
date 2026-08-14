<?php
// ==========================================
// 1. PENGATURAN KONEKSI DATABASE
// ==========================================
$db_host = "sql304.infinityfree.com";
$db_user = "if0_41262517";     
$db_pass = "ma7cUlvhfjtZJN";         
$db_name = "if0_41262517_4bsenn";

// ==========================================
// 2. PROSES SIMPAN DATA (JIKA ADA REQUEST POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Koneksi database gagal: " . $conn->connect_error]);
        exit;
    }

    $nama  = isset($_POST['nama']) ? trim($_POST['nama']) : '';
    $gugus = isset($_POST['gugus']) ? trim($_POST['gugus']) : '';
    $hari  = isset($_POST['hari']) ? trim($_POST['hari']) : '';

    if (empty($nama) || empty($gugus) || empty($hari)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Semua kolom wajib diisi."]);
        exit;
    }

    // =========================================================
    // AUTO-RESET & MERAPIKAN URUTAN ID (1, 2, 3, ...)
    // =========================================================
    // 1. Merapikan id data yang ada agar tidak ada angka lompat
    $conn->query("SET @num := 0");
    $conn->query("UPDATE presensi_pkkmb SET id = (@num := @num + 1) ORDER BY waktu_absen ASC");
    
    // 2. Mengatur agar data baru selanjutnya meneruskan urutan nomor terakhir (atau mulai dari 1 jika tabel kosong)
    $conn->query("ALTER TABLE presensi_pkkmb AUTO_INCREMENT = 1");

    // =========================================================
    // SIMPAN DATA BARU
    // =========================================================
    $stmt = $conn->prepare("INSERT INTO presensi_pkkmb (nama_lengkap, gugus_kelompok, hari_kehadiran) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama, $gugus, $hari);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Presensi berhasil disimpan."]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan data: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit; // Menghentikan eksekusi agar tidak mencetak HTML saat request AJAX
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kehadiran Peserta PKKMB 2026</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
  </style>
</head>
<body class="bg-cover bg-center bg-no-repeat bg-fixed min-h-screen flex items-center justify-center p-4 relative" style="background-image: url('bgweb.jpeg');">

  <!-- OVERLAY MASK -->
  <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px]"></div>

  <!-- Mobile Container -->
  <div class="w-full max-w-md bg-white/95 backdrop-blur-md border border-white/20 rounded-3xl p-6 shadow-2xl relative z-10 overflow-hidden">
    
    <!-- Header Form Utama -->
    <div class="text-center mb-6">
      <div class="flex justify-center mb-3">
        <img src="logodona.jpg" alt="Logo STIKes Dona Palembang" class="h-20 w-auto object-contain">
      </div>
      <h1 class="text-2xl font-bold tracking-tight text-slate-900">Absen PKKMB STIKes Dona Palembang</h1>
      <p class="text-xs text-slate-500 mt-1">Masukkan data diri untuk mencatat kehadiran</p>
    </div>

    <!-- Form Absen -->
    <form id="absenForm" onsubmit="handleSubmit(event)" class="space-y-4">
      
      <!-- Input Nama -->
      <div>
        <label for="nama" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" required placeholder="Masukkan nama..." class="w-full px-4 py-3 bg-slate-50 border border-slate-300 focus:border-slate-800 focus:bg-white focus:ring-1 focus:ring-slate-800 rounded-xl text-sm text-slate-900 placeholder-slate-400 outline-none transition-all">
      </div>

      <!-- Input Gugus / Kelompok -->
      <div>
        <label for="gugus" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Gugus / Kelompok</label>
        <input type="text" id="gugus" name="gugus" required placeholder="Gugus / Kelompok" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 focus:border-slate-800 focus:bg-white focus:ring-1 focus:ring-slate-800 rounded-xl text-sm text-slate-900 placeholder-slate-400 outline-none transition-all">
      </div>

      <!-- Input Pilihan Hari -->
      <div>
        <label for="hari" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Presensi Kehadiran</label>
        <select id="hari" name="hari" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 focus:border-slate-800 focus:bg-white focus:ring-1 focus:ring-slate-800 rounded-xl text-sm text-slate-900 outline-none transition-all">
          <option value="" disabled selected>-- Pilih Hari Kehadiran --</option>
          <option value="Hari Ke-1">Hari Ke-1</option>
          <option value="Hari Ke-2">Hari Ke-2</option>
          <option value="Hari Ke-3">Hari Ke-3</option>
        </select>
      </div>

      <!-- Submit Button -->
      <button type="submit" id="submitBtn" class="w-full py-3.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm rounded-xl shadow-md active:scale-[0.98] transition-all duration-200 mt-2">
        Kirim Presensi
      </button>

    </form>

    <!-- Tampilan Sukses -->
    <div id="alreadySubmittedMessage" class="hidden text-center py-8 space-y-3">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full border border-emerald-200 mb-1">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
        </svg>
      </div>

      <h2 class="text-xl font-bold text-slate-900">Absen Telah Tercatat, Terimakasih</h2>
      <p class="text-xs text-slate-500 max-w-xs mx-auto">Terimakasih Sudah Bergabung Bersama STIKes Dona Palembang Mahasiswa baru Semangat Ya.</p>
      
      <button onclick="resetView()" class="mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-all border border-slate-300">
        Isi Absen Lagi di Hari Lain dan tetap Semangat ya
      </button>
    </div>

  </div>

  <script>
    async function handleSubmit(e) {
      e.preventDefault();

      const submitBtn = document.getElementById('submitBtn');
      const form = document.getElementById('absenForm');
      const formData = new FormData(form);

      submitBtn.disabled = true;
      submitBtn.innerText = 'Menyimpan...';

      try {
        const response = await fetch(window.location.href, {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (response.ok && result.status === 'success') {
          handleSuccess();
        } else {
          alert(result.message || 'Terjadi kesalahan saat menyimpan absensi.');
        }
      } catch (err) {
        alert('Gagal terhubung ke server. Silakan coba lagi.');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Kirim Presensi';
      }
    }

    function handleSuccess() {
      document.getElementById('absenForm').classList.add('hidden');
      document.getElementById('alreadySubmittedMessage').classList.remove('hidden');
    }

    function resetView() {
      document.getElementById('absenForm').reset();
      document.getElementById('absenForm').classList.remove('hidden');
      document.getElementById('alreadySubmittedMessage').classList.add('hidden');
    }
  </script>
</body>
</html>
