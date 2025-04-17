<?php
require_once __DIR__ . '/../config/database.php';

class Settings {
    private $db;
    private static $instance = null;
    private $settings = [];

    private function __construct() {
        global $pdo;
        $this->db = $pdo;
        $this->loadSettings();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadSettings() {
        try {
            $stmt = $this->db->query("SELECT setting_key, setting_value FROM site_settings");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (PDOException $e) {
            error_log("Errore nel caricamento delle impostazioni: " . $e->getMessage());
        }
    }

    public function get($key, $default = '') {
        return isset($this->settings[$key]) ? $this->settings[$key] : $default;
    }

    public function update($key, $value) {
        try {
            $stmt = $this->db->prepare("INSERT INTO site_settings (setting_key, setting_value) 
                                      VALUES (?, ?) 
                                      ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->execute([$key, $value, $value]);
            $this->settings[$key] = $value;
            return true;
        } catch (PDOException $e) {
            error_log("Errore nell'aggiornamento dell'impostazione: " . $e->getMessage());
            return false;
        }
    }

    public function getAllSettings() {
        return $this->settings;
    }
} 