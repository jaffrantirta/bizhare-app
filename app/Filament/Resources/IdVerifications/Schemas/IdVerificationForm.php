<?php

namespace App\Filament\Resources\IdVerifications\Schemas;

use App\Data\IndonesianRegions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class IdVerificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Pengguna')
                    ->schema([
                        Placeholder::make('user_name')
                            ->label('Username')
                            ->content(fn ($record) => $record?->user?->name ?? '-'),
                        Placeholder::make('user_email')
                            ->label('Email')
                            ->content(fn ($record) => $record?->user?->email ?? '-'),
                    ])
                    ->columns(2),

                Fieldset::make('Identitas Diri')
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->disabled(),
                        Select::make('id_type')
                            ->label('Jenis Identitas')
                            ->options([
                                'ktp'      => 'KTP',
                                'sim'      => 'SIM',
                                'passport' => 'Passport',
                            ])
                            ->disabled(),
                        TextInput::make('id_number')
                            ->label('Nomor Identitas')
                            ->disabled(),
                        TextInput::make('place_of_birth')
                            ->label('Tempat Lahir')
                            ->disabled(),
                        DatePicker::make('date_of_birth')
                            ->label('Tanggal Lahir')
                            ->disabled(),
                    ])
                    ->columns(2),

                Fieldset::make('Informasi Personal')
                    ->schema([
                        TextInput::make('phone_number')
                            ->label('Nomor Telepon')
                            ->disabled(),
                        TextInput::make('occupation')
                            ->label('Pekerjaan')
                            ->disabled(),
                        Select::make('marital_status')
                            ->label('Status Pernikahan')
                            ->options([
                                'single'   => 'Belum Menikah',
                                'married'  => 'Menikah',
                                'divorced' => 'Cerai Hidup',
                                'widowed'  => 'Cerai Mati',
                            ])
                            ->disabled(),
                    ])
                    ->columns(3),

                Fieldset::make('Alamat')
                    ->schema([
                        Select::make('province')
                            ->label('Provinsi')
                            ->options(IndonesianRegions::provinces())
                            ->disabled()
                            ->searchable(),
                        Select::make('kabupaten')
                            ->label('Kabupaten / Kota')
                            ->options(fn (Get $get) => IndonesianRegions::kabupatens($get('province')))
                            ->disabled()
                            ->searchable(),
                        TextInput::make('kecamatan')
                            ->label('Kecamatan')
                            ->disabled(),
                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Fieldset::make('Dokumen')
                    ->schema([
                        Placeholder::make('id_photo_preview')
                            ->label('Foto KTP / SIM / Passport')
                            ->content(fn ($record) => $record?->id_photo
                                ? new HtmlString(
                                    '<a href="' . Storage::disk('public')->url($record->id_photo) . '" target="_blank">'
                                    . '<img src="' . Storage::disk('public')->url($record->id_photo) . '" '
                                    . 'class="max-h-64 rounded-lg border border-gray-200 mt-2 cursor-pointer hover:opacity-90">'
                                    . '</a>'
                                )
                                : new HtmlString('<span class="text-gray-400 italic">Tidak ada foto</span>')),

                        Placeholder::make('selfie_photo_preview')
                            ->label('Foto Selfie')
                            ->content(fn ($record) => $record?->selfie_photo
                                ? new HtmlString(
                                    '<a href="' . Storage::disk('public')->url($record->selfie_photo) . '" target="_blank">'
                                    . '<img src="' . Storage::disk('public')->url($record->selfie_photo) . '" '
                                    . 'class="max-h-64 rounded-lg border border-gray-200 mt-2 cursor-pointer hover:opacity-90">'
                                    . '</a>'
                                )
                                : new HtmlString('<span class="text-gray-400 italic">Tidak ada foto</span>')),
                    ])
                    ->columns(2),

                Fieldset::make('Status Verifikasi')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending'  => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->disabled(),
                        Placeholder::make('reviewer_name')
                            ->label('Direview Oleh')
                            ->content(fn ($record) => $record?->reviewer?->name ?? '-'),
                        DatePicker::make('reviewed_at')
                            ->label('Tanggal Review')
                            ->disabled(),
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record?->status === 'rejected'),
                    ])
                    ->columns(3),
            ]);
    }
}
