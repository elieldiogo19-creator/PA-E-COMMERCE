// ============================================
// EFEITO 1: Ondas circulares douradas (.hero)
// ============================================
const initBackground = () => {
  const hero = document.querySelector('.hero');
  if (!hero) return;

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(75, hero.offsetWidth / hero.offsetHeight, 0.1, 1000);
  
  const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
  renderer.setSize(hero.offsetWidth, hero.offsetHeight);
  renderer.setPixelRatio(window.devicePixelRatio);
  hero.appendChild(renderer.domElement);

  const geometry = new THREE.TorusGeometry(1, 0.02, 16, 100);
  const material = new THREE.MeshBasicMaterial({ 
    color: 0xf0a927,
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

  const animate = () => {
    requestAnimationFrame(animate);
    
    circles.forEach((item, index) => {
      item.mesh.rotation.z += item.speed;
      const scale = item.mesh.scale.x + Math.sin(Date.now() * 0.001 + index) * 0.001;
      item.mesh.scale.set(scale, scale, 1);
    });

    renderer.render(scene, camera);
  };

  animate();

  window.addEventListener('resize', () => {
    camera.aspect = hero.offsetWidth / hero.offsetHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(hero.offsetWidth, hero.offsetHeight);
  });
};

window.addEventListener('load', initBackground);

// ============================================
// EFEITO 2: Túnel/Abismo animado (.bg)
// ============================================
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

let scene2, camera2, renderer2;
let uniforms2, geometry2, material2, mesh2;
let startTime2 = Date.now();

function initShader() {
  scene2 = new THREE.Scene();
  camera2 = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);
  geometry2 = new THREE.PlaneGeometry(2, 2);

  uniforms2 = {
    time: { value: 0.0 },
    resolution: {
      value: new THREE.Vector2(window.innerWidth, window.innerHeight),
    },
  };

  material2 = new THREE.ShaderMaterial({
    uniforms: uniforms2,
    vertexShader: vert,
    fragmentShader: frag,
  });

  mesh2 = new THREE.Mesh(geometry2, material2);
  scene2.add(mesh2);

  renderer2 = new THREE.WebGLRenderer({ antialias: true });
  const bg = document.querySelector(".bg");

  renderer2.setSize(bg.clientWidth, bg.clientHeight);
  uniforms2.resolution.value.set(bg.clientWidth, bg.clientHeight);

  bg.appendChild(renderer2.domElement);
}

function animateShader() {
  requestAnimationFrame(animateShader);
  let elapsedMilliseconds = Date.now() - startTime2;
  material2.uniforms.time.value = elapsedMilliseconds / 1000;
  renderer2.render(scene2, camera2);
}

function resizeShader() {
  const bg = document.querySelector(".bg");
  camera2.aspect = bg.clientWidth / bg.clientHeight;
  camera2.updateProjectionMatrix();
  material2.uniforms.resolution.value.set(bg.clientWidth, bg.clientHeight);
  renderer2.setSize(bg.clientWidth, bg.clientHeight);
}

window.addEventListener("resize", resizeShader);

initShader();
animateShader();