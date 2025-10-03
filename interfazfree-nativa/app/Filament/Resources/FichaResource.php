<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FichaResource\Pages;
use App\Filament\Resources\FichaResource\RelationManagers;
use App\Models\Ficha;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FichaResource extends Resource
{
    protected static ?string $model = Ficha::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFichas::route('/'),
            'create' => Pages\CreateFicha::route('/create'),
            'edit' => Pages\EditFicha::route('/{record}/edit'),
        ];
    }
}
