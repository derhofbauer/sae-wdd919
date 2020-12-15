(function () {

  console.log('Yaay! :D')

  const base = document.baseURI

  /**
   * @todo: comment
   */
  const updateCartCount = (numberOfProducts) => {
    const $elements = document.querySelectorAll('.number-of-products')

    $elements.forEach(($element) => {
      $element.textContent = `(${numberOfProducts})`
    })
  }

  /**
   * @todo: comment
   */
  const updateInputValue = (productId, numberOfProducts) => {
    const $inputs = document.querySelectorAll(`[name="cart-quantity[${productId}]"`)

    $inputs.forEach(($input) => {
      $input.value = numberOfProducts
    })
  }

  /**
   * @todo: comment
   */
  const formatPrice = (price) => {
    return Intl.NumberFormat('de-AT', { style: 'currency', currency: 'EUR' }).
      format(price)
  }

  /**
   * @todo: comment
   */
  const updateSubTotal = (productId, subtotal) => {
    const $subtotalDomNodes = document.querySelectorAll(`.product-${productId} .subtotal`)

    $subtotalDomNodes.forEach(($subtotalDomNode) => {
      $subtotalDomNode.textContent = formatPrice(subtotal)
    })
  }

  /**
   * @todo: comment
   */
  const updateTotal = (total) => {
    const $totalDomNodes = document.querySelectorAll(`.cart-total`)

    $totalDomNodes.forEach(($totalDomNode) => {
      $totalDomNode.textContent = formatPrice(total)
    })
  }

  /**
   * @todo: comment
   */
  const updateCartTable = (cartContent) => {
    const $renderedProducts = document.querySelectorAll(
      '.cart-table tbody tr')

    $renderedProducts.forEach(($renderedProduct) => {
      let shouldStay = false
      cartContent.forEach(({ id }) => {
        if ($renderedProduct.classList.contains(`product-${id}`)) {
          shouldStay = true
        }
      })

      if (shouldStay === false) {
        $renderedProduct.remove()
      }
    })
  }

  /**
   * @todo: comment
   */
  const addToast = (text, parentNode, className, tagName = 'p') => {
    const $success = document.createElement(tagName)
    let counter = 5
    $success.className = 'alert ' + className
    $success.textContent = text + ': ' + counter
    parentNode.appendChild($success)

    const interval = setInterval(() => {
      counter--
      $success.textContent = text + ': ' + (counter)
    }, 1000)

    setTimeout(() => {
      $success.remove()
      clearInterval(interval)
    }, 1000 * counter)
  }

  /**
   * Handle form on product-single
   *
   * @todo: comment
   */
  const $forms = document.querySelectorAll('form.add-to-cart')
  $forms.forEach(($form) => {
    $form.addEventListener('submit', (event) => {
      event.preventDefault()

      const formData = new FormData(event.target)
      const productId = formData.get('product_id')

      fetch(base + `api/cart/add/${productId}`, {
        method: event.target.method,
        body: formData,
      }).
        then(response => response.json()).
        then(({ numberOfProducts }) => {
          updateCartCount(numberOfProducts)

          addToast(
            'Das Produkt wurde zum Warenkorb hinzugefügt :D',
            $form.parentNode,
            'alert-success',
          )
        }).
        catch((error) => {
          addToast(
            'Oh nein, ein Fehler ist aufgetreten :(',
            $form.parentNode,
            'alert-danger',
          )
        })
    })
  })

  /**
   * Cart: add/remove one
   *
   * @todo: comment
   */
  const $ajaxCartButtons = document.querySelectorAll('.ajax-cart-button')
  $ajaxCartButtons.forEach(($ajaxCartButton) => {
    $ajaxCartButton.addEventListener('click', (event) => {
      event.preventDefault()

      const $route = event.target.href.replace('cart', 'api/cart')
      fetch($route).
        then(response => response.json()).
        then(({ cartContent, numberOfProducts, total }) => {
          updateCartTable(cartContent)
          updateCartCount(numberOfProducts)
          updateTotal(total)

          cartContent.forEach(({ id, quantity, subtotal }) => {
            updateInputValue(id, quantity)
            updateSubTotal(id, subtotal)
          })

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
