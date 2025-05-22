<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Coming Soon</title>
  <link rel="stylesheet" href="assets/css/coming-soon.css">
</head>
<body>
  <div class="coming-soon-container">
    <h1>Coming Soon</h1>

    <div class="circle-progress">
      <svg viewBox="0 0 100 100">
        <circle class="bg" cx="50" cy="50" r="45"></circle>
        <circle class="progress" cx="50" cy="50" r="45"></circle>
        <text id="percent-text" x="50%" y="50%" text-anchor="middle" dy=".3em">0%</text>
      </svg>
    </div>

    <div id="launch-message" class="hidden">🚀 Launching Soon!</div>
    <div id="countdown" class="hidden">00d 00h 00m 00s</div>
    <button id="back-button" class="back-button hidden">← Back</button>
  </div>

  <script>
    const progressCircle = document.querySelector('.progress');
    const percentText = document.getElementById('percent-text');
    const launchMessage = document.getElementById('launch-message');
    const countdownEl = document.getElementById('countdown');
    const backButtonHistory = document.getElementById('back-button');

    const radius = 45;
    const totalLength = 2 * Math.PI * radius;
    progressCircle.style.strokeDasharray = totalLength;

    const maxPercent = 95;
    let currentPercent = 0;

    const launchDate = new Date("2026-06-01T00:00:00").getTime();

    function animateTo95() {
      const intervalTime = 20; // ms
      const increment = 1; // % per step

      const interval = setInterval(() => {
        currentPercent += increment;

        if (currentPercent >= maxPercent) {
          clearInterval(interval);
          currentPercent = maxPercent;

          // Show launch message and start countdown
          launchMessage.classList.remove("hidden");
          countdownEl.classList.remove("hidden");
          backButtonHistory.classList.remove("hidden");
          startCountdown();
        }

        const offset = totalLength * (1 - currentPercent / 100);
        progressCircle.style.strokeDashoffset = offset;
        percentText.textContent = `${currentPercent}%`;
      }, intervalTime);
    }

    function startCountdown() {
      setInterval(() => {
        const now = new Date().getTime();
        const distance = launchDate - now;

        if (distance < 0) {
          countdownEl.textContent = "🎉 Launched!";
          return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        countdownEl.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
      }, 1000);
    }

    window.addEventListener('load', animateTo95);

    document.getElementById('back-button').addEventListener('click', () => {
      history.back();
    });
  </script>
</body>
</html>
