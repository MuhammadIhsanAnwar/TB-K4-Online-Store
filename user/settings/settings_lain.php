<h3>Pengaturan Lainnya</h3>
<hr>

<form action="update_others.php" method="POST">

    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox"
            name="darkmode"
            <?php echo ($user['darkmode'] ?? 0) ? "checked" : ""; ?>>
        <label class="form-check-label">Aktifkan Mode Gelap</label>
    </div>

    <button class="btn btn-secondary">Simpan</button>
</form>

<hr>

<form action="hapus_akun.php" method="POST"
    onsubmit="return confirm('Yakin hapus akun permanen?')">

    <button class="btn btn-outline-danger">
        Hapus Akun Permanen
    </button>
</form>