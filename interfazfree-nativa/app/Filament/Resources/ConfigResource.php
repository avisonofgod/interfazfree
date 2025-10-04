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
                Forms\Components\TextInput::make('allowed_characters')
                    ->label('Caracteres Permitidos')
                    ->required()
                    ->maxLength(255)
                    ->default('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
                    ->helperText('Caracteres permitidos para generar usuarios y contraseñas'),
                Forms\Components\Select::make('encryption_type')
                    ->label('Tipo de Cifrado (DB Password Encryption Type)')
                    ->options([
                        'cleartext' => 'Cleartext (Sin Cifrado)',
                        'bcrypt' => 'Bcrypt',
                        'argon2' => 'Argon2',
                    ])
                    ->default('cleartext')
                    ->required(),
                Forms\Components\TextInput::make('longitud_usuario')
                    ->label('Longitud de Usuario')
                    ->required()
                    ->numeric()
                    ->minValue(3)
                    ->maxValue(32)
                    ->default(4)
                    ->helperText('Longitud por defecto para generar usuarios'),
                Forms\Components\TextInput::make('longitud_password')
                    ->label('Longitud de Contraseña')
                    ->required()
                    ->numeric()
                    ->minValue(3)
                    ->maxValue(32)
                    ->default(3)
                    ->helperText('Longitud por defecto para generar contraseñas'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('allowed_characters')
                    ->label('Caracteres Permitidos')
                    ->limit(50),
                Tables\Columns\TextColumn::make('encryption_type')
                    ->label('Tipo de Cifrado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cleartext' => 'gray',
                        'bcrypt' => 'success',
                        'argon2' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('longitud_usuario')
                    ->label('Longitud de Usuario')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('longitud_password')
                    ->label('Longitud de Contraseña')
                    ->alignCenter(),
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
