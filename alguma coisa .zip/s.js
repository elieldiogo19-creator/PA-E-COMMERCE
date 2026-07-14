const menuBtn = document.getElementById('menuBtn');
const navLinks = document.getElementById('navLinks');

menuBtn.addEventListener('click', () => {
  navLinks.classList.toggle('active');
});

const form = document.getElementById('contactForm');

form.addEventListener('submit', (e) => {
  e.preventDefault();

  alert('Mensagem enviada com sucesso!');

  form.reset();
});

window.addEventListener('scroll', () => {
  const header = document.querySelector('.header');

  if(window.scrollY > 50){
    header.style.background = '#020617';
  } else {
    header.style.background = 'rgba(15, 23, 42, 0.85)';
  }
});

