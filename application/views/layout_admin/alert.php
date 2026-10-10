<?php if ($this->session->flashdata('success')): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '<?= $this->session->flashdata('success') ?>',
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
</script>
<?php
    $this->session->unset_userdata('success'); 
endif; ?>

<?php if ($this->session->flashdata('error')): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Akses Ditolak',
        text: '<?= $this->session->flashdata('error') ?>',
        timer: 4000,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#d33'
    });
</script>
<?php 
    $this->session->unset_userdata('error');
endif; ?>

<?php if ($this->session->flashdata('warning')): ?>
<script>
    Swal.fire({
        icon: 'warning',
        title: 'Peringatan',
        text: '<?= $this->session->flashdata('warning') ?>',
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
</script>
<?php 
    $this->session->unset_userdata('warning');
endif; ?>
