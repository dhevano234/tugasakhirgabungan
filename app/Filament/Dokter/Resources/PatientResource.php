<?php
namespace App\Filament\Dokter\Resources;

use App\Filament\Dokter\Resources\PatientResource\Pages;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Data Pasien';
    protected static ?string $modelLabel = 'Pasien';
    protected static ?string $pluralModelLabel = 'Pasien';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Rekam Medis')
                    ->description('Nomor rekam medis akan digenerate otomatis jika kosong')
                    ->schema([
                        Forms\Components\TextInput::make('medical_record_number')
                            ->label('No. Rekam Medis')
                            ->placeholder('Akan digenerate otomatis jika kosong')
                            ->unique(ignoreRecord: true)
                            ->helperText('Format: RM-YYYYMMDD-XXXX (contoh: RM-20250611-0001)')
                            ->maxLength(20),
                    ])
                    ->columns(1),

                Section::make('Data Pribadi')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan nama lengkap pasien'),
                                    
                                Forms\Components\DatePicker::make('birth_date')
                                    ->label('Tanggal Lahir')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->maxDate(now())
                                    ->helperText('Umur akan dihitung otomatis'),
                            ]),
                            
                        Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('gender')
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'male' => 'Laki-laki',
                                        'female' => 'Perempuan',
                                    ])
                                    ->required()
                                    ->native(false),
                                    
                                Forms\Components\Select::make('blood_type')
                                    ->label('Golongan Darah')
                                    ->options([
                                        'A+' => 'A+',
                                        'A-' => 'A-',
                                        'B+' => 'B+',
                                        'B-' => 'B-',
                                        'AB+' => 'AB+',
                                        'AB-' => 'AB-',
                                        'O+' => 'O+',
                                        'O-' => 'O-',
                                    ])
                                    ->placeholder('Pilih golongan darah')
                                    ->searchable()
                                    ->native(false),
                                    
                                Forms\Components\TextInput::make('phone')
                                    ->label('No. Telepon')
                                    ->tel()
                                    ->placeholder('08123456789')
                                    ->maxLength(15),
                            ]),
                    ]),

                Section::make('Alamat & Kontak Darurat')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->rows(3)
                            ->placeholder('Masukkan alamat lengkap pasien'),
                            
                        Forms\Components\TextInput::make('emergency_contact')
                            ->label('Kontak Darurat')
                            ->placeholder('Nama dan nomor telepon kontak darurat')
                            ->helperText('Contoh: Ibu Siti (081234567890)')
                            ->maxLength(100),
                    ])
                    ->columns(1),

                Section::make('Informasi Medis')
                    ->schema([
                        Forms\Components\Textarea::make('allergies')
                            ->label('Alergi')
                            ->rows(2)
                            ->placeholder('Masukkan informasi alergi jika ada')
                            ->helperText('Sebutkan alergi obat, makanan, atau lainnya'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('medical_record_number')
                    ->label('No. RM')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nomor RM disalin!')
                    ->badge()
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->limit(30)
                    // YANG BARU - Highlight pasien temporary
                    ->color(fn (Patient $record) => 
                        str_contains($record->name, 'Pasien ') && str_contains($record->name, ' - ') 
                            ? 'warning' 
                            : null
                    ),
                    
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('age')
                    ->label('Umur')
                    ->state(function (Patient $record): string {
                        return $record->age . ' tahun';
                    })
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('birth_date', $direction === 'asc' ? 'desc' : 'asc');
                    }),
                    
                Tables\Columns\TextColumn::make('gender')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn (string $state): string => $state === 'male' ? 'Laki-laki' : 'Perempuan')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'male' ? 'info' : 'success'),
                    
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor telepon disalin!')
                    ->toggleable(),
                    
                // YANG BARU - Status Kolom untuk identifikasi pasien temporary
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->state(function (Patient $record): string {
                        // Check if it's a temporary patient
                        if (str_contains($record->name, 'Pasien ') && str_contains($record->name, ' - ')) {
                            return 'Data Belum Lengkap';
                        }
                        
                        // Check if basic data is complete
                        if (empty($record->phone) || $record->address === 'Alamat belum diisi') {
                            return 'Data Kurang Lengkap';
                        }
                        
                        return 'Data Lengkap';
                    })
                    ->badge()
                    ->color(function (Patient $record): string {
                        if (str_contains($record->name, 'Pasien ') && str_contains($record->name, ' - ')) {
                            return 'danger';
                        }
                        
                        if (empty($record->phone) || $record->address === 'Alamat belum diisi') {
                            return 'warning';
                        }
                        
                        return 'success';
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->date('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan',
                    ]),
                    
                Tables\Filters\SelectFilter::make('blood_type')
                    ->label('Golongan Darah')
                    ->options([
                        'A+' => 'A+', 'A-' => 'A-',
                        'B+' => 'B+', 'B-' => 'B-',
                        'AB+' => 'AB+', 'AB-' => 'AB-',
                        'O+' => 'O+', 'O-' => 'O-',
                    ])
                    ->multiple(),
                    
                // YANG BARU - Filter berdasarkan status data
                Tables\Filters\Filter::make('incomplete_data')
                    ->label('Data Belum Lengkap')
                    ->query(fn ($query) => $query->where('name', 'LIKE', 'Pasien %')
                        ->where('name', 'LIKE', '% - %')),
                        
                Tables\Filters\Filter::make('missing_info')
                    ->label('Info Kurang Lengkap')
                    ->query(fn ($query) => $query->where(function ($q) {
                        $q->whereNull('phone')
                          ->orWhere('address', 'Alamat belum diisi')
                          ->orWhereNull('emergency_contact');
                    })),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Lihat'),
                        
                    Tables\Actions\EditAction::make()
                        ->label('Edit'),
                        
                    // YANG BARU - Quick action untuk melengkapi data
                    Tables\Actions\Action::make('complete_data')
                        ->label('Lengkapi Data')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->visible(fn (Patient $record) => 
                            str_contains($record->name, 'Pasien ') && str_contains($record->name, ' - ')
                        )
                        ->url(fn (Patient $record) => static::getUrl('edit', ['record' => $record])),
                        
                    Tables\Actions\Action::make('create_medical_record')
                        ->label('Buat Rekam Medis')
                        ->icon('heroicon-o-document-plus')
                        ->color('success')
                        ->url(fn (Patient $record) => route('filament.dokter.resources.medical-records.create', [
                            'patient_id' => $record->id
                        ])),
                ])
                ->label('Aksi')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus'),
                        
                    Tables\Actions\BulkAction::make('export_mrn')
                        ->label('Export No. RM')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function ($records) {
                            $mrns = $records->pluck('medical_record_number', 'name')->toArray();
                            
                            $content = "Daftar Nomor Rekam Medis\n\n";
                            foreach ($mrns as $name => $mrn) {
                                $content .= "{$mrn} - {$name}\n";
                            }
                            
                            return response()->streamDownload(function () use ($content) {
                                echo $content;
                            }, 'nomor-rekam-medis-' . now()->format('Y-m-d') . '.txt');
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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'view' => Pages\ViewPatient::route('/{record}'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}