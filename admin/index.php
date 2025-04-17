<?php
require_once '../includes/auth.php';
require_once '../includes/ContactMessage.php';
require_once '../includes/Track.php';

// Verifica che l'utente sia admin
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

// Ottieni le statistiche
$messages = new ContactMessage();
$track = new Track();

$totalMessages = $messages->getTotalMessages();
$newMessages = $messages->getNewMessagesCount();
$trackStats = $track->getTrackStats();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <?php require_once 'includes/admin-header.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Dashboard</h1>

            <!-- Statistiche Principali -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gray-800 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-600 rounded-lg">
                            <i class="fas fa-music text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-400">Tracce Totali</p>
                            <p class="text-2xl font-semibold"><?php echo number_format($trackStats['total_tracks']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-600 rounded-lg">
                            <i class="fas fa-play text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-400">Riproduzioni Totali</p>
                            <p class="text-2xl font-semibold"><?php echo number_format($trackStats['total_plays']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-600 rounded-lg">
                            <i class="fas fa-envelope text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-400">Messaggi Totali</p>
                            <p class="text-2xl font-semibold"><?php echo number_format($totalMessages); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-600 rounded-lg">
                            <i class="fas fa-bell text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-400">Nuovi Messaggi</p>
                            <p class="text-2xl font-semibold"><?php echo number_format($newMessages); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiche Dettagliate -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Statistiche Interazioni</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-thumbs-up text-green-400 text-xl mr-3"></i>
                                <span>Like Totali</span>
                            </div>
                            <span class="text-xl font-semibold"><?php echo number_format($trackStats['total_likes']); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-thumbs-down text-red-400 text-xl mr-3"></i>
                                <span>Dislike Totali</span>
                            </div>
                            <span class="text-xl font-semibold"><?php echo number_format($trackStats['total_dislikes']); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-comments text-blue-400 text-xl mr-3"></i>
                                <span>Commenti Totali</span>
                            </div>
                            <span class="text-xl font-semibold"><?php echo number_format($trackStats['total_comments']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Statistiche Contenuti</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-guitar text-purple-400 text-xl mr-3"></i>
                                <span>Generi Musicali</span>
                            </div>
                            <span class="text-xl font-semibold"><?php echo number_format($trackStats['total_genres']); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-crown text-yellow-400 text-xl mr-3"></i>
                                <span>Genere più Popolare</span>
                            </div>
                            <span class="text-xl font-semibold"><?php echo htmlspecialchars($trackStats['most_popular_genre']); ?></span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-music text-pink-400 text-xl mr-3"></i>
                                <span>Chiave più Usata</span>
                            </div>
                            <span class="text-xl font-semibold"><?php echo htmlspecialchars($trackStats['most_used_key']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Azioni Rapide -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Azioni Rapide</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="tracks.php" class="flex items-center p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                            <i class="fas fa-plus mr-3"></i>
                            <span>Nuova Traccia</span>
                        </a>
                        <a href="settings.php" class="flex items-center p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                            <i class="fas fa-cog mr-3"></i>
                            <span>Impostazioni</span>
                        </a>
                        <a href="messages.php" class="flex items-center p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                            <i class="fas fa-envelope mr-3"></i>
                            <span>Messaggi</span>
                        </a>
                        <a href="../" target="_blank" class="flex items-center p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                            <i class="fas fa-external-link-alt mr-3"></i>
                            <span>Vedi Sito</span>
                        </a>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Ultimi Messaggi</h2>
                    <div class="space-y-4">
                        <?php 
                        $latestMessages = $messages->getAllMessages(1, 3);
                        if (!empty($latestMessages)): 
                            foreach ($latestMessages as $msg):
                        ?>
                        <div class="flex items-start space-x-4 p-4 bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium">
                                    <?php echo htmlspecialchars($msg['name']); ?>
                                    <span class="text-gray-400 ml-2"><?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?></span>
                                </p>
                                <p class="text-sm text-gray-400 truncate">
                                    <?php echo htmlspecialchars($msg['message']); ?>
                                </p>
                            </div>
                        </div>
                        <?php 
                            endforeach;
                        else:
                        ?>
                        <p class="text-gray-400 text-center py-4">Nessun messaggio</p>
                        <?php endif; ?>
                        
                        <a href="messages.php" class="block text-center text-purple-400 hover:text-purple-300 mt-4">
                            Vedi tutti i messaggi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 