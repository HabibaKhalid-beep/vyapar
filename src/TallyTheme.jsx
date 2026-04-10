import './TallyTheme.css'

<<<<<<< Updated upstream
const TallyTheme = ({ businessInfo, invoiceData, onCompanyClick, signature, onSignatureClick, selectedColor, terms, onTermsClick, logo, onLogoClick }) => {
=======
const TallyTheme = ({ businessInfo, onCompanyClick, signature, onSignatureClick, selectedColor, terms, onTermsClick, logo, onLogoClick, invoiceData }) => {
  const items = invoiceData?.items?.length ? invoiceData.items : [
    { name: 'Sample Item', qty: 1, rate: 100, amount: 100 }
  ]
  const totalQty = items.reduce((sum, item) => sum + Number(item.qty || 0), 0)
  const subtotal = Number(invoiceData?.subtotal ?? items.reduce((sum, item) => sum + Number(item.amount || 0), 0))
  const total = Number(invoiceData?.total ?? subtotal)
  const received = Number(invoiceData?.received ?? 0)
  const balance = Number(invoiceData?.balance ?? Math.max(total - received, 0))
  const billTo = invoiceData?.billTo || 'Walk-in Customer'
  const invoiceNo = invoiceData?.invoiceNo || '3'
  const invoiceDate = invoiceData?.date || '09/04/2026'
  const formatCurrency = (value) => `Rs ${Number(value || 0).toFixed(2)}`

>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
          <p className="tally-value">{invoiceData?.billTo || 'fida'}</p>
        </div>
        <div className="tally-invoice-details">
          <p className="tally-label">Invoice Details:</p>
          <p>No: <strong>{invoiceData?.invoiceNo || '3'}</strong></p>
          <p>Date: <strong>{invoiceData?.date || '09/04/2026'}</strong></p>
          {invoiceData?.referenceNo ? <p>Ref: <strong>{invoiceData.referenceNo}</strong></p> : null}
=======
          <p className="tally-value">{billTo}</p>
        </div>
        <div className="tally-invoice-details">
          <p className="tally-label">Invoice Details:</p>
          <p>No: <strong>{invoiceNo}</strong></p>
          <p>Date: <strong>{invoiceDate}</strong></p>
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
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
=======
          {items.map((item, index) => (
            <tr key={`${item.name}-${index}`}>
              <td>{index + 1}</td>
              <td><strong>{item.name}</strong></td>
              <td>{item.qty}</td>
              <td>{formatCurrency(item.rate)}</td>
              <td>{formatCurrency(item.amount)}</td>
            </tr>
          ))}
          <tr className="tally-total-row">
            <td></td>
            <td><strong>Total</strong></td>
            <td><strong>{totalQty}</strong></td>
            <td></td>
            <td><strong>{formatCurrency(total)}</strong></td>
>>>>>>> Stashed changes
          </tr>
        </tbody>
      </table>

      <div className="tally-summary">
        <div className="tally-summary-row">
          <span>Sub Total</span>
          <span>:</span>
<<<<<<< Updated upstream
          <span>Rs {Number(invoiceData?.amount || 100).toFixed(2)}</span>
=======
          <span>{formatCurrency(subtotal)}</span>
>>>>>>> Stashed changes
        </div>
        <div className="tally-summary-row bold">
          <span>Total</span>
          <span>:</span>
<<<<<<< Updated upstream
          <span>Rs {Number(invoiceData?.amount || 100).toFixed(2)}</span>
=======
          <span>{formatCurrency(total)}</span>
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
          <span>Rs {Number(invoiceData?.received || 0).toFixed(2)}</span>
=======
          <span>{formatCurrency(received)}</span>
>>>>>>> Stashed changes
        </div>
        <div className="tally-summary-row">
          <span>Balance</span>
          <span>:</span>
<<<<<<< Updated upstream
          <span>Rs {Number(invoiceData?.balance || 0).toFixed(2)}</span>
=======
          <span>{formatCurrency(balance)}</span>
>>>>>>> Stashed changes
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
