<?php

namespace App\Filament\Resources\Wishlists\RelationManagers;

use App\Filament\Resources\Wishlists\WishlistResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class WishlistRelationManager extends RelationManager
{
    protected static string $relationship = 'Wishlist';

    protected static ?string $relatedResource = WishlistResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
