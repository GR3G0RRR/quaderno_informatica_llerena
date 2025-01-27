<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Accesso</h1>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <button type="submit">Accedi</button>
        </div>
    </form>
    
    <div class="registration-link">
        <p>Non hai un account? <a href="Signup.php">Registrati</a></p>
    </div>
    <p>torna all'<a href="auth.html">inizio</a></p>
    <p>torna all'<a href="../index.html">indice generale</a></p>
</body>
</html>
