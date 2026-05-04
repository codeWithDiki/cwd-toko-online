<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('site.site_name', 'Toko Online');
        $this->migrator->add('site.site_description', 'Deskripsi singkat tentang toko online Anda.');
        $this->migrator->add('site.site_logo_url', null);

        $this->migrator->add('site.contact_email', null);
        $this->migrator->add('site.contact_phone', null);
        $this->migrator->add('site.contact_address', null);

        $this->migrator->add('site.facebook_url', null);
        $this->migrator->add('site.twitter_url', null);
        $this->migrator->add('site.instagram_url', null);
        $this->migrator->add('site.tiktok_url', null);

        $this->migrator->add('site.seo_title', null);
        $this->migrator->add('site.seo_description', null);
        $this->migrator->add('site.seo_keywords', null);

        $this->migrator->add('site.homepage_builder', null);
    }

    public function down(): void
    {
        $this->migrator->delete('site.site_name');
        $this->migrator->delete('site.site_description');
        $this->migrator->delete('site.site_logo_url');

        $this->migrator->delete('site.contact_email');
        $this->migrator->delete('site.contact_phone');
        $this->migrator->delete('site.contact_address');

        $this->migrator->delete('site.facebook_url');
        $this->migrator->delete('site.twitter_url');
        $this->migrator->delete('site.instagram_url');
        $this->migrator->delete('site.tiktok_url');

        $this->migrator->delete('site.seo_title');
        $this->migrator->delete('site.seo_description');
        $this->migrator->delete('site.seo_keywords');

        $this->migrator->delete('site.homepage_builder');
    }
};
