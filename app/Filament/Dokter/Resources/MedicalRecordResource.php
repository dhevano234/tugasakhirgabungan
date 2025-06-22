<?php
namespace App\Filament\Dokter\Resources;

use App\Filament\Dokter\Resources\MedicalRecordResource\Pages;
use App\Models\MedicalRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MedicalRecordResource extends Resource
{
    protected static ?string $model = MedicalRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Rekam Medis';
    protected static ?string $modelLabel = 'Rekam Medis';
    protected static ?string $pluralModelLabel = 'Rekam Medis';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Info jika dari antrian  
                Forms\Components\Placeholder::make('queue_info')
                    ->label('📋 Rekam Medis dari Antrian')
                    ->content(function () {
                        $queueNumber = request()->get('queue_number');
                        $serviceName = request()->get('service');
                        
                        if ($queueNumber) {
                            return "Antrian: {$queueNumber}" . ($serviceName ? " - {$serviceName}" : "");
                        }
                        return '';
                    })
                    ->visible(fn () => request()->has('queue_number')),

                // 1. Pasien (Required)
                Forms\Components\Select::make('patient_id')
                    ->label('Pasien')
                    ->relationship('patient', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->medical_record_number} - {$record->name}")
                    ->disabled(fn () => request()->has('patient_id')) // Disable jika auto-populated
                    ->helperText(fn () => request()->has('patient_id') 
                        ? 'Pasien otomatis dipilih dari antrian' 
                        : null),

                // 2. Gejala/Keluhan Utama (Required)
                Forms\Components\Textarea::make('chief_complaint')
                    ->label('Gejala/Keluhan Utama')
                    ->required()
                    ->rows(3)
                    ->placeholder('Jelaskan gejala atau keluhan utama pasien...'),

                // 3. Tanda Vital (Optional)
                Forms\Components\Textarea::make('vital_signs')
                    ->label('Tanda Vital')
                    ->rows(2)
                    ->placeholder('TD: 120/80 mmHg, Nadi: 80x/menit, Suhu: 36.5°C'),

                // 4. Diagnosis (Required)
                Forms\Components\Textarea::make('diagnosis')
                    ->label('Diagnosis')
                    ->required()
                    ->rows(2)
                    ->placeholder('Tuliskan diagnosis berdasarkan pemeriksaan...'),

                // 5. Resep Obat (Optional)
                Forms\Components\Textarea::make('prescription')
                    ->label('Resep Obat')
                    ->rows(3)
                    ->placeholder('Paracetamol 500mg 3x1, Amoxicillin 250mg 3x1'),

                // 6. Catatan Tambahan (Optional)
                Forms\Components\Textarea::make('additional_notes')
                    ->label('Catatan Tambahan')
                    ->rows(2)
                    ->placeholder('Catatan tambahan atau instruksi khusus...'),

                // Hidden fields untuk keperluan sistem
                Forms\Components\Hidden::make('doctor_id')
                    ->default(Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.medical_record_number')
                    ->label('No. RM')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('patient.name')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('chief_complaint')
                    ->label('Keluhan Utama')
                    ->limit(40)
                    ->wrap()
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('diagnosis')
                    ->label('Diagnosis')
                    ->limit(40)
                    ->wrap()
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('Dokter')
                    ->searchable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Pemeriksaan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($state) => $state->format('l, d F Y - H:i:s')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('doctor')
                    ->relationship('doctor', 'name')
                    ->label('Filter Dokter'),

                Tables\Filters\Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn ($query) => $query->whereDate('created_at', today()))
                    ->default(),

                Tables\Filters\Filter::make('this_week')
                    ->label('Minggu Ini')
                    ->query(fn ($query) => $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye'),
                    
                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square'),
                        
                    Tables\Actions\Action::make('print')
                        ->label('Cetak')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->action(function (MedicalRecord $record) {
                            // Logic untuk print bisa ditambahkan di sini
                        }),
                ])
                ->label('Aksi')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                        
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export Data')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            // Logic export bisa ditambahkan di sini
                        }),
                ]),
            ])
            ->searchable()
            ->striped()
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedicalRecords::route('/'),
            'create' => Pages\CreateMedicalRecord::route('/create'),
            'view' => Pages\ViewMedicalRecord::route('/{record}'),
            'edit' => Pages\EditMedicalRecord::route('/{record}/edit'),
        ];
    }
}