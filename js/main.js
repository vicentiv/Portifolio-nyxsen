/* ============================================
   MAIN.JS — Interactions, Animations, Logic
   NexFrame Portfolio
============================================ */

// ---- Custom Cursor ----
const cursor = document.getElementById('cursor');
const cursorRing = document.getElementById('cursor-ring');

let cursorX = 0, cursorY = 0;
let ringX = 0, ringY = 0;

document.addEventListener('mousemove', (e) => {
  cursorX = e.clientX;
  cursorY = e.clientY;
  cursor.style.left = cursorX + 'px';
  cursor.style.top  = cursorY + 'px';
});
                               
function animateRing() {
  ringX += (cursorX - ringX) * 0.12;
  ringY += (cursorY - ringY) * 0.12;
  cursorRing.style.left = ringX + 'px';
  cursorRing.style.top  = ringY + 'px';
  requestAnimationFrame(animateRing);
}
animateRing();

document.addEventListener('mouseleave', () => {
  cursor.style.opacity = '0';
  cursorRing.style.opacity = '0';
});
document.addEventListener('mouseenter', () => {
  cursor.style.opacity = '1';
  cursorRing.style.opacity = '1';
});

//scroll da pagina

const nav = document.getElementById('nav');
window.addEventListener('scroll', () => {
  if (window.scrollY > 60) {
    nav.classList.add('scrolled');
  } else {
    nav.classList.remove('scrolled');
  }
});

// navbar
// navbar mobile
const hamburger = document.querySelector('.nav-hamburger');
const mobileNav = document.querySelector('.nav-mobile');

if (hamburger && mobileNav && nav) {
  hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('open');
    mobileNav.classList.toggle('open');
    nav.classList.toggle('menu-open');
  });

  mobileNav.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      hamburger.classList.remove('open');
      mobileNav.classList.remove('open');
      nav.classList.remove('menu-open');
    });
  });
}
// ---- Reveal on scroll ----
const revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
    }
  });
}, { threshold: 0.12 });

revealEls.forEach(el => revealObserver.observe(el));

// ---- Active nav link on scroll ----
const sections = document.querySelectorAll('section[id]');
const navLinks = document.querySelectorAll('.nav-links a, .nav-mobile a');

const sectionObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const id = entry.target.id;
      navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${id}`) {
          link.classList.add('active');
        }
      });
    }
  });
}, { threshold: 0.4 });

sections.forEach(s => sectionObserver.observe(s));

// ---- Project filter ----
const filterBtns = document.querySelectorAll('.filter-btn');
const projectCards = document.querySelectorAll('.project-card');

filterBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    filterBtns.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const filter = btn.dataset.filter;
    projectCards.forEach(card => {
      if (filter === 'all' || card.dataset.cat === filter) {
        card.style.opacity = '1';
        card.style.pointerEvents = 'auto';
        card.style.transform = '';
      } else {
        card.style.opacity = '0.2';
        card.style.pointerEvents = 'none';
        card.style.transform = 'scale(0.97)';
      }
    });
  });
});

// ---- Count-up animation ----
function countUp(el, target, duration = 1600) {
  const start = 0;
  const startTime = performance.now();
  const isDecimal = String(target).includes('.');

  function update(currentTime) {
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    const current = start + (target - start) * eased;

    if (isDecimal) {
      el.textContent = current.toFixed(1);
    } else {
      el.textContent = Math.floor(current);
    }

    if (progress < 1) requestAnimationFrame(update);
    else el.textContent = isDecimal ? target.toFixed(1) : target;
  }
  requestAnimationFrame(update);
}

const countEls = document.querySelectorAll('[data-count]');
const countObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting && !entry.target.dataset.counted) {
      entry.target.dataset.counted = 'true';
      countUp(entry.target, parseFloat(entry.target.dataset.count));
    }
  });
}, { threshold: 0.6 });
countEls.forEach(el => countObserver.observe(el));

// ---- Parallax on hero orbs ----
window.addEventListener('scroll', () => {
  const scrollY = window.scrollY;
  const orb1 = document.querySelector('.hero-orb-1');
  const orb2 = document.querySelector('.hero-orb-2');
  if (orb1) orb1.style.transform = `translateY(${scrollY * 0.2}px)`;
  if (orb2) orb2.style.transform = `translateY(${scrollY * -0.15}px)`;
});

// ---- Tilt effect on project cards ----
projectCards.forEach(card => {
  card.addEventListener('mousemove', (e) => {
    const rect = card.getBoundingClientRect();
    const x = (e.clientX - rect.left) / rect.width - 0.5;
    const y = (e.clientY - rect.top) / rect.height - 0.5;
    card.style.transform = `perspective(800px) rotateY(${x * 6}deg) rotateX(${-y * 4}deg) scale(1.02)`;
  });
  card.addEventListener('mouseleave', () => {
    card.style.transform = '';
  });
});

// ---- Form submit ----
const form = document.querySelector('.contact-form');

if (form) {

  form.addEventListener('submit', () => {

    const btn = form.querySelector('.form-submit span');

    const original = btn.textContent;

    btn.textContent = 'Enviando...';

    setTimeout(() => {

      btn.textContent = '✓ Mensagem Enviada!';

      setTimeout(() => {

        btn.textContent = original;

      }, 3000);

    }, 1400);

  });

}
// ---- Glitch text effect on hero ----
const glitchEls = document.querySelectorAll('.glitch');
glitchEls.forEach(el => {
  el.setAttribute('data-text', el.textContent);
});

// ---- Logo N SVG glow pulse ----
const heroSvg = document.querySelector('.hero-n svg');
if (heroSvg) {
  setInterval(() => {
    heroSvg.style.filter = `drop-shadow(0 0 ${50 + Math.random() * 30}px rgba(0, 87, 255, ${0.5 + Math.random() * 0.3}))`;
  }, 2000);
}

// ---- Smooth scroll ----
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const target = document.querySelector(a.getAttribute('href'));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});
// ---- Form alerts after redirect ----
const urlParams = new URLSearchParams(window.location.search);
const contactForm = document.querySelector('.contact-form');

if (contactForm) {
  if (urlParams.get('sucesso') === '1') {
    const alertBox = document.createElement('div');

    alertBox.className = 'form-alert-success';
    alertBox.innerHTML = '✅ Mensagem enviada com sucesso! Em breve entraremos em contato.';

    contactForm.prepend(alertBox);

    setTimeout(() => {
      alertBox.remove();

      const url = new URL(window.location);
      url.searchParams.delete('sucesso');
      history.replaceState({}, '', url.pathname + url.hash);
    }, 5000);
  }

  if (urlParams.get('erro') === '1') {
    const alertBox = document.createElement('div');

    alertBox.className = 'form-alert-error';
    alertBox.innerHTML = '⚠️ Preencha todos os campos obrigatórios.';

    contactForm.prepend(alertBox);

    setTimeout(() => {
      alertBox.remove();

      const url = new URL(window.location);
      url.searchParams.delete('erro');
      history.replaceState({}, '', url.pathname + url.hash);
    }, 5000);
  }
}

const phoneInput = document.getElementById('phone');

if (phoneInput) {

  phoneInput.addEventListener('input', () => {

    phoneInput.value = phoneInput.value.replace(/\D/g, '');

  });

}
  