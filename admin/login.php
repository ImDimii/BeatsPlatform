<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Se l'utente è già loggato come admin, redirect alla dashboard
if (isAdmin()) {
    header('Location: index.php');
    exit;
}

$error = '';

// Gestione del form di login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch();
        
        if ($user) {
            login($username, true);
            header('Location: index.php');
            exit;
        } else {
            $error = 'Username o password non validi';
        }
    } catch (PDOException $e) {
        error_log("Errore nel login: " . $e->getMessage());
        $error = 'Errore durante il login';
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Beats Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold">Admin Login</h1>
            <p class="text-gray-400 mt-2">Accedi all'area amministrativa</p>
        </div>

        <?php if ($error): ?>
        <div class="bg-red-500 text-white px-4 py-3 rounded mb-6">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="bg-gray-800 rounded-lg p-8 shadow-lg">
            <div class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-400 mb-2">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="username" name="username" required
                               class="w-full pl-10 pr-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white"
                               placeholder="Inserisci username">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-400 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required
                               class="w-full pl-10 pr-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white"
                               placeholder="Inserisci password">
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition duration-300">
                    Accedi
                </button>
            </div>
        </form>

        <div class="text-center mt-6">
            <a href="../" class="text-purple-400 hover:text-purple-300">
                <i class="fas fa-arrow-left mr-2"></i>Torna al sito
            </a>
        </div>
    </div>
</body>
</html> 
