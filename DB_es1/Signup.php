<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
    
</head>
<body>
    <h1>Registrazione</h1>
    <form action="registrazione.php" method="POST" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm-password">Conferma Password</label>
            <input type="password" id="confirm-password" name="confirm_password" required>
        </div>
        <div class="form-group">
            <button type="submit">Registrati</button>
        </div>
    </form>
    
    <div class="login-link">
        <p>Hai già un account? <a href="signin.php">Accedi</a></p>
        <p>torna all'<a href="auth.html">inizio</a></p>
        <p>torna all'<a href="../index.html">indice generale</a></p>
    </div>
    <script>
        
    </script>
</body>
</html>
