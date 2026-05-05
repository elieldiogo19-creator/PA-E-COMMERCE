// Efeito de ondas circulares no background (Three.js)
const initBackground = () => {
  const hero = document.querySelector('.hero');
  if (!hero) return;

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(75, hero.offsetWidth / hero.offsetHeight, 0.1, 1000);
  
  const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
  renderer.setSize(hero.offsetWidth, hero.offsetHeight);
  renderer.setPixelRatio(window.devicePixelRatio);
  hero.appendChild(renderer.domElement);

  // Geometria de círculos concêntricos
  const geometry = new THREE.TorusGeometry(1, 0.02, 16, 100);
  const material = new THREE.MeshBasicMaterial({ 
    color: 0xf0a927, // Dourado
    transparent: true, 
    opacity: 0.3 
  });

  const circles = [];
  for (let i = 0; i < 8; i++) {
    const circle = new THREE.Mesh(geometry, material.clone());
    circle.scale.set(0.5 + i * 0.3, 0.5 + i * 0.3, 1);
    circle.rotation.x = Math.PI / 2;
    circle.material.opacity = 0.3 - (i * 0.03);
    scene.add(circle);
    circles.push({
      mesh: circle,
      speed: 0.002 + i * 0.001
    });
  }

  camera.position.z = 5;

  // Animação
  const animate = () => {
    requestAnimationFrame(animate);
    
    circles.forEach((item, index) => {
      item.mesh.rotation.z += item.speed;
      // Pulsação suave
      const scale = item.mesh.scale.x + Math.sin(Date.now() * 0.001 + index) * 0.001;
      item.mesh.scale.set(scale, scale, 1);
    });

    renderer.render(scene, camera);
  };

  animate();

  // Responsivo
  window.addEventListener('resize', () => {
    camera.aspect = hero.offsetWidth / hero.offsetHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(hero.offsetWidth, hero.offsetHeight);
  });
};

// Inicia quando carregar
window.addEventListener('load', initBackground);

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const senha = document.querySelector('input[name="senha"]');
    const senha2 = document.querySelector('input[name="senha2"]');
    const email = document.querySelector('input[name="email"]');

    // Validação em tempo real da confirmação de senha
    senha2.addEventListener('input', function() {
        if (this.value !== senha.value) {
            this.style.borderColor = '#ff4444';
        } else {
            this.style.borderColor = '#f0a927'; // Verde/dourado quando igual
        }
    });

    // Validação básica no submit
    form.addEventListener('submit', function(e) {
        // Remove alertas antigos
        const oldAlerts = document.querySelectorAll('.alert');
        oldAlerts.forEach(alert => alert.remove());

        let erros = [];

        if (senha.value.length < 6) {
            erros.push('A senha deve ter pelo menos 6 caracteres.');
        }

        if (senha.value !== senha2.value) {
            erros.push('As senhas não conferem.');
            senha2.focus();
        }

        // Se houver erros de JS, mostra e cancela o envio
        if (erros.length > 0 && !document.querySelector('.alert-erro')) {
            e.preventDefault();
            
            const divErro = document.createElement('div');
            divErro.className = 'alert alert-erro';
            divErro.innerHTML = erros.join('<br>');
            
            form.insertBefore(divErro, form.children[3]); // Insere após o título/logo
        }
    });
});