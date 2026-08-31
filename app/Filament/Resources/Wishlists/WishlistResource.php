<?php

namespace App\Filament\Resources\Wishlists;

use App\Filament\Resources\Wishlists\Pages\CreateWishlist;
use App\Filament\Resources\Wishlists\Pages\EditWishlist;
use App\Filament\Resources\Wishlists\Pages\ListWishlists;
use App\Filament\Resources\Wishlists\RelationManagers\SavingsRelationManager;
use App\Models\Wishlist;
use BackedEnum; // <-- tambahkan ini
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon; // <-- tambahkan ini
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WishlistResource extends Resource
{
    protected static ?string $model = Wishlist::class;

    // HAPUS properti $navigationIcon, ganti dengan method di bawah

    protected static ?string $navigationLabel = 'Target / Wishlist';
    protected static ?string $modelLabel = 'Wishlist';

    // Method ini menggantikan properti $navigationIcon
    public static function getNavigationIcon(): BackedEnum|string|null
    {
        return Heroicon::Gift; // atau 'heroicon-o-gift'
    }

    // STEP 1: cuma nama barang & harga target. collected_amount & status
    // sengaja TIDAK ditaruh di sini, biar user gak bisa asal isi manual.
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('item_name')
                ->label('Nama Barang')
                ->required()
                ->maxLength(255),
            TextInput::make('target_price')
                ->label('Harga Target')
                ->numeric()
                ->prefix('Rp')
                ->required(),
            FileUpload::make('image')
                ->label('Gambar')
                ->image()
                ->imageEditor()
                ->disk('public')
                ->directory('wishlists'),
            Textarea::make('notes')
                ->label('Catatan')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label(''),
                TextColumn::make('item_name')
                    ->label('Nama Barang')
                    ->searchable(),
                TextColumn::make('target_price')
                    ->label('Target')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('collected_amount')
                    ->label('Terkumpul')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('progress_percent')
                    ->label('Progres')
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->badge()
                    ->color(fn ($state): string => $state >= 100 ? 'success' : 'warning'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'tercapai' ? 'success' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'proses' => 'Proses',
                    'tercapai' => 'Tercapai',
                ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            SavingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWishlists::route('/'),
            'create' => CreateWishlist::route('/create'),
            'edit' => EditWishlist::route('/{record}/edit'),
        ];
    }
}
