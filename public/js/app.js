/* ══════════════════════════════════════════════════════════════
   ChallengeHub — app.js
   ══════════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', function () {

  // ── Hamburger Menu ────────────────────────────────────────
  const hamburger = document.getElementById('nav-hamburger');
  const navLinks  = document.getElementById('nav-links');

  if (hamburger && navLinks) {
    hamburger.addEventListener('click', function () {
      const isOpen = navLinks.classList.toggle('open');
      hamburger.setAttribute('aria-expanded', isOpen.toString());
    });

    // Close on outside click
    document.addEventListener('click', function (e) {
      if (!hamburger.contains(e.target) && !navLinks.contains(e.target)) {
        navLinks.classList.remove('open');
        hamburger.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // ── Active NavLink ────────────────────────────────────────
  const page = new URLSearchParams(window.location.search).get('page') || 'home';
  const navMap = {
    'home': 'nav-home',
    'challenges': 'nav-challenges',
    'challenge-show': 'nav-challenges',
    'challenge-create': 'nav-challenges',
    'leaderboard': 'nav-leaderboard',
  };
  const activeId = navMap[page];
  if (activeId) {
    const el = document.getElementById(activeId);
    if (el) el.classList.add('active');
  }

  // ── Auto-dismiss Flash Messages ───────────────────────────
  const flash = document.getElementById('flash-message');
  if (flash) {
    setTimeout(() => {
      flash.style.transition = 'opacity .5s ease';
      flash.style.opacity = '0';
      setTimeout(() => flash.remove(), 500);
    }, 5000);
  }

  // ── Image Upload Preview ─────────────────────────────────
  setupImagePreview('reg-avatar',    'avatar-preview',  'avatar-upload-text',  true);
  setupImagePreview('ch-image',      'ch-preview',      'ch-upload-text',      false);

  // ── Upload area click passthrough ────────────────────────
  ['avatar-upload-area', 'challenge-upload-area'].forEach(function (areaId) {
    const area = document.getElementById(areaId);
    if (!area) return;
    area.addEventListener('click', function () {
      const input = area.querySelector('input[type="file"]');
      if (input) input.click();
    });
  });

  // ── Drag & Drop on upload areas ───────────────────────────
  document.querySelectorAll('.upload-area').forEach(function (area) {
    area.addEventListener('dragover', function (e) {
      e.preventDefault();
      area.style.borderColor = 'var(--clr-primary)';
    });
    area.addEventListener('dragleave', function () {
      area.style.borderColor = '';
    });
    area.addEventListener('drop', function (e) {
      e.preventDefault();
      area.style.borderColor = '';
      const input = area.querySelector('input[type="file"]');
      if (input && e.dataTransfer.files.length > 0) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
      }
    });
  });

  // ── Filter Form: live submit on select change ─────────────
  const filterForm = document.getElementById('filters-form');
  if (filterForm) {
    filterForm.querySelectorAll('select').forEach(function (sel) {
      sel.addEventListener('change', function () {
        filterForm.submit();
      });
    });
  }

  // ── Password Confirm Validation ───────────────────────────
  const pw1 = document.getElementById('reg-password');
  const pw2 = document.getElementById('reg-password2');
  if (pw1 && pw2) {
    pw2.addEventListener('input', function () {
      if (pw2.value && pw1.value !== pw2.value) {
        pw2.setCustomValidity('Les mots de passe ne correspondent pas.');
      } else {
        pw2.setCustomValidity('');
      }
    });
  }

  // ── Navbar Scroll Opacity ─────────────────────────────────
  const navbar = document.getElementById('navbar');
  if (navbar) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 20) {
        navbar.style.background = 'rgba(10,10,20,0.97)';
      } else {
        navbar.style.background = 'rgba(10,10,20,0.85)';
      }
    }, { passive: true });
  }

  // ── Animate cards on scroll (Intersection Observer) ───────
  const animatedEls = document.querySelectorAll('.card, .submission-card, .glass-panel');
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    animatedEls.forEach(function (el) {
      if (!el.style.opacity) {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity .5s ease, transform .5s ease';
        observer.observe(el);
      }
    });
  }
});

/**
 * Set up image preview for a file input.
 * @param {string} inputId      - Input element ID
 * @param {string} previewId    - Preview <img> element ID
 * @param {string} uploadTextId - Upload text container ID
 * @param {boolean} circle      - Whether preview should be circular
 */
function setupImagePreview(inputId, previewId, uploadTextId, circle) {
  const input   = document.getElementById(inputId);
  const preview = document.getElementById(previewId);
  const text    = document.getElementById(uploadTextId);

  if (!input || !preview) return;

  input.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
      if (circle) preview.style.borderRadius = '50%';
      if (text)   text.style.display = 'none';
    };
    reader.readAsDataURL(file);
  });
}
