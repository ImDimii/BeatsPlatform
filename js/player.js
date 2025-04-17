document.addEventListener('DOMContentLoaded', function() {
    const audioPlayer = document.getElementById('audio-player');
    if (!audioPlayer) return;

    let hasStartedPlaying = false;

    // Incrementa il contatore delle riproduzioni quando il brano viene effettivamente riprodotto
    audioPlayer.addEventListener('play', function() {
        if (!hasStartedPlaying) {
            const trackId = this.dataset.trackId;
            if (!trackId) return;

            fetch('api/tracks.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'increment_plays',
                    track_id: trackId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aggiorna il contatore delle riproduzioni nell'interfaccia
                    const playsElement = document.querySelector('.plays-count');
                    if (playsElement) {
                        const currentPlays = parseInt(playsElement.textContent) || 0;
                        playsElement.textContent = currentPlays + 1;
                    }
                }
            })
            .catch(error => console.error('Errore:', error));

            hasStartedPlaying = true;
        }
    });

    // Resetta il flag quando il brano finisce
    audioPlayer.addEventListener('ended', function() {
        hasStartedPlaying = false;
    });

    // Gestione della barra di avanzamento personalizzata
    const progressBar = document.createElement('div');
    progressBar.className = 'progress-bar mt-2';
    audioPlayer.parentNode.insertBefore(progressBar, audioPlayer.nextSibling);

    const progress = document.createElement('div');
    progress.className = 'progress';
    progressBar.appendChild(progress);

    // Aggiorna la barra di avanzamento
    audioPlayer.addEventListener('timeupdate', function() {
        const percent = (audioPlayer.currentTime / audioPlayer.duration) * 100;
        progress.style.width = percent + '%';
    });

    // Permetti di cliccare sulla barra per cambiare la posizione
    progressBar.addEventListener('click', function(e) {
        const rect = this.getBoundingClientRect();
        const percent = (e.clientX - rect.left) / rect.width;
        audioPlayer.currentTime = percent * audioPlayer.duration;
    });
}); 