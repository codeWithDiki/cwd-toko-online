<?php

// config for VendorName/Skeleton

use App\Models\Product;

return [
    "brand_class" => \CodeWithDiki\ProductModule\Models\Brand::class,
    "category_class" => \CodeWithDiki\ProductModule\Models\Category::class,
    "product_class" => Product::class,
    "product_image_class" => \CodeWithDiki\ProductModule\Models\ProductImage::class,
    "product_variant_class" => \CodeWithDiki\ProductModule\Models\ProductVariant::class,
    "product_color_class" => \CodeWithDiki\ProductModule\Models\ProductColor::class,
    "product_size_class" => \CodeWithDiki\ProductModule\Models\ProductSize::class,
    "product_wrapper_class" => \CodeWithDiki\ProductModule\Models\ProductWrapper::class,
    "product_review_class" => \CodeWithDiki\ProductModule\Models\ProductReview::class
];
