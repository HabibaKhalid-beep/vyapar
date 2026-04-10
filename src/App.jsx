import { useState } from 'react'
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

  return (
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
      />

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
    </div>
  )
}

export default App
