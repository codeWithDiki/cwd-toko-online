<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SiteSettings extends Settings
{

    // General Site Settings
    public string $site_name = "Toko Online";
    public ?string $site_description;
    public ?string $site_logo_url;

    // Contact Information
    public ?string $contact_email;
    public ?string $contact_phone;
    public ?string $contact_address;

    // Social Media Links
    public ?string $facebook_url;
    public ?string $twitter_url;
    public ?string $instagram_url;
    public ?string $tiktok_url;

    // SEO Settings
    public ?string $seo_title;
    public ?string $seo_description;
    public ?array $seo_keywords;

    // Homepage Builder
    public ?array $homepage_builder;

    public static function group(): string
    {
        return 'site';
    }
}