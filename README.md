# Beats - Sistema di Gestione Traccie Musicali
![image](https://github.com/user-attachments/assets/e38982e6-a156-4cf5-9b50-46f261c1256e)

## Requisiti di Sistema
- PHP 7.4 o superiore
- MySQL 5.7 o superiore
- Apache/Nginx
- Estensione PDO PHP
- Estensione PDO_MySQL PHP

## Installazione

### 1. Configurazione del Database
1. Creare un nuovo database MySQL:
```sql
CREATE DATABASE beats_db;
```

2. Importare la struttura del database:
```bash
mysql -u root -p beats_db < database.sql
```

### 2. Configurazione del Progetto

1. Clonare il repository nella directory del web server:
```bash
git clone [url-repository] /var/www/html/beats
```

2. Configurare i permessi (su sistemi Linux):
```bash
chmod -R 755 /var/www/html/beats
chmod -R 777 /var/www/html/beats/uploads  # se presente
```

3. Modificare il file di configurazione del database:
```bash
nano /var/www/html/beats/config/database.php
```
Modificare i seguenti parametri:
```php
$dbHost = 'localhost';  // Host del database
$dbName = 'beats_db';   // Nome del database
$dbUser = 'root';       // Username del database
$dbPass = '';          // Password del database
```

### 3. Configurazione del Server Web

#### Apache
1. Abilitare il modulo rewrite:
```bash
a2enmod rewrite
```

2. Configurare il VirtualHost:
```apache
<VirtualHost *:80>
    ServerName beats.local
    DocumentRoot /var/www/html/beats
    
    <Directory /var/www/html/beats>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Riavviare Apache:
```bash
systemctl restart apache2
```

## Struttura del Progetto

```
beats/
├── api/                 # API endpoints
│   ├── tracks.php      # Gestione tracce
│   └── ...
├── config/             # File di configurazione
│   └── database.php    # Configurazione database
├── includes/           # Classi PHP
│   ├── Track.php       # Classe gestione tracce
│   └── ...
├── uploads/            # Directory per file caricati
└── index.php          # File principale
```

## Funzionalità Principali

### Gestione Tracce
- Caricamento tracce musicali
- Incremento automatico delle riproduzioni
- Gestione metadati (titolo, artista, BPM, etc.)

### API Endpoints

#### Incremento Riproduzioni
```http
POST /api/tracks.php
Content-Type: application/x-www-form-urlencoded

action=increment_plays&track_id=1
```

#### Recupero Traccia
```http
GET /api/tracks.php?track_id=1
```

## Troubleshooting

### Problemi Comuni

1. **Errore di Connessione al Database**
   - Verificare le credenziali in `config/database.php`
   - Controllare che MySQL sia in esecuzione
   - Verificare che l'utente abbia i permessi necessari

2. **Errori 500**
   - Controllare i log di Apache: `/var/log/apache2/error.log`
   - Verificare i permessi dei file
   - Abilitare la visualizzazione degli errori PHP in sviluppo

3. **Problemi di Permessi**
   - Su Linux, verificare i permessi delle directory
   - Assicurarsi che l'utente del web server (www-data) abbia accesso

### Debug

Per abilitare il debug, aggiungere all'inizio dei file PHP:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Sicurezza

1. **Protezione Directory**
   - Mantenere i file di configurazione fuori dalla root web
   - Utilizzare .htaccess per proteggere directory sensibili

2. **Database**
   - Utilizzare prepared statements per prevenire SQL injection
   - Limitare i permessi dell'utente del database

3. **Upload File**
   - Validare i tipi di file
   - Limitare la dimensione dei file
   - Rinominare i file in modo sicuro

## Manutenzione

### Backup
1. Database:
```bash
mysqldump -u root -p beats_db > backup.sql
```

2. File:
```bash
tar -czf beats_backup.tar.gz /var/www/html/beats
```

### Aggiornamenti
1. Backup dei dati
2. Aggiornare i file del progetto
3. Verificare la compatibilità del database
4. Testare le funzionalità principali

## Supporto

Per problemi o domande, aprire una issue su GitHub o contattarmi.
