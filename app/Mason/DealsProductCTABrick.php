<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class DealsProductCTABrick extends Brick
{
    public static function getId(): string
    {
        return 'deals-product-cta-brick';
    }

    public static function getIcon(): string | Heroicon | Htmlable | null
    {
        return Heroicon::OutlinedCube;
    }

    public static function getLabel(): string
    {
        return "Deals Product CTA";
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.deals-product-c-t-a-brick', array_merge([
            'config' => $config,
            'data' => $data,
        ], $config, [
            "products" => config('product-module.product_class')::whereIn('id', $config['product_ids'] )->get()
        ]))->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                TextInput::make("title")
                    ->label("Title")
                    ->required(),
                TextInput::make("description")
                    ->label("Description")
                    ->required(),
                DateTimePicker::make("deal_end_date")
                    ->label("Deal End Date")
                    ->required(),
                Select::make('product_ids')
                    ->label('Select Product')
                    ->options(function() {
                        $productClass = config('product-module.product_class');
                        return $productClass::query()->pluck('name', 'id')->toArray();
                    })
                    ->multiple(true)
                    ->searchable()
                    ->required(),
            ]);
    }
}
