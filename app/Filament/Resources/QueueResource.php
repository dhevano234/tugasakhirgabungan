<?php
// File: app/Filament/Resources/QueueResource.php (untuk admin)

namespace App\Filament\Resources;

use App\Filament\Resources\QueueResource\Pages;
use App\Models\Queue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QueueResource extends Resource
{
    protected static ?string $model = Queue::class;

    protected static ?string $label = 'Antrian';

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationGroup = 'Administrasi';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canUpdate(): bool
    {
        return false;
    }
    
    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('service_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('counter_id')
                    ->numeric(),
                Forms\Components\TextInput::make('number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('waiting'),
                Forms\Components\DateTimePicker::make('called_at'),
                Forms\Components\DateTimePicker::make('served_at'),
                Forms\Components\DateTimePicker::make('canceled_at'),
                Forms\Components\DateTimePicker::make('finished_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // ✅ PERBAIKAN: Kolom harus di dalam ->columns([])
                Tables\Columns\TextColumn::make('number')
                    ->label('Nomor Antrian')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Layanan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama User')
                    ->default('Walk-in')
                    ->searchable()
                    ->limit(25)
                    ->description(fn (Queue $record): string => 
                        $record->user ? $record->user->email : 'Antrian tanpa akun'
                    ),

                Tables\Columns\TextColumn::make('user.phone')
                    ->label('No. HP')
                    ->default('-')
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Nomor HP disalin'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'waiting' => 'Menunggu',
                        'serving' => 'Dilayani',
                        'finished' => 'Selesai',
                        'canceled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'waiting' => 'warning',
                        'serving' => 'success',
                        'finished' => 'primary',
                        'canceled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('called_at')
                    ->label('Waktu Dipanggil')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Belum dipanggil'),

                Tables\Columns\TextColumn::make('served_at')
                    ->label('Waktu Dilayani')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Belum dilayani'),

                Tables\Columns\TextColumn::make('finished_at')
                    ->label('Waktu Selesai')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Belum selesai'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($state) => $state->format('l, d F Y - H:i:s')),
            ])
            ->filters([
                // ✅ PERBAIKAN: Filter lengkap
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'waiting' => 'Menunggu',
                        'serving' => 'Dilayani',
                        'finished' => 'Selesai',
                        'canceled' => 'Dibatalkan',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('service')
                    ->label('Layanan')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('created_at')
                    ->label('Hari Ini')
                    ->query(fn ($query) => $query->whereDate('created_at', today()))
                    ->default(),

                Tables\Filters\Filter::make('this_week')
                    ->label('Minggu Ini')
                    ->query(fn ($query) => $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])),

                Tables\Filters\Filter::make('has_user')
                    ->label('Memiliki Akun')
                    ->query(fn ($query) => $query->whereNotNull('user_id')),

                Tables\Filters\Filter::make('walk_in')
                    ->label('Walk-in')
                    ->query(fn ($query) => $query->whereNull('user_id')),
            ])
            ->actions([
                // ✅ PERBAIKAN: Actions yang sesuai untuk admin
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye'),
                        
                    Tables\Actions\Action::make('change_status')
                        ->label('Ubah Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Status Baru')
                                ->options([
                                    'waiting' => 'Menunggu',
                                    'serving' => 'Dilayani',
                                    'finished' => 'Selesai',
                                    'canceled' => 'Dibatalkan',
                                ])
                                ->required()
                                ->native(false),
                        ])
                        ->action(function (Queue $record, array $data) {
                            $record->update([
                                'status' => $data['status'],
                                $data['status'] . '_at' => now(),
                            ]);
                        })
                        ->successNotificationTitle('Status antrian berhasil diubah'),
                        
                    Tables\Actions\Action::make('assign_counter')
                        ->label('Assign Loket')
                        ->icon('heroicon-o-building-office')
                        ->color('info')
                        ->visible(fn (Queue $record) => !$record->counter_id)
                        ->form([
                            Forms\Components\Select::make('counter_id')
                                ->label('Pilih Loket')
                                ->relationship('counter', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),
                        ])
                        ->action(function (Queue $record, array $data) {
                            $record->update($data);
                        })
                        ->successNotificationTitle('Loket berhasil di-assign'),

                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation(),
                ])
                ->label('Aksi')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_as_served')
                        ->label('Tandai Dilayani')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->status === 'waiting') {
                                    $record->update([
                                        'status' => 'serving',
                                        'served_at' => now(),
                                    ]);
                                }
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Tandai sebagai dilayani')
                        ->modalDescription('Apakah Anda yakin ingin menandai antrian yang dipilih sebagai sedang dilayani?'),

                    Tables\Actions\BulkAction::make('mark_as_finished')
                        ->label('Tandai Selesai')
                        ->icon('heroicon-o-check-badge')
                        ->color('primary')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if (in_array($record->status, ['waiting', 'serving'])) {
                                    $record->update([
                                        'status' => 'finished',
                                        'finished_at' => now(),
                                    ]);
                                }
                            });
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('5s') // Auto refresh setiap 5 detik
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->emptyStateHeading('Belum ada antrian')
            ->emptyStateDescription('Antrian akan muncul di sini setelah pasien mengambil nomor antrian.')
            ->emptyStateIcon('heroicon-o-queue-list');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageQueues::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'waiting')->count() ?: null;
    }
    
    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah antrian yang sedang menunggu';
    }
}