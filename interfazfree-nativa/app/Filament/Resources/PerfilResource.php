<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerfilResource\Pages;
use App\Filament\Resources\PerfilResource\RelationManagers;
use App\Models\Perfil;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PerfilResource extends Resource
{
    protected static ?string $model = Perfil::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Select::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'corrido' => 'Corrido',
                        'pausado' => 'Pausado',
                        'recurrente' => 'Recurrente',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('velocidad_subida')
                    ->label('Velocidad de Subida')
                    ->maxLength(50),
                Forms\Components\TextInput::make('velocidad_bajada')
                    ->label('Velocidad de Bajada')
                    ->maxLength(50),
                Forms\Components\TextInput::make('tiempo_vigencia')
                    ->label('Tiempo de Vigencia (días)')
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('precio')
                    ->label('Precio')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio')
                    ->label('Precio')
                    ->money('MXN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('velocidad_subida')
                    ->label('Vel. Subida')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('velocidad_bajada')
                    ->label('Vel. Bajada')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
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
            'index' => Pages\ListPerfils::route('/'),
            'create' => Pages\CreatePerfil::route('/create'),
            'edit' => Pages\EditPerfil::route('/{record}/edit'),
        ];
    }
}
