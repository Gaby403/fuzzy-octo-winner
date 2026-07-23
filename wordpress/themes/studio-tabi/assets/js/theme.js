/* Studio Tabi — interações: menu mobile, acordeão do FAQ, animações */
(function () {
  'use strict';

  /* ── Menu mobile ── */
  var burger = document.querySelector('.tabi-burger');
  var nav = document.querySelector('.tabi-nav');
  if (burger && nav) {
    burger.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      burger.classList.toggle('is-open', open);
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    nav.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') {
        nav.classList.remove('is-open');
        burger.classList.remove('is-open');
        burger.setAttribute('aria-expanded', 'false');
      }
    });
  }

  /* ── Acordeão do FAQ ── */
  document.querySelectorAll('.tabi-faq-q').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var item = btn.closest('.tabi-faq-item');
      var ans = item.querySelector('.tabi-faq-a');
      var open = item.classList.toggle('is-open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      ans.style.maxHeight = open ? ans.scrollHeight + 'px' : '0';
    });
  });

  /* ── Revelação ao rolar ── */
  var reveals = [].slice.call(document.querySelectorAll(
    '.tabi-section .tabi-eyebrow, .tabi-title, .tabi-section-intro, ' +
    '.tabi-stat, .tabi-pillar, .tabi-service, .tabi-project-card, ' +
    '.tabi-faq-item, .tabi-blog-card, .tabi-about-text, .tabi-more'
  ));
  reveals.forEach(function (el, i) {
    el.classList.add('tabi-reveal');
    el.style.transitionDelay = ( (i % 4) * 0.06 ) + 's';
  });

  function revealAll() { reveals.forEach(function (el) { el.classList.add('is-in'); }); }

  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
      });
    }, { rootMargin: '0px 0px -8% 0px' });
    reveals.forEach(function (el) { io.observe(el); });
    // Rede de segurança: nada fica escondido para sempre.
    setTimeout(revealAll, 2600);
  } else {
    revealAll();
  }

  /* ── Parallax leve das montanhas do hero ── */
  var mtns = [].slice.call(document.querySelectorAll('.tabi-mtn'));
  if (mtns.length && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    var ticking = false;
    window.addEventListener('scroll', function () {
      if (ticking) { return; }
      ticking = true;
      requestAnimationFrame(function () {
        var y = window.pageYOffset;
        mtns.forEach(function (m, i) {
          m.style.transform = 'translateY(' + ( y * (0.04 + i * 0.03) ) + 'px)';
        });
        ticking = false;
      });
    }, { passive: true });
  }
})();
