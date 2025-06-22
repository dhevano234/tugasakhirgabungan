<div>
    {{-- Audio Initialization Banner untuk Admin --}}
    <div id="admin-audio-init-banner" class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🔊</span>
                <div>
                    <h3 class="font-semibold text-yellow-800">Aktifkan Audio Antrian - Panel Admin</h3>
                    <p class="text-sm text-yellow-700">Klik tombol untuk mengaktifkan suara panggilan antrian</p>
                </div>
            </div>
            <button 
                id="admin-activate-audio-btn"
                onclick="initializeAdminAudio()"
                class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 font-medium transition-colors"
            >
                Aktifkan Audio
            </button>
        </div>
    </div>

    {{-- Audio Controls untuk Panel Admin --}}
    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 class="font-bold text-blue-800 mb-3">🎵 Audio Controls - Panel Admin</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-5 gap-2 mb-4">
            <button 
                onclick="window.testQueueCall('Test nomor antrian B001 silakan ke loket 1')"
                class="px-3 py-2 bg-blue-500 text-white rounded text-sm hover:bg-blue-600 transition-colors"
            >
                🧪 Test Audio
            </button>
            <button 
                onclick="window.stopQueueAudio()"
                class="px-3 py-2 bg-red-500 text-white rounded text-sm hover:bg-red-600 transition-colors"
            >
                🛑 Stop Audio
            </button>
            <button 
                onclick="window.getAudioStatus()"
                class="px-3 py-2 bg-green-500 text-white rounded text-sm hover:bg-green-600 transition-colors"
            >
                📊 Status
            </button>
            <button 
                onclick="initializeAdminAudio()"
                class="px-3 py-2 bg-purple-500 text-white rounded text-sm hover:bg-purple-600 transition-colors"
            >
                🔄 Re-init
            </button>
            <button 
                onclick="testMultipleAudio()"
                class="px-3 py-2 bg-orange-500 text-white rounded text-sm hover:bg-orange-600 transition-colors"
            >
                🔥 Multi Test
            </button>
        </div>
        
        <div class="text-xs text-gray-600 bg-white p-3 rounded border">
            <div id="admin-audio-status">Status: Checking...</div>
        </div>
        
        <div class="mt-3 text-xs text-gray-500 bg-gray-50 p-2 rounded">
            <strong>💡 Tips:</strong> Klik tombol "Panggil" pada tabel loket untuk memanggil antrian berikutnya
        </div>
    </div>

    <script>
    let adminAudioInitialized = false;
    let adminStatusInterval;

    async function initializeAdminAudio() {
        console.log('🎵 Admin panel - Manual audio initialization...');
        
        const button = document.getElementById('admin-activate-audio-btn');
        if (button) {
            button.disabled = true;
            button.textContent = 'Mengaktifkan...';
        }
        
        try {
            if (!window.QueueAudio) {
                console.error('❌ QueueAudio not available');
                throw new Error('QueueAudio not loaded');
            }
            
            const success = await window.QueueAudio.initializeAudio();
            
            if (success) {
                adminAudioInitialized = true;
                
                // Hide banner with animation
                const banner = document.getElementById('admin-audio-init-banner');
                if (banner) {
                    banner.style.transition = 'opacity 0.5s, transform 0.5s';
                    banner.style.opacity = '0';
                    banner.style.transform = 'translateY(-20px)';
                    setTimeout(() => {
                        banner.style.display = 'none';
                    }, 500);
                }
                
                console.log('✅ Admin audio initialization successful');
                
                // Test audio with admin-specific message
                setTimeout(() => {
                    window.QueueAudio.playQueueAudio('Audio admin berhasil diaktifkan, siap memanggil antrian');
                }, 1000);
                
            } else {
                console.error('❌ Admin audio initialization failed');
                alert('Gagal mengaktifkan audio. Pastikan browser mendukung Text-to-Speech.');
                
                if (button) {
                    button.disabled = false;
                    button.textContent = 'Coba Lagi';
                }
            }
        } catch (error) {
            console.error('❌ Admin audio initialization error:', error);
            alert('Error: ' + error.message);
            
            if (button) {
                button.disabled = false;
                button.textContent = 'Coba Lagi';
            }
        }
        
        updateAdminAudioStatus();
    }

    function updateAdminAudioStatus() {
        const statusDiv = document.getElementById('admin-audio-status');
        if (statusDiv && window.QueueAudio) {
            const status = window.QueueAudio.getStatus();
            const speechStatus = status.speechSynthesisStatus;
            
            statusDiv.innerHTML = `
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 text-xs">
                    <div>
                        <strong class="text-blue-600">Audio Status:</strong><br>
                        Admin Init: ${adminAudioInitialized ? '✅ Yes' : '❌ No'}<br>
                        Global Init: ${status.audioInitialized ? '✅ Yes' : '❌ No'}<br>
                        Currently Playing: ${status.isPlaying ? '🔊 Yes' : '🔇 No'}
                    </div>
                    <div>
                        <strong class="text-green-600">Speech API:</strong><br>
                        Available: ${status.speechSynthesisSupported ? '✅ Yes' : '❌ No'}<br>
                        Speaking: ${speechStatus?.speaking ? '✅ Yes' : '❌ No'}<br>
                        Pending: ${speechStatus?.pending ? '⏳ Yes' : '✅ No'}
                    </div>
                    <div>
                        <strong class="text-purple-600">Last Activity:</strong><br>
                        Message: ${status.lastMessage ? status.lastMessage.substring(0, 30) + '...' : 'None'}<br>
                        Time: ${status.lastPlayTime ? new Date(status.lastPlayTime).toLocaleTimeString() : 'Never'}
                    </div>
                </div>
            `;
        } else {
            if (statusDiv) {
                statusDiv.innerHTML = '<span class="text-red-600">❌ QueueAudio not available</span>';
            }
        }
    }

    function testMultipleAudio() {
        console.log('🔥 Testing multiple audio calls...');
        
        const messages = [
            'Nomor antrian A001 silakan ke loket 1',
            'Nomor antrian B002 silakan ke loket 2', 
            'Nomor antrian C003 silakan ke loket 3'
        ];
        
        messages.forEach((message, index) => {
            setTimeout(() => {
                console.log(`🔊 Playing message ${index + 1}:`, message);
                window.QueueAudio.playQueueAudio(message);
            }, index * 3000); // 3 second delay between each
        });
    }

    // Auto-check status every 4 seconds
    function startAdminStatusMonitoring() {
        adminStatusInterval = setInterval(updateAdminAudioStatus, 4000);
    }

    function stopAdminStatusMonitoring() {
        if (adminStatusInterval) {
            clearInterval(adminStatusInterval);
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔧 Admin audio controls DOM loaded');
        
        // Start status monitoring
        setTimeout(() => {
            updateAdminAudioStatus();
            startAdminStatusMonitoring();
        }, 1500);
        
        // Auto-hide banner if already initialized
        setTimeout(async () => {
            if (window.speechSynthesis && window.QueueAudio) {
                try {
                    const voices = await window.QueueAudio.getVoicesWithTimeout();
                    if (voices.length > 0) {
                        console.log('🎤 Admin - Voices already available, auto-initializing...');
                        await initializeAdminAudio();
                    }
                } catch (error) {
                    console.log('⚠️ Admin - Auto-initialization skipped:', error);
                }
            }
        }, 3000);
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        stopAdminStatusMonitoring();
    });

    // Debug Livewire events in admin panel
    document.addEventListener('livewire:initialized', () => {
        console.log('🔧 Admin panel - Livewire initialized for audio');
        
        Livewire.on('queue-called', (message) => {
            console.log('🔧 Admin panel - queue-called event received:', message);
            updateAdminAudioStatus();
            
            // Visual feedback
            const statusDiv = document.getElementById('admin-audio-status');
            if (statusDiv) {
                statusDiv.style.border = '2px solid #10b981';
                setTimeout(() => {
                    statusDiv.style.border = '1px solid #d1d5db';
                }, 2000);
            }
        });
    });

    // Global admin audio check
    setTimeout(() => {
        if (window.QueueAudio) {
            console.log('✅ QueueAudio available in admin panel');
        } else {
            console.error('❌ QueueAudio not available in admin panel');
        }
        
        if ('speechSynthesis' in window) {
            console.log('✅ Speech Synthesis API available in admin panel');
        } else {
            console.warn('⚠️ Speech Synthesis API not supported in admin panel');
        }
    }, 2000);
    </script>
</div>