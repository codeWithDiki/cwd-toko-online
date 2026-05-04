<?php

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use CodeWithDiki\ProductModule\Data\ProductReviewData;
use CodeWithDiki\ProductModule\Facades\ProductModule;
use CodeWithDiki\TransactionModule\Enums\PaymentStatus;
use CodeWithDiki\TransactionModule\Enums\TransactionStatus;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Livewire\Component;

new class extends Component implements HasTable, HasActions, HasSchemas
{
    use InteractsWithTable, InteractsWithActions, InteractsWithSchemas;

    public User $user;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    
    public function table(Table $table): Table
    {
        return $table
        ->query(Transaction::query()->whereHas("users", function($query) {
            $query->where("users.id", $this->user->id);
        }))
        ->columns([
            TextColumn::make("trx_id")
                ->label("Kode Transaksi")
                ->searchable(),
            TextColumn::make("total_amount")
                ->label("Total")
                ->sortable()
                ->money("idr"),
            TextColumn::make("payment_status")
                ->label("Status Pembayaran")
                ->badge()
                ->color(fn($state) => $state->color())
                ->formatStateUsing(fn($state) => ucfirst(strtolower(str_replace("_", " ", $state->value))))
                ->sortable(),
            TextColumn::make("status")
                ->label("Status")
                ->badge()
                ->color(fn($state) => $state->color())
                ->sortable(),
            TextColumn::make("paid_at")
                ->label("Tanggal Pembayaran")
                ->dateTime()
                ->sortable(),
            TextColumn::make("created_at")
                ->label("Tanggal")
                ->dateTime()
                ->sortable(),
        ])
        ->filters([
            SelectFilter::make("status")
                ->options(TransactionStatus::class),
            SelectFilter::make("payment_status")
                ->options(PaymentStatus::class),
        ])
        ->recordActions([
            Action::make("view")
                ->label("Lihat")
                ->icon("heroicon-o-eye")
                ->url(fn(Transaction $record) => route("transaction.view", $record->trx_id))
                ->openUrlInNewTab(),

            Action::make("review")
                ->color("success")
                ->icon("heroicon-o-star")
                ->schema([
                    Select::make("product_id")
                        ->label("Pilih Produk")
                        ->options(fn(Transaction $record) => $record->items()->whereHas("itemable", function($query) {
                            $query->where("itemable_type", Product::class)->orWhere("itemable_type", \CodeWithDiki\ProductModule\Models\Product::class);
                        })->with("itemable")->get()->pluck("itemable.name", "itemable.id"))
                        ->required(),
                    Select::make('rating')
                        ->options([
                            1 => '1 Star',
                            2 => '2 Stars',
                            3 => '3 Stars',
                            4 => '4 Stars',
                            5 => '5 Stars',
                        ])
                        ->required(),
                    Textarea::make('message'),
                ])
                ->requiresConfirmation()
                ->action(function(array $data, Transaction $record) {
                    $review_data = new ProductReviewData(
                        product_id: $data["product_id"],
                        from: $this->user->name,
                        rating: $data["rating"],
                        message: $data["message"] ?? null,
                    );

                    ProductModule::createProductReview($review_data);

                    Notification::make()
                        ->title("Review berhasil dikirim")
                        ->success()
                        ->send();
                })
                ->label("Review")
                ->visible(fn(Transaction $record) => $record->status == TransactionStatus::COMPLETED),
        ]);
    }

};
?>

<div>
    {{ $this->table }}
</div>