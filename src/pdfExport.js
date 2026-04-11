export const exportInvoicePdf = async ({ element, filename, isThermal = false }) => {
  if (!element || !window.html2pdf) {
    return false
  }

  const root = document.documentElement
  root.classList.add('pdf-export-active')

  const previousInlineStyle = element.getAttribute('style')
  element.style.height = 'auto'
  element.style.maxHeight = 'none'
  element.style.overflow = 'visible'

  try {
    await new Promise((resolve) => setTimeout(resolve, 150))
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
        windowWidth: element.scrollWidth,
        windowHeight: element.scrollHeight,
      },
      jsPDF: { unit: 'mm', format: isThermal ? [80, 297] : 'a4', orientation: 'portrait' },
      pagebreak: { mode: ['css', 'legacy'] },
    }).from(element).save()

    return true
  } finally {
    root.classList.remove('pdf-export-active')

    if (previousInlineStyle === null) {
      element.removeAttribute('style')
    } else {
      element.setAttribute('style', previousInlineStyle)
    }
  }
}
