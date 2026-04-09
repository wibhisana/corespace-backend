<?php

namespace App\Modules\IAM\Filament\Resources\Users\Pages;

use App\Modules\IAM\Filament\Resources\Users\UserResource;
use App\Modules\IAM\Imports\UsersImport;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Download Template (file fisik dari server)
            Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $path = storage_path('app/templates/Form Data Diri Kandidat.xls');

                    if (!file_exists($path)) {
                        Notification::make()
                            ->title('Template belum tersedia')
                            ->body('File template belum diupload ke server. Hubungi Administrator.')
                            ->danger()
                            ->send();
                        return;
                    }

                    return response()->download($path);
                }),

            // Tombol Import Excel
            Action::make('importExcel')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->modalHeading('Import Data Karyawan')
                ->modalDescription('Upload file Excel (.xls, .xlsx) atau CSV sesuai template. Baris pertama = header.')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel / CSV')
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/csv',
                        ])
                        ->storeFiles(false)
                        ->required(),
                ])
                ->action(function (array $data) {
                    /** @var TemporaryUploadedFile $file */
                    $file = $data['file'];

                    $import = new UsersImport();
                    $import->import($file->getRealPath());

                    $count = count($import->createdUsers);

                    if ($count > 0) {
                        $user = $import->createdUsers[0];
                        $message = "Karyawan \"{$user['name']}\" berhasil didaftarkan. Password: {$user['password']}";

                        Notification::make()
                            ->title('Import Berhasil')
                            ->body($message)
                            ->success()
                            ->send();
                    }

                    if (count($import->failedRows) > 0) {
                        $reasons = collect($import->failedRows)
                            ->map(fn ($r) => "{$r['email']}: {$r['reason']}")
                            ->join(', ');

                        Notification::make()
                            ->title('Import Gagal')
                            ->body($reasons)
                            ->danger()
                            ->send();
                    }
                }),

            CreateAction::make()
                ->label('Tambah Karyawan'),
        ];
    }
}
