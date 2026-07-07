// Mobile Interface JavaScript - Optimized & Modern
'use strict';

// Performance optimization: Use requestAnimationFrame for smooth animations
const raf = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame;

// Utility functions
const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => document.querySelectorAll(selector);

// Theme Management
class ThemeManager {
  constructor() {
    this.currentTheme = localStorage.getItem('theme') || 'auto';
    this.systemTheme = this.getSystemTheme();
    this.init();
  }

  getSystemTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  init() {
    this.applyTheme();
    this.setupEventListeners();
    this.updateThemeButtons();
  }

  applyTheme() {
    const effectiveTheme = this.currentTheme === 'auto' ? this.systemTheme : this.currentTheme;
    document.documentElement.setAttribute('data-theme', effectiveTheme);
    document.body.classList.toggle('dark-mode', effectiveTheme === 'dark');
    
    // Update theme-specific classes
    document.body.classList.remove('theme-light', 'theme-dark');
    document.body.classList.add(`theme-${effectiveTheme}`);
    
    // Update meta theme-color for mobile browsers
    this.updateMetaThemeColor(effectiveTheme);
  }

  updateMetaThemeColor(theme) {
    let metaThemeColor = document.querySelector('meta[name="theme-color"]');
    if (!metaThemeColor) {
      metaThemeColor = document.createElement('meta');
      metaThemeColor.name = 'theme-color';
      document.head.appendChild(metaThemeColor);
    }
    
    const colors = {
      light: '#ffffff',
      dark: '#1a1a1a'
    };
    metaThemeColor.content = colors[theme] || colors.light;
  }

  setTheme(theme) {
    this.currentTheme = theme;
    localStorage.setItem('theme', theme);
    this.applyTheme();
    this.updateThemeButtons();
    
    // Trigger custom event for other components
    window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
  }

  toggleTheme() {
    const themes = ['light', 'dark', 'auto'];
    const currentIndex = themes.indexOf(this.currentTheme);
    const nextIndex = (currentIndex + 1) % themes.length;
    this.setTheme(themes[nextIndex]);
  }

  updateThemeButtons() {
    // Update theme switcher buttons if they exist
    const themeButtons = $$('[data-bs-theme-value]');
    themeButtons.forEach(button => {
      const themeValue = button.getAttribute('data-bs-theme-value');
      const isActive = themeValue === this.currentTheme;
      
      button.classList.toggle('active', isActive);
      button.setAttribute('aria-pressed', isActive);
      
      const checkIcon = button.querySelector('.bi-check-lg');
      if (checkIcon) {
        checkIcon.classList.toggle('d-none', !isActive);
      }
    });
  }

  setupEventListeners() {
    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
      this.systemTheme = e.matches ? 'dark' : 'light';
      if (this.currentTheme === 'auto') {
        this.applyTheme();
      }
    });

    // Theme switcher buttons
    $$('[data-bs-theme-value]').forEach(button => {
      button.addEventListener('click', (e) => {
        e.preventDefault();
        const theme = button.getAttribute('data-bs-theme-value');
        this.setTheme(theme);
      });
    });

    // Keyboard shortcut for theme toggle (Ctrl/Cmd + T)
    document.addEventListener('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key === 't') {
        e.preventDefault();
        this.toggleTheme();
      }
    });
  }

  getCurrentTheme() {
    return this.currentTheme;
  }

  getEffectiveTheme() {
    return this.currentTheme === 'auto' ? this.systemTheme : this.currentTheme;
  }
}

// Initialize theme manager
const themeManager = new ThemeManager();

// Debounce function for performance
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Throttle function for scroll events
function throttle(func, limit) {
  let inThrottle;
  return function() {
    const args = arguments;
    const context = this;
    if (!inThrottle) {
      func.apply(context, args);
      inThrottle = true;
      setTimeout(() => inThrottle = false, limit);
    }
  };
}

// Function to confirm attendance entry
function konfirmasiAbsenMasuk() {
  Swal.fire({
    title: 'Konfirmasi Absen Masuk',
    text: 'Yakin ingin melakukan absen masuk sekarang?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, Absen!',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#667eea',
    cancelButtonColor: '#6c757d',
    customClass: {
      popup: 'swal-mobile'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      showLoading('Memproses absensi...');
      document.getElementById("formAbsenMasuk").submit();
    }
  });
}

// Function to handle ERP system access confirmation
function handleErpAccess() {
  Swal.fire({
    title: 'Masuk ERP Sistem?',
    text: "Yakin ingin masuk ke sistem ERP? Mohon gunakan device komputer atau laptop untuk akses ERP.",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#667eea',
    cancelButtonColor: '#dc3545',
    confirmButtonText: 'Ya, lanjut!',
    cancelButtonText: 'Batal',
    customClass: {
      popup: 'swal-mobile'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = this.getAttribute('href');
    }
  });
}

// Enhanced touch feedback for mobile
function addTouchFeedback() {
  const buttons = $$('.btn, .btn-masuk, .btn-pulang');
  
  buttons.forEach(button => {
    let touchStartY = 0;
    let touchEndY = 0;
    
    button.addEventListener('touchstart', function(e) {
      touchStartY = e.touches[0].clientY;
      this.style.transform = 'scale(0.95)';
      this.style.transition = 'transform 0.1s ease';
    }, { passive: true });
    
    button.addEventListener('touchend', function(e) {
      touchEndY = e.changedTouches[0].clientY;
      this.style.transform = '';
      this.style.transition = '';
      
      // Prevent click if user scrolled
      if (Math.abs(touchStartY - touchEndY) > 10) {
        e.preventDefault();
      }
    }, { passive: true });
    
    button.addEventListener('touchcancel', function() {
      this.style.transform = '';
      this.style.transition = '';
    }, { passive: true });
  });
}

// Smooth scrolling with performance optimization
function addSmoothScrolling() {
  const anchors = $$('a[href^="#"]');
  
  anchors.forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      e.preventDefault();
      const targetId = this.getAttribute('href');
      const target = $(targetId);
      
      if (target) {
        const targetPosition = target.offsetTop - 80; // Account for fixed header
        const startPosition = window.pageYOffset;
        const distance = targetPosition - startPosition;
        const duration = 800;
        let start = null;
        
        function animation(currentTime) {
          if (start === null) start = currentTime;
          const timeElapsed = currentTime - start;
          const run = ease(timeElapsed, startPosition, distance, duration);
          window.scrollTo(0, run);
          if (timeElapsed < duration) raf(animation);
        }
        
        function ease(t, b, c, d) {
          t /= d / 2;
          if (t < 1) return c / 2 * t * t + b;
          t--;
          return -c / 2 * (t * (t - 2) - 1) + b;
        }
        
        raf(animation);
      }
    });
  });
}

// Real-time clock with performance optimization
function updateClock() {
  const timeElements = $$('.stat-value.text-success');
  const now = new Date();
  const timeString = now.toLocaleTimeString('id-ID', { 
    hour: '2-digit', 
    minute: '2-digit',
    hour12: false 
  });
  
  timeElements.forEach(element => {
    if (element.textContent.includes(':')) {
      element.textContent = timeString;
    }
  });
}

// Auto-refresh time display with performance optimization
function startClockUpdate() {
  updateClock();
  setInterval(updateClock, 60000); // Update every minute
}

// Add parallax effect to header
function addParallaxEffect() {
  const header = $('.header-section');
  if (!header) return;
  
  let ticking = false;
  
  function updateParallax() {
    const scrolled = window.pageYOffset;
    const rate = scrolled * -0.5;
    header.style.transform = `translateY(${rate}px)`;
    ticking = false;
  }
  
  function requestTick() {
    if (!ticking) {
      raf(updateParallax);
      ticking = true;
    }
  }
  
  window.addEventListener('scroll', throttle(requestTick, 16), { passive: true });
}

// Add intersection observer for animations
function addScrollAnimations() {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, observerOptions);
  
  // Observe elements for animation
  const animateElements = $$('.stat-card, .face-box, .quick-info, .btn.w-100');
  animateElements.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
  });
}

// Add haptic feedback for mobile devices
function addHapticFeedback() {
  if ('vibrate' in navigator) {
    const buttons = $$('.btn-masuk, .btn-pulang, .btn.w-100');
    buttons.forEach(button => {
      button.addEventListener('click', () => {
        navigator.vibrate(50);
      });
    });
  }
}

// Optimize images for mobile
function optimizeImages() {
  const images = $$('img');
  images.forEach(img => {
    // Add lazy loading
    img.loading = 'lazy';
    
    // Add error handling
    img.addEventListener('error', function() {
      this.style.display = 'none';
    });
  });

function showLoading(message = 'Memuat...') {
function addPullToRefresh() {
  let startY = 0;
  let currentY = 0;
  let pullDistance = 0;
  const threshold = 80;
  const container = document.querySelector('.container');
  let isInput = false;
  const initialHeight = window.innerHeight;
  
  if (!container) return;
  
  function handleTouchStart(e) {
    startY = e.touches[0].clientY;
    isInput = !!(e.target.closest('input') || e.target.closest('textarea') || e.target.closest('select') || e.target.closest('.overflow-auto') || e.target.closest('.scrollable'));
  }
  
  function handleTouchMove(e) {
    if (isInput || document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA') return;
    
    // Block if keyboard likely open
    if (window.innerHeight < initialHeight * 0.9) return;

    currentY = e.touches[0].clientY;
    pullDistance = currentY - startY;
    
    if (window.scrollY === 0 && pullDistance > 0) {
      if(e.cancelable) e.preventDefault();
      const scale = Math.min(pullDistance / threshold, 1);
      container.style.transform = `translateY(${pullDistance * 0.3}px) scale(${1 + scale * 0.02})`;
    }
  }
  
  function handleTouchEnd() {
    if (isInput || document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA') {
        isInput = false;
        pullDistance = 0;
        container.style.transform = '';
        return;
    }

    if (pullDistance > threshold) {
      showLoading('Memperbarui data...');
      setTimeout(() => {
        window.location.reload();
      }, 1000);
    }
    
    pullDistance = 0;
    container.style.transform = '';
  }
  
  document.addEventListener('touchstart', handleTouchStart, { passive: true });
  document.addEventListener('touchmove', handleTouchMove, { passive: false });
  document.addEventListener('touchend', handleTouchEnd, { passive: true });
}
  if(typeof Swal !== 'undefined'){
      Swal.fire({
        title: message,
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
        customClass: {
          popup: 'swal-mobile'
        }
      });
  }
}

function hideLoading() {
  if(typeof Swal !== 'undefined') Swal.close();
}

function showSuccess(message, title = 'Berhasil!') {
  if(typeof Swal !== 'undefined'){
      Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        timer: 2000,
        showConfirmButton: false,
        customClass: {
          popup: 'swal-mobile'
        }
      });
  }
}

function showError(message, title = 'Error!') {
  if(typeof Swal !== 'undefined'){
      Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        customClass: {
          popup: 'swal-mobile'
        }
      });
  }
}

function showConfirmation(message, title = 'Konfirmasi', callback) {
  if(typeof Swal !== 'undefined'){
      Swal.fire({
        title: title,
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal',
        customClass: {
          popup: 'swal-mobile'
        }
      }).then((result) => {
        if (result.isConfirmed && callback) {
          callback();
        }
      });
  } else if(confirm(message)) {
      if(callback) callback();
  }
}

function addNetworkMonitoring() {
  function updateNetworkStatus() {
    const isOnline = navigator.onLine;
    const statusIndicator = document.querySelector('.network-status');
    
    if (statusIndicator) {
      statusIndicator.className = `network-status ${isOnline ? 'online' : 'offline'}`;
      statusIndicator.textContent = isOnline ? 'Online' : 'Offline';
    }
  }
  
  window.addEventListener('online', updateNetworkStatus);
  window.addEventListener('offline', updateNetworkStatus);
  updateNetworkStatus();
}

document.addEventListener('DOMContentLoaded', function() {
  const scheduleTask = window.requestIdleCallback || ((fn) => setTimeout(fn, 1));
  
  if(typeof addTouchFeedback === 'function') addTouchFeedback();
  if(typeof addHapticFeedback === 'function') addHapticFeedback();
  if(typeof optimizeImages === 'function') optimizeImages();
  if(typeof startClockUpdate === 'function') startClockUpdate();
  
  scheduleTask(() => {
    if(typeof addSmoothScrolling === 'function') addSmoothScrolling();
    if(typeof addScrollAnimations === 'function') addScrollAnimations();
    if(typeof addParallaxEffect === 'function') addParallaxEffect();
    addPullToRefresh();
    addNetworkMonitoring();
  });
  
  const btnErp = document.getElementById('btnErp');
  if (btnErp) {
    btnErp.addEventListener('click', function(e) {
      e.preventDefault();
      if(typeof handleErpAccess === 'function') handleErpAccess.call(this);
    });
  }
  
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(console.log);
  }
});

const style = document.createElement('style');
style.textContent = `
  .swal-mobile { font-size: 14px !important; }
  .swal-mobile .swal2-popup { width: 90% !important; max-width: 400px !important; }
  .network-status { position: fixed; top: 10px; right: 10px; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600; z-index: 9999; }
  .network-status.online { background: #28a745; color: white; }
  .network-status.offline { background: #dc3545; color: white; }
  * { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
  .theme-dark { --bg-primary: #1a1a1a; --bg-secondary: #2d2d2d; --text-primary: #ffffff; --text-secondary: #b0b0b0; --border-color: #404040; }
  .theme-light { --bg-primary: #ffffff; --bg-secondary: #f8f9fa; --text-primary: #000000; --text-secondary: #6c757d; --border-color: #dee2e6; }
`;
document.head.appendChild(style);
