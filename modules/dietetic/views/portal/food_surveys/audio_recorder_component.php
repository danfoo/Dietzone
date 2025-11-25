<!-- Audio Recorder Component -->
<style>
.audio-recorder-wrapper {
    margin-top: 10px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px dashed #dee2e6;
}

.audio-recorder-controls {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.audio-record-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    padding: 0;
    border: none;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.audio-record-btn i {
    margin: 0;
}

.audio-record-btn.record {
    background: #F3911D;
    color: white;
}

.audio-record-btn.record:hover {
    background: #e07d0f;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(243, 145, 29, 0.3);
}

.audio-record-btn.stop {
    background: #6c757d;
    color: white;
}

.audio-record-btn.stop:hover {
    background: #5a6268;
    transform: scale(1.05);
}

.audio-record-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.audio-recording-indicator {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 6px;
    color: #856404;
    font-size: 13px;
}

.audio-recording-pulse {
    width: 8px;
    height: 8px;
    background: #dc3545;
    border-radius: 50%;
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

.audio-player-list {
    margin-top: 15px;
}

.audio-player-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    margin-bottom: 8px;
}

.audio-player-item audio {
    flex: 1;
    max-width: 300px;
}

.audio-delete-btn {
    padding: 6px 12px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
}

.audio-delete-btn:hover {
    background: #c82333;
}

.audio-duration {
    font-size: 12px;
    color: #6c757d;
    margin-left: 10px;
}
</style>

<script>
class AudioRecorder {
    constructor(mealType, entryId) {
        this.mealType = mealType;
        this.entryId = entryId;
        this.mediaRecorder = null;
        this.audioChunks = [];
        this.stream = null;
        this.startTime = null;
    }

    async startRecording() {
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            this.mediaRecorder = new MediaRecorder(this.stream);
            this.audioChunks = [];
            this.startTime = Date.now();

            this.mediaRecorder.ondataavailable = (event) => {
                this.audioChunks.push(event.data);
            };

            this.mediaRecorder.onstop = async () => {
                const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                const duration = Math.round((Date.now() - this.startTime) / 1000);
                await this.uploadAudio(audioBlob, duration);
                this.cleanup();
            };

            this.mediaRecorder.start();
            return true;
        } catch (error) {
            console.error('Error starting recording:', error);
            alert('Impossible d\'accéder au microphone. Veuillez autoriser l\'accès.');
            return false;
        }
    }

    stopRecording() {
        if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
            this.mediaRecorder.stop();
        }
    }

    cleanup() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
    }

    async uploadAudio(audioBlob, duration) {
        const formData = new FormData();
        formData.append('audio', audioBlob, 'recording.webm');
        formData.append('meal_type', this.mealType);
        formData.append('entry_id', this.entryId);
        formData.append('duration', duration);
        formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

        try {
            const response = await fetch('<?php echo site_url("dietetic/portal/upload_audio_note"); ?>', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                alert('Note vocale enregistrée avec succès !');
                // Reload audio list for this meal
                await loadAudioNotes(this.mealType, this.entryId);
            } else {
                alert('Erreur: ' + (data.message || 'Échec de l\'enregistrement'));
            }
        } catch (error) {
            console.error('Upload error:', error);
            alert('Erreur lors de l\'envoi de la note vocale');
        }
    }
}

// Global audio recorders
const audioRecorders = {};

function initAudioRecorder(mealType, entryId) {
    const key = `${mealType}_${entryId}`;
    if (!audioRecorders[key]) {
        audioRecorders[key] = new AudioRecorder(mealType, entryId);
    }
    return audioRecorders[key];
}

async function toggleRecording(mealType, entryId) {
    const recorder = initAudioRecorder(mealType, entryId);
    const recordBtn = document.getElementById(`record-btn-${mealType}`);
    const stopBtn = document.getElementById(`stop-btn-${mealType}`);
    const indicator = document.getElementById(`recording-indicator-${mealType}`);

    if (!recorder.mediaRecorder || recorder.mediaRecorder.state === 'inactive') {
        // Start recording
        const started = await recorder.startRecording();
        if (started) {
            recordBtn.style.display = 'none';
            stopBtn.style.display = 'inline-flex';
            indicator.style.display = 'inline-flex';
        }
    } else {
        // Stop recording
        recorder.stopRecording();
        recordBtn.style.display = 'inline-flex';
        stopBtn.style.display = 'none';
        indicator.style.display = 'none';
    }
}

async function loadAudioNotes(mealType, entryId) {
    const container = document.getElementById(`audio-list-${mealType}`);
    if (!container) return;

    try {
        const response = await fetch(`<?php echo site_url("dietetic/portal/get_audio_notes"); ?>/${entryId}/${mealType}`);
        const data = await response.json();

        if (data.success && data.audios && data.audios.length > 0) {
            container.innerHTML = data.audios.map(audio => `
                <div class="audio-player-item" data-audio-id="${audio.id}">
                    <audio controls>
                        <source src="<?php echo base_url('uploads/dietetic/audio_notes/'); ?>${audio.audio_file}" type="audio/webm">
                        Votre navigateur ne supporte pas l'audio.
                    </audio>
                    <span class="audio-duration">${audio.duration}s</span>
                    <button type="button" class="audio-delete-btn" onclick="deleteAudioNote(${audio.id}, '${mealType}', ${entryId})">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p style="color: #6c757d; font-size: 13px; margin: 10px 0;">Aucune note vocale enregistrée</p>';
        }
    } catch (error) {
        console.error('Error loading audio notes:', error);
    }
}

async function deleteAudioNote(audioId, mealType, entryId) {
    if (!confirm('Supprimer cette note vocale ?')) return;

    try {
        const formData = new FormData();
        formData.append('audio_id', audioId);
        formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

        const response = await fetch('<?php echo site_url("dietetic/portal/delete_audio_note"); ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            await loadAudioNotes(mealType, entryId);
        } else {
            alert('Erreur: ' + (data.message || 'Échec de la suppression'));
        }
    } catch (error) {
        console.error('Delete error:', error);
        alert('Erreur lors de la suppression');
    }
}
</script>
