<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class CustomerTestimonyBrick extends Brick
{
    public static function getId(): string
    {
        return 'customer-testimony-brick';
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
        return view('mason.customer-testimony-brick', array_merge([
            'config' => $config,
            'data' => $data,
        ], [
            "title" => $config['title'] ?? null,
            "description" => $config['description'] ?? null,
            "testimonies" => collect($config['testimonies'] ?? [])->map(function($testimony){
                if(isset($testimony['customer_photo'])){
                    $testimony['customer_photo'] = \Illuminate\Support\Facades\Storage::url($testimony['customer_photo']);
                }
                return $testimony;
            })->toArray()
        ]))->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->required(),
                Repeater::make('testimonies')
                    ->schema([
                        FileUpload::make('customer_photo')
                            ->label('Customer Photo')
                            ->image()
                            ->directory('customer-testimonies')
                            ->required(),
                        TextInput::make('customer_name')->required(),
                        TextInput::make('customer_title')->required(),
                        Textarea::make('testimony')->required(),
                    ])
                    ->label('Customer Testimonies')
                    ->required()
            ]);
    }
}
