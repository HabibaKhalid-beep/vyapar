import './RightPanel.css'
import TallyTheme from './TallyTheme'
import TaxTheme1 from './TaxTheme1'
import TaxTheme2 from './TaxTheme2'
import TaxTheme3 from './TaxTheme3'
import TaxTheme4 from './TaxTheme4'
import LandScapeTheme1 from './LandScapeTheme1'
import LandScapeTheme2 from './LandScapeTheme2'
import Theme1 from './Theme1'
import Theme2 from './Theme2'
import Theme3 from './Theme3'
import Theme4 from './Theme4'
import ThermalTheme1 from './ThermalTheme1'
import ThermalTheme2 from './ThermalTheme2'
import ThermalTheme3 from './ThermalTheme3'
import ThermalTheme4 from './ThermalTheme4'
import ThermalTheme5 from './ThermalTheme5'
import FrenchElite from './FrenchElite'
import DoubleDivine from './DoubleDivine'

const RightPanel = ({ selectedTheme, selectedColor, businessInfo, invoiceData, signature, onCompanyClick, onSignatureClick, terms, onTermsClick, logo, onLogoClick }) => {

  const classicProps = { businessInfo, invoiceData, onCompanyClick, signature, onSignatureClick, selectedColor, terms, onTermsClick, logo, onLogoClick }
  const vintageProps = { businessInfo, invoiceData, onCompanyClick, signature, onSignatureClick, selectedColor, terms, onTermsClick, logo, onLogoClick }

  const renderTheme = () => {
    if (selectedTheme === 'tally') return <TallyTheme {...classicProps} />
    if (selectedTheme === 'tax1') return <TaxTheme1 {...classicProps} />
    if (selectedTheme === 'tax3') return <TaxTheme3 {...classicProps} />
    if (selectedTheme === 'LandScapeTheme1') return <LandScapeTheme1 {...classicProps} />
    if (selectedTheme === 'LandScapeTheme2') return <LandScapeTheme2 {...classicProps} />
    if (selectedTheme === 'divine') return <DoubleDivine {...classicProps} />
    if (selectedTheme === 'french') return <FrenchElite {...classicProps} />
    if (selectedTheme === 'tax2') return <TaxTheme2 {...vintageProps} />
    if (selectedTheme === 'tax4') return <TaxTheme4 {...vintageProps} />
    if (selectedTheme === 'tax5') return <TaxTheme2 {...vintageProps} />
    if (selectedTheme === 'tax6') return <TaxTheme2 {...vintageProps} />
    if (selectedTheme === 'theme1') return <Theme1 {...vintageProps} />
    if (selectedTheme === 'theme2') return <Theme2 {...vintageProps} />
    if (selectedTheme === 'theme3') return <Theme3 {...vintageProps} />
    if (selectedTheme === 'theme4') return <Theme4 {...vintageProps} />
    if (selectedTheme === 'thermal1') return <ThermalTheme1 {...vintageProps} />
    if (selectedTheme === 'thermal2') return <ThermalTheme2 {...vintageProps} />
    if (selectedTheme === 'thermal3') return <ThermalTheme3 {...vintageProps} />
    if (selectedTheme === 'thermal4') return <ThermalTheme4 {...vintageProps} />
    if (selectedTheme === 'thermal5') return <ThermalTheme5 {...vintageProps} />
    return <TallyTheme {...classicProps} />
  }

  return (
    <div className="right-panel">
      {window.paymentInInvoice && (
        <div style={{
          margin: '12px 16px 0',
          padding: '14px 16px',
          borderRadius: '14px',
          background: 'linear-gradient(135deg, #0f4c81 0%, #0f766e 100%)',
          color: '#fff',
          boxShadow: '0 12px 28px rgba(15, 76, 129, 0.18)'
        }}>
          <div style={{ fontSize: '12px', opacity: 0.85, textTransform: 'uppercase', letterSpacing: '0.08em' }}>Payment In Loaded</div>
          <div style={{ display: 'flex', justifyContent: 'space-between', gap: '12px', flexWrap: 'wrap', marginTop: '8px' }}>
            <div><strong>Party:</strong> {invoiceData.billTo}</div>
            <div><strong>Receipt:</strong> {invoiceData.receiptNo || invoiceData.invoiceNo}</div>
            <div><strong>Amount:</strong> Rs {Number(invoiceData.amount || 0).toFixed(2)}</div>
          </div>
        </div>
      )}
      {renderTheme()}
    </div>
  )
}

export default RightPanel
