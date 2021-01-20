<?php


namespace App\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\WishlistItem;
use Core\Session;
use Core\View;

/**
 * Class WishlistController
 *
 * @package App\Controllers
 */
class WishlistController
{

    /**
     * WishlistController constructor.
     *
     * Wenn ALLE Methoden in einem Controller auf die exakt selbe Art geschützt werden sollen, so kann dieser Schutz im
     * Konstruktor implementiert werden, weil der Konstruktor vor jeder anderen Methode der Klasse läuft.
     */
    public function __construct () {
        /**
         * Ist kein User eingeloggt, so darf keine Wunschliste verwaltet werden und wir geben einen Fehler 403 zurück.
         */
        if (!User::isLoggedIn()) {
            View::error403();
        }
    }

    /**
     * Produkt zur Wunschliste hinzufügen.
     *
     * @param int $id Product ID
     */
    public function add (int $id)
    {
        /**
         * Messages vorbereiten (Vgl. Errors)
         */
        $messages = [];
        /**
         * Eingeloggten User laden.
         * Wir können an dieser Stelle sicher sein, dass ein User eingeloggt ist, weil im Konstruktor sonst ein Fehler
         * geworfen worden wäre.
         */
        $user = User::getLoggedIn();

        /**
         * Prüfen, ob das Produkt, dass hinzugefügt werden soll, bereits auf der Wishlist des aktuellen Users ist.
         */
        if (!WishlistItem::isOnUsersWishlist($user->id, $id)) {
            /**
             * Ist das Produkt noch nicht auf der Wishlist, legen wir ein neues WishlistItem an und speichern es.
             */
            $wishlistItem = new WishlistItem();
            $wishlistItem->user_id = $user->id;
            $wishlistItem->product_id = $id;
            $wishlistItem->save();
            /**
             * Erfolgsmeldung schreiben.
             */
            $messages[] = 'Produkt erfolgreich auf die Wunschliste gesetzt.';
        } else {
            /**
             * Statusmeldung schrieben.
             */
            $messages[] = 'Das Produkt befindet sich bereits auf der Wunschliste.';
        }

        /**
         * Meldungen in die Session speichern und zurück zum Produkt leiten, von dem wir gekommen sind.
         */
        Session::set('success', $messages);
        header("Location: " . BASE_URL . "/products/$id");
        exit;
    }

    /**
     * Alle Einträge der Wishlist anzeigen.
     */
    public function list ()
    {
        /**
         * Eingeloggten User abfragen.
         */
        $user = User::getLoggedIn();
        /**
         * Alle WishlistItems für den eingeloggten User aus der Datenbank laden.
         */
        $wishlistItems = WishlistItem::findByUserId($user->id);

        /**
         * Produkte vorbereiten.
         */
        $products = [];
        /**
         * Für jedes WishlistItem das zugehörige Produkt aus der Datenbank lagen.
         */
        foreach ($wishlistItems as $wishlistItem) {
            $products[] = Product::find($wishlistItem->product_id);
        }

        /**
         * View laden und Daten übergeben.
         */
        View::render('wishlist', [
            'products' => $products
        ]);
    }

    /**
     * Ein Produkt von der Wishlist entfernen.
     *
     * @param int $id Product ID
     */
    public function remove (int $id)
    {
        /**
         * Eingeloggten User abfragen.
         */
        $user = User::getLoggedIn();
        /**
         * Alle WishlistItems mit der übergebenen Produkt ID abrufen, die dem aktuellen eingeloggten User gehören.
         *
         * Potentiell haben wir hier mehrere Elemente. Wir sollten nur eines haben, aber da wir mehrere haben können,
         * weil wir in der Datenbank keine Beschränkung dazu haben, behandeln wir das Ergebnis auch so.
         */
        $wishlistItems = WishlistItem::findByUserIdAndProductId($user->id, $id);

        /**
         * Meldungen vorbereiten.
         */
        $messages = [];

        /**
         * Alle gefundenen WishlistItems durchgehen ...
         * (Diese Schleife wird in den allermeisten Fällen nur einmal durchlaufen.)
         */
        foreach ($wishlistItems as $wishlistItem) {
            /**
             * ... und löschen.
             */
            $wishlistItem->delete();
            /**
             * Erfolgsmeldung schrieben.
             */
            $messages[] = "Produkt #" . $wishlistItem->product_id . " wurde erfolgreich aus der Wishlist entfernt.";
        }

        /**
         * Erfolgsmeldungen in Session speichern.
         */
        Session::set('success', $messages);
        /**
         * Zur Wishlist zurück leiten.
         */
        header('Location: ' . BASE_URL . '/wishlist');
        exit;
    }

}
