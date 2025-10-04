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
    
    protected static ?string $navigationLabel = 'Perfiles';
    
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('tiempo_vigencia')
                    ->label('Tiempo de Vigencia (días)')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('precio')
                    ->label('Precio unitario')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0),
                
                Forms\Components\Section::make('Atributos RADIUS')
                    ->description('Agregue atributos check y reply para este perfil')
                    ->schema([
                        Forms\Components\Repeater::make('atributos')
                            ->relationship('atributos')
                            ->schema([
                                Forms\Components\Select::make('nombre')
                                    ->label('Attribute')
                                    ->options([
                                        'Fall-Through' => 'Fall-Through',
                                        'Simultaneous-Use' => 'Simultaneous-Use',
                                        'Access-Period' => 'Access-Period',
                                        'Max-All-Session' => 'Max-All-Session',
                                        'WISPr-Session-Terminate-Time' => 'WISPr-Session-Terminate-Time',
                                        'Idle-Timeout' => 'Idle-Timeout',
                                        'Session-Timeout' => 'Session-Timeout',
                                        'Max-Daily-Session' => 'Max-Daily-Session',
                                        'Max-Monthly-Session' => 'Max-Monthly-Session',
                                    ])
                                    ->searchable()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('custom_attribute')
                                            ->label('Custom Attribute')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function ($data) {
                                        return $data['custom_attribute'];
                                    })
                                    ->required(),
                                Forms\Components\TextInput::make('valor')
                                    ->label('Value')
                                    ->required(),
                                Forms\Components\Select::make('operador')
                                    ->label('Op')
                                    ->options([
                                        ':=' => ':= (Assign)',
                                        '==' => '== (Equal)',
                                        '+=' => '+= (Add)',
                                        '-=' => '-= (Subtract)',
                                        '!=' => '!= (Not Equal)',
                                        '=~' => '=~ (Regex Match)',
                                    ])
                                    ->default(':=')
                                    ->required(),
                                Forms\Components\Select::make('tipo')
                                    ->label('Target')
                                    ->options([
                                        'check' => 'Check',
                                        'reply' => 'Reply',
                                        'session' => 'Session',
                                    ])
                                    ->default('reply')
                                    ->required(),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->addActionLabel('Add Attribute')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['nombre'] ?? null),
                    ])
                    ->collapsible(),
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
                Tables\Columns\TextColumn::make('tiempo_vigencia')
                    ->label('Vigencia (días)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio')
                    ->label('Precio')
                    ->money('MXN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('atributos_count')
                    ->label('Atributos')
                    ->counts('atributos')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
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
            'index' => Pages\ListPerfils::route('/'),
            'create' => Pages\CreatePerfil::route('/create'),
            'edit' => Pages\EditPerfil::route('/{record}/edit'),
        ];
    }
}
