<?php
// Logjika e lojës në PHP
$state = $_POST['state'] ?? 'start'; // Shteti: start, door_chosen, riddle_answered
$door = strtolower(trim($_POST['door'] ?? ''));
$riddle_answer = trim($_POST['riddle_answer'] ?? '');
$message = '';
$is_good = false;
$show_riddle = false;
$error = '';
$show_doors = true; // Kontroll për të shfaqur derat

if ($_POST) {
    if ($state === 'door_chosen' && $door) {
        if ($door === 'kuqe') {
            $message = '🚫 Pas derës së kuqe ka një përbindësh kozmik! Loja mbaroi. 💀';
            $is_good = false;
            $state = 'result';
            $show_doors = false;
        } elseif ($door === 'blu') {
            $message = '🚫 Pas derës blu ka një kurth hapësinor! Loja mbaroi. 🌌';
            $is_good = false;
            $state = 'result';
            $show_doors = false;
        } elseif ($door === 'gjelber') {
            $show_riddle = true;
            $state = 'riddle';
            $show_doors = false; // Fsheh derat kur shfaqet enigma
        } else {
            $error = '❓ Zgjedhje e pavlefshme. Provo një derë tjetër!';
            $state = 'start';
        }
    } elseif ($state === 'riddle_answered' && $riddle_answer !== '') {
        // Krahasim string për emrin (case-insensitive)
        $answer = strtolower($riddle_answer);
        if ($answer === 'xhoi') {
            $message = '✅ Saktë! Xhoi do të ishte krenar – je i lirë në univers! 🌟✨';
            $is_good = true;
            $state = 'result';
            $show_doors = false;
        } else {
            $message = '❌ Gabim! Nuk është Xhoi. Provo përsëri enigmën.';
            $is_good = false;
            $show_riddle = true;
            $state = 'riddle';
            $show_doors = true; // Rivendos derat për provë tjetër (opsionale)
        }
    }
    
    // Nëse "Luaj Përsëri", rivendos gjithçka
    if (isset($_POST['reset'])) {
        $state = 'start';
        $message = '';
        $show_riddle = false;
        $error = '';
        $show_doors = true;
    }
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dhomën e Misterit – Edicioni Kozmik (PHP)</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Creepster&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-dark: #0a0a23;
      --accent-red: #ff4757;
      --accent-yellow: #ffa502;
      --accent-blue: #3742fa;
      --glow: rgba(255, 255, 255, 0.3);
      --text: #fff;
    }
    body {
      margin: 0; padding: 0; height: 100vh; font-family: 'Montserrat', sans-serif;
      background: linear-gradient(135deg, #0a0a23, #1a1a3a);
      color: var(--text); display: flex; justify-content: center; align-items: center;
      overflow: hidden; position: relative;
    }
    /* Yjet kozmikë në sfond për efekt misterioz */
    body::before {
      content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
      background: radial-gradient(1px 1px at 20% 30%, #fff, transparent),
                  radial-gradient(1px 1px at 80% 80%, rgba(255,255,255,0.5), transparent),
                  radial-gradient(2px 2px at 40% 50%, #fff, transparent);
      background-repeat: repeat; background-size: 200px 150px;
      animation: stars 20s linear infinite;
    }
    @keyframes stars { from { transform: translateY(0); } to { transform: translateY(-100px); } }
    
    .container {
      text-align: center; max-width: 500px; padding: 20px; z-index: 1;
      background: rgba(0,0,0,0.5); border-radius: 20px; backdrop-filter: blur(10px);
      box-shadow: 0 0 30px var(--glow); border: 1px solid rgba(255,255,255,0.1);
    }
    .title {
      font-family: 'Creepster', cursive; font-size: 3em; margin-bottom: 20px;
      background: linear-gradient(45deg, var(--accent-red), var(--accent-yellow), var(--accent-blue));
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
      text-shadow: 0 0 20px var(--glow); animation: glow 2s ease-in-out infinite alternate;
    }
    @keyframes glow { from { text-shadow: 0 0 20px var(--glow); } to { text-shadow: 0 0 30px var(--glow); } }
    
    .doors {
      display: flex; justify-content: space-around; margin: 30px 0; flex-wrap: wrap;
    }
    .door-btn {
      padding: 15px 25px; margin: 10px; border: none; border-radius: 15px; cursor: pointer;
      font-size: 1.2em; font-weight: 700; color: var(--text); transition: all 0.3s ease;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3); min-width: 120px;
    }
    .door-red { background: linear-gradient(135deg, var(--accent-red), #ff6b7a); }
    .door-yellow { background: linear-gradient(135deg, var(--accent-yellow), #ffcc02); }
    .door-blue { background: linear-gradient(135deg, var(--accent-blue), #5a67d8); }
    .door-btn:hover:not(:disabled) { transform: scale(1.05); box-shadow: 0 8px 25px var(--glow); }
    .door-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
    
    .input-section { margin: 20px 0; display: <?php echo $show_riddle ? 'block' : 'none'; ?>; }
    input[type="text"] {
      padding: 10px; border: none; border-radius: 10px; font-size: 1em; width: 200px;
      background: rgba(255,255,255,0.1); color: var(--text); text-align: center;
    }
    .submit-btn { padding: 10px 20px; background: var(--accent-yellow); color: #000; border: none; border-radius: 10px; cursor: pointer; margin-top: 10px; }
    .submit-btn:hover { background: #ffcc02; }
    
    .result {
      margin: 20px 0; padding: 20px; border-radius: 15px; font-size: 1.5em; opacity: 0;
      animation: fadeIn 1s ease forwards; min-height: 100px; display: flex; align-items: center; justify-content: center;
      display: <?php echo $message ? 'flex' : 'none'; ?>;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .result.good { background: linear-gradient(135deg, #10b981, #34d399); color: #000; }
    .result.bad { background: linear-gradient(135deg, var(--accent-red), #ff6b7a); }
    .result.error { background: linear-gradient(135deg, #6b7280, #9ca3af); }
    
    .play-again { 
      padding: 10px 20px; background: var(--accent-blue); color: var(--text); border: none; border-radius: 10px; cursor: pointer; margin-top: 20px; 
      display: <?php echo $message ? 'block' : 'none'; ?>; 
    }
    .play-again:hover { background: #5a67d8; }
    
    .error { color: #ff6b7a; font-size: 1.1em; margin: 10px 0; display: <?php echo $error ? 'block' : 'none'; ?>; }
    
    /* Particle efekte për dera */
    .particle { position: absolute; width: 4px; height: 4px; background: #fff; border-radius: 50%; pointer-events: none; }
  </style>
</head>
<body>
  <div class="container">
    <h1 class="title">👻 Dhomën e Misterit!</h1>
    <p style="font-size: 1.1em; margin-bottom: 20px;">Zgjidh një derë kozmike: kuqe, gjelber apo blu?</p>
    
    <?php if (($state === 'start' || $state === 'riddle' || $error) && $show_doors): ?>
    <form method="POST" id="door-form">
      <input type="hidden" name="state" value="<?php echo $state === 'riddle' ? 'riddle_answered' : 'door_chosen'; ?>">
      <div class="doors">
        <button type="submit" class="door-btn door-red" name="door" value="kuqe" <?php echo ($state !== 'start' && !$error) ? 'disabled' : ''; ?>>Derë Kuqe</button>
        <button type="submit" class="door-btn door-yellow" name="door" value="gjelber" <?php echo ($state !== 'start' && !$error) ? 'disabled' : ''; ?>>Derë Gjelbër</button>
        <button type="submit" class="door-btn door-blue" name="door" value="blu" <?php echo ($state !== 'start' && !$error) ? 'disabled' : ''; ?>>Derë Blu</button>
      </div>
    </form>
    <?php endif; ?>
    
    <div class="input-section" id="riddle-section">
      <p>Pas derës së gjelbër ka një enigmë kozmike...</p>
      <p style="font-style: italic;">Kush ishte shqiptari i parë në hënë?</p>
      <form method="POST" id="riddle-form">
        <input type="hidden" name="state" value="riddle_answered">
        <input type="text" name="riddle_answer" id="riddle-input" placeholder="Shkruaj emrin..." required>
        <button type="submit" class="submit-btn">Përgjigju!</button>
      </form>
    </div>
    
    <?php if ($error): ?>
    <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($message): ?>
    <div class="result <?php echo $is_good ? 'good' : 'bad'; ?>"><?php echo $message; ?></div>
    <form method="POST">
      <input type="hidden" name="reset" value="1">
      <button type="submit" class="play-again">Luaj Përsëri! 🔄</button>
    </form>
    <?php endif; ?>
  </div>

  <script>
    // JS për efekte (particle dhe tingull)
    document.querySelectorAll('.door-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        // Tingull i lehtë (beep)
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSyBzvLYiTcIGWi77eedTRAMUKfj8LZvHAc4kdPy7HksBSR3x/DdkEAKF');
        audio.play().catch(() => {});
        
        // Efekt particle
        createParticles(this);
      });
    });
    
    function createParticles(element) {
      for (let i = 0; i < 10; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = (element.offsetLeft + Math.random() * 100) + 'px';
        particle.style.top = (element.offsetTop + Math.random() * 50) + 'px';
        particle.style.animation = `stars 2s linear infinite`;
        document.body.appendChild(particle);
        setTimeout(() => particle.remove(), 2000);
      }
    }
    
    // Auto-focus në input enigma nëse shfaqet
    <?php if ($show_riddle): ?>
    document.getElementById('riddle-input').focus();
    <?php endif; ?>
  </script>
</body>
</html>
