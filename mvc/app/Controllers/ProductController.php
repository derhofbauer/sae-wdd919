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

        /**
         * Formulardaten validieren.
         */
        $validationErrors = $this->validateAndGetErrors();

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
         * Hochgeladene, neue Bilder verarbeiten
         */
        $product = $this->validateFileupload($product, $validationErrors);

        /**
         * Sind Validierungsfehler aufgetreten ...
         */
        if (!empty($validationErrors)) {
            /**
             * ... dann speichern wir sie in die Session und leiten zurück zum Bearbeitungsformular, wo die Fehler über
             * das errors.php Partial ausgegeben werden.
             */
            Session::set('errors', $validationErrors);
            header('Location: ' . BASE_URL . '/admin/products/' . $product->id . '/edit');
            exit;
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

        /**
         * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zurück zum Bearbeitungsformular. Hier
         * könnten wir auch auf die Produkt-Übersicht im Dashboard leiten oder irgendeine andere Route.
         */
        header('Location: ' . BASE_URL . '/admin/products/' . $product->id . '/edit');
        exit;
    }

    /**
     * @todo: comment
     */
    public function createForm ()
    {
        /**
         * Prüfen ob ein User eingeloggt ist und ob dieser eingeloggt User Admin ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * Produkt, das bearbeitet werden soll, an den View übergeben.
         */
        View::render('admin/product-create');
    }

    /**
     * @todo: comment
     */
    public function create ()
    {
        /**
         * Prüfen ob ein User eingeloggt ist und ob dieser eingeloggt User Admin ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * Formulardaten validieren.
         */
        $validationErrors = $this->validateAndGetErrors();

        /**
         * Neues Produkt anlegen, damit wir die Daten aus dem Formular speichern können.
         */
        $product = new Product();

        /**
         * Eigenschaften des Produkts mit den Daten aus dem Formular aktualisieren.
         */
        $product->name = $_POST['name'];
        $product->description = $_POST['description'];
        $product->price = (float)$_POST['price'];
        $product->stock = (int)$_POST['stock'];

        /**
         * Hochgeladene, neue Bilder verarbeiten
         */
        $product = $this->validateFileupload($product, $validationErrors);

        /**
         * Sind Validierungsfehler aufgetreten ...
         */
        if (!empty($validationErrors)) {
            /**
             * ... dann speichern wir sie in die Session und leiten zurück zum Bearbeitungsformular, wo die Fehler über
             * das errors.php Partial ausgegeben werden.
             */
            Session::set('errors', $validationErrors);
            header('Location: ' . BASE_URL . '/admin/products/create');
            exit;
        }

        /**
         * Neues Produkt in die Datenbank speichern.
         * @todo: comment
         */
        if ($product->save()) {
            /**
             * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zurück zum Bearbeitungsformular. Hier
             * könnten wir auch auf die Produkt-Übersicht im Dashboard leiten oder irgendeine andere Route.
             */
            header('Location: ' . BASE_URL . '/admin/products/' . $product->id . '/edit');
            exit;
        } else {
            /**
             * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zurück zum Bearbeitungsformular. Hier
             * könnten wir auch auf die Produkt-Übersicht im Dashboard leiten oder irgendeine andere Route.
             */
            $validationErrors[] = 'Das Produkt konnte nicht gespeichert werden.';
            Session::set('errors', $validationErrors);
            header('Location: ' . BASE_URL . '/admin/products/create');
            exit;
        }

    }

    /**
     * @return array
     * @todo: comment
     */
    private function validateAndGetErrors ()
    {
        /**
         * Formulardaten validieren.
         */
        $validator = new Validator();
        $validator->validate($_POST['name'], 'Name', true, 'textnum');
        $validator->validate($_POST['stock'], 'Stock', true, 'int', 0);
        $validator->validate($_POST['price'], 'Price', true, 'float', 0);
        $validator->validate($_POST['description'], 'Description', false, 'textnum');

        /**
         * Validierungsfehler aus dem Validator holen und zurückgeben.
         */
        return $validator->getErrors();
    }

    /**
     * @param Product $product
     * @param array   $validationErrors
     *
     * @return Product
     * @todo: comment
     */
    private function validateFileupload (Product $product, array &$validationErrors) {
        /**
         * Hochgeladenen, "neuen" Bilder verarbeiten
         *
         * $_FILES beinhaltet immer ein leeres Bild, wenn keine Datei hochgeladen wurde. Daher prüfen wir, ob das nullte
         * Bild leer ist. Wenn nicht, wurden Datein hochgeladen.
         */
        if (!empty($_FILES['images']['name'][0])) {

            /**
             * Alle hochgeladenen Bilder-Namen durchgehen.
             *
             * Hier benötigen wir den $index, weil die $_FILES Superglobal so strukturiert ist, dass die einzelnen
             * Stücke an Information zu einem Bild verteilt sind und nicht zusammen in einem einzelnen Array gebündelt.
             * Es gibt also in 'name' und 'type' usw. jeweils mehrere Werte, die über den $index zur selben Datei
             * zugeordnet werden können.
             */
            foreach ($_FILES['images']['name'] as $index => $originalFileName) {
                /**
                 * Werte der aktuellen Datei mithilfe des $index sammeln.
                 */
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

                /**
                 * Limits für Dateigröße und Dimensionen (Breite & Höhe) für die hochgeladenen Bilder aus der Config
                 * auslesen.
                 */
                $uploadLimit = Config::get('app.upload-limit-filesize', 1024 * 1024 * 10);
                $imageSizeWidthLimit = Config::get('app.upload-limit-width', 1920);
                $imageSizeHeightLimit = Config::get('app.upload-limit-height', 1080);

                /**
                 * Auslesen der Dimensionen der aktuell durchlaufenen hochgeladenen Datei
                 */
                $uploadedImageSizes = getimagesize($tmp_name);
                $uploadedImageWidth = $uploadedImageSizes[0];
                $uploadedImageHeight = $uploadedImageSizes[1];

                /**
                 * Validieren der gerade durchlaufenen hochgeladenen Datei
                 */
                if ($error !== UPLOAD_ERR_OK) {
                    /**
                     * Ist der Status ungleich dem OK-Status, so ist ein Fehler aufgetreten und wir fügen eine Meldung
                     * zu den Validierungsfehlern aus dem Validator hinzu.
                     */
                    $validationErrors[] = 'Dateiupload ist fehlgeschlagen. Fehler' . $error;

                } elseif (strpos($type, 'image/') !== 0) {
                    /**
                     * Kommt 'image/' innerhalb des Mimetype, bspw. 'image/jpg' oder 'application/pdf', vor, so handelt
                     * es sich um eine Bild-Datei und wir nehmen sie an. Handelt es sich nicht um eine Bild-Datei,
                     * schreiben wir einen Fehler.
                     */
                    $validationErrors[] = 'Es sind nur Bilder erlaubt';

                } elseif ($size > $uploadLimit) {
                    /**
                     * Überschreitet die Größe der hochgeladenen Datei das Upload-Limit?
                     */
                    $validationErrors[] = 'Die Datei ist zu groß!';

                } elseif (
                    $uploadedImageWidth > $imageSizeWidthLimit
                    || $uploadedImageHeight > $imageSizeHeightLimit
                ) {
                    /**
                     * Überschreiten entweder die Breite, die Höhe oder beide Dimensionen die maximalen Dimensionen von
                     * Bildern, die wir in der Config definiert und oben ausgelesen haben?
                     */
                    $validationErrors[] = 'Die Dimensionen der Datei übeschreibten das Maximum';

                } else {
                    /**
                     * Wurden im Zuge der Validierung keine Fehler gefunden, holen wir uns die Pfade zum Uploads-Ordner
                     * aus der config.
                     */
                    $storagePath = Config::get('app.storage-path', 'storage/');
                    $uploadPath = Config::get('app.upload-path', 'uploads/');

                    /**
                     * Hier bauen wir aus $storagePath und $uploadPath einen Ordner-Pfad relativ zu dieser Datei
                     * (ProductController.php) zusammen. Diesen Ordner brauchen wir, damit wir die hochgeladene Datei
                     * aus dem temporären Ordner von PHP in den Ordner verschieben können.
                     */
                    $destinationFolder = __DIR__ . "/../../{$storagePath}{$uploadPath}";

                    /**
                     * Damit Dateien mit gleichen Namen sich nicht gegenseitig überschreiben hängen wir den aktuellen
                     * UNIX-Timestamp vorne dran.
                     */
                    $destinationFilename = time() . "_$name";

                    /**
                     * Jetzt bauen wir aus dem oben konstrukierten Ordner-Pfad und dem Dateinamen das finale Ziel, an
                     * das die hochgeladene Datei verschoben werden soll.
                     */
                    $destination = $destinationFolder . $destinationFilename;

                    /**
                     * PHP liefert eine Funktion, die genau dazu gedacht ist, hochgeladene Dateien vom temporären
                     * Verzeichnis von PHP an ihr Ziel zu verschieben. move_uploaded_file() gibt dabei true zurück, wenn
                     * der Vorgang erfolgreich war und false, wenn ein Fehler aufgetreten ist während des Verschiebens.
                     * Das kann bspw. passieren, wenn im Zielordner keine Schreibrechte vorhanden sind oder der Zielordner
                     * nicht existiert.
                     */
                    if (move_uploaded_file($tmp_name, $destination)) {
                        /**
                         * Wurde die Datei erfolgreich an ihr Ziel verschoben, verknüpfen wir sie mit dem Product, das
                         * bearbeitet wurde.
                         *
                         * Hier verwende ich absichtlich die basename()-Funktion um den Dateinamen wieder aus dem Pfad
                         * zu bekommen, weil ich an dieser Stelle 100%ig sicher sein kann, dass der Pfad, den wir in
                         * move_uploade_file() als $destination übergeben haben, valide ist und die hochgeladene Datei
                         * auch wirklich dort gespeichert ist. Ich könnte auch $destinationFilename verwenden, aber wenn
                         * wir möglicherweise noch eine Funktion bauen, die Leerzeichen in dem Pfad ersetzt oder etwas
                         * in der Art, dann macht es Sinn, hier den tatsächlich verwendeten Pfad heranzuziehen und den
                         * Dateinamen daraus zu berechnen.
                         */
                        $product->addImage(basename($destination));
                    } else {
                        /**
                         * Ist ein Fehler beim verschieben der Datei aufgetreten, schreiben wir einen Fehler.
                         */
                        $validationErrors[] = 'Die hochgeladene Datei konnte nicht gespeichert werden.';
                    }
                }
            }
        }

        /**
         * Potentiell verändertes Produkt (neue Bilder) zurückgeben
         */
        return $product;
    }

}
