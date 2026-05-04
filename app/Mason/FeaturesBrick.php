<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use FawazIwalewa\FilamentIconPicker\Forms\Components\IconPicker;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class FeaturesBrick extends Brick
{
    public static function getId(): string
    {
        return 'features-brick';
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
        return view('mason.features-brick', [
            'config' => $config,
            'data' => $data,
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                Repeater::make('features')
                    ->schema([
                        IconPicker::make('icon')
                            ->sets(['heroicons'])
                            ->label('Icon')
                            ->required(),
                        TextInput::make('title')->required(),
                        TextInput::make('description')->required(),
                    ])
                    ->label('Features')
                    ->required()
            ]);
    }
}
