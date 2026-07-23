/* Studio Tabi — interações leves (menu mobile + acordeão do FAQ) */
(function () {
  'use strict';

  // Menu mobile
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

  // Acordeão do FAQ
  document.querySelectorAll('.tabi-faq-q').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var item = btn.closest('.tabi-faq-item');
      var ans = item.querySelector('.tabi-faq-a');
      var open = item.classList.toggle('is-open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      ans.style.maxHeight = open ? ans.scrollHeight + 'px' : '0';
    });
  });
})();
