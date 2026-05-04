<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class FeaturedProductsBrick extends Brick
{
    public static function getId(): string
    {
        return 'featured-products-brick';
    }

    public static function getIcon(): string | Heroicon | Htmlable | null
    {
        return Heroicon::OutlinedCube;
    }

    public static function getLabel(): string
    {
        return parent::getLabel();
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.featured-products-brick', array_merge([
            'config' => $config,
            'data' => $data,
        ], $config, [
            "products" => config('product-module.product_class')::whereIn('id', $config['product_ids'] )->get(),
        ]))->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                TextInput::make("title")
                    ->label("Title")
                    ->required(),
                Textarea::make("description")
                    ->label("Description")
                    ->required(),
                Select::make("product_ids")
                    ->label("Products")
                    ->multiple()
                    ->options(config('product-module.product_class')::pluck('name', 'id')),
            ]);
    }
}
