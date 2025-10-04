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
                Forms\Components\TextInput::make('username')
                    ->label('Usuario')
                    ->required()
                    ->maxLength(64)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->label('Contraseña')
                    ->required()
                    ->maxLength(64),
                Forms\Components\Select::make('perfil_id')
                    ->label('Perfil')
                    ->relationship('perfil', 'nombre')
                    ->required(),
                Forms\Components\Select::make('lote_id')
                    ->label('Lote')
                    ->relationship('lote', 'nombre')
                    ->nullable(),
                Forms\Components\Textarea::make('observaciones')
                    ->label('Observaciones')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sin_usar' => 'gray',
                        'activa' => 'success',
                        'caducada' => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('perfil.nombre')
                    ->label('Perfil')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lote.nombre')
                    ->label('Lote')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_expiracion')
                    ->label('Expiración')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
