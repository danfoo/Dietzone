<!-- Recommendation Audio Recorder Component -->
<style>
.rec-audio-recorder-wrapper {
    margin-top: 12px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.rec-audio-recorder-controls {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}

.rec-audio-record-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    padding: 0;
    border: none;
    border-radius: 50%;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
}

.rec-audio-record-btn i {
    margin: 0;
}

.rec-audio-record-btn.record {
    background: #F3911D;
    color: white;
}

.rec-audio-record-btn.record:hover {
    background: #e07d0f;
    transform: scale(1.08);
    box-shadow: 0 3px 10px rgba(243, 145, 29, 0.3);
}

.rec-audio-record-btn.stop {
    background: #6c757d;
    color: white;
}

.rec-audio-record-btn.stop:hover {
    background: #5a6268;
    transform: scale(1.08);
}

.rec-audio-record-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.rec-audio-recording-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 4px;
    color: #856404;
    font-size: 11px;
}

.rec-audio-recording-pulse {
    width: 6px;
    height: 6px;
    background: #dc3545;
    border-radius: 50%;
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

.rec-audio-player-list {
    margin-top: 8px;
}

.rec-audio-player-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    margin-bottom: 8px;
}

.rec-audio-player-item.sender-dietitian {
    background: linear-gradient(135deg, rgba(1, 128, 123, 0.05) 0%, rgba(1, 128, 123, 0.1) 100%);
    border-left: 3px solid #01807B;
}

.rec-audio-player-item.sender-patient {
    background: linear-gradient(135deg, rgba(243, 145, 29, 0.05) 0%, rgba(243, 145, 29, 0.1) 100%);
    border-left: 3px solid #F3911D;
}

.rec-audio-player-item audio {
    flex: 1;
    max-width: 100%;
    height: 32px;
}

.rec-audio-info {
    display: flex;
    flex-direction: column;
    font-size: 10px;
    color: #6c757d;
    min-width: 120px;
}

.rec-audio-sender {
    font-weight: 600;
    color: #495057;
}

.rec-audio-delete-btn {
    padding: 4px 8px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-size: 11px;
    white-space: nowrap;
}

.rec-audio-delete-btn:hover {
    background: #c82333;
}

.rec-audio-duration {
    font-size: 11px;
    color: #6c757d;
}
</style>

<script>
// Recommendation Audio Recorder Class
class RecommendationAudioRecorder {
    constructor(recommendationId, uploadUrl, deleteUrl, isPatient = false) {
        this.recommendationId = recommendationId;
        this.uploadUrl = uploadUrl;
        this.deleteUrl = deleteUrl;
        this.isPatient = isPatient;
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
        if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
            this.mediaRecorder.stop();
        }
    }

    cleanup() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
        this.mediaRecorder = null;
        this.audioChunks = [];
    }

    async uploadAudio(audioBlob, duration) {
        const formData = new FormData();
        formData.append('audio', audioBlob, 'recording.webm');
        formData.append('recommendation_id', this.recommendationId);
        formData.append('duration', duration);
        formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

        try {
            const response = await fetch(this.uploadUrl, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Reload audio list
                loadRecommendationAudios(this.recommendationId, this.isPatient);
            } else {
                alert('Erreur lors de l\'envoi de la note vocale: ' + (result.message || 'Erreur inconnue'));
            }
        } catch (error) {
            console.error('Upload error:', error);
            alert('Erreur lors de l\'envoi de la note vocale');
        }
    }

    async deleteAudio(audioId) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette note vocale ?')) {
            return;
        }

        const formData = new FormData();
        formData.append('audio_id', audioId);
        formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

        try {
            const response = await fetch(this.deleteUrl, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Reload audio list
                loadRecommendationAudios(this.recommendationId, this.isPatient);
            } else {
                alert('Erreur lors de la suppression: ' + (result.message || 'Erreur inconnue'));
            }
        } catch (error) {
            console.error('Delete error:', error);
            alert('Erreur lors de la suppression');
        }
    }
}

// Global recorders storage
const recommendationRecorders = {};

function toggleRecommendationRecording(recommendationId, uploadUrl, deleteUrl, isPatient = false) {
    if (!recommendationRecorders[recommendationId]) {
        recommendationRecorders[recommendationId] = new RecommendationAudioRecorder(recommendationId, uploadUrl, deleteUrl, isPatient);
    }

    const recorder = recommendationRecorders[recommendationId];
    const recordBtn = document.getElementById(`rec-record-btn-${recommendationId}`);
    const stopBtn = document.getElementById(`rec-stop-btn-${recommendationId}`);
    const indicator = document.getElementById(`rec-recording-indicator-${recommendationId}`);

    if (!recorder.mediaRecorder || recorder.mediaRecorder.state === 'inactive') {
        recorder.startRecording().then(success => {
            if (success) {
                recordBtn.style.display = 'none';
                stopBtn.style.display = 'inline-flex';
                indicator.style.display = 'inline-flex';
            }
        });
    } else {
        recorder.stopRecording();
        recordBtn.style.display = 'inline-flex';
        stopBtn.style.display = 'none';
        indicator.style.display = 'none';
    }
}

async function loadRecommendationAudios(recommendationId, isPatient = false) {
    const listContainer = document.getElementById(`rec-audio-list-${recommendationId}`);
    if (!listContainer) return;

    const getUrl = isPatient
        ? `<?php echo site_url('dietetic/portal/get_recommendation_audio_notes/'); ?>${recommendationId}`
        : `<?php echo admin_url('dietetic/food_surveys/get_recommendation_audios/'); ?>${recommendationId}`;

    try {
        const response = await fetch(getUrl);
        const result = await response.json();

        if (result.success && result.audios && result.audios.length > 0) {
            listContainer.innerHTML = '';
            result.audios.forEach(audio => {
                const senderClass = audio.sender_type === 'dietitian' ? 'sender-dietitian' : 'sender-patient';
                const canDelete = isPatient
                    ? (audio.sender_type === 'patient')
                    : (audio.sender_type === 'dietitian');

                const audioItem = document.createElement('div');
                audioItem.className = `rec-audio-player-item ${senderClass}`;
                audioItem.innerHTML = `
                    <div class="rec-audio-info">
                        <span class="rec-audio-sender">${audio.sender_name || (audio.sender_type === 'dietitian' ? 'Diététicien' : 'Patient')}</span>
                        <span class="rec-audio-duration">${formatDuration(audio.duration)} - ${formatDate(audio.created_at)}</span>
                    </div>
                    <audio controls>
                        <source src="<?php echo base_url('uploads/dietetic/recommendation_audio/'); ?>${audio.audio_file}" type="audio/webm">
                        <source src="<?php echo base_url('uploads/dietetic/recommendation_audio/'); ?>${audio.audio_file}" type="audio/mpeg">
                    </audio>
                    ${canDelete ? `<button class="rec-audio-delete-btn" onclick="deleteRecommendationAudio(${recommendationId}, ${audio.id}, ${isPatient})"><i class="fa fa-trash"></i></button>` : ''}
                `;
                listContainer.appendChild(audioItem);
            });
        } else {
            listContainer.innerHTML = '<p style="color: #6c757d; font-size: 12px; margin: 0;">Aucune note vocale</p>';
        }
    } catch (error) {
        console.error('Error loading audios:', error);
        listContainer.innerHTML = '<p style="color: #dc3545; font-size: 12px; margin: 0;">Erreur de chargement</p>';
    }
}

function deleteRecommendationAudio(recommendationId, audioId, isPatient = false) {
    if (!recommendationRecorders[recommendationId]) {
        const uploadUrl = isPatient
            ? '<?php echo site_url('dietetic/portal/upload_recommendation_audio_response'); ?>'
            : '<?php echo admin_url('dietetic/food_surveys/upload_recommendation_audio'); ?>';
        const deleteUrl = isPatient
            ? '<?php echo site_url('dietetic/portal/delete_recommendation_audio_response'); ?>'
            : '<?php echo admin_url('dietetic/food_surveys/delete_recommendation_audio'); ?>';

        recommendationRecorders[recommendationId] = new RecommendationAudioRecorder(recommendationId, uploadUrl, deleteUrl, isPatient);
    }

    recommendationRecorders[recommendationId].deleteAudio(audioId);
}

function formatDuration(seconds) {
    if (!seconds) return '0:00';
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>
