<?php

namespace App\Filament\Resources\Transactions;

use App\Exports\TransactionExport;
use App\Filament\Resources\Transactions\Pages\CreateTransaction;
use App\Filament\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Models\Transaction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationLabel = 'Pemasukan & Pengeluaran';
    protected static ?string $modelLabel = 'Transaksi';

    public static function getNavigationIcon(): BackedEnum|string|null
    {
        return Heroicon::Banknotes;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('pemasukan')
                ->label('Pemasukan')
                ->numeric()
                ->prefix('Rp')
                ->nullable(),
            TextInput::make('pengeluaran')
                ->label('Pengeluaran')
                ->numeric()
                ->prefix('Rp')
                ->nullable(),
            TextInput::make('title')
                ->label('Judul')
                ->required()
                ->maxLength(255),
            TextInput::make('pegangan')
                ->label('Pegangan')
                ->maxLength(255)
                ->nullable(),
            TextInput::make('save')
                ->label('Save')
                ->maxLength(255)
                ->nullable()
                ->helperText('Isi dengan keterangan simpanan / referensi'),
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
                TextColumn::make('pemasukan')
                    ->label('Pemasukan')
                    ->money('IDR')
                    ->default(0),
                    TextColumn::make('pengeluaran')
                    ->label('Pengeluaran')
                    ->money('IDR')
                    ->default(0),
                    TextColumn::make('pegangan')
                    ->label('Pegangan')
                    ->money('IDR')
                    ->searchable()
                    ->toggleable(),
                    TextColumn::make('save')
                    ->label('Save')
                    ->money('IDR')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
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
            ->headerActions([
                Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        $filters = [
                            'month' => request()->input('tableFilters.month.month', null),
                        ];
                        return Excel::download(new TransactionExport($filters), 'transactions.xlsx');
                    })
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
