<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoteResource\Pages;
use App\Filament\Resources\LoteResource\RelationManagers;
use App\Models\Lote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoteResource extends Resource
{
    protected static ?string $model = Lote::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre del Lote')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('cantidad')
                    ->label('Cantidad de Fichas')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(156),
                Forms\Components\Select::make('perfil_id')
                    ->label('Perfil')
                    ->relationship('perfil', 'nombre')
                    ->required(),
                Forms\Components\Select::make('nas_id')
                    ->label('NAS')
                    ->relationship('nas', 'nombre')
                    ->nullable(),
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
                Tables\Columns\TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('perfil.nombre')
                    ->label('Perfil')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nas.nombre')
                    ->label('NAS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('pdf')
                    ->label('Imprimir PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Lote $record): string => route('lotes.pdf', $record)),
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
            'index' => Pages\ListLotes::route('/'),
            'create' => Pages\CreateLote::route('/create'),
            'edit' => Pages\EditLote::route('/{record}/edit'),
        ];
    }
}
