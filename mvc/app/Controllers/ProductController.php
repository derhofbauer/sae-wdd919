<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\User;
use Core\View;

/**
 * Class ProductController
 *
 * @package App\Controllers
 */
class ProductController
{

    /**
     * @param int $id
     */
    public function show (int $id)
    {
        /**
         * Ein einzelnes Produkt anhand des Parameters in der URL über das Product Model aus der Datenbank abfragen.
         */
        $product = Product::find($id);

        /**
         * Produkt an View übergeben
         */
        View::render('product-single', [
            'product' => $product
        ]);
    }

    /**
     * Product Bearbeitungsformular anzeigen.
     *
     * @param int $id
     */
    public function updateForm (int $id)
    {
        /**
         * Prüfen ob ein User eingeloggt ist und ob dieser eingeloggt User Admin ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * Produkt, das bearbeitet werden soll, aus der Datenbank abfragen
         */
        $product = Product::find($id);

        /**
         * Produkt, das bearbeitet werden soll, an den View übergeben.
         */
        View::render('admin/product-update', [
            'product' => $product
        ]);
    }

    /**
     * Produkt mit den Daten aus dem Bearbeitungsformular aktualisieren.
     *
     * @param int $id
     */
    public function update (int $id)
    {
        /**
         * Prüfen ob ein User eingeloggt ist und ob dieser eingeloggt User Admin ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * HIER MÜSSTE VALIDIERT WERDEN!!
         */

        var_dump($_POST, $_FILES);

        /**
         * [x] Product abrufen
         * [x] Geänderte Formulardaten ins Product schreiben
         * [x] Bilder ggf. löschen
         * [ ] Datei-Upload verwalten
         * [x] Daten in die Datenbank speichern
         */

        /**
         * Produkt, das bearbeitet werden soll, aus der Datenbank laden.
         */
        $product = Product::find($id);

        /**
         * Eigenschaften des Produkts mit den Daten aus dem Formular aktualisieren.
         */
        $product->name = $_POST['name'];
        $product->description = $_POST['description'];
        $product->price = $_POST['price'];
        $product->stock = $_POST['stock'];

        /**
         * Sollen Bilder gelöscht werden?
         */
        if (isset($_POST['delete-image'])) {
            /**
             * Wenn ja, alle Bilder, die gelöscht werden sollen, durchgehen und die Verknüpfung zu dem Produkt aufheben.
             */
            foreach ($_POST['delete-image'] as $path => $on) {
                /**
                 * Dateinamen aus dem Bild-Pfad auslesen.
                 */
                $filename = basename($path);

                /**
                 * Verknüpfung zwischen Bild und Produkt aufheben. Das Bild wird dabei nicht aus dem uploads-Ordner
                 * gelöscht.
                 */
                $product->removeImage($filename);
            }
        }

        /**
         * Geändertes Produkt in der Datenbank aktualisieren.
         */
        $product->save();

        /**
         * Wie oben, werden hier jetzt die Bilder, die gelöscht werden sollen, physisch aus dem uploads-Ordner gelöscht.
         * Das ganze muss in einem eigenen Schritt passieren, weil sonst Bilder gelöscht werden könnten, die in der
         * Datenbank noch referenziert sind - das darf nicht passieren. Lieber haben wir Bilder, die nicht mehr
         * referenziert sind und somit sinnlos gespeichert werden.
         */
        if (isset($_POST['delete-image'])) {
            foreach ($_POST['delete-image'] as $path => $on) {
                unlink(__DIR__ . "/../../$path");
            }
        }
    }

}
