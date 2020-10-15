<?php

namespace App\Controllers;

use Core\View;
use App\Models\Product;

/*
 * @todo: comment
 */
class ProductController
{

    public function show (int $id)
    {
        $product = Product::find($id);
        
        View::render('product-single', [
            'product' => $product
        ]);
    }

}
