<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
    
    * {
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Poppins', sans-serif;
        background: #ffffff;
        min-height: 100vh;
        overflow-x: hidden;
    }
    
    .main-container {
        background: #ffffff;
        position: relative;
        z-index: 1;
    }
    
    .counter-card {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }
    
    .counter-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, transparent, #3b82f6, transparent);
        transition: left 0.8s ease;
    }
    
    .counter-card:hover::before {
        left: 100%;
    }
    
    .counter-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 51, 234, 0.1));
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    
    .counter-card:hover::after {
        opacity: 1;
    }
    
    .counter-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 
            0 32px 64px -12px rgba(0, 0, 0, 0.25),
            0 0 0 1px rgba(59, 130, 246, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.5);
        border-color: rgba(59, 130, 246, 0.3);
    }
    
    .counter-title {
        background: linear-gradient(135deg, #1e293b, #475569);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 800;
        position: relative;
    }
    
    .service-name {
        color: #64748b;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.875rem;
    }
    
    .queue-number-active {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 900;
        position: relative;
        animation: glow 2s ease-in-out infinite alternate;
    }
    
    .queue-number-active::before {
        content: attr(data-number);
        position: absolute;
        top: 0;
        left: 0;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: blur(8px);
        opacity: 0.7;
        z-index: -1;
    }
    
    @keyframes glow {
        from { filter: drop-shadow(0 0 5px rgba(59, 130, 246, 0.5)); }
        to { filter: drop-shadow(0 0 20px rgba(139, 92, 246, 0.8)); }
    }
    
    .queue-number-inactive {
        color: #cbd5e1;
        font-weight: 900;
        opacity: 0.6;
        animation: breathe 3s ease-in-out infinite;
    }
    
    @keyframes breathe {
        0%, 100% { opacity: 0.4; }
        50% { opacity: 0.7; }
    }
    
    .kiosk-label {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
        font-weight: 600;
        position: relative;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
        animation: shimmer 3s ease-in-out infinite;
    }
    
    .kiosk-label::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, #fbbf24, #f59e0b, #fbbf24);
        border-radius: inherit;
        z-index: -1;
        opacity: 0.5;
        animation: rotate 4s linear infinite;
    }
    
    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    @keyframes shimmer {
        0%, 100% { box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4); }
        50% { box-shadow: 0 8px 25px rgba(245, 158, 11, 0.6); }
    }
    
    .status-badge {
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.75rem;
        position: relative;
        overflow: hidden;
    }
    
    .status-inactive {
        background: linear-gradient(135deg, #64748b, #475569);
        color: white;
        animation: fadeInOut 4s ease-in-out infinite;
    }
    
    @keyframes fadeInOut {
        0%, 100% { opacity: 0.7; }
        50% { opacity: 1; }
    }
    
    .status-available {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .status-serving {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        animation: serving-pulse 1.5s ease-in-out infinite;
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.5);
    }
    
    @keyframes serving-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    
    .no-queue-text {
        color: #94a3b8;
        font-weight: 500;
        font-style: italic;
        animation: fadeInOut 3s ease-in-out infinite;
    }
    
    /* Responsive grid improvements */
    .counter-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
        padding: 2rem;
    }
    
    @media (max-width: 768px) {
        .counter-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
            padding: 1rem;
        }
        
        .counter-card {
            padding: 1.5rem;
        }
        
        .queue-number-active,
        .queue-number-inactive {
            font-size: 2.5rem;
        }
    }
    
    /* Loading animation for wire:poll */
    .loading-indicator {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 20px;
        height: 20px;
        border: 2px solid rgba(59, 130, 246, 0.3);
        border-top: 2px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .counter-card:hover .loading-indicator {
        opacity: 1;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<div class="flex flex-col flex-grow main-container" wire:poll.500ms="callNextQueue">
    
    <div class="counter-grid">
        @foreach($counters as $counter)
        <div class="counter-card p-6 rounded-xl shadow-2xl text-center relative">
            <div class="mb-6">
                <h2 class="counter-title text-2xl mb-2">{{ $counter->name }}</h2>
                <p class="service-name">{{ $counter->service->name }}</p>
            </div>

            <div class="space-y-3">
                @if($counter->activeQueue)
                    <div class="queue-number-active text-5xl font-bold" data-number="{{ $counter->activeQueue->number }}">
                        {{ $counter->activeQueue->number }}
                    </div>

                    <div class="kiosk-label text-lg font-semibold px-6 py-2 rounded-full inline-block">
                        {{ $counter->activeQueue->kiosk_label }}
                    </div>
                @else
                    <div class="queue-number-inactive text-5xl font-bold">
                        ---
                    </div>
                    <div class="no-queue-text text-lg">
                        Tidak ada antrian
                    </div>
                @endif
            </div>

            <div class="mt-6">
                <p class="status-badge text-sm rounded-full px-4 py-2 inline-block relative
                    @if(!$counter->is_active)
                        status-inactive
                    @elseif($counter->is_available)
                        status-available
                    @else
                        status-serving
                    @endif
                ">
                    @if(!$counter->is_active)
                        Loket tidak aktif
                    @elseif($counter->is_available)
                        Siap Melayani
                    @else
                        Sedang Melayani
                    @endif
                </p>
            </div>
        </div>
        @endforeach
    </div>
</div>