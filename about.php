<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/Settings.php';

$settings = Settings::getInstance();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi siamo - <?php echo htmlspecialchars($settings->get('site_title', 'Beats')); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Hero Section -->
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 bg-gradient-to-r from-purple-400 to-pink-600 text-transparent bg-clip-text">
                    La Tua Fonte di Beats
                </h1>
                <p class="text-xl text-gray-400">
                    Produciamo beats di alta qualità per artisti emergenti
                </p>
            </div>

            <!-- Chi Siamo Section -->
            <div class="bg-gray-800 rounded-lg p-8 mb-12">
                <h2 class="text-3xl font-bold mb-6">Chi Siamo</h2>
                <div class="prose prose-lg text-gray-300 space-y-4">
                    <p>
                        Siamo un team di produttori musicali appassionati, dedicati a creare beats di alta qualità 
                        per artisti di tutti i generi. La nostra missione è fornire produzioni musicali accessibili a tutti 
                        che aiutino gli artisti a realizzare la loro visione creativa.
                    </p>
                    <p>
                        Con anni di esperienza nel settore musicale, comprendiamo l'importanza di avere una base 
                        musicale che non solo suoni bene, ma che si distingua anche nel panorama musicale attuale.
                    </p>
                </div>
            </div>

            <!-- I Nostri Servizi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                <div class="bg-gray-800 rounded-lg p-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-music text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold">Beats Personalizzati</h3>
                    </div>
                    <p class="text-gray-400">
                        Creiamo beats su misura per le tue esigenze specifiche, 
                        lavorando a stretto contatto con te per realizzare la tua visione musicale.
                    </p>
                </div>

                <div class="bg-gray-800 rounded-lg p-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-pink-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-sliders-h text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold">Mixing & Mastering</h3>
                    </div>
                    <p class="text-gray-400">
                        Offriamo servizi entry level di mixing e mastering 
                        per assicurare che la tua musica suoni al meglio su qualsiasi sistema.
                    </p>
                </div>
            </div>

            <!-- Statistiche -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
                <div class="bg-gray-800 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-purple-400 mb-2">100+</div>
                    <div class="text-gray-400">Beats Prodotti</div>
                </div>
                <div class="bg-gray-800 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-purple-400 mb-2">5+</div>
                    <div class="text-gray-400">Clienti Soddisfatti</div>
                </div>
                <div class="bg-gray-800 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-purple-400 mb-2">5+</div>
                    <div class="text-gray-400">Generi Musicali</div>
                </div>
                <div class="bg-gray-800 rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-purple-400 mb-2">3+</div>
                    <div class="text-gray-400">Anni di Esperienza</div>
                </div>
            </div>

            <!-- Contattaci -->
            <div class="bg-gray-800 rounded-lg p-8">
                <h2 class="text-3xl font-bold mb-6">Contattaci</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <p class="text-gray-300">
                            Sei interessato a collaborare con noi o hai domande sui nostri servizi? 
                            Non esitare a contattarci!, Risponderemo il prima possibile!
                        </p>
                        <div class="space-y-2">
                            <div class="flex items-center space-x-3 text-gray-400">
                                <i class="fas fa-envelope w-6"></i>
                                <span><?php echo htmlspecialchars($settings->get('contact_email')); ?></span>
                            </div>
                            <div class="flex items-center space-x-3 text-gray-400">
                                <i class="fas fa-phone w-6"></i>
                                <span><?php echo htmlspecialchars($settings->get('contact_phone')); ?></span>
                            </div>
                            <div class="flex items-center space-x-3 text-gray-400">
                                <i class="fas fa-map-marker-alt w-6"></i>
                                <span><?php echo htmlspecialchars($settings->get('contact_address')); ?></span>
                            </div>
                        </div>
                        <div class="flex space-x-4 pt-4">
                            <a href="<?php echo htmlspecialchars($settings->get('social_instagram')); ?>" target="_blank" class="text-gray-400 hover:text-purple-400 transition">
                                <i class="fab fa-instagram text-2xl"></i>
                            </a>
                            <a href="<?php echo htmlspecialchars($settings->get('social_facebook')); ?>" target="_blank" class="text-gray-400 hover:text-purple-400 transition">
                                <i class="fab fa-facebook text-2xl"></i>
                            </a>
                            <a href="<?php echo htmlspecialchars($settings->get('social_youtube')); ?>" target="_blank" class="text-gray-400 hover:text-purple-400 transition">
                                <i class="fab fa-youtube text-2xl"></i>
                            </a>
                            <a href="<?php echo htmlspecialchars($settings->get('social_soundcloud')); ?>" target="_blank" class="text-gray-400 hover:text-purple-400 transition">
                                <i class="fab fa-soundcloud text-2xl"></i>
                            </a>
                        </div>
                    </div>
                    <form id="contact-form" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Nome</label>
                            <input type="text" name="name" required
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                            <span class="text-red-500 text-sm hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                            <input type="email" name="email" required
                                   class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white">
                            <span class="text-red-500 text-sm hidden"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Messaggio</label>
                            <textarea name="message" rows="4" required
                                      class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:border-purple-500 text-white"></textarea>
                            <span class="text-red-500 text-sm hidden"></span>
                        </div>
                        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition duration-300">
                            Invia Messaggio
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <!-- Popup di notifica personalizzato -->
    <div id="notification-popup" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
        <div class="bg-gray-800 rounded-lg p-6 shadow-xl transform transition-all scale-95 opacity-0" id="popup-content">
            <div class="flex items-center space-x-4 mb-4">
                <div id="popup-icon" class="w-12 h-12 rounded-full flex items-center justify-center text-2xl"></div>
                <div>
                    <h3 id="popup-title" class="text-xl font-semibold"></h3>
                    <p id="popup-message" class="text-gray-400"></p>
                </div>
            </div>
            <button onclick="closePopup()" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition duration-300 mt-4">
                Chiudi
            </button>
        </div>
    </div>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Stili per il popup */
        #notification-popup.active {
            display: flex;
        }

        #notification-popup.active #popup-content {
            transform: scale(1);
            opacity: 1;
        }

        #popup-content {
            transition: all 0.3s ease-out;
        }
    </style>

    <script>
        function showPopup(type, title, message) {
            const popup = document.getElementById('notification-popup');
            const content = document.getElementById('popup-content');
            const popupIcon = document.getElementById('popup-icon');
            const popupTitle = document.getElementById('popup-title');
            const popupMessage = document.getElementById('popup-message');

            // Configura l'icona e i colori in base al tipo
            if (type === 'success') {
                popupIcon.className = 'w-12 h-12 rounded-full flex items-center justify-center text-2xl bg-green-500 text-white';
                popupIcon.innerHTML = '<i class="fas fa-check"></i>';
            } else if (type === 'error') {
                popupIcon.className = 'w-12 h-12 rounded-full flex items-center justify-center text-2xl bg-red-500 text-white';
                popupIcon.innerHTML = '<i class="fas fa-times"></i>';
            }

            // Imposta il titolo e il messaggio
            popupTitle.textContent = title;
            popupMessage.textContent = message;

            // Mostra il popup con animazione
            popup.classList.add('active');
            setTimeout(() => {
                content.style.transform = 'scale(1)';
                content.style.opacity = '1';
            }, 10);
        }

        function closePopup() {
            const popup = document.getElementById('notification-popup');
            const content = document.getElementById('popup-content');
            
            content.style.transform = 'scale(0.95)';
            content.style.opacity = '0';
            
            setTimeout(() => {
                popup.classList.remove('active');
            }, 300);
        }

        document.getElementById('contact-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Reset error messages
            this.querySelectorAll('span.text-red-500').forEach(span => span.classList.add('hidden'));
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('process_contact.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showPopup('success', 'Messaggio Inviato!', 'Il tuo messaggio è stato inviato con successo. Ti risponderemo il prima possibile.');
                    this.reset();
                } else if (result.errors) {
                    // Show validation errors
                    Object.entries(result.errors).forEach(([field, message]) => {
                        const errorSpan = this.querySelector(`[name="${field}"] + span`);
                        if (errorSpan) {
                            errorSpan.textContent = message;
                            errorSpan.classList.remove('hidden');
                        }
                    });
                } else {
                    showPopup('error', 'Errore', result.message || 'Si è verificato un errore durante l\'invio del messaggio');
                }
            } catch (error) {
                showPopup('error', 'Errore', 'Si è verificato un errore durante l\'invio del messaggio');
                console.error('Error:', error);
            }
        });
    </script>
</body>
</html> 