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
 * @todo    : comment
 */
class WishlistController
{

    /**
     * WishlistController constructor.
     * @todo: comment
     */
    public function __construct () {
        if (!User::isLoggedIn()) {
            View::error403();
        }
    }

    /**
     * @param int $id Product ID
     *
     * @todo: comment
     */
    public function add (int $id)
    {
        $messages = [];
        $user = User::getLoggedIn();
        if (!WishlistItem::isOnUsersWishlist($user->id, $id)) {
            $wishlistItem = new WishlistItem();
            $wishlistItem->user_id = $user->id;
            $wishlistItem->product_id = $id;
            $wishlistItem->save();
            $messages[] = 'Produkt erfolgreich auf die Wunschliste gesetzt.';
        } else {
            $messages[] = 'Das Produkt befindet sich bereits auf der Wunschliste.';
        }

        Session::set('success', $messages);
        header("Location: " . BASE_URL . "/products/$id");
        exit;
    }

    /**
     * @todo: comment
     */
    public function list ()
    {
        $user = User::getLoggedIn();
        $wishlistItems = WishlistItem::findByUserId($user->id);

        $products = [];
        foreach ($wishlistItems as $wishlistItem) {
            $products[] = Product::find($wishlistItem->product_id);
        }

        View::render('wishlist', [
            'products' => $products
        ]);
    }

    /**
     * @param int $id Product ID
     * @todo: comment
     */
    public function remove (int $id)
    {
        $user = User::getLoggedIn();
        $wishlistItems = WishlistItem::findByUserIdAndProductId($user->id, $id);
        $messages = [];

        foreach ($wishlistItems as $wishlistItem) {
            $wishlistItem->delete();
            $messages[] = "Produkt #" . $wishlistItem->product_id . " wurde erfolgreich aus der Wishlist entfernt.";
        }

        Session::set('success', $messages);
        header('Location: ' . BASE_URL . '/wishlist');
        exit;
    }

}
