import './LandScapeTheme1.css'

const LandScapeTheme1 = ({ businessInfo, onCompanyClick, onLogoClick, logo, signature, onSignatureClick, selectedColor, terms, onTermsClick }) => {
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
            <p>Phone: {businessInfo.phone}</p>
          </div>
        </div>

        <div className="tax3-cell tax3-meta-cell tax3-no-right-border">
          <p className="tax3-meta-label">Invoice No.: <strong>{3}</strong></p>
          <p className="tax3-meta-label">Date: <strong>09/04/2026</strong></p>
        </div>

      </div>

      {/* ROW 2: Bill To full width */}
      <div className="tax3-billto-row">
        <p className="tax3-billto-label">Bill To:</p>
        <p className="tax3-billto-name">fida</p>
      </div>

      {/* ROW 3: Table */}
      <table className="tax3-table">
        <thead>
          <tr>
            <th className="tax3-th-left tax3-col-num">#</th>
            <th className="tax3-th-left tax3-col-name">Item name</th>
            <th className="tax3-th-right tax3-col-qty">Quantity</th>
            <th className="tax3-th-right tax3-col-price">Price/ Unit(Rs)</th>
            <th className="tax3-th-right tax3-col-amount">Amount(Rs)</th>
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

      {/* ROW 4: Sub Total | Total in words */}
      <div className="tax3-summary-band">
        <div className="tax3-summary-band-cell tax3-band-left">
          <span>Sub Total: <strong>Rs 100.00</strong></span>
        </div>
        <div className="tax3-summary-band-cell tax3-band-right">
          <span>Total: <strong>Rs 100.00</strong>(<strong>One Hundred Rupees only</strong>)</span>
        </div>
      </div>

      {/* ROW 5: Received | Balance */}
      <div className="tax3-summary-band">
        <div className="tax3-summary-band-cell tax3-band-left">
          <span>Received: <strong>Rs 0.00</strong></span>
        </div>
        <div className="tax3-summary-band-cell tax3-band-right">
          <span>Balance: <strong>Rs 100.00</strong></span>
        </div>
      </div>

      {/* ROW 6: Terms | Signature */}
      <div className="tax3-footer-row">

        <div
          className="tax3-cell tax3-terms-cell"
          onClick={onTermsClick}
          style={{ cursor: 'pointer', position: 'relative' }}
        >
          <p className="tax3-terms-label">Terms &amp; Conditions:</p>
          <p className="tax3-terms-text">{terms}</p>
          <span className="tax3-edit-icon">✎</span>
        </div>

        <div
          className="tax3-cell tax3-sign-cell tax3-no-right-border"
          onClick={onSignatureClick}
          style={{ cursor: 'pointer' }}
        >
          <p className="tax3-for-text">For {businessInfo.name}:</p>
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

export default LandScapeTheme1