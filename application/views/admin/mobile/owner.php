<style>
    .container.py-4 {
        max-width: 480px;
        margin: 0 auto;
        padding: 0 8px;
    }

    .h4.mb-4.text-center {
        font-size: 1.18rem;
        font-weight: 700;
        color: #1a73e8;
        margin-bottom: 18px !important;
        letter-spacing: 0.5px;
    }

    .row.g-3 {
        row-gap: 16px;
        column-gap: 0;
    }

    .col-3 {
        padding-left: 6px;
        padding-right: 6px;
    }

    .btn.w-100 {
        min-height: 70px;
        font-size: 0.97rem;
        border-radius: 10px;
        margin-bottom: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .btn i {
        font-size: 1.5rem;
        margin-bottom: 2px;
    }

    @media (max-width: 400px) {
        .btn.w-100 {
            min-height: 60px;
            font-size: 0.89rem;
        }

        .h4.mb-4.text-center {
            font-size: 1.05rem;
        }
    }
</style>
<div class="container py-4">
    <h1 class="h4 mb-4 text-center">Dashboard Absensi Karyawan</h1>


    <!-- Attendance Report Section -->
    <div class="mt-5">
        <?php $this->load->view('admin/mobile/laporan_absensi'); ?>
    </div>
</div>