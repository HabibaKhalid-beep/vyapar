import './FrenchElite.css'

const FrenchElite = ({ selectedColor, businessInfo, signature, onCompanyClick, onSignatureClick, terms, onTermsClick, logo, onLogoClick }) => {
  return (
    <div className="fe-wrapper">

      <div className="fe-top-row">
        <div className="fe-invoice-banner" style={{ backgroundColor: selectedColor }}>
          <h1>INVOICE</h1>
        </div>
        <div
          className="fe-logo"
          onClick={onLogoClick}
          style={{ cursor: 'pointer', backgroundColor: selectedColor }}
        >
          {logo
            ? <img src={logo} alt="logo" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
            : 'LOGO'
          }
        </div>
      </div>

      <div className="fe-company" onClick={onCompanyClick} style={{ cursor: 'pointer' }}>
        <h2>{businessInfo.name}</h2>
        <p className="fe-phone-label">Phone:</p>
        <p className="fe-phone-value">{businessInfo.phone}</p>
      </div>

      <hr className="fe-divider" />

      <div className="fe-meta">
        <div className="fe-meta-left">
          <p>Invoice No.:{3}</p>
          <div className="fe-date-row">
            <span>Date:</span>
            <span>09/04/2026</span>
          </div>
        </div>
        <div className="fe-meta-right">
          <p className="fe-bill-label">Bill To:</p>
          <p className="fe-bill-value">fida</p>
        </div>
      </div>

      <table className="fe-table">
        <thead>
          <tr style={{ backgroundColor: selectedColor }}>
            <th className="fe-th-left">#</th>
            <th className="fe-th-left">Item name</th>
            <th className="fe-th-right">Quantity</th>
            <th className="fe-th-right">Price/ Unit</th>
            <th className="fe-th-right">Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td className="fe-td-left">1</td>
            <td className="fe-td-left"><strong>Sample Item</strong></td>
            <td className="fe-td-right">1</td>
            <td className="fe-td-right">Rs 100.00</td>
            <td className="fe-td-right">Rs 100.00</td>
          </tr>
          <tr style={{ backgroundColor: selectedColor }} className="fe-total-row">
            <td className="fe-td-left"></td>
            <td className="fe-td-left"><strong>Total</strong></td>
            <td className="fe-td-right"><strong>1</strong></td>
            <td className="fe-td-right"></td>
            <td className="fe-td-right"><strong>Rs 100.00</strong></td>
          </tr>
        </tbody>
      </table>

      <div className="fe-bottom">
        <div className="fe-bottom-left">
          <p className="fe-section-label">Invoice Amount In Words</p>
          <p className="fe-words">One Hundred Rupees only</p>
          <p className="fe-section-label fe-terms-label" onClick={onTermsClick} style={{ cursor: 'pointer' }}>Terms And Conditions</p>
          <p className="fe-terms-text" onClick={onTermsClick} style={{ cursor: 'pointer' }}>{terms}</p>
        </div>
        <div className="fe-bottom-right">
          <div className="fe-summary-row">
            <span>Sub Total</span>
            <span>Rs 100.00</span>
          </div>
          <div className="fe-summary-row fe-summary-total" style={{ backgroundColor: selectedColor }}>
            <span>Total</span>
            <span>Rs 100.00</span>
          </div>
          <div className="fe-summary-row">
            <span>Received</span>
            <span>Rs 0.00</span>
          </div>
          <div className="fe-summary-row">
            <span>Balance</span>
            <span>Rs 100.00</span>
          </div>
        </div>
      </div>

      <div className="fe-footer" onClick={onSignatureClick} style={{ cursor: 'pointer' }}>
        <p className="fe-for-text">For : {businessInfo.name}</p>
        <div className="fe-sign">
          {signature
            ? <img src={signature} alt="signature" style={{ height: '50px' }} />
            : <p className="fe-signatory">Authorized Signatory</p>
          }
        </div>
      </div>

    </div>
  )
}

export default FrenchElite