<?php
require_once '../includes/auth.php';
require_once '../includes/Track.php';

// Verifica che l'utente sia admin
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$track = new Track();
$message = '';
$error = '';

// Gestione eliminazione traccia
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $trackInfo = $track->getTrack($id);
    
    if ($trackInfo) {
        // Elimina i file fisici
        if (!empty($trackInfo['file_path'])) {
            $trackPath = "../uploads/tracks/" . $trackInfo['file_path'];
            if (file_exists($trackPath)) {
                unlink($trackPath);
            }
        }
        if (!empty($trackInfo['cover_image']) && $trackInfo['cover_image'] !== 'default.png') {
            $coverPath = "../uploads/covers/" . $trackInfo['cover_image'];
            if (file_exists($coverPath)) {
                unlink($coverPath);
            }
        }
        
        // Elimina il record dal database
        if ($track->deleteTrack($id)) {
            $message = "Traccia eliminata con successo";
        } else {
            $error = "Errore durante l'eliminazione della traccia";
        }
    }
}

// Gestione modifica traccia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_track'])) {
    $id = (int)$_POST['track_id'];
    $title = trim($_POST['title'] ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $bpm = (int)($_POST['bpm'] ?? 0);
    $key = trim($_POST['key'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Gestione upload nuovo file MP3
    $fileName = null;
    if (isset($_FILES['track_file']) && $_FILES['track_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../uploads/tracks/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['track_file']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['track_file']['tmp_name'], $targetPath)) {
            // Elimina il vecchio file
            $oldTrack = $track->getTrack($id);
            if ($oldTrack && !empty($oldTrack['file_path'])) {
                $oldPath = "../uploads/tracks/" . $oldTrack['file_path'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
        } else {
            $error = "Errore durante l'upload del nuovo file";
        }
    }
    
    // Gestione upload nuova cover
    $coverFileName = null;
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $coverDir = "../uploads/covers/";
        if (!file_exists($coverDir)) {
            mkdir($coverDir, 0777, true);
        }
        
        $coverFileName = time() . '_' . basename($_FILES['cover_image']['name']);
        $coverPath = $coverDir . $coverFileName;
        
        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $coverPath)) {
            // Elimina la vecchia cover se non è quella di default
            $oldTrack = $track->getTrack($id);
            if ($oldTrack && !empty($oldTrack['cover_image']) && $oldTrack['cover_image'] !== 'default.png') {
                $oldPath = "../uploads/covers/" . $oldTrack['cover_image'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
        } else {
            $error = "Errore durante l'upload della nuova cover";
        }
    }
    
    // Aggiorna nel database
    if ($track->updateTrack($id, $title, $artist, $bpm, $key, $genre, $fileName, $description, $coverFileName)) {
        $message = "Traccia aggiornata con successo";
    } else {
        $error = "Errore durante l'aggiornamento della traccia";
    }
}

// Gestione upload nuova traccia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $bpm = (int)($_POST['bpm'] ?? 0);
    $key = trim($_POST['key'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Gestione upload file MP3
    if (isset($_FILES['track_file']) && $_FILES['track_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../uploads/tracks/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['track_file']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['track_file']['tmp_name'], $targetPath)) {
            // Gestione upload cover
            $coverFileName = 'default.png';
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $coverDir = "../uploads/covers/";
                if (!file_exists($coverDir)) {
                    mkdir($coverDir, 0777, true);
                }
                
                $coverFileName = time() . '_' . basename($_FILES['cover_image']['name']);
                $coverPath = $coverDir . $coverFileName;
                
                if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $coverPath)) {
                    $coverFileName = 'default.png';
                }
            }
            
            // Inserisci nel database
            if ($track->createTrack($title, $artist, $bpm, $key, $genre, $fileName, $description, $coverFileName)) {
                $message = "Traccia caricata con successo";
            } else {
                $error = "Errore durante il salvataggio della traccia nel database";
            }
        } else {
            $error = "Errore durante l'upload del file";
        }
    } else {
        $error = "Seleziona un file MP3 da caricare";
    }
}

// Recupera tutte le tracce
$tracks = $track->getAllTracks();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Tracce - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <?php require_once 'includes/admin-header.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold">Gestione Tracce</h1>
                <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
                        class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg transition duration-300">
                    <i class="fas fa-plus mr-2"></i>Nuova Traccia
                </button>
            </div>

            <?php if ($message): ?>
            <div class="bg-green-500 text-white px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="bg-red-500 text-white px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <!-- Lista Tracce -->
            <div class="bg-gray-800 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-700">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Cover</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Titolo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Artista</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">BPM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Chiave</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Genere</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Plays</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Like</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Dislike</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Commenti</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <?php foreach ($tracks as $track): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <img src="../uploads/covers/<?php echo htmlspecialchars($track['cover_image']); ?>" 
                                         alt="Cover" class="w-10 h-10 rounded object-cover">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($track['title']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($track['artist']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($track['bpm']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($track['key_signature']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($track['genre']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo number_format($track['plays']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-green-400">
                                    <i class="fas fa-thumbs-up mr-1"></i>
                                    <?php echo number_format($track['likes']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-red-400">
                                    <i class="fas fa-thumbs-down mr-1"></i>
                                    <?php echo number_format($track['dislikes']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-blue-400">
                                    <i class="fas fa-comments mr-1"></i>
                                    <?php echo number_format($track['comments']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <button onclick="editTrack(<?php echo $track['id']; ?>)"
                                                class="text-blue-400 hover:text-blue-300">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="?delete=<?php echo $track['id']; ?>" 
                                           onclick="return confirm('Sei sicuro di voler eliminare questa traccia?')"
                                           class="text-red-400 hover:text-red-300">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($tracks)): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-400">
                                    Nessuna traccia trovata
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Upload -->
    <div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-lg max-w-2xl w-full p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Carica Nuova Traccia</h2>
                    <button onclick="document.getElementById('uploadModal').classList.add('hidden')"
                            class="text-gray-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Titolo</label>
                        <input type="text" name="title" required
                               class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Artista</label>
                        <input type="text" name="artist" required
                               class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">BPM</label>
                            <input type="number" name="bpm" required
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Chiave</label>
                            <select name="key" required
                                    class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                                <option value="">Seleziona chiave</option>
                                <?php
                                $keys = ['C', 'C#/Db', 'D', 'D#/Eb', 'E', 'F', 'F#/Gb', 'G', 'G#/Ab', 'A', 'A#/Bb', 'B'];
                                foreach ($keys as $k) {
                                    echo "<option value=\"" . htmlspecialchars($k) . "\">" . htmlspecialchars($k) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Genere</label>
                            <input type="text" name="genre" required
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Descrizione</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">File MP3</label>
                        <input type="file" name="track_file" accept=".mp3" required
                               class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Cover Image (opzionale)</label>
                        <input type="file" name="cover_image" accept="image/*"
                               class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button"
                                onclick="document.getElementById('uploadModal').classList.add('hidden')"
                                class="px-4 py-2 rounded-lg bg-gray-700 hover:bg-gray-600 transition duration-300">
                            Annulla
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 transition duration-300">
                            Carica
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Modifica -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-lg max-w-2xl w-full p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Modifica Traccia</h2>
                    <button onclick="document.getElementById('editModal').classList.add('hidden')"
                            class="text-gray-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form method="POST" enctype="multipart/form-data" class="space-y-6" id="editForm">
                    <input type="hidden" name="edit_track" value="1">
                    <input type="hidden" name="track_id" id="edit_track_id">

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Titolo</label>
                        <input type="text" name="title" id="edit_title" required
                               class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Artista</label>
                        <input type="text" name="artist" id="edit_artist" required
                               class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">BPM</label>
                            <input type="number" name="bpm" id="edit_bpm" required
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Chiave</label>
                            <select name="key" id="edit_key" required
                                    class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                                <option value="">Seleziona chiave</option>
                                <?php
                                $keys = ['C', 'C#/Db', 'D', 'D#/Eb', 'E', 'F', 'F#/Gb', 'G', 'G#/Ab', 'A', 'A#/Bb', 'B'];
                                foreach ($keys as $k) {
                                    echo "<option value=\"" . htmlspecialchars($k) . "\">" . htmlspecialchars($k) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Genere</label>
                            <input type="text" name="genre" id="edit_genre" required
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Descrizione</label>
                        <textarea name="description" id="edit_description" rows="3"
                                  class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Nuovo File MP3 (opzionale)</label>
                        <input type="file" name="track_file" accept=".mp3"
                               class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Nuova Cover Image (opzionale)</label>
                        <input type="file" name="cover_image" accept="image/*"
                               class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button"
                                onclick="document.getElementById('editModal').classList.add('hidden')"
                                class="px-4 py-2 rounded-lg bg-gray-700 hover:bg-gray-600 transition duration-300">
                            Annulla
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 transition duration-300">
                            Salva Modifiche
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        async function editTrack(id) {
            try {
                const response = await fetch(`get_track.php?id=${id}`);
                const track = await response.json();
                
                if (track) {
                    document.getElementById('edit_track_id').value = track.id;
                    document.getElementById('edit_title').value = track.title;
                    document.getElementById('edit_artist').value = track.artist;
                    document.getElementById('edit_bpm').value = track.bpm;
                    document.getElementById('edit_key').value = track.key_signature;
                    document.getElementById('edit_genre').value = track.genre;
                    document.getElementById('edit_description').value = track.description || '';
                    
                    document.getElementById('editModal').classList.remove('hidden');
                }
            } catch (error) {
                console.error('Errore nel recupero dei dati della traccia:', error);
                alert('Errore nel recupero dei dati della traccia');
            }
        }
    </script>
</body>
</html> 