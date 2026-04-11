import './TaxTheme1.css'
import { formatCurrency, getInvoiceViewModel } from './invoiceData'

const TaxTheme1 = ({ businessInfo, onCompanyClick, onLogoClick, logo, signature, onSignatureClick, selectedColor, terms, onTermsClick, invoiceData }) => {
  const view = getInvoiceViewModel(invoiceData)

  return (
    <div className="tax1-wrapper">

      {/* HEADER: Company left, Logo right */}
      <div className="tax1-header">
        <div className="tax1-company" onClick={onCompanyClick} style={{ cursor: 'pointer' }}>
          <h1>{businessInfo.name}</h1>
          <p>Phone no. : {businessInfo.phone}</p>
        </div>
        <div className="tax1-logo" onClick={onLogoClick} style={{ cursor: 'pointer' }}>
          {logo
            ? <img src={logo} alt="logo" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
            : 'LOGO'
          }
        </div>
      </div>

      {/* INVOICE TITLE */}
      <h2 className="tax1-title">Invoice</h2>

      {/* BILL TO + INVOICE DETAILS */}
      <div className="tax1-info">
        <div className="tax1-bill">
          <p className="tax1-label">Bill To</p>
          <p className="tax1-value">{view.billTo}</p>
        </div>
        <div className="tax1-invoice-details">
          <p className="tax1-label">Invoice Details</p>
          <p>Invoice No. : <strong>{view.invoiceNo}</strong></p>
          <p>Date : <strong>{view.date}</strong></p>
        </div>
      </div>

      {/* TABLE */}
      <table className="tax1-table">
        <thead>
          <tr style={{ backgroundColor: selectedColor || '#888888' }}>
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
          <tr className="tax1-total-row">
            <td></td>
            <td><strong>Total</strong></td>
            <td><strong>{view.totalQty}</strong></td>
            <td></td>
            <td><strong>{formatCurrency(view.total)}</strong></td>
          </tr>
        </tbody>
      </table>

      {/* BOTTOM: two columns */}
      <div className="tax1-bottom">

        {/* LEFT: Words + Terms */}
        <div className="tax1-left">
          <div className="tax1-words">
            <p className="tax1-label">Invoice Amount In Words</p>
            <p>{view.amountInWords}</p>
          </div>
          <div
            className="tax1-terms"
            onClick={onTermsClick}
            style={{ cursor: 'pointer', position: 'relative' }}
          >
            <p className="tax1-label">Terms and Conditions</p>
            <p>{terms}</p>
            <span className="edit-icon">✎</span>
          </div>
        </div>

        {/* RIGHT: Summary */}
        <div className="tax1-right">
          <div className="tax1-summary-row">
            <span>Sub Total</span>
            <span>{formatCurrency(view.subtotalPaid)}</span>
          </div>
          <div className="tax1-summary-row total-highlight" style={{ backgroundColor: selectedColor || '#888888' }}>
            <span>Total</span>
            <span>{formatCurrency(view.paidTotal)}</span>
          </div>
          <div className="tax1-summary-row">
            <span>Received</span>
            <span>{formatCurrency(view.received)}</span>
          </div>
          <div className="tax1-summary-row">
            <span>Balance</span>
            <span>{formatCurrency(view.balance)}</span>
          </div>
        </div>

      </div>

      {/* SIGNATURE */}
      <div className="tax1-sign">
        <p className="tax1-for">For :{businessInfo.name}</p>
        <div
          className="tax1-sign-box"
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

export default TaxTheme1
