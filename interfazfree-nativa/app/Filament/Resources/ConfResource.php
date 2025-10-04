<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConfResource\Pages;
use App\Models\Config;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ConfResource extends Resource
{
    protected static ?string $model = Config::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationLabel = 'Conf';
    
    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([])->filters([])->actions([])->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageConfs::route('/'),
        ];
    }
}
