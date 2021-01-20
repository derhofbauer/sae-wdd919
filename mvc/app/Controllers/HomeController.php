<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use Core\Config;
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
         * Alle Posts aus der Datenbank abfragen. Anders als die "normale" BaseModel::all() Methode gibt die
         * BaseModel::allPaginated() Methode nur ein paar Elemente aus der Datenbank zurück und berücksichtigt dabei
         * aber den GET-Parameter page, den wir im Paginator Partial setzen.
         */
        $posts = Post::allPaginated();
        /**
         * Anzahl aller Elemente in der Tabelle ausgeben. Diese Information brauchen wir, damit wir berechnen können,
         * wie viele Seiten es im Paginator geben muss.
         */
        $count = Post::countAll();
        /**
         * Anzahl der Seiten berechnen. Wir verwenden die ceil() Funktion, die eine Gleitkommazahl auf die nächste
         * Ganzzahl aufrundet, weil wir auch eine eigene Seite brauchen, wenn nur ein einzelnes Element "zu viel" ist.
         */
        $numberOfPages = ceil($count / Config::get('app.pagination-limit'));

        /**
         * Aktuelle URL berechnen und den GET-Paramater page entfernen, damit wir ihn im Paginator nicht ein zweites
         * mal setzen.
         *
         * Zunächst prüfen wir, ob HTTP oder HTTPS verwendet wird.
         */
        $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
        /**
         * Dann bauen wir uns die aktuelle URL zusammen.
         */
        $currentUrl = ($isHttps ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        /**
         * Dann entfernen wir &page=<int> und page=<int>.
         */
        $currentUrlWithoutPage = preg_replace('/&?page=[0-9]+/', '', $currentUrl);
        /**
         * Es kann vorkommen, dass am Ende der URL ein Fragezeichen übrig bleibt, wenn page der einzige GET Parameter
         * war. Hier trimmen wir von rechts alle ? weg, die sich finden lassen.
         */
        $currentUrlWithoutPage = rtrim($currentUrlWithoutPage, '?');
        /**
         * Es kann auch passieren, dass ? und & direkt aufeinander folgen, wenn page der erste von mehreren GET
         * Parametern war. Hier lösen wir dieses Problem, indem wir ?& mit ? ersetzen.
         */
        $currentUrlWithoutPage = str_replace('?&', '?', $currentUrlWithoutPage);

        /**
         * View laden und Daten übergeben.
         */
        View::render('blog', [
            'posts' => $posts,
            'numberOfPages' => $numberOfPages,
            'currentUrl' => $currentUrlWithoutPage
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
