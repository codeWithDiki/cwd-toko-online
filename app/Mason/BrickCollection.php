<?php

namespace App\Mason;

class BrickCollection
{
    public static function make(): array
    {
        return [
            BannerBrick::class,
            BrandBrick::class,
            DealsProductCTABrick::class,
            FeaturedProductsBrick::class,
            FeaturesBrick::class,
            CustomerTestimonyBrick::class
        ];
    }
}