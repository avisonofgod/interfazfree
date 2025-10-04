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
                    ->required(),
                Forms\Components\Select::make('lote_id')
                    ->label('Lote')
                    ->relationship('lote', 'nombre')
                    ->nullable(),
                Forms\Components\Textarea::make('observaciones')
                    ->label('Observaciones')
                    ->maxLength(255)
                    ->columnSpanFull(),
                    
                Forms\Components\Section::make('Atributos RADIUS')
                    ->description('Atributos asignados a este usuario')
                    ->schema([
                        Forms\Components\Section::make('Atributos Heredados del Perfil')
                            ->description('Atributos definidos en el perfil')
                            ->schema([
                                Forms\Components\Repeater::make('perfil_atributos')
                                    ->schema([
                                        Forms\Components\TextInput::make('nombre')
                                            ->label('Attribute')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('operador')
                                            ->label('Op')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('valor')
                                            ->label('Value')
                                            ->disabled(),
                                    ])
                                    ->columns(3)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->perfil && $record->perfil->atributos->isNotEmpty()) {
                                            $component->state(
                                                $record->perfil->atributos->map(function ($atributo) {
                                                    return [
                                                        'nombre' => $atributo->nombre,
                                                        'operador' => $atributo->operador,
                                                        'valor' => $atributo->valor,
                                                    ];
                                                })->toArray()
                                            );
                                        }
                                    })
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->collapsible()
                                    ->collapsed()
                                    ->itemLabel(fn (array $state): ?string => $state['nombre'] ?? null)
                                    ->hidden(fn ($record) => !$record || !$record->perfil || $record->perfil->atributos->isEmpty())
                            ])
                            ->collapsible()
                            ->collapsed()
                            ->hidden(fn ($record) => !$record || !$record->perfil || $record->perfil->atributos->isEmpty()),
                        Forms\Components\Section::make('Atributos Personalizados (Radreply)')
                            ->description('Atributos reply específicos de este usuario')
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
                            ->collapsible()
                            ->collapsed(),
                    ])
                    ->hidden(fn ($record) => $record === null)
                    ->collapsible()
                    ->collapsed(),
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
