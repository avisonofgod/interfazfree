<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NasResource\Pages;
use App\Filament\Resources\NasResource\RelationManagers;
use App\Models\Nas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NasResource extends Resource
{
    protected static ?string $model = Nas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('shortname')
                    ->label('Nombre Corto')
                    ->required()
                    ->maxLength(32),
                Forms\Components\Select::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'mikrotik' => 'Mikrotik',
                        'opnsense' => 'OPNsense',
                        'other' => 'Otro',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('ip')
                    ->label('Dirección IP')
                    ->required()
                    ->maxLength(15)
                    ->rule('ip'),
                Forms\Components\TextInput::make('puerto')
                    ->label('Puerto')
                    ->numeric()
                    ->default(1812)
                    ->minValue(1)
                    ->maxValue(65535),
                Forms\Components\TextInput::make('secreto')
                    ->label('Secreto RADIUS')
                    ->required()
                    ->maxLength(60)
                    ->password()
                    ->revealable(),
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
                Tables\Columns\TextColumn::make('shortname')
                    ->label('Nombre Corto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ip')
                    ->label('IP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('puerto')
                    ->label('Puerto')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->sortable(),
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
            'index' => Pages\ListNas::route('/'),
            'create' => Pages\CreateNas::route('/create'),
            'edit' => Pages\EditNas::route('/{record}/edit'),
        ];
    }
}
