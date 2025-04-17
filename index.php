<?php
require_once 'config.php';

// Gestione della ricerca
$search = isset($_GET['search']) ? $_GET['search'] : '';
$bpm = isset($_GET['bpm']) ? $_GET['bpm'] : '';
$key = isset($_GET['key']) ? $_GET['key'] : '';

// Costruzione della query
$sql = "SELECT t.*, 
        COUNT(DISTINCT CASE WHEN r.reaction_type = 'like' THEN r.id END) as likes,
        COUNT(DISTINCT CASE WHEN r.reaction_type = 'dislike' THEN r.id END) as dislikes
        FROM tracks t
        LEFT JOIN reactions r ON t.id = r.track_id
        WHERE 1=1";

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (t.title LIKE '%$search%' OR t.artist LIKE '%$search%')";
}
if (!empty($bpm)) {
    $bpm = mysqli_real_escape_string($conn, $bpm);
    $sql .= " AND t.bpm = '$bpm'";
}
if (!empty($key)) {
    $key = mysqli_real_escape_string($conn, $key);
    $sql .= " AND t.key_signature = '$key'";
}

$sql .= " GROUP BY t.id ORDER BY t.upload_date DESC";

$result = mysqli_query($conn, $sql);
$tracks = [];
if($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $tracks[] = $row;
    }
}

// Recupera i BPM e le chiavi uniche per i filtri
$bpm_sql = "SELECT DISTINCT bpm FROM tracks ORDER BY bpm";
$key_sql = "SELECT DISTINCT key_signature FROM tracks ORDER BY key_signature";
$bpm_result = mysqli_query($conn, $bpm_sql);
$key_result = mysqli_query($conn, $key_sql);
$bpms = [];
$keys = [];
while($row = mysqli_fetch_assoc($bpm_result)) $bpms[] = $row['bpm'];
while($row = mysqli_fetch_assoc($key_result)) $keys[] = $row['key_signature'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beats Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-2">Beats Platform</h1>
            <p class="text-gray-400">Ascolta i miei beats</p>
        </div>

        <!-- Barra di ricerca -->
        <div class="mb-8 bg-gray-800 p-6 rounded-lg backdrop-blur-lg">
            <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Cerca per titolo o artista..." 
                       class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                <select name="bpm" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                    <option value="">Tutti i BPM</option>
                    <?php foreach($bpms as $b): ?>
                        <option value="<?php echo $b; ?>" <?php echo $bpm == $b ? 'selected' : ''; ?>><?php echo $b; ?> BPM</option>
                    <?php endforeach; ?>
                </select>
                <select name="key" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                    <option value="">Tutte le chiavi</option>
                    <?php foreach($keys as $k): ?>
                        <option value="<?php echo $k; ?>" <?php echo $key == $k ? 'selected' : ''; ?>><?php echo $k; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg transition">Cerca</button>
            </form>
        </div>

        <!-- Griglia delle tracce -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($tracks as $track): ?>
                <div class="bg-gray-800 rounded-lg overflow-hidden hover:transform hover:scale-105 transition duration-300">
                    <div class="p-6 cursor-pointer track-card" data-track-id="<?php echo $track['id']; ?>" 
                         data-file-path="<?php echo htmlspecialchars($track['file_path']); ?>"
                         data-title="<?php echo htmlspecialchars($track['title']); ?>"
                         data-artist="<?php echo htmlspecialchars($track['artist']); ?>">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                <?php if($track['cover_image']): ?>
                                    <img src="uploads/covers/<?php echo htmlspecialchars($track['cover_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($track['title']); ?>"
                                         class="w-full h-full object-cover rounded-lg">
                                <?php else: ?>
                                    <i class="fas fa-music text-gray-500 text-2xl"></i>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow">
                                <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($track['title']); ?></h3>
                                <p class="text-gray-400"><?php echo htmlspecialchars($track['artist']); ?></p>
                                <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                    <span><i class="fas fa-play"></i> <span class="plays-count"><?php echo $track['plays']; ?></span></span>
                                    <span><i class="fas fa-drum"></i> <?php echo $track['bpm']; ?></span>
                                    <span><i class="fas fa-music"></i> <?php echo htmlspecialchars($track['key_signature']); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-4">
                            <div class="flex space-x-4">
                                <button onclick="handleReaction(<?php echo $track['id']; ?>, 'like')" class="text-gray-400 hover:text-green-500 transition">
                                    <i class="fas fa-thumbs-up"></i> <span><?php echo $track['likes']; ?></span>
                                </button>
                                <button onclick="handleReaction(<?php echo $track['id']; ?>, 'dislike')" class="text-gray-400 hover:text-red-500 transition">
                                    <i class="fas fa-thumbs-down"></i> <span><?php echo $track['dislikes']; ?></span>
                                </button>
                            </div>
                            <a href="track.php?id=<?php echo $track['id']; ?>" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg transition text-sm">
                                Dettagli
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Player fisso in basso -->
    <div class="fixed bottom-0 left-0 right-0 bg-gray-800 border-t border-gray-700 shadow-lg z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center space-x-6">
                <!-- Info traccia corrente -->
                <div class="flex items-center space-x-4 w-1/4">
                    <div class="w-12 h-12 bg-gray-700 rounded-lg flex-shrink-0 overflow-hidden" id="currentTrackCover">
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-music text-gray-500 text-xl"></i>
                        </div>
                    </div>
                    <div id="currentTrackInfo" class="hidden">
                        <h3 id="currentTitle" class="font-semibold text-sm truncate"></h3>
                        <p id="currentArtist" class="text-gray-400 text-xs truncate"></p>
                    </div>
                </div>

                <!-- Controlli Player -->
                <div class="flex-1 flex flex-col items-center">
                    <!-- Controlli principali -->
                    <div class="flex items-center space-x-4 mb-2">
                        <button id="prevTrack" class="text-gray-400 hover:text-white transition">
                            <i class="fas fa-step-backward"></i>
                        </button>
                        <button id="playPauseBtn" class="w-10 h-10 rounded-full bg-purple-600 hover:bg-purple-700 transition flex items-center justify-center">
                            <i class="fas fa-play"></i>
                        </button>
                        <button id="nextTrack" class="text-gray-400 hover:text-white transition">
                            <i class="fas fa-step-forward"></i>
                        </button>
                    </div>

                    <!-- Progress bar -->
                    <div class="w-full flex items-center space-x-3">
                        <span id="currentTime" class="text-xs text-gray-400 w-12 text-right">0:00</span>
                        
                        <div class="flex-1 h-4 relative group cursor-pointer" id="progressBarContainer">
                            <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-700 rounded-full -translate-y-1/2">
                                <div id="progressBar" class="h-full bg-purple-600 w-0 relative">
                                    <div class="absolute left-full top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity shadow-md transform -translate-x-1/2 hover:scale-110"></div>
                                </div>
                            </div>
                        </div>
                        
                        <span id="duration" class="text-xs text-gray-400 w-12">0:00</span>
                    </div>

                </div>

                <!-- Volume e altri controlli -->
                <div class="w-1/4 flex items-center justify-end space-x-4">
                    <button id="shuffleBtn" class="text-gray-400 hover:text-white transition">
                        <i class="fas fa-random"></i>
                    </button>
                    <button id="repeatBtn" class="text-gray-400 hover:text-white transition">
                        <i class="fas fa-redo"></i>
                    </button>
                    <div class="flex items-center space-x-2">
                        <button id="volumeBtn" class="text-gray-400 hover:text-white transition">
                            <i class="fas fa-volume-up"></i>
                        </button>
                        <div class="w-20 h-1 bg-gray-700 rounded-full overflow-hidden">
                            <div id="volumeBar" class="h-full bg-purple-600 w-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const audioElement = document.createElement('audio');
            const playPauseBtn = document.getElementById('playPauseBtn');
            const progressBar = document.getElementById('progressBar');
            const volumeBar = document.getElementById('volumeBar');
            const currentTime = document.getElementById('currentTime');
            const duration = document.getElementById('duration');
            const volumeBtn = document.getElementById('volumeBtn');
            const currentTrackInfo = document.getElementById('currentTrackInfo');
            const currentTitle = document.getElementById('currentTitle');
            const currentArtist = document.getElementById('currentArtist');
            const currentTrackCover = document.getElementById('currentTrackCover');
            const shuffleBtn = document.getElementById('shuffleBtn');
            const repeatBtn = document.getElementById('repeatBtn');
            let currentTrackId = null;
            let hasStartedPlaying = false;
            let isShuffle = false;
            let isRepeat = false;
            let tracks = [];
            let currentTrackIndex = -1;

            // Raccogli tutte le tracce disponibili
            document.querySelectorAll('.track-card').forEach(card => {
                tracks.push({
                    id: card.dataset.trackId,
                    filePath: card.dataset.filePath,
                    title: card.dataset.title,
                    artist: card.dataset.artist,
                    cover: card.querySelector('img')?.src || null
                });
            });

            // Funzione per formattare il tempo
            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                seconds = Math.floor(seconds % 60);
                return `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }

            // Gestione click sulle card delle tracce
            document.querySelectorAll('.track-card').forEach((card, index) => {
                card.addEventListener('click', function(e) {
                    if (e.target.closest('button') || e.target.closest('a')) return;
                    
                    const trackId = this.dataset.trackId;
                    currentTrackIndex = index;
                    playTrack(trackId);
                });
            });

            // Funzione per riprodurre una traccia
            function playTrack(trackId) {
                const track = tracks.find(t => t.id === trackId);
                if (!track) return;

                currentTrackId = trackId;
                audioElement.src = 'uploads/tracks/' + track.filePath;
                currentTitle.textContent = track.title;
                currentArtist.textContent = track.artist;
                currentTrackInfo.classList.remove('hidden');

                if (track.cover) {
                    currentTrackCover.innerHTML = `<img src="${track.cover}" class="w-full h-full object-cover">`;
                } else {
                    currentTrackCover.innerHTML = '<i class="fas fa-music text-gray-500 text-xl flex items-center justify-center h-full"></i>';
                }

                audioElement.play();
                playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                hasStartedPlaying = false;
            }

            // Gestione Play/Pause
            playPauseBtn.addEventListener('click', () => {
                if (audioElement.paused) {
                    audioElement.play();
                    playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                } else {
                    audioElement.pause();
                    playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                }
            });

            // Gestione Progress Bar
            audioElement.addEventListener('timeupdate', () => {
                const progress = (audioElement.currentTime / audioElement.duration) * 100;
                progressBar.style.width = progress + '%';
                currentTime.textContent = formatTime(audioElement.currentTime);
            });

            audioElement.addEventListener('loadedmetadata', () => {
                duration.textContent = formatTime(audioElement.duration);
            });

            // Click sulla progress bar
            const progressBarContainer = document.getElementById('progressBarContainer');
            progressBarContainer.addEventListener('click', (e) => {
                const rect = progressBarContainer.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const width = rect.width;
                const percentage = x / width;
                audioElement.currentTime = percentage * audioElement.duration;
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
                    audioElement.currentTime = percentage * audioElement.duration;
                }
            });

            document.addEventListener('mouseup', () => {
                isDragging = false;
            });

            // Gestione Volume
            let lastVolume = 1;
            volumeBtn.addEventListener('click', () => {
                if (audioElement.volume > 0) {
                    lastVolume = audioElement.volume;
                    audioElement.volume = 0;
                    volumeBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
                    volumeBar.style.width = '0%';
                } else {
                    audioElement.volume = lastVolume;
                    volumeBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
                    volumeBar.style.width = (lastVolume * 100) + '%';
                }
            });

            volumeBar.parentElement.addEventListener('click', (e) => {
                const rect = e.target.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const width = rect.width;
                const volume = x / width;
                audioElement.volume = volume;
                volumeBar.style.width = (volume * 100) + '%';
                volumeBtn.innerHTML = volume === 0 ? '<i class="fas fa-volume-mute"></i>' : '<i class="fas fa-volume-up"></i>';
            });

            // Gestione Shuffle
            shuffleBtn.addEventListener('click', () => {
                isShuffle = !isShuffle;
                shuffleBtn.classList.toggle('text-purple-600');
            });

            // Gestione Repeat
            repeatBtn.addEventListener('click', () => {
                isRepeat = !isRepeat;
                repeatBtn.classList.toggle('text-purple-600');
            });

            // Gestione traccia precedente/successiva
            document.getElementById('prevTrack').addEventListener('click', () => {
                if (currentTrackIndex > 0) {
                    currentTrackIndex--;
                    playTrack(tracks[currentTrackIndex].id);
                }
            });

            document.getElementById('nextTrack').addEventListener('click', () => {
                playNextTrack();
            });

            function playNextTrack() {
                if (isShuffle) {
                    const randomIndex = Math.floor(Math.random() * tracks.length);
                    currentTrackIndex = randomIndex;
                } else if (currentTrackIndex < tracks.length - 1) {
                    currentTrackIndex++;
                } else if (isRepeat) {
                    currentTrackIndex = 0;
                } else {
                    return;
                }
                playTrack(tracks[currentTrackIndex].id);
            }

            // Quando una traccia finisce
            audioElement.addEventListener('ended', () => {
                playNextTrack();
            });

            // Gestione incremento riproduzioni
            audioElement.addEventListener('play', function() {
                if (!hasStartedPlaying && currentTrackId) {
                    fetch('api/tracks.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'increment_plays',
                            track_id: currentTrackId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const card = document.querySelector(`.track-card[data-track-id="${currentTrackId}"]`);
                            const playsCount = card.querySelector('.plays-count');
                            if (playsCount) {
                                const currentPlays = parseInt(playsCount.textContent) || 0;
                                playsCount.textContent = currentPlays + 1;
                            }
                        }
                    });
                    hasStartedPlaying = true;
                }
            });
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
    </script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding-bottom: 5rem;
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

        /* Animazione per il cambio traccia */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #currentTrackInfo:not(.hidden) {
            animation: fadeIn 0.3s ease;
        }

        /* Stili per il pallino della progress bar */
        #progressBar:hover .handle {
            transform: scale(1.2);
        }
    </style>
</body>
</html> 