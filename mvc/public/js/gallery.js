(function () {
  console.log('Yaay! Gallery works :D')

  const $fullImg = document.querySelector('.gallery__full img')
  const $fullCaption = document.querySelector('.gallery__full figcaption')

  const $thumbs = document.querySelectorAll('.gallery__thumbs .figure')
  const $paginationButtons = document.querySelectorAll(
    '.gallery__navigation .page-link')
  const $paginationItems = document.querySelectorAll(
    '.gallery__navigation .page-item')

  /**
   * @todo: comment
   * @param {HTMLElement} $img
   */
  const updateFull = ($img) => {
    const { src, alt } = $img

    $fullImg.setAttribute('src', src)
    $fullImg.setAttribute('alt', alt)

    $fullCaption.textContent = alt
  }

  /**
   * @todo: comment
   * @param {string} currentSelector
   * @param {EventTarget} currentTarget
   * @param {string} currentClass
   */
  const updateCurrent = (
    currentSelector, currentTarget, currentClass = 'current') => {
    const $currents = document.querySelectorAll(currentSelector)
    $currents.forEach(($current) => {
      $current.classList.remove(currentClass)
    })

    currentTarget.classList.add(currentClass)
  }

  /**
   * EventListener auf Thumbs erstellen
   * @todo: comment
   */
  $thumbs.forEach(($thumb) => {
    $thumb.addEventListener('click', (event) => {
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
   * @todo: comment
   */
  $paginationButtons.forEach(($paginationButton) => {
    $paginationButton.addEventListener('click', (event) => {
      const $parent = event.currentTarget.parentNode
      const index = [...$paginationItems].indexOf($parent)
      const lastIndex = $paginationItems.length - 1
      const $currentActive = document.querySelector(
        '.gallery__navigation .active')
      const currentActiveIndex = [...$paginationItems].indexOf($currentActive)

      /**
       * [x] Wenn index 0: ein Bild nach links
       * [x] wenn index = lastIndex: ein Bild nach rechts
       * [x] sonst: zugehöriges Bild
       */
      let newActiveIndex
      let $newActivePaginationItem
      if (index === 0) {

        newActiveIndex = Math.max(0, currentActiveIndex - 2)
        $newActivePaginationItem = $paginationItems[newActiveIndex + 1]

      } else if (index === lastIndex) {

        newActiveIndex = Math.min(lastIndex - 2, currentActiveIndex)
        $newActivePaginationItem = $paginationItems[newActiveIndex + 1]

      } else {

        newActiveIndex = index - 1
        $newActivePaginationItem = $parent

      }

      const $figure = document.querySelectorAll(
        '.gallery__thumbs .figure')[newActiveIndex]
      updateFull($figure.children[0])
      updateCurrent('.gallery .current', $figure)
      updateCurrent('.gallery__navigation .active',
        $newActivePaginationItem, 'active')

    })
  })

})()
