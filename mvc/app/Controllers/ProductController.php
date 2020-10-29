<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\User;
use Core\Config;
use Core\Session;
use Core\Validator;
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

        $validator = new Validator();
        $validator->validate($_POST['name'], 'Name', true, 'textnum');
        $validator->validate($_POST['stock'], 'Stock', true, 'int', 0);
        $validator->validate($_POST['price'], 'Price', true, 'float', 0);
        $validator->validate($_POST['description'], 'Description', false, 'textnum');
        $validationErrors = $validator->getErrors();

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
        $product->price = (float)$_POST['price'];
        $product->stock = (int)$_POST['stock'];

        /**
         * Sollen Bilder gelöscht werden?
         */
        if (isset($_POST['delete-image'])) {
            /**
             * Wenn ja, alle Bilder, die gelöscht werden sollen, durchgehen und die Verknüpfung zu dem Produkt aufheben.
             */
            foreach ($_POST['delete-image'] as $path => $on) {
                /**
                 * Die basename-Funktion extrahiert aus einem Dateipfad nur den Dateinamen, z.B.:
                 * basename('/var/www/html/index.php') --> 'index.php'
                 *
                 * s. https://www.php.net/manual/en/function.basename.php
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
         * Hochgeladenen, "neuen" Bilder verarbeiten
         *
         * @todo: comment
         */
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $index => $originalFileName) {
                $name = $originalFileName;
                $type = $_FILES['images']['type'][$index];
                $tmp_name = $_FILES['images']['tmp_name'][$index];
                $error = $_FILES['images']['error'][$index];
                $size = $_FILES['images']['size'][$index];

                /**
                 * [x] Handelt es sich um eine Bild?
                 * [x] Ist die Datei übermäßig groß (Dateigröße)? max. 10MB
                 * [x] Liegen die Dimensionen im Rahmen?
                 * [x] Wenn ja: speichern!
                 * [x] Wenn nein: Fehler!
                 */

                $uploadLimit = 1024 * 1024 * 10; // 10MB
                $imageSizeWidthLimit = 1920;
                $imageSizeHeightLimit = 1080;

                $uploadedImageSizes = getimagesize($tmp_name);
                $uploadedImageWidth = $uploadedImageSizes[0];
                $uploadedImageHeight = $uploadedImageSizes[1];

                if ($error !== UPLOAD_ERR_OK) {
                    $validationErrors[] = 'Dateiupload ist fehlgeschlagen. Fehler' . $error;
                } elseif (strpos($type, 'image/') !== 0) {
                    $validationErrors[] = 'Es sind nur Bilder erlaubt';
                } elseif ($size > $uploadLimit) {
                    $validationErrors[] = 'Die Datei ist zu groß!';
                } elseif (
                    $uploadedImageWidth > $imageSizeWidthLimit
                    || $uploadedImageHeight > $imageSizeHeightLimit
                ) {
                    $validationErrors[] = 'Die Dimenstionen der Datei übeschreibten das Maximum';
                } else {
                    $storagePath = Config::get('app.storage-path', 'storage/');
                    $uploadPath = Config::get('app.upload-path', 'uploads/');

                    $destinationFolder = __DIR__ . "/../../{$storagePath}{$uploadPath}";
                    $destinationFilename = time() . "_$name";
                    $destination = $destinationFolder . $destinationFilename;

                    if (move_uploaded_file($tmp_name, $destination)) {
                        $product->addImage(basename($destination));
                    } else {
                        $validationErrors[] = 'Die hochgeladene Datei konnte nicht gespeichert werden.';
                    }
                }
            }
        }

        /**
         * Geändertes Produkt in der Datenbank aktualisieren.
         */
        if (!empty($validationErrors)) {
            Session::set('errors', $validationErrors);
            header('Location: ' . BASE_URL . '/admin/products/' . $product->id . '/edit');
            exit;
        }

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

        /**
         * @todo: comment
         */
        header('Location: ' . BASE_URL . '/admin/products/' . $product->id . '/edit');
        exit;
    }

}
