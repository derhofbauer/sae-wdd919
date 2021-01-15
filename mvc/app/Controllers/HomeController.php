<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use Core\Database;
use Core\View;

/**
 * Class HomeController
 *
 * @package App\Controllers
 */
class HomeController
{

    public function show ()
    {
        /**
         * Alle Produkte über das Product-Model aus der Datenbank laden.
         */
        $products = Product::all();

        /**
         * Um nicht in jeder Action den Header und den Footer und dann den View laden zu müssen, haben wir uns eine View
         * Klasse gebaut.
         */
        View::render('home', [
            'products' => $products
        ]);
    }

    /**
     * @param int $id
     */
    public function category (int $id)
    {
        /**
         * Alle Produkte, die der Category $id zugewiesen sind, über das Product-Model aus der Datenbank laden.
         */
        $products = Product::findByCategoryId($id);

        /**
         * Um nicht in jeder Action den Header und den Footer und dann den View laden zu müssen, haben wir uns eine View
         * Klasse gebaut.
         */
        View::render('home', [
            'products' => $products
        ]);
    }

    /**
     * Übersicht aller Blog Posts ausgeben.
     */
    public function blog ()
    {
        /**
         * Alle Posts aus der Datenbank abfragen.
         */
        $posts = Post::all();

        /**
         * View laden und Daten übergeben.
         */
        View::render('blog', [
            'posts' => $posts
        ]);
    }

    /**
     * Blog Post Einzelansicht ausgeben.
     *
     * @param int $id
     */
    public function post (int $id)
    {
        /**
         * Einzelnen Post aus der Datenbank laden.
         */
        $post = Post::find($id);
        /**
         * Produkte, die mit diesem Post verknüpft sind, aus der Datenbank laden.
         */
        $products = Product::findByPostId($post->id);

        /**
         * View laden und Daten übergeben.
         */
        View::render('post', [
            'post' => $post,
            'products' => $products
        ]);
    }
}
