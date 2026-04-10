import './SharePanel.css'

const SharePanel = () => {

  const handleWhatsapp = () => {
    window.open('https://wa.me/?text=Please find the invoice attached', '_blank')
  }

  const handleGmail = () => {
    window.open('https://mail.google.com/mail/?view=cm&fs=1&su=Invoice&body=Please find the invoice attached', '_blank')
  }

  const handleDownloadPDF = () => {
    window.print()
  }

  const handlePrintNormal = () => {
    window.print()
  }

  const handlePrintThermal = () => {
    window.print()
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