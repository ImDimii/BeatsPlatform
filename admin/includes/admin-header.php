<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="bg-gray-800 text-white py-4 mb-8">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-8">
                <a href="../index.php" class="text-2xl font-bold">Admin Panel</a>
                <nav class="hidden md:flex space-x-4">
                    <a href="index.php" class="hover:text-purple-400 transition">Dashboard</a>
                    <a href="tracks.php" class="hover:text-purple-400 transition">Tracce</a>
                    <a href="settings.php" class="hover:text-purple-400 transition">Impostazioni</a>
                    <a href="messages.php" class="hover:text-purple-400 transition">Messaggi</a>
                </nav>
            </div>
            <div>
                <a href="logout.php" class="text-red-400 hover:text-red-300 transition">Logout</a>
            </div>
        </div>
    </div>
</header> 