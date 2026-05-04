<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BannerBrick extends Brick
{
    public static function getId(): string
    {
        return 'banner';
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
        return view('mason.banner-brick', array_merge(
            [
            'config' => $config,
            'data' => $data,
        ], collect($config)->map(function($item, $key){
            if($key === "banners" && is_array($item)){
                return collect($item)->map(function($banner){
                    if(isset($banner['image_url'])){
                        $banner['image_url'] = Storage::url($banner['image_url']);
                    }
                    return $banner;
                })->toArray();
            }
            return $item;
        })->toArray()
        ))->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                Repeater::make('banners')
                    ->schema([
                        FileUpload::make('image_url')
                            ->directory('banner-images')
                            ->image()
                            ->label('Banner Image'),
                        TextInput::make('link_url')
                            ->label('Link URL'),
                    ])
                    ->label('Banners')
                    ->columns(1),
                Toggle::make('autoplay')
                    ->label('Autoplay')
                    ->default(false),
            ]);
    }
}
