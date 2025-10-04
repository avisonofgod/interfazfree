<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LogResource\Pages;
use App\Filament\Resources\LogResource\RelationManagers;
use App\Models\Radpostauth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LogResource extends Resource
{
    protected static ?string $model = Radpostauth::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    
    protected static ?string $navigationLabel = 'Logs';
    
    protected static ?int $navigationSort = 60;
    
    protected static ?string $modelLabel = 'Error de Autenticación';
    
    protected static ?string $pluralModelLabel = 'Errores de Autenticación';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pass')
                    ->label('Contraseña Intentada')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reply')
                    ->label('Respuesta')
                    ->badge()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('authdate')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable()
                    ->default(now()),
            ])
            ->defaultSort('authdate', 'desc')
            ->filters([])
            ->actions([])
            ->bulkActions([])
            ->modifyQueryUsing(fn ($query) => $query->where('reply', 'Access-Reject'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLogs::route('/'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}
