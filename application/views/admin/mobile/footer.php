<!-- Bottom Navigation -->
<div class="mobile-bottom-nav">
  <div class="nav-container">
    <a href="<?= base_url('mobile') ?>" class="nav-item <?= (current_url() == base_url('mobile')) ? 'active' : '' ?>">
      <i class="bi bi-grid-1x2-fill"></i>
      <span>Home</span>
    </a>
    <a href="<?= base_url('mobile/absen') ?>"
      class="nav-item <?= strpos(current_url(), 'absen') !== false ? 'active' : '' ?>">
      <i class="bi bi-qr-code-scan"></i>
      <span>Absen</span>
    </a>
    <a href="<?= base_url('mobile/sakit') ?>"
      class="nav-item <?= (strpos(current_url(), 'sakit') !== false || strpos(current_url(), 'cuti') !== false) ? 'active' : '' ?>">
      <i class="bi bi-calendar-event"></i>
      <span>Izin</span>
    </a>
    <a href="javascript:void(0)"
      onclick="openProtectedLink('<?= base_url('mobile/slip_gaji') ?>', 'Slip Gaji', 'Masukkan password untuk membuka slip gaji.')"
      class="nav-item <?= strpos(current_url(), 'slip_gaji') !== false ? 'active' : '' ?>">
      <i class="bi bi-wallet2"></i>
      <span>Gaji</span>
    </a>
    <a href="javascript:void(0)"
      onclick="openProtectedLink('<?= base_url('mobile/profile') ?>', 'Akses Profil', 'Verifikasi identitas untuk melihat data pribadi.')"
      class="nav-item <?= strpos(current_url(), 'profile') !== false ? 'active' : '' ?>">
      <i class="bi bi-person-circle"></i>
      <span>Profil</span>
    </a>
  </div>
</div>
</div> <!-- End App Shell -->

<style>
  .mobile-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(25px) saturate(180%);
    -webkit-backdrop-filter: blur(25px) saturate(180%);
    border-top: 1px solid rgba(0, 0, 0, 0.04);
    z-index: 1050;
    padding: 8px 0;
    box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.04);
  }

  @media (min-width: 768px) {
    .mobile-bottom-nav {
      max-width: 480px;
      left: 50%;
      transform: translateX(-50%);
      border-radius: 40px;
      margin-bottom: 20px;
      border: 1px solid rgba(0, 0, 0, 0.08);
    }
  }

  .nav-container {
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 0 12px;
  }

  .nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: #94a3b8;
    font-size: 0.62rem;
    font-weight: 700;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    gap: 4px;
    width: 20%;
    position: relative;
    padding: 6px 0;
  }

  .nav-item i {
    font-size: 1.25rem;
    transition: all 0.3s ease;
    z-index: 1;
  }

  .nav-item span {
    z-index: 1;
    letter-spacing: -0.2px;
  }

  .nav-item.active {
    color: #3b82f6;
    /* More vibrant blue for active state */
  }

  .nav-item.active i {
    transform: translateY(-2px);
  }

  /* Active Indicator Pill */
  .nav-item.active::before {
    content: '';
    position: absolute;
    top: 0;
    width: 40px;
    height: 4px;
    background: #3b82f6;
    border-radius: 0 0 10px 10px;
    box-shadow: 0 2px 10px rgba(59, 130, 246, 0.4);
  }

  .nav-item:active i {
    transform: scale(0.85);
  }

  @supports (padding-bottom: env(safe-area-inset-bottom)) {
    .mobile-bottom-nav {
      padding-bottom: calc(8px + env(safe-area-inset-bottom));
    }
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function confirmLogout() {
    Swal.fire({
      title: 'Keluar Aplikasi?',
      text: 'Sesi Anda akan segera diakhiri.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Keluar',
      cancelButtonText: 'Batal',
      reverseButtons: true,
      borderRadius: '15px'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = '<?= base_url('auth/logout') ?>';
      }
    });
  }

  function openProtectedLink(url, title = 'Akses Terbatas', text = 'Masukkan password login Anda untuk melanjutkan.') {
    Swal.fire({
      title: `🔒 ${title}`,
      text: text,
      input: 'password',
      inputAttributes: {
        autocapitalize: 'off',
        placeholder: 'Password',
        autocomplete: 'current-password'
      },
      showCancelButton: true,
      confirmButtonText: 'Buka',
      confirmButtonColor: '#1e293b',
      cancelButtonText: 'Batal',
      showLoaderOnConfirm: true,
      preConfirm: (password) => {
        if (!password) {
          Swal.showValidationMessage('Password wajib diisi');
          return false;
        }

        const formData = new FormData();
        formData.append('password', password);

        const getCookie = (name) => {
          const value = `; ${document.cookie}`;
          const parts = value.split(`; ${name}=`);
          if (parts.length === 2) return parts.pop().split(';').shift();
        }
        const csrfName = '<?= $this->security->get_csrf_token_name() ?>';
        const csrfHash = getCookie(csrfName);
        if (csrfHash) {
          formData.append(csrfName, csrfHash);
        }

        return fetch('<?= base_url('mobile/verify_password_ajax') ?>', {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
          .then(response => {
            if (!response.ok) {
              throw new Error(response.statusText)
            }
            return response.json()
          })
          .then(data => {
            if (!data.success) {
              throw new Error(data.message || 'Password Salah')
            }
            return data
          })
          .catch(error => {
            Swal.showValidationMessage(`${error}`)
          })
      },
      allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = url;
      }
    })
  }
</script>
</body>

</html>