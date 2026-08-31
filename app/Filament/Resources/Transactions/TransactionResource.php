<?php

namespace App\Filament\Resources\Transactions;

use App\Filament\Resources\Transactions\Pages\CreateTransaction;
use App\Filament\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Models\Transaction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum; // <-- tambahkan ini

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    // HAPUS properti $navigationIcon, ganti dengan method di bawah

    protected static ?string $navigationLabel = 'Pemasukan & Pengeluaran';
    protected static ?string $modelLabel = 'Transaksi';

    // Method ini menggantikan properti $navigationIcon
    public static function getNavigationIcon(): BackedEnum|string|null
    {
        return Heroicon::Banknotes; // atau 'heroicon-o-banknotes'
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Radio::make('type')
                ->label('Tipe')
                ->options([
                    'pemasukan' => 'Pemasukan',
                    'pengeluaran' => 'Pengeluaran',
                ])
                ->inline()
                ->required(),
            TextInput::make('title')
                ->label('Judul')
                ->required()
                ->maxLength(255),
            TextInput::make('amount')
                ->label('Jumlah')
                ->numeric()
                ->prefix('Rp')
                ->required(),
            DatePicker::make('date')
                ->label('Tanggal')
                ->required()
                ->default(now()),
            Textarea::make('description')
                ->label('Catatan')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->description(fn (Transaction $record): ?string => $record->description),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'pemasukan' ? 'success' : 'danger'),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn (Transaction $record): string => $record->type === 'pemasukan' ? 'success' : 'danger'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'pemasukan' => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran',
                    ]),
                Filter::make('month')
                    ->schema([
                        Select::make('month')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ]),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['month'] ?? null,
                        fn (Builder $query, $month): Builder => $query->whereMonth('date', $month),
                    )),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'edit' => EditTransaction::route('/{record}/edit'),
        ];
    }
}
