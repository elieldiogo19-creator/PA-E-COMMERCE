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

const vert = `
void main() {
  gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
}
`;

const frag = `
precision highp float;

vec4 abyssColor = vec4(0.0, 0.0, 0.0, 1.0);
vec4 tunnelColor = vec4(1.0, 1, 1, 1.0);

uniform float time;
uniform vec2 resolution;

void main() {

    vec2 uv = (gl_FragCoord.xy - 0.5 * resolution.xy) / resolution.y * 0.6;
    
    float r = length(uv);
    float y = fract(r / 0.005 / (r - 0.01) + time);
    y = smoothstep(0.01, 4.0, y);
   
    float x = length(uv);
    x = smoothstep(0.5, 0.01, x);

    gl_FragColor = mix(tunnelColor, abyssColor, x) * y;
}
`;

let scene, camera, renderer;
let uniforms, geometry, material, mesh;
let startTime = Date.now();

function init() {
  scene = new THREE.Scene();

  camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);
  geometry = new THREE.PlaneGeometry(2, 2);

  uniforms = {
    time: { value: 0.0 },
    resolution: {
      value: new THREE.Vector2(window.innerWidth, window.innerHeight),
    },
  };

  material = new THREE.ShaderMaterial({
    uniforms: uniforms,
    vertexShader: vert,
    fragmentShader: frag,
  });

  mesh = new THREE.Mesh(geometry, material);
  scene.add(mesh);

  renderer = new THREE.WebGLRenderer({ antialias: true });
  const bg = document.querySelector(".bg");

  renderer.setSize(bg.clientWidth, bg.clientHeight);

  uniforms.resolution.value.set(bg.clientWidth, bg.clientHeight);

  bg.appendChild(renderer.domElement);
}

function animate() {
  requestAnimationFrame(animate);
  let elapsedMilliseconds = Date.now() - startTime;
  material.uniforms.time.value = elapsedMilliseconds / 1000;
  renderer.render(scene, camera);
}

function resize() {
  const bg = document.querySelector(".bg");

  camera.aspect = bg.clientWidth / bg.clientHeight;
  camera.updateProjectionMatrix();

  material.uniforms.resolution.value.set(bg.clientWidth, bg.clientHeight);

  renderer.setSize(bg.clientWidth, bg.clientHeight);
}

window.addEventListener("resize", resize);

init();
animate();