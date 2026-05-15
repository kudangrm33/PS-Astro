(function(){
  if (window.feather && typeof window.feather.replace==='function') return;
  var ICONS = {
    search: '<circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>',
    'shopping-cart': '<circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 12.39a2 2 0 0 0 2 1.61h7.72a2 2 0 0 0 2-1.61L23 6H6"></path>',
    menu: '<line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line>',
    x: '<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>',
    star: '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>',
    'dollar-sign': '<line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>',
    user: '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
    mail: '<path d="M4 4h16v16H4z"></path><polyline points="22,6 12,13 2,6"></polyline>',
    phone: '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 3.16 9.81a19.79 19.79 0 0 1-3.07-8.63A2 2 0 0 1 2.09 1h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.63a2 2 0 0 1-.45 2.11L6 8a16 16 0 0 0 8 8l.54-.26a2 2 0 0 1 2.11-.45c.85.29 1.73.5 2.63.62A2 2 0 0 1 22 16.92z"></path>'
  };
  function replace(){
    var nodes = document.querySelectorAll('i[data-feather]');
    nodes.forEach(function(n){
      var name = n.getAttribute('data-feather');
      var svg = document.createElementNS('http://www.w3.org/2000/svg','svg');
      svg.setAttribute('width','24'); svg.setAttribute('height','24');
      svg.setAttribute('fill','none'); svg.setAttribute('stroke','currentColor');
      svg.setAttribute('stroke-width','2'); svg.setAttribute('stroke-linecap','round'); svg.setAttribute('stroke-linejoin','round');
      svg.innerHTML = ICONS[name] || '';
      n.replaceWith(svg);
    });
  }
  if (document.readyState!=='loading') replace();
  else document.addEventListener('DOMContentLoaded', replace);
})();