<?php
// Avvia la sessione se non è già attiva
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/Settings.php';
$settings = Settings::getInstance();
?>
<header class="bg-gray-800 text-white py-4">
    <div class="container mx-auto px-4 flex justify-between items-center">
        <div class="flex items-center">
            <a href="index.php" class="text-2xl font-bold"><?php echo htmlspecialchars($settings->get('site_title', 'Beats')); ?></a>
        </div>
        
        <nav class="hidden md:flex space-x-6">
            <a href="index.php" class="hover:text-purple-400 transition">Home</a>
           <!-- <a href="tracks.php" class="hover:text-purple-400 transition">Tracce</a> -->
            <a href="about.php" class="hover:text-purple-400 transition">Chi siamo</a>
        </nav>
        
        <!-- Menu mobile -->
        <div class="md:hidden">
            <button id="mobile-menu-button" class="text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Menu mobile espanso -->
    <div id="mobile-menu" class="md:hidden hidden bg-gray-700 py-2">
        <div class="container mx-auto px-4 flex flex-col space-y-2">
            <a href="index.php" class="py-2 hover:text-purple-400 transition">Home</a>
           <!-- <a href="tracks.php" class="py-2 hover:text-purple-400 transition">Tracce</a> -->
            <a href="about.php" class="py-2 hover:text-purple-400 transition">Chi siamo</a>
        </div>
    </div>
</header>

<script>
    // Toggle menu mobile
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script> 