<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class CategoryController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class CategoryController
{

    /**
     * @todo: comment
     */
    public function updateForm (int $id)
    {
        /**
         * [x] Prüfen ob der Zugriff authorisiert ist
         * [x] Category aus DB laden
         * [x] View laden
         */

        /**
         * Prüfen ob ein User eingeloggt ist und ob dieser eingeloggt User Admin ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * Kategorie, die bearbeitet werden soll, aus der Datenbank abfragen
         */
        $category = Category::find($id);

        /**
         * Alle Produkte aus der Datenbank abfragen
         */
        $allProducts = Product::all();

        /**
         * Produkte, die der Category zugewiesen sind, aus der der Datenbank abfragen
         */
        $categoryProducts = Product::findByCategoryId($category->id);

        /**
         * View laden und Variablen übergeben
         */
        View::render('admin/category-update', [
            'category' => $category,
            'allProducts' => $allProducts,
            'categoryProducts' => $categoryProducts
        ]);
    }

    /**
     * @todo: comment
     */
    public function update (int $id)
    {
        /**
         * [x] Prüfen ob der Zugriff authorisiert ist
         * [x] Validierung
         * [x] Category aus DB laden
         * [x] Werte aus Formular in Category speichern
         * [x] geänderte Category in die DB zurück speichern
         * [x] Erfolgsmeldung in Session speichern
         * [x] Redirect
         */

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
         * Kategorie, die bearbeitet werden soll, aus der Datenbank laden.
         */
        $category = Category::find($id);

        /**
         * Sind keine Validierungsfehler aufgetreten ...
         */
        if (!empty($validationErrors)) {
            /**
             * ... dann speichern wir sie in die Session und leiten zurück zum Bearbeitungsformular, wo die Fehler über
             * das errors.php Partial ausgegeben werden.
             */
            Session::set('errors', $validationErrors);

            /**
             * Redirect zurück zum Bearbeitungsformular.
             */
            header('Location: ' . BASE_URL . '/admin/categories/' . $category->id . '/edit');
            exit;
        }

        /**
         * Eigenschaften der Category mit den Daten aus dem Formular aktualisieren.
         */
        $category->name = $_POST['name'];

        /**
         * Neues Produkt in die Datenbank speichern.
         *
         * Die User::save() Methode gibt true zurück, wenn die Speicherung in die Datenbank funktioniert hat.
         */
        if ($category->save()) {
            /**
             * Category-Checkboxen verarbeiten
             */
            $this->handleProducts($category);

            /**
             * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zurück zum Bearbeitungsformular.
             */
            Session::set('success', ['Die Kategorie wurde erfolgreich gespeichert.']);
            header('Location: ' . BASE_URL . '/admin/categories/' . $category->id . '/edit');
            exit;
        } else {
            /**
             * Fehlermeldung erstellen und in die Session speichern.
             */
            $validationErrors[] = 'Die Kategorie konnte nicht gespeichert werden.';
            Session::set('errors', $validationErrors);

            /**
             * Redirect zurück zum Erstellungsformular.
             */
            header('Location: ' . BASE_URL . '/admin/categories/' . $category->id . '/edit');
            exit;
        }
    }

    /**
     * @todo: comment
     */
    public function createForm ()
    {
        /**
         * [x] Prüfen ob der Zugriff authorisiert ist
         * [x] View laden
         */

        /**
         * Prüfen ob ein User eingeloggt ist und ob dieser eingeloggt User Admin ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * View laden
         */
        View::render('admin/category-create');
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
         * Sind Validierungsfehler aufgetreten ...
         */
        if (!empty($validationErrors)) {
            /**
             * ... dann speichern wir sie in die Session und leiten zurück zum Bearbeitungsformular, wo die Fehler über
             * das errors.php Partial ausgegeben werden.
             */
            Session::set('errors', $validationErrors);

            /**
             * Redirect zurück zum Erstellungsformular.
             */
            header('Location: ' . BASE_URL . '/admin/categories/create');
            exit;
        }

        /**
         * Neue Kategorie anlegen, damit wir die Daten aus dem Formular speichern können.
         */
        $category = new Category();

        /**
         * Eigenschaften der Kategorie mit den Daten aus dem Formular befüllen.
         */
        $category->name = $_POST['name'];

        /**
         * Neues Produkt in die Datenbank speichern.
         *
         * Die User::save() Methode gibt true zurück, wenn die Speicherung in die Datenbank funktioniert hat.
         */
        if ($category->save()) {
            /**
             * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zurück zum Bearbeitungsformular.
             *
             * Um eine Erfolgsmeldung ausgeben zu können, verwenden wir die selbe Mechanik wie für die errors.
             */
            Session::set('success', ['Das Produkt wurde erfolgreich gespeichert.']);

            /**
             * Redirect zur Bearbeitungsseite.
             */
            header('Location: ' . BASE_URL . '/admin/categories/' . $category->id . '/edit');
            exit;
        } else {
            /**
             * Fehlermeldung erstellen und in die Session speichern.
             */
            $validationErrors[] = 'Die Kategorie konnte nicht gespeichert werden.';
            Session::set('errors', $validationErrors);

            /**
             * Redirect zurück zum Erstellungsformular.
             */
            header('Location: ' . BASE_URL . '/admin/categories/create');
            exit;
        }


    }

    /**
     * @todo: comment
     */
    public function validateAndGetErrors ()
    {
        /**
         * Formulardaten validieren.
         */
        $validator = new Validator();
        $validator->validate($_POST['name'], 'Name', true, 'textnum');

        /**
         * Validierungsfehler aus dem Validator holen und zurückgeben.
         */
        return $validator->getErrors();
    }

    /**
     * Product-Checkboxen aus dem Category-Formular entgegennehmen und verarbeiten.
     *
     * @param Category $category
     * @todo: comment
     */
    private function handleProducts (Category $category)
    {
        /**
         * Alle Produkte zu der $category aus der Datenbank abfragen
         */
        $categoryProducts = Product::findByCategoryId($category->id);
        /**
         * Indizes des Arrays, der aus dem Formular übergeben wird, als Werte eines neuen Arrays speichern, damit wir
         * leichter damit arbeiten können.
         */
        $newProductIds = array_keys($_POST['products']);
        /**
         * Array vorbereiten, in den wir die IDs der bereits verknüpften Products rein speichern, wenn wir durchgehen,
         * welche Verknüpfungen gelöst werden müssen.
         */
        $idsOfLinkedProducts = [];

        /**
         * Nun gehen wir alle Produkte durch, die mit der Kategorie verknüpft sind.
         */
        foreach ($categoryProducts as $product) {
            /**
             * Ist ein Produkt verknüpft, das auch im Formular angehakerlt ist, so soll die Verknüpfung bestehen
             * bleiben und wir speichern die ID des Produkts in unser vorbereitetes Array.
             */
            if (in_array($product->id, $newProductIds)) {
                $idsOfLinkedProducts[] = $product->id;
            } else {
                /**
                 * Ist ein Produkt verknüpft, das nicht im Formular angehakerlt ist, so soll die Verknüpfung gelöst
                 * werden.
                 */
                $product->detachFromCategory($category->id);
            }
        }

        /**
         * Nun gehen wir alle angehakerlten Checkboxen durch.
         */
        foreach ($newProductIds as $productId) {
            /**
             * Wenn eine Produkt Checkbox angehakerlt wurde, die in $idsOfLinkedProducts vorhanden ist, so besteht
             * die Verbindung zwischen Category und Product bereits. Daher invertieren wir die Bedingung und prüfen, ob
             * die Checkbox noch nicht in dem vorbereiteten Array vorkommt - in diesem Fall muss eine neue Verknüpfung
             * zwischen Produkt und Kategorie angelegt werden.
             */
            if (!in_array($productId, $idsOfLinkedProducts)) {
                $product = Product::find($productId);
                $product->attachToCategory($category->id);
            }
        }
    }
}
