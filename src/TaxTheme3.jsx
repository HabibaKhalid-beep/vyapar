import './TaxTheme3.css'

const TaxTheme3 = ({ businessInfo, onCompanyClick, onLogoClick, logo, signature, onSignatureClick, selectedColor, terms, onTermsClick }) => {
  return (
    <div className="tax3-wrapper">

      {/* TITLE */}
      <div className="tax3-title-row">
        <h2 className="tax3-title">Invoice</h2>
      </div>

      {/* ROW 1: Company | Invoice No | Date */}
      <div className="tax3-header-row">

        <div
          className="tax3-cell tax3-company-cell"
          onClick={onCompanyClick}
          style={{ cursor: 'pointer' }}
        >
          <div
            className="tax3-logo"
            onClick={(e) => { e.stopPropagation(); onLogoClick(); }}
            style={{ cursor: 'pointer' }}
          >
            {logo
              ? <img src={logo} alt="logo" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
              : 'LOGO'
            }
          </div>
          <div className="tax3-company-info">
            <h1>{businessInfo.name}</h1>
            <p>Phone no.: {businessInfo.phone}</p>
          </div>
        </div>

        <div className="tax3-cell tax3-meta-cell">
          <p className="tax3-meta-label">Invoice No.</p>
          <p className="tax3-meta-value">3</p>
        </div>

        <div className="tax3-cell tax3-meta-cell tax3-no-right-border">
          <p className="tax3-meta-label">Date</p>
          <p className="tax3-meta-value">09-04-2026</p>
        </div>

      </div>

      {/* ROW 2: Bill To full width */}
      <div className="tax3-billto-row">
        <p className="tax3-billto-label">Bill To</p>
        <p className="tax3-billto-name">fida</p>
      </div>

      {/* ROW 3: Table */}
      <table className="tax3-table">
        <thead>
          <tr>
            <th className="tax3-th-left">#</th>
            <th className="tax3-th-left">Item name</th>
            <th className="tax3-th-right">Quantity</th>
            <th className="tax3-th-right">Price/ Unit</th>
            <th className="tax3-th-right">Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td className="tax3-td-left">1</td>
            <td className="tax3-td-left"><strong>Sample Item</strong></td>
            <td className="tax3-td-right">1</td>
            <td className="tax3-td-right">Rs 100.00</td>
            <td className="tax3-td-right">Rs 100.00</td>
          </tr>
          <tr className="tax3-total-row">
            <td className="tax3-td-left"></td>
            <td className="tax3-td-left"><strong>Total</strong></td>
            <td className="tax3-td-right"><strong>1</strong></td>
            <td className="tax3-td-right"></td>
            <td className="tax3-td-right"><strong>Rs 100.00</strong></td>
          </tr>
        </tbody>
      </table>

      {/* ROW 4: Invoice Amount in Words | Amounts */}
      <div className="tax3-mid-row">

        <div className="tax3-cell tax3-words-cell">
          <p className="tax3-section-label">Invoice Amount in Words</p>
          <p className="tax3-words-value">One Hundred Rupees only</p>
        </div>

        <div className="tax3-cell tax3-amounts-cell tax3-no-right-border">
          <p className="tax3-section-label">Amounts</p>
          <div className="tax3-summary-row">
            <span>Sub Total</span>
            <span>Rs 100.00</span>
          </div>
          <div className="tax3-summary-row tax3-bold-row">
            <span>Total</span>
            <span>Rs 100.00</span>
          </div>
          <div className="tax3-summary-row">
            <span>Received</span>
            <span>Rs 0.00</span>
          </div>
          <div className="tax3-summary-row tax3-last-row">
            <span>Balance</span>
            <span>Rs 100.00</span>
          </div>
        </div>

      </div>

      {/* ROW 5: Terms | Signature */}
      <div className="tax3-footer-row">

        <div
          className="tax3-cell tax3-terms-cell"
          onClick={onTermsClick}
          style={{ cursor: 'pointer', position: 'relative' }}
        >
          <p className="tax3-terms-label">Terms and conditions</p>
          <p className="tax3-terms-text">{terms}</p>
          <span className="tax3-edit-icon">✎</span>
        </div>

        <div
          className="tax3-cell tax3-sign-cell tax3-no-right-border"
          onClick={onSignatureClick}
          style={{ cursor: 'pointer' }}
        >
          <p className="tax3-for-text">For : {businessInfo.name}</p>
          <div className="tax3-sign-box">
            {signature
              ? <img src={signature} alt="signature" style={{ height: '50px' }} />
              : <p className="tax3-signatory">Authorized Signatory</p>
            }
          </div>
        </div>

      </div>

    </div>
  )
}

export default TaxTheme3