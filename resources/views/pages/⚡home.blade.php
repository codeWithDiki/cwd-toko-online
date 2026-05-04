<?php

use Livewire\Component;

new class extends Component
{
    public ?array $homepage_builder;

    public function mount()
    {
        $settings = app(\App\Settings\SiteSettings::class);
        $this->homepage_builder = $settings->homepage_builder;
    }
};
?>
@section("title", "Home - {$siteSettings->site_name}")
<div class="w-full">
    {!! mason(content: $homepage_builder, bricks: \App\Mason\BrickCollection::make())->toHtml() !!}
</div>