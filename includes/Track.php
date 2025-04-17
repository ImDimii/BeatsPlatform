<?php
require_once __DIR__ . '/../config/database.php';

class Track {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * Crea una nuova traccia
     * @param string $title Titolo della traccia
     * @param string $artist Nome dell'artista
     * @param int $bpm BPM della traccia
     * @param string $key Chiave musicale
     * @param string $genre Genere musicale
     * @param string $filePath Percorso del file MP3
     * @param string $description Descrizione della traccia
     * @param string $coverImage Percorso dell'immagine di copertina
     * @return bool
     */
    public function createTrack($title, $artist, $bpm, $key, $genre, $filePath, $description = '', $coverImage = 'default.png') {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO tracks (title, artist, bpm, key_signature, genre, file_path, description, cover_image) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            return $stmt->execute([$title, $artist, $bpm, $key, $genre, $filePath, $description, $coverImage]);
        } catch (PDOException $e) {
            error_log("Errore nella creazione della traccia: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina una traccia
     * @param int $id ID della traccia
     * @return bool
     */
    public function deleteTrack($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM tracks WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Errore nell'eliminazione della traccia: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Incrementa il contatore delle riproduzioni di una traccia
     * @param int $id ID della traccia
     * @return bool
     */
    public function incrementPlays($id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE tracks SET plays = plays + 1 WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Errore nell'incremento delle riproduzioni: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Recupera una traccia specifica
     * @param int $id ID della traccia
     * @return array|false
     */
    public function getTrack($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM tracks WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Errore nel recupero della traccia: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Recupera tutte le tracce con conteggio di like, dislike e commenti
     * @return array
     */
    public function getAllTracks() {
        try {
            $sql = "SELECT t.*, 
                    COUNT(DISTINCT CASE WHEN r.reaction_type = 'like' THEN r.id END) as likes,
                    COUNT(DISTINCT CASE WHEN r.reaction_type = 'dislike' THEN r.id END) as dislikes,
                    COUNT(DISTINCT c.id) as comments
                    FROM tracks t
                    LEFT JOIN reactions r ON t.id = r.track_id
                    LEFT JOIN comments c ON t.id = c.track_id
                    GROUP BY t.id
                    ORDER BY t.upload_date DESC";
            
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore nel recupero delle tracce: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Conta il numero totale di tracce
     * @return int
     */
    public function getTotalTracks() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM tracks");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Errore nel conteggio delle tracce: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Conta il numero totale di riproduzioni
     * @return int
     */
    public function getTotalPlays() {
        try {
            $stmt = $this->pdo->query("SELECT SUM(plays) FROM tracks");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Errore nel conteggio delle riproduzioni: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Recupera statistiche generali delle tracce
     * @return array
     */
    public function getTrackStats() {
        try {
            $sql = "SELECT 
                    COUNT(*) as total_tracks,
                    SUM(plays) as total_plays,
                    COUNT(DISTINCT genre) as total_genres,
                    (SELECT COUNT(*) FROM reactions WHERE reaction_type = 'like') as total_likes,
                    (SELECT COUNT(*) FROM reactions WHERE reaction_type = 'dislike') as total_dislikes,
                    (SELECT COUNT(*) FROM comments) as total_comments,
                    (SELECT genre FROM tracks GROUP BY genre ORDER BY COUNT(*) DESC LIMIT 1) as most_popular_genre,
                    (SELECT key_signature FROM tracks GROUP BY key_signature ORDER BY COUNT(*) DESC LIMIT 1) as most_used_key
                    FROM tracks";
            
            $stmt = $this->pdo->query($sql);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Errore nel recupero delle statistiche: " . $e->getMessage());
            return [];
        }
    }

    public function updateTrack($id, $title, $artist, $bpm, $key, $genre, $mp3_path = null, $description = '', $cover_path = null) {
        try {
            // Recupera i dati attuali della traccia
            $currentTrack = $this->getTrack($id);
            if (!$currentTrack) {
                return false;
            }
            
            $sql = "UPDATE tracks SET title = ?, artist = ?, bpm = ?, key_signature = ?, genre = ?, description = ?";
            $params = [$title, $artist, $bpm, $key, $genre, $description];
            
            // Usa il percorso file esistente se non viene fornito un nuovo file
            if (!empty($mp3_path)) {
                $sql .= ", file_path = ?";
                $params[] = $mp3_path;
            } else {
                $sql .= ", file_path = ?";
                $params[] = $currentTrack['file_path'];
            }
            
            // Usa il percorso cover esistente se non viene fornita una nuova cover
            if (!empty($cover_path)) {
                $sql .= ", cover_image = ?";
                $params[] = $cover_path;
            } else {
                $sql .= ", cover_image = ?";
                $params[] = $currentTrack['cover_image'];
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;

            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Errore nell'aggiornamento della traccia: " . $e->getMessage());
            return false;
        }
    }
} 