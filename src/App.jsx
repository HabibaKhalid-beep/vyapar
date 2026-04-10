import { useMemo, useState } from 'react'
import TermsModal from './TermsModal'
import LeftPanel from './LeftPanel'
import RightPanel from './RightPanel'
import SharePanel from './SharePanel'
import BusinessModal from './BusinessModal'
import SignatureModal from './SignatureModal'
import LogoModal from './LogoModal'
import './App.css'

<<<<<<< Updated upstream
const paymentInSource = window.paymentInInvoice || null

const formatDate = (value) => {
  if (!value) return ''
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return date.toLocaleDateString('en-GB')
}

const App = () => {
  const [showTermsModal, setShowTermsModal] = useState(false)
  const [terms, setTerms] = useState(paymentInSource?.description || 'Thanks for doing business with us!')
  const [selectedTheme, setSelectedTheme] = useState('tally')
  const [selectedColor, setSelectedColor] = useState('#707070')
=======
const appData = window.invoiceAppData || {}
const initialInvoiceData = appData.invoiceData || null

const App = () => {
  const [showTermsModal, setShowTermsModal] = useState(false)
  const [terms, setTerms] = useState(initialInvoiceData?.description || 'Thanks for doing business with us!')
  const [selectedTheme, setSelectedTheme] = useState(appData.initialTheme || 'tally')
  const [selectedColor, setSelectedColor] = useState(appData.initialColor || '#707070')
>>>>>>> Stashed changes
  const [showBusinessModal, setShowBusinessModal] = useState(false)
  const [showSignatureModal, setShowSignatureModal] = useState(false)
  const [showLogoModal, setShowLogoModal] = useState(false)
  const [logo, setLogo] = useState(null)

  const [businessInfo, setBusinessInfo] = useState({
<<<<<<< Updated upstream
    name: 'My Company',
    phone: paymentInSource?.party?.phone || '3177645375',
    email: '',
    address: paymentInSource?.party?.billing_address || ''
  })

  const [signature, setSignature] = useState(null)
  const invoiceData = useMemo(() => {
    if (!paymentInSource) {
      return {
        title: 'Invoice',
        billTo: 'fida',
        invoiceNo: '3',
        date: '09/04/2026',
        itemName: 'Sample Item',
        quantity: 1,
        unitPrice: 100,
        amount: 100,
        received: 0,
        balance: 100,
        referenceNo: '',
        receiptNo: '',
      }
    }

    const amount = Number(paymentInSource.amount || 0)
    return {
      title: 'Payment In Invoice',
      billTo: paymentInSource.party?.name || 'Customer',
      invoiceNo: paymentInSource.receipt_no || String(paymentInSource.id || '-'),
      date: formatDate(paymentInSource.date),
      itemName: `Payment In${paymentInSource.reference_no ? ` - ${paymentInSource.reference_no}` : ''}`,
      quantity: 1,
      unitPrice: amount,
      amount,
      received: amount,
      balance: 0,
      referenceNo: paymentInSource.reference_no || '',
      receiptNo: paymentInSource.receipt_no || '',
    }
  }, [])
=======
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
>>>>>>> Stashed changes

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
        invoiceData={invoiceData}
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
        selectedTheme={selectedTheme}
        selectedColor={selectedColor}
        saleId={appData.saleId}
        invoicePdfUrl={appData.invoicePdfUrl}
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
