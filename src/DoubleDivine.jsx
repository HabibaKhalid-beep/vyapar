import './DoubleDivine.css'
import { formatCurrency, getInvoiceViewModel } from './invoiceData'

const DoubleDivine = ({ businessInfo, onCompanyClick, onLogoClick, logo, signature, onSignatureClick, selectedColor, terms, onTermsClick, invoiceData }) => {
  const view = getInvoiceViewModel(invoiceData)

  return (
    <div className="divine-wrapper">

      {/* HEADER */}
      <div className="divine-header" style={{ backgroundColor: selectedColor }}>

        {/* Dark navy block: logo + company name, curved bottom-right */}
        <div className="divine-navy-block">
          <div className="divine-logo" onClick={onLogoClick} style={{ cursor: 'pointer' }}>
            {logo
              ? <img src={logo} alt="logo" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
              : 'LOGO'
            }
          </div>
          <div className="divine-company-block" onClick={onCompanyClick} style={{ cursor: 'pointer' }}>
            <h1 className="divine-company-name">{businessInfo.name}</h1>
          </div>
        </div>

        {/* Phone on right inside color area */}
        
        <div className="divine-phone">
          <span className="phone-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
              <path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/>
            </svg>
          </span>
          <span>| {businessInfo.phone}</span>
        </div>

      </div>

      {/* INVOICE DETAILS */}
      <div className="divine-invoice-block">
        <h2 className="divine-title">Invoice</h2>
        <table className="divine-meta-table">
          <tbody>
            <tr>
              <td><strong>Invoice No.:</strong></td>
              <td>{view.invoiceNo}</td>
            </tr>
            <tr>
              <td><strong>Date:</strong></td>
              <td>{view.date}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div className="divine-bill-row">
        <p className="divine-bill-label" style={{ color: selectedColor }}>Bill To:</p>
        <p className="divine-bill-value">{view.billTo}</p>
      </div>

      <table className="divine-table">
        <thead>
          <tr style={{ backgroundColor: selectedColor }}>
            <th>#</th>
            <th>Item name</th>
            <th>Quantity</th>
            <th>Price/ Unit</th>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody>
          {view.items.map((item, index) => (
            <tr key={`${item.name}-${index}`}>
              <td>{index + 1}</td>
              <td><strong>{item.name}</strong></td>
              <td>{item.qty}</td>
              <td>{formatCurrency(item.rate)}</td>
              <td>{formatCurrency(item.amount)}</td>
            </tr>
          ))}
          <tr className="divine-total-row" style={{ backgroundColor: selectedColor }}>
            <td></td>
            <td><strong>Total</strong></td>
            <td><strong>{view.totalQty}</strong></td>
            <td></td>
            <td><strong>{formatCurrency(view.total)}</strong></td>
          </tr>
        </tbody>
      </table>

      <div className="divine-bottom">
        <div className="divine-left-bottom">
          <p className="divine-section-label" style={{ color: selectedColor }}>Invoice Amount In Words</p>
          <p>{view.amountInWords}</p>
          <br />
          <p className="divine-section-label" style={{ color: selectedColor }}>Terms And Conditions</p>
          <div
            className="divine-terms"
            onClick={onTermsClick}
            style={{ cursor: 'pointer', position: 'relative' }}
          >
            <p>{terms}</p>
            <span className="edit-icon">✎</span>
          </div>
        </div>

        <div className="divine-right-bottom">
          <div className="divine-summary-row">
            <span>Sub Total</span>
            <span>{formatCurrency(view.subtotalPaid)}</span>
          </div>
          <div className="divine-summary-row divine-total-highlight" style={{ backgroundColor: selectedColor }}>
            <span>Total</span>
            <span>{formatCurrency(view.paidTotal)}</span>
          </div>
          <div className="divine-summary-row">
            <span>Received</span>
            <span>{formatCurrency(view.received)}</span>
          </div>
          <div className="divine-summary-row">
            <span>Balance</span>
            <span>{formatCurrency(view.balance)}</span>
          </div>
        </div>
      </div>

      <div className="divine-footer">
        <p>For : {businessInfo.name}</p>
        <div className="divine-sign" onClick={onSignatureClick} style={{ cursor: 'pointer' }}>
          {signature
            ? <img src={signature} alt="signature" style={{ height: '50px' }} />
            : <p><strong>Authorized Signatory</strong></p>
          }
        </div>
      </div>

    </div>
  )
}

export default DoubleDivine
