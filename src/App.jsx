import { useState } from 'react'
import TermsModal from './TermsModal'
import LeftPanel from './LeftPanel'
import RightPanel from './RightPanel'
import BusinessModal from './BusinessModal'
import SignatureModal from './SignatureModal'
import LogoModal from './LogoModal'
import './App.css'

const App = () => {
  const [showTermsModal, setShowTermsModal] = useState(false)
  const [terms, setTerms] = useState('Thanks for doing business with us!')
  const [selectedTheme, setSelectedTheme] = useState('tally')
  const [selectedColor, setSelectedColor] = useState('#707070')
  const [showBusinessModal, setShowBusinessModal] = useState(false)
  const [showSignatureModal, setShowSignatureModal] = useState(false)
  const [showLogoModal, setShowLogoModal] = useState(false)
  const [logo, setLogo] = useState(null)

  const [businessInfo, setBusinessInfo] = useState({
    name: 'My Company',
    phone: '3177645375',
    email: '',
    address: ''
  })

  const [signature, setSignature] = useState(null)

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
        onCompanyClick={() => setShowBusinessModal(true)}
        onSignatureClick={() => setShowSignatureModal(true)}
        onTermsClick={() => setShowTermsModal(true)}
        onLogoClick={() => setShowLogoModal(true)}
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