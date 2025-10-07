<?php
session_start();

// Inicializim sesioni nëse nuk ekziston
if (!isset($_SESSION['secret_number'])) {
    $_SESSION['secret_number'] = rand(1, 100);
    $_SESSION['guess_count'] = 0;
    $_SESSION['game_over'] = false;
    $_SESSION['state'] = 'start'; // start, playing, over
}

// Trajto POST requests
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'guess' && !$_SESSION['game_over']) {
        $guess = intval($_POST['guess'] ?? 0);
        if ($guess >= 1 && $guess <= 100) {
            $_SESSION['guess_count']++;
            if ($guess === $_SESSION['secret_number']) {
                $_SESSION['state'] = 'win';
                $_SESSION['game_over'] = true;
            } elseif ($guess < $_SESSION['secret_number']) {
                $_SESSION['state'] = 'higher';
            } else {
                $_SESSION['state'] = 'lower';
            }
        } else {
            $_SESSION['state'] = 'invalid';
        }
    } elseif ($action === 'quit') {
        $_SESSION['state'] = 'lose';
        $_SESSION['game_over'] = true;
    } elseif ($action === 'reset') {
        // Rivendos sesionin për lojë të re
        $_SESSION['secret_number'] = rand(1, 100);
        $_SESSION['guess_count'] = 0;
        $_SESSION['game_over'] = false;
        $_SESSION['state'] = 'start';
    }
}

// Marr variablat nga sesioni
$secretNumber = $_SESSION['secret_number'];
$guessCount = $_SESSION['guess_count'];
$gameOver = $_SESSION['game_over'];
$state = $_SESSION['state'];
$message = '';
$class = '';

if ($state === 'win') {
    $message = "🎉 URime! Ke gjetur numrin sekret: $secretNumber. E ke bërë me vetëm $guessCount hamendësime! Ti je një gjeni! 🏆";
    $class = 'win';
} elseif ($state === 'lose') {
    $message = "😢 Ke humbur! Numri sekret ishte: $secretNumber. Provo përsëri herën tjetër! 👋";
    $class = 'lose';
} elseif ($state === 'higher') {
    $lastGuess = intval($_POST['guess'] ?? 0);
    $message = "📈 Numri sekret është më i madh se $lastGuess. Provo një numër më të madh!";
    $class = 'hint';
} elseif ($state === 'lower') {
    $lastGuess = intval($_POST['guess'] ?? 0);
    $message = "📉 Numri sekret është më i vogël se $lastGuess. Provo një numër më të vogël!";
    $class = 'hint';
} elseif ($state === 'invalid') {
    $message = "❌ Hamendësimi duhet të jetë një numër midis 1 dhe 100. Provo përsëri!";
    $class = 'hint';
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gjetja e Numrit Sekret – Edicioni Kozmik (PHP)</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Creepster&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-dark: #0a0a23;
      --accent-gold: #ffd700;
      --accent-blue: #3742fa;
      --glow: rgba(255, 215, 0, 0.3);
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
      text-align: center; max-width: 400px; padding: 20px; z-index: 1;
      background: rgba(0,0,0,0.5); border-radius: 20px; backdrop-filter: blur(10px);
      box-shadow: 0 0 30px var(--glow); border: 1px solid rgba(255,255,255,0.1);
    }
    .title {
      font-family: 'Creepster', cursive; font-size: 2.5em; margin-bottom: 10px;
      background: linear-gradient(45deg, var(--accent-gold), var(--accent-blue));
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
      text-shadow: 0 0 20px var(--glow); animation: glow 2s ease-in-out infinite alternate;
    }
    @keyframes glow { from { text-shadow: 0 0 20px var(--glow); } to { text-shadow: 0 0 30px var(--glow); } }
    
    .instructions {
      font-size: 1em; margin-bottom: 20px; opacity: 0.8;
    }
    
    .input-section {
      margin: 20px 0;
    }
    input[type="number"] {
      padding: 12px; border: none; border-radius: 10px; font-size: 1.2em; width: 150px;
      background: rgba(255,255,255,0.1); color: var(--text); text-align: center;
      margin-right: 10px;
    }
    .guess-btn, .quit-btn, .reset-btn {
      padding: 12px 20px; border: none; border-radius: 10px; cursor: pointer; font-weight: 700;
      transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }
    .guess-btn {
      background: linear-gradient(135deg, var(--accent-gold), #ffed4e); color: #000;
    }
    .guess-btn:hover:not(:disabled) { transform: scale(1.05); box-shadow: 0 6px 20px var(--glow); }
    .quit-btn {
      background: linear-gradient(135deg, #ff4757, #ff6b7a); color: var(--text); margin-left: 10px;
    }
    .quit-btn:hover:not(:disabled) { transform: scale(1.05); }
    .reset-btn {
      background: var(--accent-blue); color: var(--text); margin-left: 10px; 
      opacity: <?php echo $gameOver ? '1' : '0.5'; ?>; cursor: <?php echo $gameOver ? 'pointer' : 'not-allowed'; ?>;
    }
    .reset-btn.enabled { opacity: 1; cursor: pointer; }
    .reset-btn:hover.enabled { background: #5a67d8; transform: scale(1.05); }
    .reset-btn.main { margin-top: 20px; display: <?php echo $gameOver ? 'block' : 'none'; ?>; }
    
    .output {
      margin: 20px 0; padding: 15px; border-radius: 10px; font-size: 1.1em; min-height: 50px;
      opacity: 0; transition: opacity 0.5s ease; display: <?php echo $message ? 'block' : 'none'; ?>;
    }
    .output.show { opacity: 1; }
    .hint { background: rgba(255,215,0,0.2); border: 1px solid var(--accent-gold); }
    .win { background: linear-gradient(135deg, #10b981, #34d399); color: #000; animation: bounce 0.5s ease; }
    .lose { background: linear-gradient(135deg, #ff4757, #ff6b7a); }
    @keyframes bounce { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
    
    .guess-count { font-size: 0.9em; color: var(--accent-gold); margin-top: 10px; }
    
    /* Particle efekte për hamendësime */
    .particle { position: absolute; width: 4px; height: 4px; background: var(--accent-gold); border-radius: 50%; pointer-events: none; }
    /* Konfeti për fitore */
    .confetti { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 10; }
    .confetti-piece { position: absolute; width: 10px; height: 10px; background: var(--accent-gold); animation: confetti-fall 3s linear infinite; }
    @keyframes confetti-fall { 0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; } 100% { transform: translateY(100vh) rotate(720deg); opacity: 0; } }
  </style>
</head>
<body>
  <div class="container">
    <h1 class="title">🎲 Numri Sekret!</h1>
    <p class="instructions">Unë kam zgjedhur një numër sekret midis 1 dhe 100. Hamendëso duke e shkruar! Unë do të të jap sugjerime.</p>
    
    <?php if (!$gameOver): ?>
    <form method="POST" class="input-section">
      <input type="hidden" name="action" value="guess">
      <input type="number" name="guess" min="1" max="100" placeholder="Hamendëso numrin..." required>
      <button type="submit" class="guess-btn">Hamendëso!</button>
      <button type="submit" name="action" value="quit" class="quit-btn" formnovalidate>Dorëzohem</button>
      <button type="submit" name="action" value="reset" class="reset-btn" <?php echo $gameOver ? 'enabled' : 'disabled'; ?>>Reset</button>
    </form>
    <?php else: ?>
    <div class="input-section" style="opacity: 0.5;">
      <p>Loja ka mbaruar. Përdor butonin poshtë për të luajtur përsëri.</p>
    </div>
    <?php endif; ?>
    
    <?php if ($message): ?>
    <div class="output <?php echo $class; ?> show"><?php echo $message; ?></div>
    <?php endif; ?>
    <div class="guess-count">Hamendësime: <?php echo $guessCount; ?></div>
    
    <form method="POST" style="margin-top: 20px;">
      <input type="hidden" name="action" value="reset">
      <button type="submit" class="reset-btn main enabled">Luaj Përsëri!</button>
    </form>
  </div>

  <script>
    // JS për efekte (particle dhe konfeti)
    document.querySelector('form').addEventListener('submit', function() {
      // Tingull i lehtë (beep)
      const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSyBzvLYiTcIGWi77eedTRAMUKfj8LZvHAc4kdPy7HksBSR3x/DdkEAKF');
      audio.play().catch(() => {});
      
      // Efekt particle
      createParticles();
      
      // Konfeti nëse është fitore (nga PHP state)
      <?php if ($state === 'win'): ?>
      createConfetti();
      <?php endif; ?>
    });
    
    function createParticles() {
      for (let i = 0; i < 10; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + 'vw';
        particle.style.top = Math.random() * 100 + 'vh';
        particle.style.animationDelay = Math.random() * 0.5 + 's';
        document.body.appendChild(particle);
        setTimeout(() => particle.remove(), 1000);
      }
    }
    
    function createConfetti() {
      const confetti = document.createElement('div');
      confetti.className = 'confetti';
      document.body.appendChild(confetti);
      
      for (let i = 0; i < 50; i++) {
        const piece = document.createElement('div');
        piece.className = 'confetti-piece';
        piece.style.left = Math.random() * 100 + 'vw';
        piece.style.background = `hsl(${Math.random() * 360}, 100%, 50%)`;
        piece.style.animationDelay = Math.random() * 3 + 's';
        confetti.appendChild(piece);
      }
      
      setTimeout(() => confetti.remove(), 3000);
    }
    
    // Enter key për hamendësim
    document.querySelector('input[type="number"]').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') this.form.submit();
    });
  </script>
</body>
</html>
