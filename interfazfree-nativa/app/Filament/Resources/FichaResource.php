<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FichaResource\Pages;
use App\Filament\Resources\FichaResource\RelationManagers;
use App\Models\Ficha;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FichaResource extends Resource
{
    protected static ?string $model = Ficha::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?int $navigationSort = 20;

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
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        $perfil = \App\Models\Perfil::find($state);
                        if ($perfil && $perfil->tipo === 'recurrente') {
                            $set('wispr_terminate_time', '2026-01-02T20:00:00');
                        } else {
                            $set('wispr_terminate_time', null);
                        }
                    }),
                Forms\Components\DateTimePicker::make('wispr_terminate_time')
                    ->label('WISPr-Session-Terminate-Time')
                    ->default('2026-01-02T20:00:00')
                    ->seconds(false)
                    ->helperText('Fecha y hora de terminación de sesión (formato ISO 8601)')
                    ->visible(fn (Get $get): bool => 
                        \App\Models\Perfil::find($get('perfil_id'))?->tipo === 'recurrente'
                    ),
                Forms\Components\Select::make('lote_id')
                    ->label('Lote')
                    ->relationship('lote', 'nombre')
                    ->nullable(),
                Forms\Components\Textarea::make('observaciones')
                    ->label('Observaciones')
                    ->maxLength(255)
                    ->columnSpanFull(),
                    
                Forms\Components\Section::make('Atributos RADIUS')
                    ->description('Atributos reply asignados a este usuario')
                    ->schema([
                        Forms\Components\Repeater::make('radreply')
                            ->relationship('radreply')
                            ->schema([
                                Forms\Components\TextInput::make('attribute')
                                    ->label('Attribute')
                                    ->required(),
                                Forms\Components\TextInput::make('op')
                                    ->label('Op')
                                    ->default(':=')
                                    ->required(),
                                Forms\Components\TextInput::make('value')
                                    ->label('Value')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Add Attribute')
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['attribute'] ?? null)
                    ])
                    ->hidden(fn ($record) => $record === null)
                    ->collapsible(),
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
                Tables\Columns\TextColumn::make('password')
                    ->label('Contraseña')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('perfil.nombre')
                    ->label('Perfil')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tiempo_usado')
                    ->label('Usado')
                    ->formatStateUsing(fn ($state) => gmdate('H:i:s', $state))
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
            ])
            ->filters([
                Tables\Filters\Filter::make('usuario')
                    ->form([
                        Forms\Components\TextInput::make('username')
                            ->label('Usuario'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['username'],
                            fn (Builder $query, $username): Builder => $query->where('username', 'like', "%{$username}%")
                        );
                    }),
            ])
            ->paginationPageOptions([5, 10, 25, 52, 'all'])
            ->defaultPaginationPageOption(52)
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
