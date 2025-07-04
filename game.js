const canvas = document.getElementById('game');
const ctx = canvas.getContext('2d');
const images = {
  player: new Image(),
  enemy: new Image(),
  goal: new Image(),
  bullet: new Image(),
  chocolate: new Image()
};

images.player.src = "carrito.png";
images.enemy.src = "enemigo.png";
images.goal.src = "meta.png";
images.bullet.src = "bala.png";
images.chocolate.src = "chocolate.png";
const menu = document.getElementById('menu');

// Música de fondo
const backgroundMusic = new Audio("musiquita.mp3");
backgroundMusic.loop = true;
backgroundMusic.volume = 0.5; // Puedes ajustar entre 0.0 y 1.0

let cameraX = 0;
let gravity = 0.5;
let isPaused = true;

let keys = {};
["ArrowRight", "ArrowLeft", "ArrowUp", "Space", "Escape"].forEach(k => keys[k] = false);

let player = {
  x: 50, y: 290, width: 50, height: 50, // ← Aumentado de 30x30 a 50x50
  dx: 0, dy: 0, jump: false, grounded: false, jumpTimer: 0,
  color: "red",
  lives: 3,
  invulnerable: false,
  invulnTimer: 0
};

let ground = { x: 0, y: 350, width: 3000, height: 50, color: "green" };

function getWorldWidth() {
  return ground.width;
}

let wall = { x: -200, y: 0, width: 200, height: 800, color: "pink" };
let rightWall = { x: getWorldWidth(), y: 0, width: 200, height: 800, color: "pink" };
let goal = { x: getWorldWidth() - 170, y: 200, width: 150, height: 150 }; // Antes 40x50


let finalMessage = "";
let coins = [
  { x: 200, y: 300, collected: false },
  { x: 400, y: 280, collected: false },
  { x: 520, y: 220, collected: false },
  { x: 1100, y: 270, collected: false },
  { x: 950, y: 220, collected: false },
  { x: 1250, y: 220, collected: false },
  { x: 1450, y: 170, collected: false },
  { x: 1700, y: 300, collected: false },
  { x: 1900, y: 270, collected: false },
  { x: 2300, y: 300, collected: false },
  { x: 2600, y: 180, collected: false },
  { x: 2800, y: 300, collected: false }
];

let platforms = [
  { x: 500, y: 250, width: 100, height: 20 },
  { x: 900, y: 270, width: 100, height: 20 },
  { x: 1200, y: 270, width: 100, height: 20 },
  { x: 1400, y: 220, width: 150, height: 20 },
  { x: 2000, y: 270, width: 100, height: 20 },
  { x: 2500, y: 220, width: 120, height: 20 }
];

let enemies = [
  { x: 300, y: 320, width: 30, height: 30, alive: true, vx: 1, minX: 300, maxX: 400 },
  { x: 1000, y: 320, width: 30, height: 30, alive: true, vx: 1, minX: 1000, maxX: 1100 },
  { x: 1600, y: 320, width: 30, height: 30, alive: true, vx: 1, minX: 1600, maxX: 1700 },
  { x: 2200, y: 320, width: 30, height: 30, alive: true, vx: 1, minX: 2200, maxX: 2300 },
  { x: 920, y: 220, width: 30, height: 30, alive: true },
  { x: 1250, y: 220, width: 30, height: 30, alive: true },
  { x: 2020, y: 220, width: 30, height: 30, alive: true },
  { x: 2520, y: 170, width: 30, height: 30, alive: true }
];

enemies.forEach(e => {
  e.width = 50;
  e.height = 50;
  if (e.y >= 300) e.y = 300; // ← Para que los enemigos no se hundan en el suelo
});

let projectiles = [];

function fireProjectile(enemy) {
  const angle = Math.atan2(player.y - enemy.y, player.x - enemy.x);
  const speed = 1;
  projectiles.push({
    x: enemy.x + enemy.width / 2,
    y: enemy.y + enemy.height / 2,
    dx: Math.cos(angle) * speed,
    dy: Math.sin(angle) * speed,
    radius: 5 // en vez de 5, ya que ahora son más grandes
  });
}

setInterval(() => {
  if (!isPaused) {
    enemies.forEach(e => {
      if (e.alive && !e.vx) fireProjectile(e);
    });
  }
}, 4000);

function drawRect(x, y, w, h, color) {
  ctx.fillStyle = color;
  ctx.fillRect(x - cameraX, y, w, h);
}

function drawPlayer() {
  ctx.drawImage(images.player, player.x - cameraX, player.y, player.width, player.height);
}

function drawCoins() {
  const time = performance.now() / 500; // controla la velocidad del movimiento

  coins.forEach(coin => {
    if (!coin.collected && Number.isFinite(coin.x) && Number.isFinite(coin.y)) {
      const floatOffset = Math.sin(time + coin.x) * 5; // movimiento hacia arriba y abajo

      ctx.drawImage(
        images.chocolate,
        coin.x - cameraX - 15,
        coin.y + floatOffset - 15,
        30,
        30
      );
    }
  });
}

function drawEnemies() {
  enemies.forEach(e => {
    if (e.alive) {
      ctx.drawImage(images.enemy, e.x - cameraX, e.y, e.width, e.height);
    }
  });
}

function drawPlatforms() {
  platforms.forEach(p => drawRect(p.x, p.y, p.width, p.height, "brown"));
}

function drawGoal() {
  ctx.drawImage(images.goal, goal.x - cameraX, goal.y, goal.width, goal.height);
}

function drawGround() {
  drawRect(ground.x, ground.y, ground.width, ground.height, ground.color);
  drawRect(wall.x, wall.y, wall.width, wall.height, wall.color);
  drawRect(rightWall.x, rightWall.y, rightWall.width, rightWall.height, rightWall.color);
}

function drawProjectiles() {
  projectiles.forEach(p => {
    ctx.drawImage(images.bullet, p.x - cameraX - 10, p.y - 10, 20, 20); // ← De 10x10 a 20x20
  });
}

function drawCoinCounter() {
  const collected = coins.filter(c => c.collected).length;
  ctx.fillStyle = "white";
  ctx.font = "20px sans-serif";
  const text = `● = ${collected}`;
  const textWidth = ctx.measureText(text).width;
  ctx.fillText(text, canvas.width - textWidth - 20, 30); // Ahora se mantiene cerca del borde derecho visible
}

function drawLives() {
  ctx.fillStyle = player.invulnerable ? "orange" : "white";
  ctx.font = "20px sans-serif";
  ctx.fillText(`♥ = ${player.lives}`, 50, 60);
}

function applyPhysics() {
  player.dy += gravity;
  player.y += player.dy;
  player.x += player.dx;

  if (player.y + player.height >= ground.y) {
    player.y = ground.y - player.height;
    player.dy = 0;
    player.grounded = true;
  } else {
    player.grounded = false;
  }

  if (player.x < wall.x + wall.width) player.x = wall.x + wall.width;
  if (player.x + player.width > rightWall.x) player.x = rightWall.x - player.width;

  platforms.forEach(p => {
    if (
      player.x + player.width > p.x &&
      player.x < p.x + p.width &&
      player.y + player.height >= p.y &&
      player.y + player.height <= p.y + 10 &&
      player.dy >= 0
    ) {
      player.y = p.y - player.height;
      player.dy = 0;
      player.grounded = true;
    }
    if (
      player.x + player.width > p.x &&
      player.x < p.x + p.width &&
      player.y <= p.y + p.height &&
      player.y >= p.y &&
      player.dy < 0
    ) {
      player.dy = 0;
      player.y = p.y + p.height;
    }
    if (
      player.y + player.height > p.y &&
      player.y < p.y + p.height
    ) {
      if (player.x < p.x + p.width && player.x + player.width > p.x + p.width && player.dx < 0) {
        player.x = p.x + p.width;
      }
      if (player.x + player.width > p.x && player.x < p.x && player.dx > 0) {
        player.x = p.x - player.width;
      }
    }
  });

  const canvasCenter = canvas.width / 2;
  cameraX = Math.min(Math.max(player.x - canvasCenter, 0), getWorldWidth() - canvas.width);
}

function takeDamage() {
  if (player.invulnerable) return;

  player.lives--;
  if (player.lives <= 0) {
    finalMessage = "¡Has muerto!\nCómprate un chocolate para endulzar tu día 🍫";
    isPaused = true;
    return;
  }

  player.invulnerable = true;
  player.invulnTimer = 300; // 60 FPS * 5 segundos = 300 frames
}

function checkCollisions() {
  coins.forEach(c => {
    if (!c.collected && Math.hypot(player.x - c.x, player.y - c.y) < 25) c.collected = true;
  });

  enemies.forEach(e => {
    if (!e.alive) return;
    if (
      player.x < e.x + e.width &&
      player.x + player.width > e.x &&
      player.y < e.y + e.height &&
      player.y + player.height > e.y
    ) {
      if (player.dy > 0 && player.y + player.height - e.y < 30) {
        e.alive = false;
        player.dy = -10;
      } else {
        takeDamage();
      }
    }
  });

  projectiles.forEach(p => {
    if (
      player.x < p.x + p.radius &&
      player.x + player.width > p.x - p.radius &&
      player.y < p.y + p.radius &&
      player.y + player.height > p.y - p.radius
    ) {
      takeDamage();
    }
  });

  if (
    player.x < goal.x + goal.width &&
    player.x + player.width > goal.x &&
    player.y < goal.y + goal.height &&
    player.y + player.height > goal.y
  ) {
    const collected = coins.filter(c => c.collected).length;
    const code = Math.floor(Math.random() * 900 + 100);

    if (collected <= 3) {
      finalMessage = "¡Felicidades! Ganaste.\nSiempre serás bienvenido en Sweet Melt.";
    } else if (collected <= 7) {
      finalMessage = `¡Felicidades! Ganaste.\nReclama tu regalito con el código ${code} en Sweet Melt.`;
    } else if (collected <= 10) {
      finalMessage = `¡Felicidades! Ganaste.\nCupón de descuento: ${code} en Sweet Melt.`;
    } else {
      finalMessage = `¡Impresionante! ¡Recogiste todos los chocolates!\nReclama uno de verdad con el código ${code} en Sweet Melt.`;
    }

    isPaused = true; // ✅ Ahora sí, solo se llama cuando realmente se gana
  }
}

function resetGame() {
  finalMessage = "";
  cameraX = 0;
  Object.assign(player, {
    x: 50, y: 300, dx: 0, dy: 0,
    lives: 3, invulnerable: false, invulnTimer: 0
  });
  coins.forEach(c => c.collected = false);
  enemies.forEach(e => {
    e.alive = true;
    if (e.vx) e.x = e.minX;
  });
  projectiles = [];
  ["ArrowRight", "ArrowLeft", "ArrowUp", "Space", "Escape"].forEach(k => keys[k] = false);
}

function update() {
  if (isPaused) return;

  player.dx = 0;
  if (keys["ArrowRight"]) player.dx = 2;
  if (keys["ArrowLeft"]) player.dx = -2;

  if ((keys["Space"] || keys["ArrowUp"])) {
    if (player.grounded && !player.jump) {
      player.dy = -9;
      player.jump = true;
      player.jumpTimer = 0;
    } else if (player.jump && player.jumpTimer < 15) {
      player.dy -= 0.4;
      player.jumpTimer++;
    }
  } else {
    player.jump = false;
  }

  applyPhysics();
  checkCollisions();

  enemies.forEach(e => {
    if (!e.alive || !e.vx) return;
    e.x += e.vx;
    if (e.x < e.minX || e.x + e.width > e.maxX) e.vx *= -1;
  });

  projectiles.forEach(p => {
    p.x += p.dx;
    p.y += p.dy;
  });

  if (player.invulnerable) {
    player.invulnTimer--;
    if (player.invulnTimer <= 0) {
      player.invulnerable = false;
    }
  } 
}

function draw() {
  // Fondo celeste
  ctx.fillStyle = "#87CEEB";
  ctx.fillRect(0, 0, canvas.width, canvas.height);

  drawGround();
  drawPlatforms();
  drawGoal();
  drawCoins();
  drawEnemies();
  drawPlayer();
  drawProjectiles();
  drawCoinCounter();
  drawLives();

  if (isPaused) {
    ctx.fillStyle = "rgba(0,0,0,0.5)";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = "white";
    ctx.font = "30px sans-serif";
    ctx.textAlign = "center";
    ctx.fillStyle = "white";
    ctx.font = "30px sans-serif";
    ctx.textAlign = "center";

    if (finalMessage) {
      finalMessage.split("\n").forEach((line, i) => {
        ctx.fillText(line, canvas.width / 2, canvas.height / 2 + i * 40);
      });
    } else {
      ctx.fillText("PAUSA", canvas.width / 2, canvas.height / 2);
    }
  }

  if (isPaused && finalMessage) {
    // Dibuja el botón
    const btnX = canvas.width / 2 - 75;
    const btnY = canvas.height / 2 + 100;
    const btnWidth = 150;
    const btnHeight = 40;

    // Fondo del botón
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(btnX, btnY, btnWidth, btnHeight);

    // Borde
    ctx.strokeStyle = "#000000";
    ctx.strokeRect(btnX, btnY, btnWidth, btnHeight);

    // Texto
    ctx.fillStyle = "#000000";
    ctx.font = "20px sans-serif";
    ctx.textAlign = "center";
    ctx.fillText("Reiniciar", canvas.width / 2, btnY + 27);
  } 
}

function gameLoop() {
  update();
  draw();
  requestAnimationFrame(gameLoop);
}

document.addEventListener("keydown", e => {
  keys[e.key] = true;
  if (e.key === "Escape") togglePause();
});
document.addEventListener("keyup", e => keys[e.key] = false);
window.addEventListener("keydown", function(e) {
    // Si el juego está activo, prevenir scroll con flechas y barra espaciadora
    if (!isPaused && ["ArrowUp", "ArrowDown", "ArrowLeft", "ArrowRight", " "].includes(e.key)) {
        e.preventDefault();
    }
}, { passive: false });

function startGame() {
  isPaused = false;
  menu.style.display = "none"; // OCULTA EL MENÚ
  finalMessage = "";
  backgroundMusic.play();
}

function reanudarJuego() {
  if (finalMessage) {
    resetGame();
    startGame();
  } else {
    isPaused = false;
    menu.style.display = "none";
    backgroundMusic.play();
  }
}

// Opcional: cambia el texto del botón según el estado
function updateMenuButtons() {
  const btnReanudar = document.getElementById('btn-reanudar');
  if (finalMessage) {
    btnReanudar.textContent = "Reiniciar";
  } else {
    btnReanudar.textContent = "Reanudar";
  }
}

// Llama a updateMenuButtons() cada vez que muestres el menú
function showMenu() {
  isPaused = true;
  menu.style.display = "flex"; // MUESTRA EL MENÚ
  updateMenuButtons();
}
function togglePause() {
  isPaused = !isPaused;
  menu.style.display = isPaused ? "flex" : "none";
  if (isPaused) updateMenuButtons();
}

canvas.addEventListener("click", e => {
  if (!isPaused || !finalMessage) return;

  const rect = canvas.getBoundingClientRect();
  const clickX = e.clientX - rect.left;
  const clickY = e.clientY - rect.top;

  const btnX = canvas.width / 2 - 75;
  const btnY = canvas.height / 2 + 100;
  const btnWidth = 150;
  const btnHeight = 40;

  const insideButton =
    clickX >= btnX && clickX <= btnX + btnWidth &&
    clickY >= btnY && clickY <= btnY + btnHeight;

  if (insideButton) {
    resetGame();
    startGame();
  }
});

// Iniciar el bucle del juego al cargar la página
gameLoop();
showMenu();


