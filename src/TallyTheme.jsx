import './TallyTheme.css'

const TallyTheme = ({ businessInfo, invoiceData, onCompanyClick, signature, onSignatureClick, selectedColor, terms, onTermsClick, logo, onLogoClick }) => {
  return (
    <div className="tally-wrapper">

      <h2 className="tally-title">{invoiceData?.title || 'Invoice'}</h2>

      <div className="tally-header" onClick={onCompanyClick} style={{ cursor: 'pointer' }}>
        <div
          className="tally-logo"
          onClick={(e) => { e.stopPropagation(); onLogoClick(); }}
          style={{ cursor: 'pointer' }}
        >
          {logo
            ? <img src={logo} alt="logo" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
            : 'LOGO'
          }
        </div>
        <div className="tally-company">
          <h1>{businessInfo.name}</h1>
          <p>Phone: {businessInfo.phone}</p>
        </div>
      </div>

      <div className="tally-info">
        <div className="tally-bill">
          <p className="tally-label">Bill To:</p>
          <p className="tally-value">{invoiceData?.billTo || 'fida'}</p>
        </div>
        <div className="tally-invoice-details">
          <p className="tally-label">Invoice Details:</p>
          <p>No: <strong>{invoiceData?.invoiceNo || '3'}</strong></p>
          <p>Date: <strong>{invoiceData?.date || '09/04/2026'}</strong></p>
          {invoiceData?.referenceNo ? <p>Ref: <strong>{invoiceData.referenceNo}</strong></p> : null}
        </div>
      </div>

      <table className="tally-table">
        <thead>
          <tr style={{ backgroundColor: selectedColor }}>
            <th>#</th>
            <th>Item name</th>
            <th>Quantity</th>
            <th>Price/ Unit(Rs)</th>
            <th>Amount(Rs)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td><strong>{invoiceData?.itemName || 'Sample Item'}</strong></td>
            <td>{invoiceData?.quantity || 1}</td>
            <td>Rs {Number(invoiceData?.unitPrice || 100).toFixed(2)}</td>
            <td>Rs {Number(invoiceData?.amount || 100).toFixed(2)}</td>
          </tr>
          <tr className="tally-total-row">
            <td></td>
            <td><strong>Total</strong></td>
            <td><strong>{invoiceData?.quantity || 1}</strong></td>
            <td></td>
            <td><strong>Rs {Number(invoiceData?.amount || 100).toFixed(2)}</strong></td>
          </tr>
        </tbody>
      </table>

      <div className="tally-summary">
        <div className="tally-summary-row">
          <span>Sub Total</span>
          <span>:</span>
          <span>Rs {Number(invoiceData?.amount || 100).toFixed(2)}</span>
        </div>
        <div className="tally-summary-row bold">
          <span>Total</span>
          <span>:</span>
          <span>Rs {Number(invoiceData?.amount || 100).toFixed(2)}</span>
        </div>
      </div>

      <div className="tally-words">
        <p className="tally-label">Invoice Amount in Words:</p>
        <p>{terms}</p>
      </div>

      <div className="tally-summary">
        <div className="tally-summary-row">
          <span>Received</span>
          <span>:</span>
          <span>Rs {Number(invoiceData?.received || 0).toFixed(2)}</span>
        </div>
        <div className="tally-summary-row">
          <span>Balance</span>
          <span>:</span>
          <span>Rs {Number(invoiceData?.balance || 0).toFixed(2)}</span>
        </div>
      </div>

      <div
        className="tally-terms"
        onClick={onTermsClick}
        style={{ cursor: 'pointer', position: 'relative' }}
      >
        <p className="tally-label">Terms & Conditions:</p>
        <p>{terms}</p>
        <span className="edit-icon">✎</span>
      </div>

      <div className="tally-sign">
        <p className="tally-label">For {businessInfo.name}:</p>
        <div
          className="tally-sign-box"
          onClick={onSignatureClick}
          style={{ cursor: 'pointer' }}
        >
          {signature
            ? <img src={signature} alt="signature" style={{ height: '50px' }} />
            : <p>Authorized Signatory</p>
          }
        </div>
      </div>

    </div>
  )
}

export default TallyTheme
