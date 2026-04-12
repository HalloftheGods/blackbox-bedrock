(function () {
  // Do not inject the smoke canvas if we are inside a Compass sub-app iframe
  if (window.name === "blackbox-sub-app") return;

  if (window !== window.top && window.location.search.includes("theme=transparent")) {
    // We are in an iframe, and transparent!
  }
  if (document.getElementById("blackbox-smoke-canvas")) return;
  const canvas = document.createElement("canvas");
  canvas.id = "blackbox-smoke-canvas";
  canvas.style.position = "fixed";
  canvas.style.top = "0";
  canvas.style.left = "0";
  canvas.style.width = "100vw";
  canvas.style.height = "100vh";
  canvas.style.pointerEvents = "none";
  canvas.style.zIndex = "0";
  canvas.style.opacity = "0.8";

  const waves = [
    {
      y: 0.48,
      amplitude: 45,
      wavelength: 700,
      speed: 0.0002,
      offset: 0,
      color: "rgba(220, 230, 240, 0.20)",
      blur: 5,
      thickness: 100,
      scales: [1.0, 2.3, 0.7, 0.2],
      drifts: [1.0, 1.5, 0.8, 0.2],
      thickScales: [0.5, 1.2, 0.3],
      thickDrifts: [0.8, 0.4, 1.1]
    },
    {
      y: 0.48,
      amplitude: 35,
      wavelength: 900,
      speed: -0.0004,
      offset: Math.PI,
      color: "rgba(200, 210, 220, 0.17)",
      blur: 5,
      thickness: 175,
      scales: [1.2, 1.8, 0.5, 0.3],
      drifts: [0.9, 1.7, 0.6, 0.4],
      thickScales: [0.7, 0.9, 0.4],
      thickDrifts: [1.2, 0.5, 0.8]
    },
    {
      y: 0.55,
      amplitude: 28,
      wavelength: 1200,
      speed: 0.0004,
      offset: Math.PI / 2,
      color: "rgba(255, 255, 255, 0.55)",
      blur: 3,
      thickness: 15,
      scales: [0.8, 2.7, 1.1, 0.1],
      drifts: [1.1, 1.3, 1.2, 0.1],
      thickScales: [0.4, 1.5, 0.2],
      thickDrifts: [0.6, 0.9, 1.3],
      isRibbon: true
    },
    {
      y: 0.48,
      amplitude: 40,
      wavelength: 800,
      speed: -0.0003,
      offset: Math.PI * 1.5,
      color: "rgba(180, 190, 200, 0.17)",
      blur: 5,
      thickness: 250,
      scales: [1.5, 1.2, 0.9, 0.4],
      drifts: [0.7, 2.0, 0.5, 0.3],
      thickScales: [0.6, 0.8, 0.5],
      thickDrifts: [1.0, 0.3, 0.7]
    }
  ];

  const particles = [];
  const maxParticles = 160;

  function createParticle(x, y) {
    return {
      x,
      orbitRadiusOffset: (Math.random() - 0.5) * 35,
      orbitAngleOffset: (Math.random() - 0.5) * 0.5,
      size: Math.random() * 0.5 + 0.8,
      speedX: 0.15 + Math.random() * 0.5,
      flickerSpeed: Math.random() * 0.0005 + 0.002,
      flickerPhase: Math.random() * Math.PI * 2,
      orbitPhase: 0
    };
  }

  function getWaveY(wave, x, time, height) {
    const baseline = height * wave.y;
    const t = time * wave.speed + wave.offset;
    const relX = x / wave.wavelength;
    const w1 = Math.sin(relX * wave.scales[0] + t * wave.drifts[0]);
    const w2 = Math.sin(relX * wave.scales[1] + t * wave.drifts[1]) * 0.5;
    const w3 = Math.sin(relX * wave.scales[2] - t * wave.drifts[2]) * 0.3;
    const w4 = Math.sin(t * wave.scales[3]) * wave.drifts[3];
    return baseline + (w1 + w2 + w3 + w4) * wave.amplitude;
  }

  function getWaveThickness(wave, x, time) {
    const t = time * wave.speed + wave.offset;
    const relX = x / wave.wavelength;
    const th1 = Math.sin(relX * wave.thickScales[0] + t * wave.thickDrifts[0]);
    const th2 = Math.sin(relX * wave.thickScales[1] + t * wave.thickDrifts[1]) * 0.4;
    const th3 = Math.sin(t * wave.thickScales[2]) * wave.thickDrifts[2];
    return wave.thickness + (th1 + th2 + th3) * (wave.amplitude * 0.6);
  }

  function initCanvas() {
    // Don't inject if visual editor wrapper isn't present
    document.body.prepend(canvas);
    const ctx = canvas.getContext("2d");

    function resize() {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    }
    window.addEventListener("resize", resize);
    resize();

    const half = maxParticles / 2;
    const step = canvas.width / half;
    for (let i = 0; i < half; i++) {
      const x = i * step;
      particles.push(createParticle(x, 0));
      const p2 = createParticle(x, 0);
      p2.speedX *= -1;
      p2.orbitPhase = Math.PI;
      particles.push(p2);
    }

    function animate(time) {
      if (!ctx) return;
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      waves.forEach((wave) => {
        const baseline = canvas.height * wave.y;
        ctx.beginPath();
        ctx.moveTo(0, baseline);
        for (let x = 0; x <= canvas.width; x += 10) {
          ctx.lineTo(x, getWaveY(wave, x, time, canvas.height));
        }
        for (let x = canvas.width; x >= 0; x -= 10) {
          const t = time * wave.speed + wave.offset;
          const th1 = Math.sin(
            (x / wave.wavelength) * wave.thickScales[0] + t * wave.thickDrifts[0]
          );
          const th2 =
            Math.sin((x / wave.wavelength) * wave.thickScales[1] + t * wave.thickDrifts[1]) * 0.4;
          const th3 = Math.sin(t * wave.thickScales[2]) * wave.thickDrifts[2];
          const currentThickness = wave.thickness + (th1 + th2 + th3) * (wave.amplitude * 0.6);
          ctx.lineTo(x, getWaveY(wave, x, time, canvas.height) + currentThickness);
        }
        ctx.closePath();

        const grad = ctx.createLinearGradient(0, 0, canvas.width, 0);
        grad.addColorStop(0, "rgba(200, 210, 220, 0.05)");
        grad.addColorStop(0.2, "rgba(215, 225, 235, 0.10)");
        grad.addColorStop(0.4, "rgba(215, 225, 235, 0.18)");
        grad.addColorStop(0.75, wave.color);
        grad.addColorStop(1, "rgba(200, 210, 220, 0.05)");
        ctx.fillStyle = grad;
        if (wave.blur > 0) ctx.filter = `blur(${wave.blur}px)`;
        ctx.fill();
        ctx.filter = "none";
      });

      const ribbonWave = waves.find((w) => w.isRibbon);
      if (ribbonWave) {
        ctx.save();
        ctx.shadowBlur = 1;
        ctx.shadowColor = "white";
        for (let i = 0; i < particles.length; i++) {
          const p = particles[i];
          p.x += p.speedX;
          if (p.x > canvas.width) p.x = 0;
          if (p.x < 0) p.x = canvas.width;
          const finalAngle = p.x * 0.008 + time * 0.0003 + p.orbitPhase + p.orbitAngleOffset;
          const volThick = getWaveThickness(ribbonWave, p.x, time);
          const centerY = getWaveY(ribbonWave, p.x, time, canvas.height) + volThick / 2;
          const currentY =
            centerY + Math.sin(finalAngle) * (volThick / 2 + 55 + p.orbitRadiusOffset);
          const depthFactor = (Math.cos(finalAngle) + 1) / 2;
          const flicker = 0.5 + Math.sin(time * p.flickerSpeed + p.flickerPhase) * 0.5;
          ctx.beginPath();
          ctx.arc(p.x, currentY, p.size * (0.7 + depthFactor * 0.6), 0, Math.PI * 2);
          ctx.fillStyle = `rgba(255, 255, 255, ${(depthFactor * 0.7 + 0.3) * (0.8 + flicker * 0.2)})`;
          ctx.fill();
        }
        ctx.restore();
      }
      requestAnimationFrame(animate);
    }
    requestAnimationFrame(animate);
  }

  if (document.readyState === "complete" || document.readyState === "interactive") {
    initCanvas();
  } else {
    document.addEventListener("DOMContentLoaded", initCanvas);
  }
})();
