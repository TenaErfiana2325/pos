<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../database/tugas.php';

// Mengambil parameter 'urut' dari URL
$urut = isset($_GET['urut']) ? $_GET['urut'] : '';

if (!empty($urut)) {
    // Menggunakan Prepared Statement untuk keamanan dari SQL Injection
    $stmt = mysqli_prepare($con, "DELETE FROM tbl_detail_notabeli WHERE urut = ?");
    mysqli_stmt_bind_param($stmt, "i", $urut); // 'i' untuk tipe data integer
    
    if (mysqli_stmt_execute($stmt)) {
        echo '<script>
            alert("Satu data barang berhasil dihapus");
            window.location.href="../detail_notabeli_superadmin";
        </script>';
    } else {
        echo '<script>
            alert("Gagal menghapus data: ' . mysqli_error($con) . '");
            window.location.href="../detail_notabeli_superadmin";
        </script>';
    }
    mysqli_stmt_close($stmt);
} else {
    echo '<script>
        alert("ID/Urut Barang Tidak Ditemukan");
        window.location.href="../detail_notabeli_superadmin";
    </script>';
}
?>