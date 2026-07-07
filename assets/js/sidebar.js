/**
 * Sidebar JavaScript Functions
 * Handles sidebar interactions and access control
 */

// Show access denied message for restricted menu items
function showAccessDenied() {
    Swal.fire({
        icon: 'warning',
        title: 'Akses Terbatas',
        text: 'Laporan Keuangan hanya dapat diakses oleh Administrator, Bagian Keuangan, dan Owner.',
        confirmButtonText: 'Mengerti',
        confirmButtonColor: '#3085d6'
    });
}

// Toggle sidebar on mobile devices
function toggleSidebar() {
    const sidebar = document.querySelector('.app-sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    if (sidebar.classList.contains('show')) {
        sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
    } else {
        sidebar.classList.add('show');
        if (overlay) overlay.classList.add('show');
    }
}

// Close sidebar when clicking overlay
function closeSidebar() {
    const sidebar = document.querySelector('.app-sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    sidebar.classList.remove('show');
    if (overlay) overlay.classList.remove('show');
}

// Handle sidebar navigation highlighting
function highlightActiveMenu() {
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.nav-link');
    
    menuItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && currentPath.includes(href.replace('<?= base_url() ?>', ''))) {
            item.classList.add('active');
            item.closest('.nav-item').classList.add('menu-open');
        }
    });
}

// Initialize sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
    // Highlight active menu
    highlightActiveMenu();
    
    // Add mobile menu toggle button if not exists
    if (!document.querySelector('.sidebar-toggle')) {
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'btn btn-link sidebar-toggle d-md-none position-fixed';
        toggleBtn.style.cssText = 'top: 10px; left: 10px; z-index: 1040; background: rgba(0,0,0,0.5); color: white; border: none; padding: 8px 12px; border-radius: 4px;';
        toggleBtn.innerHTML = '<i class="bi bi-list"></i>';
        toggleBtn.onclick = toggleSidebar;
        document.body.appendChild(toggleBtn);
    }
    
    // Add overlay for mobile
    if (!document.querySelector('.sidebar-overlay')) {
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        overlay.onclick = closeSidebar;
        document.body.appendChild(overlay);
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    });
});

// Smooth scrolling for sidebar
function smoothScrollTo(element) {
    element.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest'
    });
}

// Add loading state to menu items
function addLoadingState(element) {
    const originalContent = element.innerHTML;
    element.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Loading...';
    element.style.pointerEvents = 'none';
    
    return function() {
        element.innerHTML = originalContent;
        element.style.pointerEvents = 'auto';
    };
}

// Handle menu item clicks with loading state
function handleMenuClick(event) {
    const link = event.currentTarget;
    const href = link.getAttribute('href');
    
    // Skip if it's an access denied link
    if (link.onclick === showAccessDenied) {
        return;
    }
    
    // Add loading state
    const removeLoading = addLoadingState(link);
    
    // Simulate loading time (remove this in production)
    setTimeout(removeLoading, 500);
}

// Add click handlers to menu items
document.addEventListener('DOMContentLoaded', function() {
    const menuLinks = document.querySelectorAll('.nav-link:not([onclick])');
    menuLinks.forEach(link => {
        link.addEventListener('click', handleMenuClick);
    });
});

// Export functions for global use
window.SidebarFunctions = {
    showAccessDenied,
    toggleSidebar,
    closeSidebar,
    highlightActiveMenu,
    smoothScrollTo
};
