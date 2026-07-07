/**
 * Modern Akun Management JavaScript
 * TGM ERP System - User Account Management
 * Version: 2.0
 * Author: TGM Development Team
 */

class AkunManager {
    constructor() {
        this.baseUrl = window.location.origin;
        this.currentPage = 1;
        this.searchParams = new URLSearchParams(window.location.search);
        this.isLoading = false;
        this.debounceTimer = null;
        
        this.init();
    }

    /**
     * Initialize the application
     */
    init() {
        this.setupEventListeners();
        this.initializeComponents();
        this.loadUserPreferences();
        this.showWelcomeMessage();
    }

    /**
     * Setup all event listeners
     */
    setupEventListeners() {
        // Search and filter events
        this.setupSearchEvents();
        this.setupFilterEvents();
        
        // Action button events
        this.setupActionEvents();
        
        // Modal events
        this.setupModalEvents();
        
        // Pagination events
        this.setupPaginationEvents();
        
        // Keyboard shortcuts
        this.setupKeyboardShortcuts();
        
        // Window events
        this.setupWindowEvents();
        
        // New features
        this.setupBulkOperations();
        this.setupAdvancedSearch();
        
        // Load analytics
        this.loadAnalytics();
    }

    /**
     * Setup search functionality
     */
    setupSearchEvents() {
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        const resetFilterBtn = document.getElementById('resetFilter');

        if (searchInput) {
            // Real-time search with debounce
            searchInput.addEventListener('input', (e) => {
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => {
                    this.performSearch(e.target.value);
                }, 300);
            });

            // Search on Enter
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.submitSearch();
                }
            });

            // Clear search
            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', () => {
                    searchInput.value = '';
                    this.subformSearch();
                });
            }
        }

        // Reset all filters
        if (resetFilterBtn) {
            resetFilterBtn.addEventListener('click', () => {
                this.resetAllFilters();
            });
        }
    }

    /**
     * Setup filter events
     */
    setupFilterEvents() {
        const filterSelects = document.querySelectorAll('.filter-select');
        
        filterSelects.forEach(select => {
            select.addEventListener('change', () => {
                this.applyFilters();
            });
        });
    }

    /**
     * Setup action button events
     */
    setupActionEvents() {
        // Add user button
        const addUserBtn = document.querySelector('[data-bs-target="#modalTambahUser"]');
        if (addUserBtn) {
            addUserBtn.addEventListener('click', () => {
                this.prepareAddUserModal();
            });
        }

        // Export button
        const exportBtn = document.getElementById('exportBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', () => {
                this.exportData();
            });
        }

        // Bulk actions button
        const bulkActionsBtn = document.getElementById('bulkActionsBtn');
        if (bulkActionsBtn) {
            bulkActionsBtn.addEventListener('click', () => {
                const modal = new bootstrap.Modal(document.getElementById('modalBulkActions'));
                modal.show();
            });
        }

        // Edit user buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-edit-akun')) {
                const userId = e.target.closest('.btn-edit-akun').dataset.userid;
                this.editUser(userId);
            }
        });

        // Reset password buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-reset-password')) {
                const userId = e.target.closest('.btn-reset-password').dataset.userid;
                this.resetPassword(userId);
            }
        });

        // Delete user buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-hapus-akun')) {
                const href = e.target.closest('.btn-hapus-akun').dataset.href;
                this.deleteUser(href);
            }
        });
    }

    /**
     * Setup modal events
     */
    setupModalEvents() {
        // Auto-fill form when karyawan is selected
        const karyawanSelect = document.getElementById('tambahKaryawanId');
        if (karyawanSelect) {
            karyawanSelect.addEventListener('change', (e) => {
                this.autoFillUserData(e.target.value);
            });
        }

        // Form submission
        const addUserForm = document.getElementById('formTambahUser');
        if (addUserForm) {
            addUserForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitAddUser();
            });
        }

        const editUserForm = document.getElementById('formEditUser');
        if (editUserForm) {
            editUserForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitEditUser();
            });
        }
    }

    /**
     * Setup pagination events
     */
    setupPaginationEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.page-link')) {
                e.preventDefault();
                const href = e.target.closest('.page-link').href;
                this.navigateToPage(href);
            }
        });
    }

    /**
     * Setup keyboard shortcuts
     */
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + F: Focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.focus();
                }
            }

            // Ctrl/Cmd + N: Add new user
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                const addUserBtn = document.querySelector('[data-bs-target="#modalTambahUser"]');
                if (addUserBtn) {
                    addUserBtn.click();
                }
            }

            // Ctrl/Cmd + E: Export data
            if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
                e.preventDefault();
                this.exportData();
            }

            // Escape: Close modals
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });
    }

    /**
     * Setup window events
     */
    setupWindowEvents() {
        // Handle browser back/forward
        window.addEventListener('popstate', (e) => {
            this.handlePopState(e);
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            this.handleResize();
        });

        // Handle page visibility change
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.refreshData();
            }
        });
    }

    /**
     * Initialize components
     */
    initializeComponents() {
        // Initialize Select2
        this.initSelect2();
        
        // Initialize tooltips
        this.initTooltips();
        
        // Initialize loading states
        this.initLoadingStates();
        
        // Animation initialization removed as requested
    }

    /**
     * Initialize Select2 dropdowns
     */
    initSelect2() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih opsi...',
                allowClear: true
            });
        }
    }

    /**
     * Initialize tooltips
     */
    initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    /**
     * Initialize loading states
     */
    initLoadingStates() {
        // Add loading overlay
        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.innerHTML = '<div class="loading-spinner"></div>';
        loadingOverlay.style.display = 'none';
        document.body.appendChild(loadingOverlay);
    }

    /**
     * Initialize animations
     */
    initAnimations() {
        // Animation effects removed as requested
    }

    /**
     * Setup animations
     */
    setupAnimations() {
        // Animation effects removed as requested
    }

    /**
     * Load user preferences
     */
    loadUserPreferences() {
        const preferences = localStorage.getItem('akun_preferences');
        if (preferences) {
            const prefs = JSON.parse(preferences);
            this.applyUserPreferences(prefs);
        }
    }

    /**
     * Apply user preferences
     */
    applyUserPreferences(preferences) {
        // Apply theme preference
        if (preferences.theme) {
            document.body.setAttribute('data-theme', preferences.theme);
        }

        // Apply table preferences
        if (preferences.tableSize) {
            document.querySelector('.table-body').style.fontSize = preferences.tableSize;
        }
    }

    /**
     * Show welcome message
     */
    showWelcomeMessage() {
        const lastVisit = localStorage.getItem('akun_last_visit');
        const today = new Date().toDateString();
        
        if (lastVisit !== today) {
            this.showNotification('Selamat datang di Manajemen Akun!', 'info');
            localStorage.setItem('akun_last_visit', today);
        }
    }

    /**
     * Perform search with debounce
     */
    performSearch(query) {
        if (query.length >= 2 || query.length === 0) {
            this.searchParams.set('search', query);
            this.searchParams.set('page', '1');
            this.navigateToSearch();
        }
    }

    /**
     * Submit search form
     */
    submitSearch() {
        const form = document.getElementById('searchForm');
        if (form) {
            form.submit();
        }
    }

    /**
     * Apply filters
     */
    applyFilters() {
        const statusFilter = document.getElementById('statusFilter');
        const roleFilter = document.getElementById('roleFilter');
        const perusahaanFilter = document.getElementById('perusahaanFilter');

        if (statusFilter) this.searchParams.set('status', statusFilter.value);
        if (roleFilter) this.searchParams.set('role', roleFilter.value);
        if (perusahaanFilter) this.searchParams.set('perusahaan', perusahaanFilter.value);

        this.searchParams.set('page', '1');
        this.navigateToSearch();
    }

    /**
     * Reset all filters
     */
    resetAllFilters() {
        this.searchParams.delete('search');
        this.searchParams.delete('status');
        this.searchParams.delete('role');
        this.searchParams.delete('perusahaan');
        this.searchParams.set('page', '1');
        
        this.navigateToSearch();
    }

    /**
     * Navigate to search results
     */
    navigateToSearch() {
        const url = `${this.baseUrl}/akun?${this.searchParams.toString()}`;
        window.location.href = url;
    }

    /**
     * Navigate to specific page
     */
    navigateToPage(href) {
        window.location.href = href;
    }

    /**
     * Handle pop state (browser back/forward)
     */
    handlePopState(event) {
        // Refresh data when navigating back/forward
        this.refreshData();
    }

    /**
     * Handle window resize
     */
    handleResize() {
        // Adjust table layout on resize
        this.adjustTableLayout();
    }

    /**
     * Adjust table layout
     */
    adjustTableLayout() {
        const table = document.querySelector('.modern-table');
        if (table && window.innerWidth < 1024) {
            table.classList.add('mobile-layout');
        } else if (table) {
            table.classList.remove('mobile-layout');
        }
    }

    /**
     * Refresh data
     */
    refreshData() {
        // Reload page to get fresh data
        window.location.reload();
    }

    /**
     * Prepare add user modal
     */
    prepareAddUserModal() {
        // Reset form
        const form = document.getElementById('formTambahUser');
        if (form) {
            form.reset();
        }

        // Clear Select2
        if (typeof $.fn.select2 !== 'undefined') {
            $('#tambahKaryawanId').val('').trigger('change');
        }

        this.showNotification('Form tambah akun siap digunakan', 'info');
    }

    /**
     * Auto-fill user data when karyawan is selected
     */
    autoFillUserData(karyawanId) {
        if (!karyawanId) return;

        // Get karyawan data
        fetch(`${this.baseUrl}/akun/get_karyawan_data/${karyawanId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const karyawan = data.data;
                    
                    // Auto-fill username
                    const usernameInput = document.getElementById('tambahUsername');
                    if (usernameInput && karyawan.nama_lengkap) {
                        const username = karyawan.nama_lengkap.toLowerCase()
                            .replace(/[^a-z0-9]/g, '')
                            .substring(0, 20);
                        usernameInput.value = username;
                    }

                    // Auto-fill email
                    const emailInput = document.getElementById('tambahEmail');
                    if (emailInput && karyawan.email) {
                        emailInput.value = karyawan.email;
                    }

                    this.showNotification('Data karyawan berhasil dimuat', 'success');
                }
            })
            .catch(error => {
                console.error('Error loading karyawan data:', error);
                this.showNotification('Gagal memuat data karyawan', 'error');
            });
    }

    /**
     * Submit add user form
     */
    submitAddUser() {
        const form = document.getElementById('formTambahUser');
        if (!form) return;

        this.showLoading();

        const formData = new FormData(form);
        
        fetch(`${this.baseUrl}/akun/action_add_user`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(() => {
            this.hideLoading();
            this.showNotification('Akun berhasil ditambahkan', 'success');
            
            // Close modal and refresh
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalTambahUser'));
            if (modal) {
                modal.hide();
            }
            
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        })
        .catch(error => {
            this.hideLoading();
            console.error('Error adding user:', error);
            this.showNotification('Gagal menambahkan akun', 'error');
        });
    }

    /**
     * Edit user
     */
    editUser(userId) {
        this.showLoading();

        fetch(`${this.baseUrl}/akun/get_user_detail/${userId}`)
            .then(response => response.json())
            .then(data => {
                this.hideLoading();
                if (data.success) {
                    this.populateEditForm(data.data);
                } else {
                    this.showNotification(data.message || 'Gagal memuat data akun', 'error');
                }
            })
            .catch(error => {
                this.hideLoading();
                console.error('Error loading user data:', error);
                this.showNotification('Terjadi kesalahan sistem', 'error');
            });
    }

    /**
     * Populate edit form
     */
    populateEditForm(userData) {
        // Fill form fields
        document.getElementById('editUserId').value = userData.user_id;
        document.getElementById('editUsername').value = userData.username;
        document.getElementById('editEmail').value = userData.email;
        document.getElementById('editKaryawanId').value = userData.karyawan_id;
        document.getElementById('editPerusahaanId').value = userData.perusahaan_id;
        document.getElementById('editRoleId').value = userData.role_id;

        // Update labels
        document.getElementById('editNamaLengkapLabel').textContent = userData.nama_karyawan || '-';
        document.getElementById('editEmailLabel').textContent = userData.email;

        // Update avatar
        const avatar = document.getElementById('editAvatar');
        if (avatar) {
            avatar.src = userData.foto ? 
                `${this.baseUrl}/assets/images/karyawan/${userData.foto}` : 
                `${this.baseUrl}/assets/images/karyawan/default.jpg`;
        }

        // Trigger Select2 updates
        if (typeof $.fn.select2 !== 'undefined') {
            $('#editKaryawanId').trigger('change');
            $('#editPerusahaanId').trigger('change');
            $('#editRoleId').trigger('change');
        }

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('modalEditUser'));
        modal.show();
    }

    /**
     * Submit edit user form
     */
    submitEditUser() {
        const form = document.getElementById('formEditUser');
        if (!form) return;

        this.showLoading();

        const formData = new FormData(form);
        
        fetch(`${this.baseUrl}/akun/action_edit_user`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(() => {
            this.hideLoading();
            this.showNotification('Akun berhasil diperbarui', 'success');
            
            // Close modal and refresh
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditUser'));
            if (modal) {
                modal.hide();
            }
            
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        })
        .catch(error => {
            this.hideLoading();
            console.error('Error updating user:', error);
            this.showNotification('Gagal memperbarui akun', 'error');
        });
    }

    /**
     * Reset password
     */
    resetPassword(userId) {
        Swal.fire({
            title: 'Reset Password?',
            text: "Password akan direset ke 'default123'",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                this.showLoading();
                
                fetch(`${this.baseUrl}/akun/reset_password/${userId}`)
                    .then(response => response.text())
                    .then(() => {
                        this.hideLoading();
                        this.showNotification('Password berhasil direset', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    })
                    .catch(error => {
                        this.hideLoading();
                        console.error('Error resetting password:', error);
                        this.showNotification('Gagal mereset password', 'error');
                    });
            }
        });
    }

    /**
     * Delete user
     */
    deleteUser(href) {
        Swal.fire({
            title: 'Hapus Akun?',
            text: "Akun yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                this.showLoading();
                
                fetch(href)
                    .then(response => response.text())
                    .then(() => {
                        this.hideLoading();
                        this.showNotification('Akun berhasil dihapus', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    })
                    .catch(error => {
                        this.hideLoading();
                        console.error('Error deleting user:', error);
                        this.showNotification('Gagal menghapus akun', 'error');
                    });
            }
        });
    }

    /**
     * Export data
     */
    exportData() {
        this.showLoading();
        
        const exportUrl = `${this.baseUrl}/akun/export_users?${this.searchParams.toString()}`;
        
        fetch(exportUrl)
            .then(response => response.blob())
            .then(blob => {
                this.hideLoading();
                
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `Data_Akun_${new Date().toISOString().split('T')[0]}.xlsx`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                this.showNotification('Data berhasil diekspor', 'success');
            })
            .catch(error => {
                this.hideLoading();
                console.error('Error exporting data:', error);
                this.showNotification('Gagal mengekspor data', 'error');
            });
    }

    /**
     * Close all modals
     */
    closeAllModals() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        });
    }

    /**
     * Show loading overlay
     */
    showLoading() {
        const overlay = document.querySelector('.loading-overlay');
        if (overlay) {
            overlay.style.display = 'flex';
        }
        this.isLoading = true;
    }

    /**
     * Hide loading overlay
     */
    hideLoading() {
        const overlay = document.querySelector('.loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
        this.isLoading = false;
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="bi bi-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }

    /**
     * Get notification icon
     */
    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle-fill',
            error: 'x-circle-fill',
            warning: 'exclamation-triangle-fill',
            info: 'info-circle-fill'
        };
        return icons[type] || 'info-circle-fill';
    }

    /**
     * Get user statistics
     */
    getStatistics() {
        fetch(`${this.baseUrl}/akun/get_user_statistics`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateStatistics(data.data);
                }
            })
            .catch(error => {
                console.error('Error loading statistics:', error);
            });
    }

    /**
     * Update statistics display
     */
    updateStatistics(stats) {
        // Update stat cards
        const totalElement = document.querySelector('.stat-card.total .stat-number');
        const activeElement = document.querySelector('.stat-card.active .stat-number');
        const inactiveElement = document.querySelector('.stat-card.inactive .stat-number');
        const blockedElement = document.querySelector('.stat-card.blocked .stat-number');

        if (totalElement) totalElement.textContent = stats.total_users;
        if (activeElement) activeElement.textContent = stats.active_users;
        if (inactiveElement) inactiveElement.textContent = stats.inactive_users;
        if (blockedElement) blockedElement.textContent = stats.blocked_users;
    }

    /**
     * Setup bulk operations
     */
    setupBulkOperations() {
        const selectAll = document.getElementById('selectAll');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const bulkActionsBtn = document.getElementById('bulkActionsBtn');
        const bulkAction = document.getElementById('bulkAction');
        const performBulkAction = document.getElementById('performBulkAction');
        const selectedCount = document.getElementById('selectedCount');
        const bulkWarning = document.getElementById('bulkWarning');
        const bulkWarningText = document.getElementById('bulkWarningText');

        // Select all functionality
        if (selectAll) {
            selectAll.addEventListener('change', (e) => {
                userCheckboxes.forEach(checkbox => {
                    checkbox.checked = e.target.checked;
                });
                this.updateBulkActionsState();
            });
        }

        // Individual checkbox functionality
        userCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.updateBulkActionsState();
            });
        });

        // Bulk action selection
        if (bulkAction) {
            bulkAction.addEventListener('change', (e) => {
                const action = e.target.value;
                this.showBulkWarning(action);
                performBulkAction.disabled = !action;
            });
        }

        // Perform bulk action
        if (performBulkAction) {
            performBulkAction.addEventListener('click', () => {
                this.performBulkAction();
            });
        }

        // Update bulk actions state
        this.updateBulkActionsState = () => {
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            const count = checkedBoxes.length;
            
            selectedCount.textContent = count;
            bulkActionsBtn.disabled = count === 0;
            
            // Update select all state
            if (selectAll) {
                selectAll.checked = count === userCheckboxes.length && count > 0;
                selectAll.indeterminate = count > 0 && count < userCheckboxes.length;
            }
        };

        // Show bulk warning
        this.showBulkWarning = (action) => {
            const warnings = {
                'delete': 'Aksi ini akan menghapus semua akun yang dipilih secara permanen!',
                'block': 'Aksi ini akan memblokir semua akun yang dipilih!',
                'reset_password': 'Password semua akun yang dipilih akan direset ke default!'
            };
            
            if (warnings[action]) {
                bulkWarningText.textContent = warnings[action];
                bulkWarning.style.display = 'block';
            } else {
                bulkWarning.style.display = 'none';
            }
        };

        // Perform bulk action
        this.performBulkAction = () => {
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            const userIds = Array.from(checkedBoxes).map(cb => cb.value);
            const action = bulkAction.value;

            if (!action || userIds.length === 0) return;

            Swal.fire({
                title: 'Konfirmasi Aksi Massal',
                text: `Apakah Anda yakin ingin melakukan aksi "${action}" pada ${userIds.length} akun?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Eksekusi!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.executeBulkAction(action, userIds);
                }
            });
        };

        // Execute bulk action
        this.executeBulkAction = (action, userIds) => {
            this.showLoading();

            const formData = new FormData();
            formData.append('action', action);
            userIds.forEach(id => formData.append('user_ids[]', id));

            fetch(`${this.baseUrl}/akun/bulk_operations`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(() => {
                this.hideLoading();
                this.showNotification(`Aksi massal berhasil dieksekusi`, 'success');
                
                // Close modal and refresh
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalBulkActions'));
                if (modal) {
                    modal.hide();
                }
                
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            })
            .catch(error => {
                this.hideLoading();
                console.error('Error performing bulk action:', error);
                this.showNotification('Gagal melakukan aksi massal', 'error');
            });
        };
    }

    /**
     * Setup advanced search
     */
    setupAdvancedSearch() {
        const performAdvancedSearch = document.getElementById('performAdvancedSearch');
        const advancedSearchForm = document.getElementById('advancedSearchForm');

        if (performAdvancedSearch) {
            performAdvancedSearch.addEventListener('click', () => {
                this.performAdvancedSearch();
            });
        }

        // Perform advanced search
        this.performAdvancedSearch = () => {
            const formData = new FormData(advancedSearchForm);
            const searchData = {};
            
            for (let [key, value] of formData.entries()) {
                if (value) {
                    searchData[key] = value;
                }
            }

            this.showLoading();

            fetch(`${this.baseUrl}/akun/advanced_search`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                this.hideLoading();
                if (data.success) {
                    this.updateTableWithResults(data.data);
                    this.showNotification(`Ditemukan ${data.total} hasil`, 'success');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalAdvancedSearch'));
                    if (modal) {
                        modal.hide();
                    }
                } else {
                    this.showNotification('Gagal melakukan pencarian lanjutan', 'error');
                }
            })
            .catch(error => {
                this.hideLoading();
                console.error('Error performing advanced search:', error);
                this.showNotification('Terjadi kesalahan saat pencarian', 'error');
            });
        };

        // Update table with search results
        this.updateTableWithResults = (results) => {
            const tableBody = document.querySelector('.table-body');
            if (!tableBody) return;

            tableBody.innerHTML = '';

            results.forEach((user, index) => {
                const row = this.createTableRow(user, index + 1);
                tableBody.appendChild(row);
            });
        };

        // Create table row
        this.createTableRow = (user, index) => {
            const row = document.createElement('div');
            row.className = 'table-row';
            
            const statusBadge = this.getStatusBadge(user.active);
            
            row.innerHTML = `
                <div class="table-cell">
                    <input type="checkbox" class="form-check-input user-checkbox" value="${user.user_id}">
                </div>
                <div class="table-cell">${index}</div>
                <div class="table-cell">
                    <img src="${this.baseUrl}/assets/images/karyawan/${user.foto || 'default.jpg'}" 
                         class="user-avatar" alt="Avatar">
                    <div class="user-info">
                        <div class="user-name">${user.nama_karyawan || '-'}</div>
                        <div class="user-email">${user.email}</div>
                    </div>
                </div>
                <div class="table-cell">
                    <span class="store-badge">${user.nama_perusahaan || '-'}</span>
                </div>
                <div class="table-cell">
                    <span class="text-primary fw-bold">${user.username}</span>
                </div>
                <div class="table-cell">
                    <span class="text-secondary">${user.email}</span>
                </div>
                <div class="table-cell">
                    <span class="role-badge">${user.role_name || '-'}</span>
                </div>
                <div class="table-cell">
                    ${statusBadge}
                </div>
                <div class="table-cell action-buttons-cell">
                    <button class="action-btn btn-reset btn-reset-password" data-userid="${user.user_id}" title="Reset Password">
                        <i class="bi bi-key-fill"></i>
                    </button>
                    <button class="action-btn btn-delete btn-hapus-akun" data-href="${this.baseUrl}/akun/delete_user/${user.user_id}" title="Hapus Akun">
                        <i class="bi bi-trash3"></i>
                    </button>
                    <button class="action-btn btn-edit btn-edit-akun" data-userid="${user.user_id}" title="Edit Akun">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                </div>
            `;
            
            return row;
        };

        // Get status badge
        this.getStatusBadge = (active) => {
            switch (active) {
                case '1':
                    return '<span class="status-badge status-active"><i class="bi bi-check-circle me-1"></i>Aktif</span>';
                case '0':
                    return '<span class="status-badge status-inactive"><i class="bi bi-exclamation-circle me-1"></i>Tidak Aktif</span>';
                default:
                    return '<span class="status-badge status-blocked"><i class="bi bi-x-circle me-1"></i>Diblokir</span>';
            }
        };
    }

    /**
     * Load analytics data
     */
    loadAnalytics() {
        fetch(`${this.baseUrl}/akun/get_analytics`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateAnalytics(data.data);
                }
            })
            .catch(error => {
                console.error('Error loading analytics:', error);
            });
    }

    /**
     * Update analytics display
     */
    updateAnalytics(analytics) {
        // Update charts and analytics displays
        console.log('Analytics data:', analytics);
        // Implementation for charts and analytics visualization
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.akunManager = new AkunManager();
});

// Export for global access
window.AkunManager = AkunManager; 