<?php

namespace App\Filament\Resources\ConfigResource\Pages;

use App\Filament\Resources\ConfigResource;
use App\Models\Config;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ManageRecords;

class ManageConfigs extends ManageRecords
{
    protected static string $resource = ConfigResource::class;
    
    protected static string $view = 'filament.resources.config-resource.pages.manage-configs';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->record(Config::first() ?? new Config()),
        ];
    }
    
    public function getConfig()
    {
        return Config::first() ?? new Config();
    }
}
