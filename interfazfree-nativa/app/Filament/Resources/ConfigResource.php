<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConfigResource\Pages;
use App\Filament\Resources\ConfigResource\RelationManagers;
use App\Models\Config;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConfigResource extends Resource
{
    protected static ?string $model = Config::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationLabel = 'Config';
    
    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuración de Base de Datos')
                    ->schema([
                        Forms\Components\TextInput::make('db_user')
                            ->label('Usuario de BD')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('db_password')
                            ->label('Contraseña de BD')
                            ->password()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Configuración de Generación')
                    ->schema([
                        Forms\Components\TextInput::make('allowed_characters')
                            ->label('Caracteres Permitidos')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Caracteres permitidos para generar usuarios y contraseñas'),
                        Forms\Components\Select::make('encryption_type')
                            ->label('Tipo de Cifrado')
                            ->options([
                                'bcrypt' => 'Bcrypt',
                                'argon2' => 'Argon2',
                                'plaintext' => 'Sin Cifrado (Texto Plano)',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('db_user')
                    ->label('Usuario BD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('allowed_characters')
                    ->label('Caracteres Permitidos')
                    ->limit(50),
                Tables\Columns\TextColumn::make('encryption_type')
                    ->label('Cifrado')
                    ->badge(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageConfigs::route('/'),
        ];
    }
}
