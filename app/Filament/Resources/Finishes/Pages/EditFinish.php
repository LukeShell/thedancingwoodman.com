<?php

namespace App\Filament\Resources\Finishes\Pages;

use App\Filament\Resources\Finishes\FinishResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinish extends EditRecord
{
    protected static string $resource = FinishResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
