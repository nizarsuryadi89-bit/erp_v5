<style>
    .danger-hero {
        background: linear-gradient(135deg, #ef4444 0%, #991b1b 100%);
        color: white;
        padding: 4rem 0;
        border-radius: 0 0 40px 40px;
        box-shadow: 0 15px 40px rgba(153, 27, 27, 0.2);
        margin-bottom: 3rem;
    }

    .reset-card {
        background: white;
        border-radius: 30px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        border: 1px solid #fee2e2;
        max-width: 800px;
        margin: -4rem auto 4rem;
        overflow: hidden;
    }

    .confirmation-box {
        background: #fff1f2;
        border: 2px dashed #f43f5e;
        border-radius: 20px;
        padding: 2rem;
        margin: 2rem 0;
    }

    .btn-reset-final {
        background: #f43f5e;
        color: white;
        border: none;
        padding: 1.25rem 3rem;
        border-radius: 15px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s;
        width: 100%;
    }

    .btn-reset-final:hover {
        background: #e11d48;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(225, 29, 72, 0.3);
        color: white;
    }

    .warning-item {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        align-items: flex-start;
    }

    .warning-item i {
        color: #f43f5e;
        margin-top: 4px;
    }

    .code-input {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        font-family: monospace;
        font-size: 1.25rem;
        text-align: center;
        letter-spacing: 5px;
        font-weight: 900;
    }
</style>

<div class="danger-hero">
    <div class="container text-center">
        <i class="fas fa-exclamation-triangle fa-4x mb-4 text-white opacity-75"></i>
        <h1 class="fw-900 mb-2">FACTORY RESET</h1>
        <p class="lead opacity-75">Kosongkan Seluruh Riwayat Transaksi Gudang & Produksi</p>
    </div>
</div>

<div class="container">
    <div class="reset-card">
        <div class="p-5">
            <h4 class="fw-900 text-dark mb-4">Informasi Penting</h4>
            <p class="text-muted">Gunakan fitur ini hanya jika Anda ingin memulai pendataan dari awal (Fresh Start).
                Proses ini bersifat <strong>IRREVERSIBLE</strong> (tidak dapat dibatalkan).</p>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h6 class="fw-800 text-danger text-uppercase small mb-3">Data yang akan DIHAPUS:</h6>
                    <div class="warning-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Seluruh riwayat Pembelian (PO) & Hutang Supplier.</span>
                    </div>
                    <div class="warning-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Seluruh saldo Stok & Log Mutasi di semua gudang.</span>
                    </div>
                    <div class="warning-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Seluruh riwayat Pengambilan & Stok Opname.</span>
                    </div>
                    <div class="warning-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Seluruh riwayat Produksi (WIP) & Detail Proses.</span>
                    </div>
                </div>
                <div class="col-md-6 border-start ps-4">
                    <h6 class="fw-800 text-primary text-uppercase small mb-3">Data yang tetap ADA:</h6>
                    <div class="warning-item">
                        <i class="fas fa-shield-alt text-primary"></i>
                        <span>Daftar Nama Bahan/Jasa & Varian (Master Data).</span>
                    </div>
                    <div class="warning-item">
                        <i class="fas fa-shield-alt text-primary"></i>
                        <span>Daftar Supplier, Konsumen, & Karyawan.</span>
                    </div>
                    <div class="warning-item">
                        <i class="fas fa-shield-alt text-primary"></i>
                        <span>Pengaturan Sistem & Hak Akses.</span>
                    </div>
                </div>
            </div>

            <form action="<?= base_url('warehouse/action_system_reset') ?>" method="POST" id="formReset">
                <div class="confirmation-box text-center">
                    <p class="fw-800 text-danger mb-3">Ketik "RESET-GUDANG" untuk konfirmasi:</p>
                    <input type="text" name="confirm_code" class="form-control code-input mb-4"
                        placeholder="Ketik Di Sini" required autocomplete="off">

                    <button type="submit" class="btn btn-reset-final">
                        <i class="fas fa-trash-alt me-2"></i>EKSEKUSI PENGOSONGAN DATA
                    </button>
                    <p class="small text-muted mt-3 mb-0">Klik tombol di atas berarti Anda setuju untuk menghapus data
                        secara permanen.</p>
                </div>
            </form>

            <div class="text-center">
                <a href="<?= base_url('warehouse') ?>" class="btn btn-link text-muted fw-bold">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    $('#formReset').on('submit', function (e) {
        const input = $('.code-input').val();
        if (input !== 'RESET-GUDANG') {
            e.preventDefault();
            Swal.fire('Error', 'Kode konfirmasi tidak cocok!', 'error');
            return false;
        }

        e.preventDefault();
        Swal.fire({
            title: 'Apakah Anda SANGAT Yakin?',
            text: "Data transaksi tidak akan bisa dikembalikan lagi!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'YA, HAPUS SEKARANG!',
            cancelButtonText: 'Gak Jadi, Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Membersihkan Sistem...',
                    html: 'Mohon jangan tutup jendela ini.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                this.submit();
            }
        });
    });
</script>