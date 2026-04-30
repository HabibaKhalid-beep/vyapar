export const exportInvoicePdf = async ({ element, filename, isThermal = false }) => {
  if (!element || !window.html2pdf) {
    return false
  }

  // Store original styles before any modifications
  const originalStyles = new Map()
  const storeOriginalStyles = (el) => {
    originalStyles.set(el, {
      style: el.getAttribute('style') || '',
      width: el.style.width,
      maxWidth: el.style.maxWidth,
      margin: el.style.margin,
      marginLeft: el.style.marginLeft,
      marginRight: el.style.marginRight,
    })
    Array.from(el.children).forEach(storeOriginalStyles)
  }
  storeOriginalStyles(element)

  // Recursively remove all max-width and margin constraints
  const removeConstraints = (el) => {
    // Remove max-width completely
    el.style.removeProperty('max-width')
    // Set width to 100%
    el.style.width = '100%'
    // Remove auto margins
    el.style.margin = '0'
    el.style.marginLeft = '0'
    el.style.marginRight = '0'
    // Ensure box-sizing
    el.style.boxSizing = 'border-box'

    Array.from(el.children).forEach(removeConstraints)
  }
  removeConstraints(element)

  // Create wrapper with exact PDF dimensions
  const pdfWidthMm = isThermal ? 80 : 210
  const pxWidth = pdfWidthMm * 3.779528
  const pxHeight = 297 * 3.779528

  const wrapper = document.createElement('div')
  wrapper.style.cssText = `
    width: ${pxWidth}px !important;
    height: auto !important;
    margin: 0 !important;
    padding: 0 !important;
    background-color: #ffffff !important;
    display: block !important;
    overflow: visible !important;
    box-sizing: border-box !important;
    position: relative !important;
    left: 0 !important;
    top: 0 !important;
  `

  // Store parent and position info
  const parent = element.parentNode
  const nextSibling = element.nextSibling

  // Move element to wrapper
  wrapper.appendChild(element)
  document.body.appendChild(wrapper)

  // Set CSS on element itself using cssText
  element.style.cssText = `
    width: 100% !important;
    max-width: none !important;
    margin: 0 !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    padding: 0 !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    height: auto !important;
    max-height: none !important;
    overflow: visible !important;
    display: block !important;
    box-sizing: border-box !important;
    flex: none !important;
    flex-basis: auto !important;
  `

  try {
    await new Promise((resolve) => setTimeout(resolve, 500))

    await window.html2pdf().set({
      margin: isThermal ? [2, 2, 2, 2] : [6, 6, 6, 6],
      filename,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: {
        scale: 2,
        useCORS: true,
        backgroundColor: '#ffffff',
        scrollX: 0,
        scrollY: 0,
        windowWidth: pxWidth,
        windowHeight: pxHeight,
        allowTaint: true,
        logging: false,
      },
      jsPDF: {
        unit: 'mm',
        format: isThermal ? [pdfWidthMm, 297] : 'a4',
        orientation: 'portrait',
        compress: true,
      },
      pagebreak: { mode: ['css', 'legacy'] },
    }).from(wrapper).save()

    return true
  } finally {
    // Restore element to original parent
    if (parent) {
      if (nextSibling) {
        parent.insertBefore(element, nextSibling)
      } else {
        parent.appendChild(element)
      }
    }

    // Remove wrapper
    wrapper.remove()

    // Restore original styles
    const restoreOriginalStyles = (el) => {
      const original = originalStyles.get(el)
      if (original) {
        if (original.style) {
          el.setAttribute('style', original.style)
        } else {
          el.removeAttribute('style')
        }
      }
      Array.from(el.children).forEach(restoreOriginalStyles)
    }
    restoreOriginalStyles(element)
  }
}
