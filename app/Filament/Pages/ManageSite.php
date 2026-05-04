<?php

namespace App\Filament\Pages;

use App\Settings\SiteSettings;
use Awcodes\Mason\Mason;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSite extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string $settings = SiteSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Site Settings')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Site Name')
                            ->required(),
                        Textarea::make('site_description')
                            ->rows(3)
                            ->label('Site Description'),
                        FileUpload::make('site_logo_url')
                            ->directory('site-logos')
                            ->image()
                            ->label('Site Logo'),
                    ])
                    ->aside(),
                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('contact_email')
                            ->label('Contact Email'),
                        TextInput::make('contact_phone')
                            ->label('Contact Phone'),
                        TextInput::make('contact_address')
                            ->label('Contact Address'),
                    ])
                    ->aside(),
                Section::make('Social Media Links')
                    ->schema([
                        TextInput::make('facebook_url')
                            ->label('Facebook URL'),
                        TextInput::make('twitter_url')
                            ->label('Twitter URL'),
                        TextInput::make('instagram_url')
                            ->label('Instagram URL'),
                        TextInput::make('tiktok_url')
                            ->label('TikTok URL'),
                    ])
                    ->aside(),
                Section::make('SEO Settings')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('SEO Title'),
                        TextInput::make('seo_description')
                            ->label('SEO Description'),
                        TagsInput::make('seo_keywords')
                            ->label('SEO Keywords'),
                    ])
                    ->aside(),
                Section::make('Homepage Builder')
                    ->schema([
                        Mason::make('homepage_builder')
                            ->bricks(\App\Mason\BrickCollection::make())
                            ->previewLayout("layouts.app-preview")
                            ->label('Homepage Builder')
                    ])
            ])
            ->columns(1);
    }
}
