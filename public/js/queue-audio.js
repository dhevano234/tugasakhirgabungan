// File: public/js/queue-audio.js
// SISTEM AUDIO UNTUK PANEL ADMIN (Global System - tetap seperti sebelumnya)

/**
 * Queue Audio System - ALWAYS READY VERSION untuk PANEL ADMIN
 */

// Global state untuk Admin Panel
window.AdminAudioState = {
    isInitialized: false,
    isPlaying: false,
    lastMessage: null,
    voices: [],
    preferredVoice: null
};

// Sistem Audio untuk Panel Admin
window.AdminQueueAudio = {
    isSupported() {
        return 'speechSynthesis' in window;
    },

    async initializeAudio() {
        const state = window.AdminAudioState;
        
        if (!this.isSupported()) {
            console.warn('Speech synthesis not supported');
            return false;
        }

        if (state.isInitialized) {
            return true;
        }

        try {
            await this._waitForVoices();
            await this._silentTest();
            
            state.isInitialized = true;
            console.log('🔊 Admin Panel Audio - Ready');
            return true;
            
        } catch (error) {
            console.warn('Admin audio initialization failed:', error.message);
            return false;
        }
    },

    _waitForVoices(timeout = 5000) {
        return new Promise((resolve) => {
            const state = window.AdminAudioState;
            let resolved = false;
            
            const checkVoices = () => {
                if (resolved) return;
                
                const voices = speechSynthesis.getVoices();
                if (voices.length > 0) {
                    state.voices = voices;
                    state.preferredVoice = voices.find(voice => 
                        voice.lang.includes('id') || voice.name.toLowerCase().includes('indonesia')
                    ) || voices[0];
                    
                    console.log(`🎤 Admin - Loaded ${voices.length} voices`);
                    resolved = true;
                    resolve();
                }
            };

            checkVoices();
            speechSynthesis.onvoiceschanged = checkVoices;
            
            setTimeout(() => {
                if (!resolved) {
                    resolved = true;
                    resolve();
                }
            }, timeout);
        });
    },

    _silentTest() {
        return new Promise((resolve) => {
            try {
                const utterance = new SpeechSynthesisUtterance(' ');
                utterance.volume = 0;
                utterance.rate = 10;
                utterance.onend = () => resolve();
                utterance.onerror = () => resolve();
                
                speechSynthesis.speak(utterance);
                setTimeout(resolve, 100);
            } catch (error) {
                resolve();
            }
        });
    },

    async playQueueAudio(message) {
        const state = window.AdminAudioState;
        
        if (!message) return false;

        if (!state.isInitialized) {
            await this.initializeAudio();
        }

        try {
            state.isPlaying = true;
            state.lastMessage = message;
            
            const utterance = new SpeechSynthesisUtterance(message);
            utterance.rate = 0.9;
            utterance.volume = 1.0;
            utterance.lang = 'id-ID';
            
            if (state.preferredVoice) {
                utterance.voice = state.preferredVoice;
            }
            
            utterance.onstart = () => {
                console.log('🔊 Admin Panel - Audio playing:', message.substring(0, 50) + '...');
            };
            
            utterance.onend = () => {
                state.isPlaying = false;
                console.log('✅ Admin Panel - Audio completed');
            };
            
            utterance.onerror = (error) => {
                console.warn('Admin Panel Audio error:', error.error);
                state.isPlaying = false;
            };
            
            speechSynthesis.cancel();
            speechSynthesis.speak(utterance);
            
            return true;
            
        } catch (error) {
            console.error('Admin Panel Audio error:', error);
            state.isPlaying = false;
            return false;
        }
    },

    stop() {
        try {
            speechSynthesis.cancel();
            window.AdminAudioState.isPlaying = false;
        } catch (error) {
            console.warn('Error stopping admin audio:', error);
        }
    }
};

// Global functions untuk Admin Panel
window.handleQueueCall = function(message) {
    console.log('📞 Admin Panel - Queue call:', message);
    return window.AdminQueueAudio.playQueueAudio(message);
};

window.playQueueAudio = function(message) {
    return window.AdminQueueAudio.playQueueAudio(message);
};

window.stopQueueAudio = function() {
    return window.AdminQueueAudio.stop();
};

// Auto-initialize untuk Admin Panel
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎵 Admin Panel Audio System - Loading...');
    
    setTimeout(async () => {
        await window.AdminQueueAudio.initializeAudio();
    }, 1000);
});

// Livewire events untuk Admin Panel
document.addEventListener('livewire:initialized', function() {
    if (window.Livewire && window.Livewire.on) {
        window.Livewire.on('queue-called', function(message) {
            window.handleQueueCall(message);
        });
        console.log('✅ Admin Panel - Livewire events registered');
    }
});

console.log('✅ Admin Panel Audio System - Loaded');