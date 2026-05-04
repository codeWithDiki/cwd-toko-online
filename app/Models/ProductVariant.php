<?php

namespace App\Models;
use CodeWithDiki\ProductModule\Models\ProductVariant as BaseProduct;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class ProductVariant extends BaseProduct
{
    use HasSEO;

    public function getDynamicSEOData(): SEOData
    {
        // Override only the properties you want:
        return new SEOData(
            title: $this->name,
            description: $this->description,
            image: $this?->product?->primary_image_url,
        );
    }

}
