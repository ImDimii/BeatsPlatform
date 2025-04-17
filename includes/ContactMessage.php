<?php
require_once __DIR__ . '/../config/database.php';

class ContactMessage {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * Crea un nuovo messaggio
     * @param string $name Nome dell'utente
     * @param string $email Email dell'utente
     * @param string $message Testo del messaggio
     * @return bool
     */
    public function create($name, $email, $message) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
            return $stmt->execute([$name, $email, $message]);
        } catch (PDOException $e) {
            error_log("Errore nella creazione del messaggio: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Recupera tutti i messaggi con paginazione
     * @param int $page Numero di pagina
     * @param int $perPage Elementi per pagina
     * @return array
     */
    public function getAllMessages($page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            $stmt = $this->pdo->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Errore nel recupero dei messaggi: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Conta il numero totale di messaggi
     * @return int
     */
    public function getTotalMessages() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM contact_messages");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Errore nel conteggio dei messaggi: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Conta il numero di messaggi nuovi
     * @return int
     */
    public function getNewMessagesCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Errore nel conteggio dei nuovi messaggi: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Aggiorna lo stato di un messaggio
     * @param int $id ID del messaggio
     * @param string $status Nuovo stato ('new', 'read', 'replied')
     * @return bool
     */
    public function updateStatus($id, $status) {
        try {
            $stmt = $this->pdo->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log("Errore nell'aggiornamento dello stato del messaggio: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un messaggio
     * @param int $id ID del messaggio
     * @return bool
     */
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Errore nell'eliminazione del messaggio: " . $e->getMessage());
            return false;
        }
    }
} 