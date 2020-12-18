(function () {
  console.log('Yaay! Gallery works :D')

  /**
   * Zuerst selektieren wir uns ein paar Elemente, die wir später wieder
   * brauchen werden.
   */
  const $fullImg = document.querySelector('.gallery__full img')
  const $fullCaption = document.querySelector('.gallery__full figcaption')
  const $thumbs = document.querySelectorAll('.gallery__thumbs .figure')
  const $paginationButtons = document.querySelectorAll(
    '.gallery__navigation .page-link')
  const $paginationItems = document.querySelectorAll(
    '.gallery__navigation .page-item')

  /**
   * Aktualisieren des großen Bildes im Slider
   *
   * @param {HTMLElement} $img
   */
  const updateFull = ($img) => {
    /**
     * Properties des $img auslesen mittels Object Destructuring
     */
    const { src, alt } = $img

    /**
     * src- und alt-Attribute des großen Bildes überschreiben
     */
    $fullImg.setAttribute('src', src)
    $fullImg.setAttribute('alt', alt)

    /**
     * Caption des großen Bildes überschreiben
     */
    $fullCaption.textContent = alt
  }

  /**
   * Aktualisieren des active-Status der Thumbnails
   *
   * @param {string} currentSelector
   * @param {EventTarget} currentTarget
   * @param {string} currentClass
   */
  const updateCurrent = (
    currentSelector, currentTarget, currentClass = 'current') => {

    /**
     * Aktuell ausgewähltes Element suchen
     * @type {NodeListOf<Element>}
     */
    const $currents = document.querySelectorAll(currentSelector)

    /**
     * Alle gefundenen Elemente durchgehen und die active-Klasse entfernen.
     */
    $currents.forEach(($current) => {
      $current.classList.remove(currentClass)
    })

    /**
     * Neues active-Element aktiv schalten
     */
    currentTarget.classList.add(currentClass)
  }

  /**
   * EventListener auf Thumbs erstellen
   */
  $thumbs.forEach(($thumb) => {
    $thumb.addEventListener('click', (event) => {
      /**
       * Wir brauchen den Index des geklickten Thumbnails, damit wir die
       * Pagination aktualisieren können.
       * @type {number}
       */
      const newActiveIndex = [...$thumbs].indexOf(event.currentTarget)

      /**
       * [x] Bild inkl. Beschrichtung & alt-Attribut tauschen
       * [x] current Klasse neu setzen
       */
      updateFull(event.currentTarget.children[0])
      updateCurrent('.gallery .current', event.currentTarget)
      updateCurrent('.gallery__navigation .active',
        $paginationItems[newActiveIndex + 1], 'active')
    })
  })

  /**
   * EventListener auf Pagination erstellen
   */
  $paginationButtons.forEach(($paginationButton) => {
    $paginationButton.addEventListener('click', (event) => {
      /**
       * Wir brauchen den Index des geklickten Pagination Buttons, damit wir die
       * Thumbails aktualisieren können. Dazu holen wir uns alle Pagination
       * Buttons und suchen den Index des geglickten Buttons Pagination Items in
       * dieser Liste.
       */
      const $parent = event.currentTarget.parentNode
      const index = [...$paginationItems].indexOf($parent)

      /**
       * Index des letzten Pagination Buttons holen
       * @type {number}
       */
      const lastIndex = $paginationItems.length - 1
      /**
       * Aktuell ausgewähltes Pagination Item abrufen
       * @type {Element}
       */
      const $currentActive = document.querySelector(
        '.gallery__navigation .active')
      /**
       * Index des aktuell ausgewählten Pagination Items abrufen. Das brauchen
       * wir für die relative Navigation (vor/zurück)
       * @type {number}
       */
      const currentActiveIndex = [...$paginationItems].indexOf($currentActive)

      /**
       * [x] Wenn index 0: ein Bild nach links
       * [x] wenn index = lastIndex: ein Bild nach rechts
       * [x] sonst: zugehöriges Bild
       */
      let newActiveIndex
      let $newActivePaginationItem
      /**
       * Wurde der erste Button geklickt und soll nach links navigiert werden?
       */
      if (index === 0) {

        /**
         * Werte setzen um sie später zu verwenden
         */
        newActiveIndex = Math.max(0, currentActiveIndex - 2)
        $newActivePaginationItem = $paginationItems[newActiveIndex + 1]

      } else if (index === lastIndex) {
        /**
         * Wurde der letzte Button geklickt und soll nach rechts navigiert
         * werden, dann setzen wir die Werte wieder entsprechend.
         */
        newActiveIndex = Math.min(lastIndex - 2, currentActiveIndex)
        $newActivePaginationItem = $paginationItems[newActiveIndex + 1]

      } else {

        /**
         * Wurde einer der Buttons dazwischen geklickt, der eine Ziffer hat, so
         * setzen wir die beiden Werte entsprechend.
         */
        newActiveIndex = index - 1
        $newActivePaginationItem = $parent

      }

      /**
       * Thumbnail holen, das mit dem newActiveIndex übereinstimmt.
       */
      const $figure = document.querySelectorAll(
        '.gallery__thumbs .figure')[newActiveIndex]

      /**
       * Großes Bild, active-Status von Thumbnails und Pagination aktualisieren
       */
      updateFull($figure.children[0])
      updateCurrent('.gallery .current', $figure)
      updateCurrent('.gallery__navigation .active',
        $newActivePaginationItem, 'active')

    })
  })

})()
