import './SharePanel.css'
import { exportInvoicePdf } from './pdfExport'

const SharePanel = ({ invoiceData, saleId, selectedTheme, selectedColor }) => {
  const invoiceNumber = invoiceData?.invoiceNo || saleId || 'Invoice'
  const total = Number(invoiceData?.total || 0).toFixed(2)
  const received = Number(invoiceData?.received || 0).toFixed(2)
  const balance = Number(invoiceData?.balance || 0).toFixed(2)
  const previewUrl = window.location.href
  const shareText = `Invoice #${invoiceNumber}
Party: ${invoiceData?.billTo || '-'}
Total: Rs ${total}
Received: Rs ${received}
Balance: Rs ${balance}
Preview: ${previewUrl}`

  const handleWhatsapp = () => {
    window.open(`https://wa.me/?text=${encodeURIComponent(shareText)}`, '_blank')
  }

  const handleGmail = () => {
    window.open(`https://mail.google.com/mail/?view=cm&fs=1&su=${encodeURIComponent(`Invoice #${invoiceNumber}`)}&body=${encodeURIComponent(shareText)}`, '_blank')
  }

  const resolvePdfConfig = () => {
    const theme = selectedTheme || 'tally'
    const color = selectedColor || '#1f4e79'
    const regularMap = {
      tally: 1,
      LandScapeTheme1: 2,
      LandScapeTheme2: 3,
      tax1: 4,
      tax2: 5,
      tax3: 6,
      tax4: 7,
      tax5: 8,
      tax6: 9,
      divine: 10,
      french: 11,
      theme1: 12,
      theme2: 13,
      theme3: 14,
      theme4: 15,
    }
    const thermalMap = {
      thermal1: 1,
      thermal2: 2,
      thermal3: 3,
      thermal4: 4,
      thermal5: 5,
    }

    if (theme.startsWith('thermal')) {
      return { mode: 'thermal', themeId: thermalMap[theme] || 1, accent: color }
    }

    return { mode: 'regular', themeId: regularMap[theme] || 1, accent: color }
  }

  const openPrintWindow = () => {
    const printable = document.querySelector('.right-panel')
    if (!printable) {
      window.print()
      return
    }

    const styles = Array.from(document.querySelectorAll('link[rel="stylesheet"]'))
      .map((link) => link.href)
      .filter(Boolean)

    const win = window.open('', '_blank')
    if (!win) {
      window.print()
      return
    }

    const styleLinks = styles.map((href) => `<link rel="stylesheet" href="${href}">`).join('')
    win.document.open()
    win.document.write(`<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    ${styleLinks}
    <style>
      body { margin: 0; padding: 0; background: #fff; }
      .right-panel { width: 100% !important; height: auto !important; overflow: visible !important; padding: 0 !important; background: #fff !important; }
    </style>
  </head>
  <body>
    <div class="right-panel">${printable.innerHTML}</div>
  </body>
</html>`)
    win.document.close()
    win.onload = () => {
      win.focus()
      win.print()
    }
  }

  const handleDownloadPDF = async () => {
    const printable = document.querySelector('.right-panel')
    if (!printable) {
      return
    }

    if (window.html2pdf) {
      const isThermal = (selectedTheme || '').startsWith('thermal')
      const filename = `invoice-${invoiceNumber}.pdf`
      await exportInvoicePdf({ element: printable, filename, isThermal })
      return
    }

    if (!saleId) {
      openPrintWindow()
      return
    }

    const { mode, themeId, accent } = resolvePdfConfig()
    const url = `/dashboard/sales/${saleId}/invoice-pdf?download=1&mode=${encodeURIComponent(mode)}&theme_id=${encodeURIComponent(themeId)}&accent=${encodeURIComponent(accent)}`
    const win = window.open(url, '_blank')
    if (!win) {
      window.location.href = url
    }
  }

  const handlePrintNormal = () => {
    openPrintWindow()
  }

  const handlePrintThermal = () => {
    openPrintWindow()
  }

  return (
    <div className="share-panel">
      <div className="share-section">
        <p className="share-heading">Share Invoice</p>
        <div className="share-icons">
          <div className="share-item" onClick={handleWhatsapp}>
            <div className="share-icon-box whatsapp-box">
              <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="whatsapp" width="36" height="36" />
            </div>
            <p>Whatsapp</p>
          </div>
          <div className="share-item" onClick={handleGmail}>
            <div className="share-icon-box gmail-box">
              <img src="https://upload.wikimedia.org/wikipedia/commons/7/7e/Gmail_icon_%282020%29.svg" alt="gmail" width="36" height="36" />
            </div>
            <p>Gmail</p>
          </div>
        </div>
      </div>

      <div className="share-divider"></div>

      <div className="share-section">
        <div className="action-icons">
          <div className="share-item" onClick={handleDownloadPDF}>
            <div className="share-icon-box action-box">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" strokeWidth="2">
                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
              </svg>
            </div>
            <p>Download<br/>PDF</p>
          </div>

          <div className="share-item" onClick={handlePrintThermal}>
            <div className="share-icon-box action-box">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" strokeWidth="2">
                <polyline points="6 9 6 2 18 2 18 9"/>
                <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
              </svg>
            </div>
            <p>Print Invoice<br/><span>(Thermal)</span></p>
          </div>

          <div className="share-item" onClick={handlePrintNormal}>
            <div className="share-icon-box action-box-filled">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2">
                <polyline points="6 9 6 2 18 2 18 9"/>
                <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
              </svg>
            </div>
            <p>Print Invoice<br/><span>(Normal)</span></p>
          </div>
        </div>
      </div>
    </div>
  )
}

export default SharePanel
