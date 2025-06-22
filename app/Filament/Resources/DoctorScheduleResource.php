<?php
// File: app/Filament/Resources/DoctorScheduleResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorScheduleResource\Pages;
use App\Models\DoctorSchedule;
use App\Models\Service;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class DoctorScheduleResource extends Resource
{
    protected static ?string $model = DoctorSchedule::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $navigationGroup = 'Administrasi';
    
    protected static ?string $navigationLabel = 'Jadwal Dokter';
    
    protected static ?string $modelLabel = 'Jadwal Dokter';
    
    protected static ?string $pluralModelLabel = 'Jadwal Dokter';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dokter')
                    ->description('Data dokter dan poli praktik')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('doctor_name')
                                    ->label('Nama Dokter')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('dr. Nama Dokter')
                                    ->helperText('Masukkan nama lengkap dokter'),
                                    
                                Forms\Components\Select::make('service_id')
                                    ->label('Poli')
                                    ->required()
                                    ->relationship('service', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Pilih poli/layanan dari data layanan yang sudah ada')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Poli/Layanan')
                                            ->required(),
                                        Forms\Components\TextInput::make('prefix')
                                            ->label('Prefix Antrian')
                                            ->required()
                                            ->default('A')
                                            ->maxLength(3),
                                        Forms\Components\TextInput::make('padding')
                                            ->label('Padding Nomor')
                                            ->required()
                                            ->numeric()
                                            ->default(3),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Status Aktif')
                                            ->default(true),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        $service = Service::create($data);
                                        return $service->id;
                                    }),
                            ]),
                    ]),

                Forms\Components\Section::make('Jadwal Praktik')
                    ->description('Atur hari dan jam praktik dokter')
                    ->schema([
                        Forms\Components\CheckboxList::make('days')
                            ->label('Hari Praktik')
                            ->options([
                                'monday' => 'Senin',
                                'tuesday' => 'Selasa',
                                'wednesday' => 'Rabu',
                                'thursday' => 'Kamis',
                                'friday' => 'Jumat',
                                'saturday' => 'Sabtu',
                                'sunday' => 'Minggu',
                            ])
                            ->columns(3)
                            ->required()
                            ->default(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])
                            ->helperText('Pilih hari-hari praktik dokter'),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TimePicker::make('start_time')
                                    ->label('Jam Mulai')
                                    ->required()
                                    ->seconds(false)
                                    ->format('H:i')
                                    ->default('08:00'),
                                    
                                Forms\Components\TimePicker::make('end_time')
                                    ->label('Jam Selesai')
                                    ->required()
                                    ->seconds(false)
                                    ->format('H:i')
                                    ->default('16:00')
                                    ->after('start_time'),
                            ]),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Jadwal hanya berlaku jika status aktif'),
                    ]),

                // Hidden field untuk menyimpan day_of_week (akan diisi otomatis)
                Forms\Components\Hidden::make('day_of_week'),

                // Hidden field untuk link ke user (nanti)
                Forms\Components\Hidden::make('user_id'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('doctor_name')
                    ->label('Nama Dokter')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->icon('heroicon-m-user-circle')
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Poli')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->limit(20),
                    
                Tables\Columns\TextColumn::make('day_name')
                    ->label('Hari')
                    ->sortable(query: function ($query, $direction) {
                        $query->orderByRaw("CASE 
                            WHEN day_of_week = 'monday' THEN 1
                            WHEN day_of_week = 'tuesday' THEN 2
                            WHEN day_of_week = 'wednesday' THEN 3
                            WHEN day_of_week = 'thursday' THEN 4
                            WHEN day_of_week = 'friday' THEN 5
                            WHEN day_of_week = 'saturday' THEN 6
                            WHEN day_of_week = 'sunday' THEN 7
                            END " . $direction);
                    })
                    ->badge()
                    ->color(fn (DoctorSchedule $record): string => match ($record->day_of_week) {
                        'monday' => 'blue',
                        'tuesday' => 'green',
                        'wednesday' => 'yellow',
                        'thursday' => 'orange',
                        'friday' => 'red',
                        'saturday' => 'purple',
                        'sunday' => 'gray',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('time_range')
                    ->label('Jam Praktik')
                    ->icon('heroicon-m-clock')
                    ->weight('medium'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Linked User')
                    ->placeholder('Belum terhubung')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-link')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('day_of_week')
                    ->label('Hari')
                    ->options([
                        'monday' => 'Senin',
                        'tuesday' => 'Selasa',
                        'wednesday' => 'Rabu',
                        'thursday' => 'Kamis',
                        'friday' => 'Jumat',
                        'saturday' => 'Sabtu',
                        'sunday' => 'Minggu',
                    ])
                    ->multiple(),
                    
                Tables\Filters\SelectFilter::make('service_id')
                    ->label('Poli')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua Jadwal')
                    ->trueLabel('Hanya Aktif')
                    ->falseLabel('Hanya Tidak Aktif'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Lihat')
                        ->icon('heroicon-o-eye'),
                        
                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square'),
                        
                    Tables\Actions\ReplicateAction::make()
                        ->label('Duplikasi')
                        ->icon('heroicon-o-document-duplicate')
                        ->form([
                            Forms\Components\Select::make('day_of_week')
                                ->label('Duplikasi ke Hari')
                                ->options([
                                    'monday' => 'Senin',
                                    'tuesday' => 'Selasa',
                                    'wednesday' => 'Rabu',
                                    'thursday' => 'Kamis',
                                    'friday' => 'Jumat',
                                    'saturday' => 'Sabtu',
                                    'sunday' => 'Minggu',
                                ])
                                ->required()
                                ->multiple()
                                ->native(false),
                        ])
                        ->beforeReplicaSaved(function (array $data, DoctorSchedule $replica): void {
                            // Handle multiple days
                            $days = $data['day_of_week'];
                            $replica->day_of_week = is_array($days) ? $days[0] : $days;
                        }),
                        
                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash'),
                ])
                ->label('Aksi')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $count = $records->count();
                            DoctorSchedule::whereIn('id', $records->pluck('id'))
                                ->update(['is_active' => true]);
                                
                            Notification::make()
                                ->title("✅ {$count} jadwal berhasil diaktifkan")
                                ->success()
                                ->send();
                        }),
                        
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $count = $records->count();
                            DoctorSchedule::whereIn('id', $records->pluck('id'))
                                ->update(['is_active' => false]);
                                
                            Notification::make()
                                ->title("⚠️ {$count} jadwal berhasil dinonaktifkan")
                                ->warning()
                                ->send();
                        })
                        ->requiresConfirmation(),
                        
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ]),
            ])
            ->defaultSort('day_of_week', 'asc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->searchable()
            ->persistSearchInSession()
            ->emptyStateHeading('Belum ada jadwal dokter')
            ->emptyStateDescription('Buat jadwal dokter pertama untuk mulai mengatur praktik.')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDoctorSchedules::route('/'),
            'create' => Pages\CreateDoctorSchedule::route('/create'),
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