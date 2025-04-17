<?php
require_once '../includes/auth.php';
require_once '../includes/Settings.php';

// Verifica che l'utente sia admin
if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$settings = Settings::getInstance();
$message = '';

// Gestione del form di aggiornamento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settingsToUpdate = [
        'site_title',
        'contact_email',
        'contact_phone',
        'contact_address',
        'social_instagram',
        'social_facebook',
        'social_youtube',
        'social_soundcloud'
    ];

    foreach ($settingsToUpdate as $key) {
        if (isset($_POST[$key])) {
            $settings->update($key, $_POST[$key]);
        }
    }
    
    $message = 'Impostazioni aggiornate con successo!';
}

$currentSettings = $settings->getAllSettings();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Impostazioni - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <?php require_once 'includes/admin-header.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Gestione Impostazioni</h1>

            <?php if ($message): ?>
            <div class="bg-green-500 text-white px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="bg-gray-800 rounded-lg p-6 space-y-6">
                <!-- Impostazioni Generali -->
                <div>
                    <h2 class="text-xl font-semibold mb-4">Impostazioni Generali</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Titolo del Sito</label>
                            <input type="text" name="site_title" 
                                   value="<?php echo htmlspecialchars($currentSettings['site_title'] ?? ''); ?>"
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                        </div>
                    </div>
                </div>

                <!-- Informazioni di Contatto -->
                <div>
                    <h2 class="text-xl font-semibold mb-4">Informazioni di Contatto</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                            <input type="email" name="contact_email"
                                   value="<?php echo htmlspecialchars($currentSettings['contact_email'] ?? ''); ?>"
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Telefono</label>
                            <input type="text" name="contact_phone"
                                   value="<?php echo htmlspecialchars($currentSettings['contact_phone'] ?? ''); ?>"
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Indirizzo</label>
                            <input type="text" name="contact_address"
                                   value="<?php echo htmlspecialchars($currentSettings['contact_address'] ?? ''); ?>"
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div>
                    <h2 class="text-xl font-semibold mb-4">Social Media</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">
                                <i class="fab fa-instagram mr-2"></i>Instagram URL
                            </label>
                            <input type="url" name="social_instagram"
                                   value="<?php echo htmlspecialchars($currentSettings['social_instagram'] ?? ''); ?>"
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">
                                <i class="fab fa-facebook mr-2"></i>Facebook URL
                            </label>
                            <input type="url" name="social_facebook"
                                   value="<?php echo htmlspecialchars($currentSettings['social_facebook'] ?? ''); ?>"
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">
                                <i class="fab fa-youtube mr-2"></i>YouTube URL
                            </label>
                            <input type="url" name="social_youtube"
                                   value="<?php echo htmlspecialchars($currentSettings['social_youtube'] ?? ''); ?>"
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">
                                <i class="fab fa-soundcloud mr-2"></i>SoundCloud URL
                            </label>
                            <input type="url" name="social_soundcloud"
                                   value="<?php echo htmlspecialchars($currentSettings['social_soundcloud'] ?? ''); ?>"
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition duration-300">
                        Salva Impostazioni
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html> 