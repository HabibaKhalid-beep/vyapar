import './FrenchElite.css'
import { formatCurrency, getInvoiceViewModel } from './invoiceData'

const FrenchElite = ({ selectedColor, businessInfo, signature, onCompanyClick, onSignatureClick, terms, onTermsClick, logo, onLogoClick, invoiceData }) => {
  const view = getInvoiceViewModel(invoiceData)

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
          <p>Invoice No.:{view.invoiceNo}</p>
          <div className="fe-date-row">
            <span>Date:</span>
            <span>{view.date}</span>
          </div>
        </div>
        <div className="fe-meta-right">
          <p className="fe-bill-label">Bill To:</p>
          <p className="fe-bill-value">{view.billTo}</p>
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
          {view.items.map((item, index) => (
            <tr key={`${item.name}-${index}`}>
              <td className="fe-td-left">{index + 1}</td>
              <td className="fe-td-left"><strong>{item.name}</strong></td>
              <td className="fe-td-right">{item.qty}</td>
              <td className="fe-td-right">{formatCurrency(item.rate)}</td>
              <td className="fe-td-right">{formatCurrency(item.amount)}</td>
            </tr>
          ))}
          <tr style={{ backgroundColor: selectedColor }} className="fe-total-row">
            <td className="fe-td-left"></td>
            <td className="fe-td-left"><strong>Total</strong></td>
            <td className="fe-td-right"><strong>{view.totalQty}</strong></td>
            <td className="fe-td-right"></td>
            <td className="fe-td-right"><strong>{formatCurrency(view.total)}</strong></td>
          </tr>
        </tbody>
      </table>

      <div className="fe-bottom">
        <div className="fe-bottom-left">
          <p className="fe-section-label">Invoice Amount In Words</p>
          <p className="fe-words">{view.amountInWords}</p>
          <p className="fe-section-label fe-terms-label" onClick={onTermsClick} style={{ cursor: 'pointer' }}>Terms And Conditions</p>
          <p className="fe-terms-text" onClick={onTermsClick} style={{ cursor: 'pointer' }}>{terms}</p>
        </div>
        <div className="fe-bottom-right">
          <div className="fe-summary-row">
            <span>Sub Total</span>
            <span>{formatCurrency(view.subtotalPaid)}</span>
          </div>
          <div className="fe-summary-row fe-summary-total" style={{ backgroundColor: selectedColor }}>
            <span>Total</span>
            <span>{formatCurrency(view.paidTotal)}</span>
          </div>
          <div className="fe-summary-row">
            <span>Received</span>
            <span>{formatCurrency(view.received)}</span>
          </div>
          <div className="fe-summary-row">
            <span>Balance</span>
            <span>{formatCurrency(view.balance)}</span>
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
