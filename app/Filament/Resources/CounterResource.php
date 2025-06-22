<?php
namespace App\Filament\Resources;

use App\Filament\Resources\CounterResource\Pages;
use App\Models\Counter;
use App\Services\QueueService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class CounterResource extends Resource
{
    protected static ?string $model = Counter::class;
    
    protected static ?string $label = "Loket";
    
    protected static ?string $pluralLabel = "Loket";
    
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    
    protected static ?string $navigationGroup = 'Administrasi';
    
    protected static ?string $navigationLabel = 'Kelola Loket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Loket')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Loket')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Loket 1')
                            ->helperText('Nama loket yang akan ditampilkan'),
                            
                        Forms\Components\Select::make('service_id')
                            ->label('Layanan')
                            ->required()
                            ->relationship('service', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih layanan yang akan dilayani oleh loket ini'),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->required()
                            ->helperText('Loket hanya bisa digunakan jika status aktif'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Loket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-building-office'),
                    
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Layanan')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-cog-6-tooth'),
                    
                Tables\Columns\TextColumn::make('activeQueue.number')
                    ->label('Antrian Aktif')
                    ->placeholder('Tidak ada')
                    ->badge()
                    ->size('lg')
                    ->weight('bold')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->icon(fn ($state) => $state ? 'heroicon-m-queue-list' : 'heroicon-m-minus'),
                    
                Tables\Columns\TextColumn::make('activeQueue.status')
                    ->label('Status Antrian')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'waiting' => 'Menunggu',
                        'serving' => 'Sedang Dilayani',
                        'finished' => 'Selesai',
                        'canceled' => 'Dibatalkan',
                        null => 'Kosong',
                        default => $state,
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'waiting' => 'warning',
                        'serving' => 'success',
                        'finished' => 'primary',
                        'canceled' => 'danger',
                        null => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (?string $state): string => match ($state) {
                        'waiting' => 'heroicon-m-clock',
                        'serving' => 'heroicon-m-play',
                        'finished' => 'heroicon-m-check-circle',
                        'canceled' => 'heroicon-m-x-circle',
                        null => 'heroicon-m-minus',
                        default => 'heroicon-m-question-mark-circle',
                    }),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status Loket')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Loket')
                    ->placeholder('Semua Loket')
                    ->trueLabel('Hanya Aktif')
                    ->falseLabel('Hanya Tidak Aktif'),
                    
                Tables\Filters\SelectFilter::make('service')
                    ->label('Layanan')
                    ->relationship('service', 'name')
                    ->placeholder('Semua Layanan'),
                    
                Tables\Filters\Filter::make('has_queue')
                    ->label('Memiliki Antrian')
                    ->query(fn ($query) => $query->whereHas('activeQueue')),
            ])
            ->actions([
                // ===== ACTION PANGGIL ANTRIAN (MAIN FEATURE) =====
                Action::make('callNextQueue')
                    ->label('Panggil')
                    ->icon('heroicon-o-speaker-wave')
                    ->color('warning')
                    ->size('sm')
                    ->button()
                    ->visible(fn(Counter $record) => $record->hasNextQueue && $record->is_active)
                    ->action(function (Counter $record, $livewire) {
                        try {
                            // Get next queue using service
                            $nextQueue = app(QueueService::class)->callNextQueue($record->id);

                            if (!$nextQueue) {
                                Notification::make()
                                    ->title('Tidak ada antrian tersedia')
                                    ->body('Tidak ada antrian yang menunggu di ' . $record->name)
                                    ->warning()
                                    ->duration(5000)
                                    ->send();
                                return;
                            }

                            // Update queue status
                            $nextQueue->update([
                                'status' => 'serving',
                                'called_at' => now(),
                            ]);

                            // Prepare audio message
                            $message = "Nomor antrian {$nextQueue->number} segera ke {$record->name}";

                            // Show success notification
                            Notification::make()
                                ->title("🔊 Antrian {$nextQueue->number} berhasil dipanggil!")
                                ->body("Mengarahkan pasien ke {$record->name}")
                                ->success()
                                ->duration(8000)
                                ->send();

                            // ===== TRIGGER AUDIO - METHOD 1: LIVEWIRE EVENT =====
                            $livewire->dispatch('queue-called', $message);
                            
                            // ===== TRIGGER AUDIO - METHOD 2: DIRECT JAVASCRIPT =====
                            $livewire->js("
                                console.log('🔊 Admin Panel: Triggering audio for message: $message');
                                
                                // Try multiple methods to ensure audio plays
                                if (window.handleQueueCall) {
                                    console.log('✅ Using handleQueueCall function');
                                    window.handleQueueCall('$message');
                                } else if (window.QueueAudio && window.QueueAudio.speak) {
                                    console.log('✅ Using QueueAudio.speak');
                                    window.QueueAudio.speak('$message');
                                } else if (window.playQueueAudio) {
                                    console.log('✅ Using playQueueAudio function');
                                    window.playQueueAudio('$message');
                                } else {
                                    console.log('⚠️ Using direct speechSynthesis fallback');
                                    if (window.speechSynthesis) {
                                        speechSynthesis.cancel();
                                        const utterance = new SpeechSynthesisUtterance('$message');
                                        utterance.lang = 'id-ID';
                                        utterance.rate = 0.9;
                                        utterance.volume = 1.0;
                                        speechSynthesis.speak(utterance);
                                        console.log('✅ Direct speechSynthesis executed');
                                    } else {
                                        console.error('❌ speechSynthesis not available');
                                    }
                                }
                            ");

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('❌ Error')
                                ->body('Gagal memanggil antrian: ' . $e->getMessage())
                                ->danger()
                                ->duration(10000)
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Panggil Antrian Berikutnya')
                    ->modalDescription(fn(Counter $record) => "Panggil antrian berikutnya untuk {$record->name}? Audio akan diputar secara otomatis.")
                    ->modalSubmitActionLabel('Ya, Panggil Sekarang')
                    ->modalCancelActionLabel('Batal')
                    ->tooltip('Panggil antrian berikutnya dengan audio'),

                // ===== ACTION MULAI MELAYANI =====
                Action::make('serve')
                    ->label('Layani')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->size('sm')
                    ->button()
                    ->visible(fn(Counter $record) => $record->is_active && $record->activeQueue && $record->activeQueue->status === 'waiting')
                    ->action(function (Counter $record) {
                        try {
                            app(QueueService::class)->serveQueue($record->activeQueue);
                            
                            Notification::make()
                                ->title("✅ Antrian {$record->activeQueue->number} mulai dilayani")
                                ->body("Status diubah menjadi 'Sedang Dilayani'")
                                ->success()
                                ->duration(5000)
                                ->send();
                                
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('❌ Error')
                                ->body('Gagal melayani antrian: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Mulai Melayani')
                    ->modalDescription(fn(Counter $record) => "Mulai melayani antrian {$record->activeQueue?->number}?")
                    ->tooltip('Ubah status menjadi sedang dilayani'),

                // ===== ACTION SELESAIKAN ANTRIAN =====
                Action::make('finishQueue')
                    ->label('Selesai')
                    ->icon('heroicon-o-check')
                    ->color('primary')
                    ->size('sm')
                    ->button()
                    ->visible(fn(Counter $record) => $record->activeQueue?->status === 'serving')
                    ->action(function (Counter $record) {
                        try {
                            app(QueueService::class)->finishQueue($record->activeQueue);
                            
                            Notification::make()
                                ->title("🎉 Antrian {$record->activeQueue->number} selesai dilayani")
                                ->body("Loket {$record->name} siap untuk antrian berikutnya")
                                ->success()
                                ->duration(5000)
                                ->send();
                                
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('❌ Error')
                                ->body('Gagal menyelesaikan antrian: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Selesaikan Antrian')
                    ->modalDescription(fn(Counter $record) => "Selesaikan antrian {$record->activeQueue?->number}?")
                    ->tooltip('Tandai antrian sebagai selesai'),

                // ===== ACTION BATALKAN ANTRIAN =====
                Action::make('cancelQueue')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->size('sm')
                    ->button()
                    ->visible(fn(Counter $record) => $record->is_active && $record->activeQueue && in_array($record->activeQueue->status, ['waiting', 'serving']))
                    ->action(function (Counter $record) {
                        try {
                            app(QueueService::class)->cancelQueue($record->activeQueue);
                            
                            Notification::make()
                                ->title("⚠️ Antrian {$record->activeQueue->number} dibatalkan")
                                ->body("Loket {$record->name} siap untuk antrian berikutnya")
                                ->warning()
                                ->duration(5000)
                                ->send();
                                
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('❌ Error')
                                ->body('Gagal membatalkan antrian: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Antrian')
                    ->modalDescription(fn(Counter $record) => "Batalkan antrian {$record->activeQueue?->number}? Tindakan ini tidak dapat dibatalkan.")
                    ->tooltip('Batalkan antrian saat ini'),

                // ===== ACTION GROUP UNTUK MANAGEMENT =====
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye'),
                        
                    Tables\Actions\EditAction::make()
                        ->label('Edit Loket')
                        ->icon('heroicon-o-pencil-square'),
                        
                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus Loket')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Loket')
                        ->modalDescription('Apakah Anda yakin ingin menghapus loket ini? Data tidak dapat dikembalikan.'),
                ])
                ->label('Kelola')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->tooltip('Kelola loket'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // ===== BULK ACTION: PANGGIL MULTIPLE ANTRIAN =====
                    Tables\Actions\BulkAction::make('call_multiple')
                        ->label('Panggil Semua')
                        ->icon('heroicon-o-megaphone')
                        ->color('warning')
                        ->action(function ($records, $livewire) {
                            $called = 0;
                            $messages = [];
                            
                            foreach ($records as $record) {
                                if ($record->hasNextQueue && $record->is_active) {
                                    try {
                                        $nextQueue = app(QueueService::class)->callNextQueue($record->id);
                                        
                                        if ($nextQueue) {
                                            $nextQueue->update([
                                                'status' => 'serving',
                                                'called_at' => now(),
                                            ]);
                                            
                                            $message = "Nomor antrian {$nextQueue->number} segera ke {$record->name}";
                                            $messages[] = $message;
                                            $called++;
                                            
                                            // Trigger audio for each with delay
                                            $livewire->dispatch('queue-called', $message);
                                            
                                            // Add delay between calls
                                            if ($called < count($records)) {
                                                sleep(2);
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        // Continue with next record
                                    }
                                }
                            }
                            
                            if ($called > 0) {
                                Notification::make()
                                    ->title("🔊 {$called} antrian berhasil dipanggil")
                                    ->body("Audio diputar dengan jeda 2 detik antar panggilan")
                                    ->success()
                                    ->duration(8000)
                                    ->send();
                                    
                                // Trigger multiple audio calls
                                $livewire->js("
                                    const messages = " . json_encode($messages) . ";
                                    messages.forEach((message, index) => {
                                        setTimeout(() => {
                                            if (window.handleQueueCall) {
                                                window.handleQueueCall(message);
                                            } else if (window.QueueAudio && window.QueueAudio.speak) {
                                                window.QueueAudio.speak(message);
                                            }
                                        }, index * 3000);
                                    });
                                ");
                            } else {
                                Notification::make()
                                    ->title('⚠️ Tidak ada antrian yang bisa dipanggil')
                                    ->body('Pastikan loket aktif dan memiliki antrian yang menunggu')
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Panggil Multiple Antrian')
                        ->modalDescription('Panggil semua antrian yang dipilih? Audio akan diputar dengan jeda 3 detik antar panggilan.')
                        ->deselectRecordsAfterCompletion(),

                    // ===== BULK ACTION: AKTIFKAN LOKET =====
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $activated = 0;
                            foreach ($records as $record) {
                                if (!$record->is_active) {
                                    $record->update(['is_active' => true]);
                                    $activated++;
                                }
                            }
                            
                            Notification::make()
                                ->title("✅ {$activated} loket berhasil diaktifkan")
                                ->success()
                                ->send();
                        }),
                        
                    // ===== BULK ACTION: NONAKTIFKAN LOKET =====
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $deactivated = 0;
                            foreach ($records as $record) {
                                if ($record->is_active) {
                                    $record->update(['is_active' => false]);
                                    $deactivated++;
                                }
                            }
                            
                            Notification::make()
                                ->title("⚠️ {$deactivated} loket berhasil dinonaktifkan")
                                ->warning()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Nonaktifkan Loket')
                        ->modalDescription('Loket yang dinonaktifkan tidak dapat melayani antrian.'),

                    // ===== BULK ACTION: DELETE =====
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Loket')
                        ->modalDescription('Apakah Anda yakin ingin menghapus loket yang dipilih?'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('5s') // Auto refresh every 5 seconds
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->searchable()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->extremePaginationLinks()
            ->emptyStateHeading('Belum ada loket')
            ->emptyStateDescription('Buat loket pertama untuk mulai melayani antrian.')
            ->emptyStateIcon('heroicon-o-ticket');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCounters::route('/'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count() ?: null;
    }
    
    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}