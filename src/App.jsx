import { useEffect, useState } from 'react'
import TermsModal from './TermsModal'
import LeftPanel from './LeftPanel'
import RightPanel from './RightPanel'
import SharePanel from './SharePanel'
import BusinessModal from './BusinessModal'
import SignatureModal from './SignatureModal'
import LogoModal from './LogoModal'
import './App.css'

const appData = window.invoiceAppData || {}
const initialInvoiceData = appData.invoiceData || null

const App = () => {
  const [showTermsModal, setShowTermsModal] = useState(false)
  const [terms, setTerms] = useState(initialInvoiceData?.description || 'Thanks for doing business with us!')
  const [selectedTheme, setSelectedTheme] = useState(appData.initialTheme || 'tally')
  const [selectedColor, setSelectedColor] = useState(appData.initialColor || '#707070')
  const [showBusinessModal, setShowBusinessModal] = useState(false)
  const [showSignatureModal, setShowSignatureModal] = useState(false)
  const [showLogoModal, setShowLogoModal] = useState(false)
  const [logo, setLogo] = useState(null)

  const [businessInfo, setBusinessInfo] = useState({
    name: initialInvoiceData?.businessName || 'My Company',
    phone: initialInvoiceData?.businessPhone || initialInvoiceData?.billPhone || '',
    email: '',
    address: initialInvoiceData?.billAddress || ''
  })

  const [signature, setSignature] = useState(null)

  const invoiceData = {
    ...initialInvoiceData,
    businessName: businessInfo.name,
    businessPhone: businessInfo.phone,
    description: terms,
  }

  useEffect(() => {
    if (!appData.saleId) return

    const theme = selectedTheme || 'tally'
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

    const payload = {
      mode: theme.startsWith('thermal') ? 'thermal' : 'regular',
      regularThemeId: theme.startsWith('thermal') ? null : (regularMap[theme] || 1),
      thermalThemeId: theme.startsWith('thermal') ? (thermalMap[theme] || 1) : null,
      accent: selectedColor || '#1f4e79',
      accent2: null,
    }

    try {
      window.localStorage.setItem(`saleInvoiceTheme:${appData.saleId}`, JSON.stringify(payload))
    } catch (error) {
      // ignore storage errors
    }
  }, [selectedTheme, selectedColor])

  useEffect(() => {
    const params = new URLSearchParams(window.location.search)
    const shouldDownload = params.get('download') === '1'
    const shouldPrint = params.get('print') === '1'

    if (!shouldDownload && !shouldPrint) return

    const run = async () => {
      await new Promise(resolve => setTimeout(resolve, 600))
      if (shouldDownload && window.html2pdf) {
        const printable = document.querySelector('.right-panel')
        if (!printable) return
        const isThermal = (selectedTheme || '').startsWith('thermal')
        await window.html2pdf().set({
          margin: isThermal ? [2, 2, 2, 2] : [6, 6, 6, 6],
          filename: `invoice-${invoiceData.invoiceNo || appData.saleId || 'invoice'}.pdf`,
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: { scale: 2, useCORS: true, backgroundColor: '#ffffff' },
          jsPDF: { unit: 'mm', format: isThermal ? [80, 297] : 'a4', orientation: 'portrait' },
        }).from(printable).save()
        return
      }
      if (shouldPrint) {
        window.print()
      }
    }

    run()
  }, [selectedTheme, invoiceData])

  return (
    <>
      <div className="preview-topbar">
        <div className="preview-tab">
          <span className="preview-tab-label">{appData.browserTabLabel || 'Invoice Preview'}</span>
          <div className="preview-tab-actions">
            <button type="button" className="preview-tab-btn" aria-label="Close">x</button>
            <button type="button" className="preview-tab-btn" onClick={() => window.open(window.location.href, '_blank')} aria-label="New Tab">+</button>
          </div>
        </div>
        <div className="preview-top-icons">
          <span className="preview-top-icon">o</span>
          <span className="preview-top-icon">[]</span>
          <span className="preview-top-icon">*</span>
          <span className="preview-top-icon">x</span>
        </div>
      </div>
      <div className="preview-head">
        <h1>Preview</h1>
        <div className="preview-head-right">
          <label className="preview-checkbox">
            <input type="checkbox" />
            <span>Do not show invoice preview again</span>
          </label>
          <span className="preview-divider">|</span>
          <a className="preview-save" href={appData.saveCloseUrl || '/dashboard/sales'}>Save &amp; Close</a>
        </div>
      </div>
      <div className="app-container">
        <LeftPanel
          selectedTheme={selectedTheme}
          setSelectedTheme={setSelectedTheme}
          selectedColor={selectedColor}
          setSelectedColor={setSelectedColor}
        />
        <RightPanel
          selectedTheme={selectedTheme}
          selectedColor={selectedColor}
          businessInfo={businessInfo}
          signature={signature}
          terms={terms}
          logo={logo}
          invoiceData={invoiceData}
          onCompanyClick={() => setShowBusinessModal(true)}
          onSignatureClick={() => setShowSignatureModal(true)}
          onTermsClick={() => setShowTermsModal(true)}
          onLogoClick={() => setShowLogoModal(true)}
        />
        <SharePanel
          invoiceData={invoiceData}
          saleId={appData.saleId}
          selectedTheme={selectedTheme}
          selectedColor={selectedColor}
        />
      </div>

      {showBusinessModal && (
        <BusinessModal
          businessInfo={businessInfo}
          setBusinessInfo={setBusinessInfo}
          onClose={() => setShowBusinessModal(false)}
        />
      )}

      {showSignatureModal && (
        <SignatureModal
          setSignature={setSignature}
          onClose={() => setShowSignatureModal(false)}
        />
      )}

      {showTermsModal && (
        <TermsModal
          terms={terms}
          setTerms={setTerms}
          onClose={() => setShowTermsModal(false)}
        />
      )}

      {showLogoModal && (
        <LogoModal
          setLogo={setLogo}
          onClose={() => setShowLogoModal(false)}
        />
      )}
    </>
  )
}

export default App
