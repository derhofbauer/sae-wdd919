(function () {

  /**
   * Prüfen, ob das Script aufgerufen wird.
   */
  console.log('Yaay! :D')

  /**
   * Base Url aus dem <base> Tab holen
   */
  const base = document.baseURI

  /**
   * Anzahl der Produkte im Cart in der Navbar ausbessern
   *
   * @param {number} numberOfProducts
   */
  const updateCartCount = (numberOfProducts) => {
    /**
     * Element selektieren
     */
    const $elements = document.querySelectorAll('.number-of-products')

    /**
     * Alle Elemente durchgehen und den Text ändern.
     */
    $elements.forEach(($element) => {
      $element.textContent = `(${numberOfProducts})`
    })
  }

  /**
   * Cart Popup aktualisieren
   *
   * Hier gehen wir vom Prinzip her vor, wie Vue.js und React vorgehen würden.
   * Wir suchen nicht die einzelnen Nodes aus dem DOM, die aktualisiert werden
   * müssen, sondern wir erstellen sie einfach neu und ersetzen die alten, nicht
   * mehr aktuellen Nodes, mit den neuen Nodes.
   *
   * @param {Array} cartContent
   */
  const updateCartPopup = (cartContent) => {
    /**
     * Array vorbereiten
     */
    const newPopupItems = []

    /**
     * API Response durchgenen und für jedes Produkt die benötigten HTMLElements
     * erstellen.
     */
    cartContent.forEach((product) => {
      /**
       * <div.popup-item> erstellen und Klasse hinzufügen
       * @type {HTMLDivElement}
       */
      const $popupItem = document.createElement('div')
      $popupItem.classList.add('popup-item')

      /**
       * Gibt es für das aktuell verarbeitete Produkt Bilder?
       */
      if (product._images.length > 0) {
        /**
         * Wenn ja, erstellen wir einen <img>-Node und setzen die src- und alt-
         * Attribute und fügen eine Klasse hinzu.
         * @type {HTMLImageElement}
         */
        const $img = document.createElement('img')
        $img.src = product._images[0]
        $img.alt = product.name
        $img.classList.add('img-thumbnail')

        /**
         * Den fertigen <img>-Node hängen wir in $popupItem ein.
         */
        $popupItem.append($img)
      }

      /**
       * Wir gehen für die Quantity vor, wie für das Bild, nur setzen wir hier
       * einen Text Inhalt für den Node und nicht src und alt.
       * @type {HTMLSpanElement}
       */
      const $quantity = document.createElement('span')
      $quantity.classList.add('quantity')
      $quantity.textContent = product.quantity + 'x'
      /**
       * Auch dieses Element wird in $popupItem eingehängt.
       */
      $popupItem.append($quantity)

      /**
       * Wir gehen für den Produktnamen vor, wie für die Quantit.
       * @type {HTMLSpanElement}
       */
      const $name = document.createElement('span')
      $name.classList.add('name')
      $name.textContent = product.name
      /**
       * Auch dieses Element wird in $popupItem eingehängt.
       */
      $popupItem.append($name)

      /**
       * Haben wir dieses einzelne $popupItem fertig zusammengebaut, fügen wir
       * es in den Array hinzu.
       */
      newPopupItems.push($popupItem)
    })

    /**
     * Nun holen wir uns alle aktuell im HTML befindlichen .popup-items, gehen
     * sie durch und löschen sie.
     */
    const $currentPopupItems = document.querySelectorAll('.cart-popup .popup-item')
    $currentPopupItems.forEach(($node) => $node.remove())

    /**
     * Nachdem das Cart Popup jetzt nur noch einen Button hat, gehen wir die
     * zuvor neu erstellen $newPopupItems durch und fügen jedes einzelne inkl.
     * seiner Kindelemente (img, quantity, name) in .popup-content ein.
     */
    const $popupContent = document.querySelector('.cart-popup .popup-content')
    newPopupItems.forEach((newPopupItem) => {
      $popupContent.append(newPopupItem)
    })
  }

  /**
   * Cart Table Input Value ändern
   *
   * @param {number} productId
   * @param {number} numberOfProducts
   */
  const updateInputValue = (productId, numberOfProducts) => {
    /**
     * Element selektieren
     */
    const $inputs = document.querySelectorAll(`[name="cart-quantity[${productId}]"`)

    /**
     * Alle Elemente durchgehen und den value ändern.
     */
    $inputs.forEach(($input) => {
      $input.value = numberOfProducts
    })
  }

  /**
   * Preis formatieren
   * @param {number} price
   * @return {string}
   */
  const formatPrice = (price) => {
    /**
     * Die JavaScript Intl-API kann numbers als Währung formatieren.
     */
    return Intl.NumberFormat('de-AT', { style: 'currency', currency: 'EUR' }).
      format(price)
  }

  /**
   * Subtotal im Cart Table aktualisieren
   *
   * @param {number} productId
   * @param {number} subtotal
   */
  const updateSubTotal = (productId, subtotal) => {
    /**
     * Element selektieren
     */
    const $subtotalDomNodes = document.querySelectorAll(`.product-${productId} .subtotal`)

    /**
     * Alle Elemente durchgehen und den Text ändern.
     */
    $subtotalDomNodes.forEach(($subtotalDomNode) => {
      $subtotalDomNode.textContent = formatPrice(subtotal)
    })
  }

  /**
   * Total im Cart Table aktualisieren
   *
   * @param {number} total
   */
  const updateTotal = (total) => {
    /**
     * Element selektieren
     */
    const $totalDomNodes = document.querySelectorAll(`.cart-total`)

    /**
     * Alle Elemente durchgehen und den Text ändern.
     */
    $totalDomNodes.forEach(($totalDomNode) => {
      $totalDomNode.textContent = formatPrice(total)
    })
  }

  /**
   * Cart Table aktualisieren
   *
   * @param {object} cartContent
   */
  const updateCartTable = (cartContent) => {
    /**
     * Alle <tr>s selektieren
     */
    const $renderedProducts = document.querySelectorAll(
      '.cart-table tbody tr')

    /**
     * Alle <tr>s durchgehen
     */
    $renderedProducts.forEach(($renderedProduct) => {
      /**
       * Schalter, ob ein Produkt in der Tabelle erhalten bleiben soll, oder
       * nicht.
       */
      let shouldStay = false

      /**
       * Alle Produkte aus der API Response durchgehen
       */
      cartContent.forEach(({ id }) => {
        /**
         * Ist ein Produkt aus der API Response in der Tabelle, so legen wir den
         * Schalter um.
         */
        if ($renderedProduct.classList.contains(`product-${id}`)) {
          shouldStay = true
        }
      })

      /**
       * Soll das Produkt nicht erhalten bleiben, so löschen wir den DOM-Node.
       */
      if (shouldStay === false) {
        $renderedProduct.remove()
      }
    })
  }

  /**
   * Toast Message hinzufügen und nach einer gewissen Zeit wieder löschen.
   *
   * @param {string} text
   * @param {HTMLElement} parentNode
   * @param {string} className
   * @param {string} tagName
   */
  const addToast = (text, parentNode, className, tagName = 'p') => {
    /**
     * Neuen DOM-Node erstellen
     */
    const $success = document.createElement(tagName)

    /**
     * Counter initialisieren
     */
    let counter = 5

    /**
     * Klassen und Text zu DOM-Node hinzufügen
     */
    $success.className = 'alert ' + className
    $success.textContent = text + ': ' + counter

    /**
     * DOM-Node in parentNode einhängen
     */
    parentNode.appendChild($success)

    /**
     * Hier starten wir ein Interval, dass alle 1000ms (1 Sekunde) läuft und den
     * Counter im Text der Toast Message aktualisiert.
     */
    const interval = setInterval(() => {
      counter--
      $success.textContent = text + ': ' + (counter)
    }, 1000)

    /**
     * Hier setzen wir ein Timeout, dass nach counter Sekunden die Toast Message
     * wieder löscht und das Interval von oben wieder löscht.
     */
    setTimeout(() => {
      $success.remove()
      clearInterval(interval)
    }, 1000 * counter)
  }

  /**
   * Formular auf der Produkt Detail Seite selektieren.
   */
  const $forms = document.querySelectorAll('form.add-to-cart')
  /**
   * Alle selektierten Elemente durchgehen
   */
  $forms.forEach(($form) => {
    /**
     * EventListener an das Event binden, damit wir am submit Event mithören
     * können.
     */
    $form.addEventListener('submit', (event) => {
      /**
       * Browser-Standard für das submit Event unterbinden
       */
      event.preventDefault()

      /**
       * FormData Objekt erstellen, damit wir die Formulardaten gut aus dem
       * Formular herausbekommen.
       */
      const formData = new FormData(event.target)

      /**
       * Produkt ID aus dem hidden <input> Feld auslesen
       */
      const productId = formData.get('product_id')

      /**
       * Request an die API schicken und die formData übertragen
       */
      fetch(base + `api/cart/add/${productId}`, {
        method: event.target.method,
        body: formData,
      }).
        /**
         * API Response als JSON interpretieren
         */
        then(response => response.json()).
        /**
         * Daten aus API Response verarbeiten
         */
        then(({ numberOfProducts, cartContent }) => {
          /**
           * Cart Counter in Navbar aktualisieren
           */
          updateCartCount(numberOfProducts)
          updateCartPopup(cartContent)

          /**
           * Toast Message erzeugen
           */
          addToast(
            'Das Produkt wurde zum Warenkorb hinzugefügt :D',
            $form.parentNode,
            'alert-success',
          )
        }).
        /**
         * Wenn ein Fehler im fetch-Befehl aufgetreten ist
         */
        catch((error) => {
          /**
           * Toast Message erzeugen
           */
          addToast(
            'Oh nein, ein Fehler ist aufgetreten :(',
            $form.parentNode,
            'alert-danger',
          )
        })
    })
  })

  /**
   * Cart Table Buttons selektieren
   */
  const $ajaxCartButtons = document.querySelectorAll('.ajax-cart-button')
  /**
   * Alle Buttons durchgehen
   */
  $ajaxCartButtons.forEach(($ajaxCartButton) => {
    /**
     * Event Listener auf das click-Event binden
     */
    $ajaxCartButton.addEventListener('click', (event) => {
      /**
       * Browser-Standard unterbinden
       */
      event.preventDefault()

      /**
       * API-Route aus dem <form> target berechnen.
       */
      const $route = event.target.href.replace('cart', 'api/cart')

      /**
       * Request an die $route schicken
       */
      fetch($route).
        /**
         * API Response als JSON behandeln
         */
        then(response => response.json()).
        /**
         * Daten aus der API Response verarbeiten
         */
        then(({ cartContent, numberOfProducts, total }) => {
          /**
           * Alles aktualisieren, was geht
           */
          updateCartTable(cartContent)
          updateCartCount(numberOfProducts)
          updateCartPopup(cartContent)
          updateTotal(total)

          /**
           * Für alle Produkte aus der API Response die zugehörigen Werte
           * aktualisieren.
           */
          cartContent.forEach(({ id, quantity, subtotal }) => {
            updateInputValue(id, quantity)
            updateSubTotal(id, subtotal)
          })

          /**
           * Wenn gar keine Produkte mehr im Warenkorb sind, löschen wir auch
           * die Checkout- und Update-Buttons in der Fußzeile des Cart Tables.
           */
          if (numberOfProducts === 0) {
            document.querySelectorAll('.cart-buttons').
              forEach(($cartButtons) => {
                $cartButtons.remove()
              })
          }

        })
    })
  })
})()
