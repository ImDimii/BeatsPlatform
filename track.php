<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Verifica se l'ID del track è stato fornito
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$track_id = (int)$_GET['id'];

// Recupera i dettagli del track
$stmt = $pdo->prepare("
    SELECT t.*, 
           COUNT(DISTINCT c.id) as comment_count,
           COUNT(DISTINCT CASE WHEN r.reaction_type = 'like' THEN r.id END) as likes,
           COUNT(DISTINCT CASE WHEN r.reaction_type = 'dislike' THEN r.id END) as dislikes
    FROM tracks t
    LEFT JOIN comments c ON t.id = c.track_id
    LEFT JOIN reactions r ON t.id = r.track_id
    WHERE t.id = ?
    GROUP BY t.id
");
$stmt->execute([$track_id]);
$track = $stmt->fetch(PDO::FETCH_ASSOC);

// Se il track non esiste, reindirizza alla homepage
if (!$track) {
    header('Location: index.php');
    exit();
}

// Recupera la traccia precedente
$stmt = $pdo->prepare("
    SELECT id, title 
    FROM tracks 
    WHERE id < ? 
    ORDER BY id DESC 
    LIMIT 1
");
$stmt->execute([$track_id]);
$prev_track = $stmt->fetch(PDO::FETCH_ASSOC);

// Recupera la traccia successiva
$stmt = $pdo->prepare("
    SELECT id, title 
    FROM tracks 
    WHERE id > ? 
    ORDER BY id ASC 
    LIMIT 1
");
$stmt->execute([$track_id]);
$next_track = $stmt->fetch(PDO::FETCH_ASSOC);

// Recupera i commenti
$stmt = $pdo->prepare("
    SELECT c.*
    FROM comments c
    WHERE c.track_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$track_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($track['title']); ?> - Beats</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <!-- Navigazione tracce -->
        <div class="flex justify-between items-center mb-6">
            <?php if ($prev_track): ?>
            <a href="track.php?id=<?php echo $prev_track['id']; ?>" 
               class="flex items-center space-x-2 px-4 py-2 bg-gray-800 rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-chevron-left"></i>
                <span class="hidden md:inline"><?php echo htmlspecialchars($prev_track['title']); ?></span>
            </a>
            <?php else: ?>
            <div></div>
            <?php endif; ?>

            <?php if ($next_track): ?>
            <a href="track.php?id=<?php echo $next_track['id']; ?>" 
               class="flex items-center space-x-2 px-4 py-2 bg-gray-800 rounded-lg hover:bg-gray-700 transition">
                <span class="hidden md:inline"><?php echo htmlspecialchars($next_track['title']); ?></span>
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php else: ?>
            <div></div>
            <?php endif; ?>
        </div>

        <div class="max-w-4xl mx-auto">
            <!-- Dettagli del track -->
            <div class="bg-gray-800 rounded-lg p-6 mb-8">
                <div class="flex flex-col md:flex-row gap-8">
                    <!-- Copertina -->
                    <div class="w-full md:w-1/3">
                        <div class="aspect-square rounded-lg overflow-hidden bg-gray-700 mb-4">
                            <?php if($track['cover_image']): ?>
                                <img src="uploads/covers/<?php echo htmlspecialchars($track['cover_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($track['title']); ?>"
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-music text-gray-500 text-6xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Descrizione -->
                        <?php if(!empty($track['description'])): ?>
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-2">Descrizione</h3>
                            <p class="text-gray-300 text-sm whitespace-pre-wrap"><?php echo nl2br(htmlspecialchars($track['description'])); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Informazioni -->
                    <div class="w-full md:w-2/3">
                        <h1 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($track['title']); ?></h1>
                        <p class="text-xl text-gray-400 mb-4"><?php echo htmlspecialchars($track['artist']); ?></p>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <span class="text-gray-400">BPM</span>
                                <p class="text-xl font-semibold"><?php echo $track['bpm']; ?></p>
                            </div>
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <span class="text-gray-400">Key</span>
                                <p class="text-xl font-semibold"><?php echo htmlspecialchars($track['key_signature']); ?></p>
                            </div>
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <span class="text-gray-400">Genre</span>
                                <p class="text-xl font-semibold"><?php echo htmlspecialchars($track['genre']); ?></p>
                            </div>
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <span class="text-gray-400">Plays</span>
                                <p class="text-xl font-semibold plays-count"><?php echo $track['plays']; ?></p>
                            </div>
                        </div>

                        <!-- Player -->
                        <div class="bg-gray-700 p-6 rounded-lg mb-6">
                            <!-- Visualizzazione forma d'onda -->
                            <div class="mb-4 h-24 bg-gray-800 rounded-lg overflow-hidden relative" id="waveform">
                                <div class="absolute inset-0 flex items-center justify-center text-gray-600" id="waveform-loading">
                                    <i class="fas fa-spinner fa-spin text-2xl"></i>
                                </div>
                            </div>

                            <!-- Controlli Player -->
                            <div class="flex flex-col">
                                <!-- Progress bar e tempi -->
                                <div class="flex items-center space-x-3 mb-4">
                                    <span id="currentTime" class="text-sm text-gray-400 w-12 text-right">0:00</span>
                                    <div class="flex-1 h-4 relative group cursor-pointer" id="progressBarContainer">
                                        <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-800 rounded-full -translate-y-1/2">
                                            <div id="progressBar" class="h-full bg-purple-600 w-0 relative">
                                                <div class="absolute left-full top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity shadow-md transform -translate-x-1/2 hover:scale-110"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="duration" class="text-sm text-gray-400 w-12">0:00</span>
                                </div>

                                <!-- Controlli principali -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <button id="shuffleBtn" class="text-gray-400 hover:text-white transition">
                                            <i class="fas fa-random"></i>
                                        </button>
                                        <?php if ($prev_track): ?>
                                        <a href="track.php?id=<?php echo $prev_track['id']; ?>" id="prevTrack" class="text-gray-400 hover:text-white transition">
                                            <i class="fas fa-step-backward"></i>
                                        </a>
                                        <?php else: ?>
                                        <button disabled class="text-gray-600 cursor-not-allowed">
                                            <i class="fas fa-step-backward"></i>
                                        </button>
                                        <?php endif; ?>
                                        <button id="playPauseBtn" class="w-12 h-12 rounded-full bg-purple-600 hover:bg-purple-700 transition flex items-center justify-center">
                                            <i class="fas fa-play"></i>
                                        </button>
                                        <?php if ($next_track): ?>
                                        <a href="track.php?id=<?php echo $next_track['id']; ?>" id="nextTrack" class="text-gray-400 hover:text-white transition">
                                            <i class="fas fa-step-forward"></i>
                                        </a>
                                        <?php else: ?>
                                        <button disabled class="text-gray-600 cursor-not-allowed">
                                            <i class="fas fa-step-forward"></i>
                                        </button>
                                        <?php endif; ?>
                                        <button id="repeatBtn" class="text-gray-400 hover:text-white transition">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button id="volumeBtn" class="text-gray-400 hover:text-white transition">
                                            <i class="fas fa-volume-up"></i>
                                        </button>
                                        <div class="w-24 h-1 bg-gray-800 rounded-full overflow-hidden">
                                            <div id="volumeBar" class="h-full bg-purple-600 w-full"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reazioni e Azioni -->
                        <div class="flex items-center space-x-4">
                            <button onclick="handleReaction(<?php echo $track['id']; ?>, 'like')" 
                                    class="flex items-center space-x-2 px-6 py-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                                <i class="fas fa-thumbs-up"></i>
                                <span><?php echo $track['likes']; ?></span>
                            </button>
                            <button onclick="handleReaction(<?php echo $track['id']; ?>, 'dislike')" 
                                    class="flex items-center space-x-2 px-6 py-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition">
                                <i class="fas fa-thumbs-down"></i>
                                <span><?php echo $track['dislikes']; ?></span>
                            </button>
                            <a href="uploads/tracks/<?php echo htmlspecialchars($track['file_path']); ?>" download 
                               class="flex items-center space-x-2 px-6 py-3 bg-green-600 hover:bg-green-700 rounded-lg transition">
                                <i class="fas fa-download"></i>
                                <span>Download</span>
                            </a>
                            
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sezione commenti -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Commenti (<?php echo count($comments); ?>)</h2>
                
                <!-- Form per nuovo commento -->
                <div class="mb-8">
                    <div class="flex gap-4 mb-4">
                        <div class="flex-1">
                            <label for="username" class="block text-sm font-medium text-gray-400 mb-2">Nome (opzionale)</label>
                            <input type="text" id="username" 
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white" 
                                   placeholder="guest">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="comment-input" class="block text-sm font-medium text-gray-400 mb-2">Il tuo commento</label>
                        <textarea id="comment-input" 
                                class="w-full px-4 py-3 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500" 
                                placeholder="Scrivi un commento..." 
                                rows="3"></textarea>
                    </div>
                    <button onclick="submitComment(<?php echo $track['id']; ?>)" 
                            class="px-6 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg transition">
                        Invia
                    </button>
                </div>

                <!-- Lista commenti -->
                <div id="comments-list" class="space-y-4">
                    <?php foreach($comments as $comment): ?>
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold"><?php echo htmlspecialchars($comment['user_name']); ?></span>
                                <span class="text-sm text-gray-400">
                                    <?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?>
                                </span>
                            </div>
                            <p class="text-gray-300"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <!-- WaveSurfer.js -->
    <script src="https://unpkg.com/wavesurfer.js@7/dist/wavesurfer.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inizializzazione WaveSurfer
            const wavesurfer = WaveSurfer.create({
                container: '#waveform',
                waveColor: '#4B5563',
                progressColor: '#9333EA',
                cursorColor: '#9333EA',
                barWidth: 2,
                barGap: 1,
                height: 96,
                barRadius: 2,
                normalize: true,
                backend: 'WebAudio'
            });

            // Carica l'audio
            wavesurfer.load('uploads/tracks/<?php echo htmlspecialchars($track['file_path']); ?>');

            // Elementi DOM
            const playPauseBtn = document.getElementById('playPauseBtn');
            const progressBar = document.getElementById('progressBar');
            const volumeBar = document.getElementById('volumeBar');
            const currentTime = document.getElementById('currentTime');
            const duration = document.getElementById('duration');
            const volumeBtn = document.getElementById('volumeBtn');
            const waveformLoading = document.getElementById('waveform-loading');
            let hasStartedPlaying = false;

            // Funzione per formattare il tempo
            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                seconds = Math.floor(seconds % 60);
                return `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }

            // Eventi WaveSurfer
            wavesurfer.on('ready', () => {
                waveformLoading.style.display = 'none';
                duration.textContent = formatTime(wavesurfer.getDuration());
            });

            wavesurfer.on('audioprocess', () => {
                currentTime.textContent = formatTime(wavesurfer.getCurrentTime());
                const progress = (wavesurfer.getCurrentTime() / wavesurfer.getDuration()) * 100;
                progressBar.style.width = progress + '%';
            });

            wavesurfer.on('finish', () => {
                playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                hasStartedPlaying = false;
            });

            // Gestione Play/Pause
            playPauseBtn.addEventListener('click', () => {
                wavesurfer.playPause();
                if (wavesurfer.isPlaying()) {
                    playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                    if (!hasStartedPlaying) {
                        incrementPlays();
                        hasStartedPlaying = true;
                    }
                } else {
                    playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                }
            });

            // Click sulla progress bar
            const progressBarContainer = document.getElementById('progressBarContainer');
            progressBarContainer.addEventListener('click', (e) => {
                const rect = progressBarContainer.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const width = rect.width;
                const percentage = x / width;
                wavesurfer.seekTo(percentage);
            });

            // Aggiunta del drag and drop sulla progress bar
            let isDragging = false;

            progressBarContainer.addEventListener('mousedown', () => {
                isDragging = true;
            });

            document.addEventListener('mousemove', (e) => {
                if (isDragging) {
                    const rect = progressBarContainer.getBoundingClientRect();
                    const x = Math.max(0, Math.min(e.clientX - rect.left, rect.width));
                    const percentage = x / rect.width;
                    wavesurfer.seekTo(percentage);
                }
            });

            document.addEventListener('mouseup', () => {
                isDragging = false;
            });

            // Gestione Volume
            let lastVolume = 1;
            volumeBtn.addEventListener('click', () => {
                if (wavesurfer.getVolume() > 0) {
                    lastVolume = wavesurfer.getVolume();
                    wavesurfer.setVolume(0);
                    volumeBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
                    volumeBar.style.width = '0%';
                } else {
                    wavesurfer.setVolume(lastVolume);
                    volumeBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
                    volumeBar.style.width = (lastVolume * 100) + '%';
                }
            });

            volumeBar.parentElement.addEventListener('click', (e) => {
                const rect = e.target.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const width = rect.width;
                const volume = x / width;
                wavesurfer.setVolume(volume);
                volumeBar.style.width = (volume * 100) + '%';
                volumeBtn.innerHTML = volume === 0 ? '<i class="fas fa-volume-mute"></i>' : '<i class="fas fa-volume-up"></i>';
            });

            // Funzione per incrementare le riproduzioni
            function incrementPlays() {
                fetch('api/tracks.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'increment_plays',
                        track_id: <?php echo $track['id']; ?>
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const playsCount = document.querySelector('.plays-count');
                        if (playsCount) {
                            const currentPlays = parseInt(playsCount.textContent) || 0;
                            playsCount.textContent = currentPlays + 1;
                        }
                    }
                });
            }
        });

        // Funzione per gestire le reazioni
        function handleReaction(trackId, type) {
            fetch('api/reactions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    track_id: trackId,
                    reaction_type: type,
                    user_name: 'guest'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Errore durante il salvataggio della reazione');
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                alert('Si è verificato un errore durante la richiesta');
            });
        }

        // Funzione per inviare un commento
        function submitComment(trackId) {
            const content = document.getElementById('comment-input').value;
            const username = document.getElementById('username').value.trim() || 'guest';
            
            if (!content.trim()) {
                alert('Per favore, scrivi un commento prima di inviare.');
                return;
            }

            fetch('api/comments.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    track_id: trackId,
                    comment: content,
                    user_name: username
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Errore durante il salvataggio del commento');
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                alert('Si è verificato un errore durante la richiesta');
            });
        }
    </script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Stili per il player */
        .progress-bar {
            cursor: pointer;
            transition: height 0.2s ease;
        }

        .progress-bar:hover {
            height: 0.5rem;
        }

        /* Stili per i pulsanti del player */
        #playPauseBtn {
            transition: transform 0.2s ease;
        }

        #playPauseBtn:hover {
            transform: scale(1.1);
        }

        /* Stili per il volume */
        #volumeBar {
            transition: width 0.2s ease;
        }

        /* Stili per la forma d'onda */
        #waveform wave {
            border-radius: 4px;
        }

        /* Animazioni */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease;
        }
    </style>
</body>
</html> 