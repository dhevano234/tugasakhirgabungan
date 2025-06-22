<?php
namespace App\Filament\Dokter\Resources\QueueResource\Pages;

use App\Filament\Dokter\Resources\QueueResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Actions;

class ViewQueue extends ViewRecord
{
    protected static string $resource = QueueResource::class;
    
    protected static ?string $title = 'Detail Antrian';

    protected function getHeaderActions(): array
    {
        return [
            // TOMBOL REKAM MEDIS DI HEADER VIEW
            Actions\Action::make('create_medical_record')
                ->label('Buat Rekam Medis')
                ->icon('heroicon-o-document-plus')
                ->color('success')
                ->visible(fn () => in_array($this->record->status, ['serving', 'waiting']))
                ->action(function () {
                    if (!$this->record->patient_id) {
                        return redirect()->route('filament.dokter.resources.medical-records.create');
                    }

                    return redirect()->route('filament.dokter.resources.medical-records.create', [
                        'patient_id' => $this->record->patient_id,
                        'queue_number' => $this->record->number,
                        'service' => $this->record->service->name ?? null,
                    ]);
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Antrian')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('number')
                                    ->label('Nomor Antrian')
                                    ->badge()
                                    ->size('lg')
                                    ->weight('bold'),
                                    
                                Infolists\Components\TextEntry::make('service.name')
                                    ->label('Layanan')
                                    ->badge()
                                    ->color('info'),
                                    
                                Infolists\Components\TextEntry::make('status')
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
                                    
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Waktu Daftar')
                                    ->dateTime('d/m/Y H:i'),
                            ]),
                    ]),

                // INFO PASIEN JIKA ADA - ENHANCED
                Infolists\Components\Section::make('Informasi Pasien')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('patient.name')
                                    ->label('Nama Pasien')
                                    ->default('Pasien Walk-in')
                                    ->weight('semibold'),
                                    
                                Infolists\Components\TextEntry::make('patient.medical_record_number')
                                    ->label('No. Rekam Medis')
                                    ->badge()
                                    ->color('primary')
                                    ->placeholder('Walk-in')
                                    ->visible(fn ($record) => $record->patient_id),
                                    
                                Infolists\Components\TextEntry::make('patient.gender_label')
                                    ->label('Jenis Kelamin')
                                    ->visible(fn ($record) => $record->patient_id),
                                    
                                Infolists\Components\TextEntry::make('patient.age')
                                    ->label('Umur')
                                    ->suffix(' tahun')
                                    ->visible(fn ($record) => $record->patient_id),
                            ]),
                    ])
                    ->visible(fn ($record) => $record->patient_id),

                Infolists\Components\Section::make('Timeline Antrian')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('called_at')
                                    ->label('Waktu Dipanggil')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('Belum dipanggil'),
                                    
                                Infolists\Components\TextEntry::make('served_at')
                                    ->label('Waktu Mulai Dilayani')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('Belum dilayani'),
                                    
                                Infolists\Components\TextEntry::make('finished_at')
                                    ->label('Waktu Selesai')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('Belum selesai'),
                                    
                                Infolists\Components\TextEntry::make('canceled_at')
                                    ->label('Waktu Dibatalkan')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('Tidak dibatalkan')
                                    ->visible(fn ($record) => $record->status === 'canceled'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}