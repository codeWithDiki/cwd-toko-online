<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class BrandBrick extends Brick
{
    public static function getId(): string
    {
        return 'brand-brick';
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
        return view('mason.brand-brick', array_merge([
            'config' => $config,
            'data' => $data,
        ], [
            "brands" => config('product-module.brand_class')::whereIn('id', $config['brand_ids'] )->get()->map(function($brand){
                return [
                    "name" => $brand->name,
                    "thumbnail_url" => $brand->thumbnail_url ? \Illuminate\Support\Facades\Storage::url($brand->thumbnail_url) : null,
                ];
            })->toArray()
        ]))->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                Select::make('brand_ids')
                    ->label('Select Brand')
                    ->options(function() {
                        $brandClass = config('product-module.brand_class');
                        return $brandClass::query()->pluck('name', 'id')->toArray();
                    })
                    ->searchable()
                    ->multiple(true)
                    ->required(),
            ]);
    }
}
