<?php

namespace App\Filament\Resources\Wishlists\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SavingsRelationManager extends RelationManager
{
    protected static string $relationship = 'savings';
    protected static ?string $title = 'Riwayat Tabungan';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('amount')
                ->label('Nominal')
                ->numeric()
                ->prefix('Rp')
                ->required(),
            DatePicker::make('date')
                ->label('Tanggal')
                ->required()
                ->default(now()),
            Textarea::make('note')
                ->label('Catatan')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                TextColumn::make('date')->label('Tanggal')->date('d M Y')->sortable(),
                TextColumn::make('amount')->label('Nominal')->money('IDR')->sortable(),
                TextColumn::make('note')->label('Catatan')->limit(30),
            ])
            ->defaultSort('date', 'desc')
            ->headerActions([CreateAction::make()])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
