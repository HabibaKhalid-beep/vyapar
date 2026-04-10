import './TaxTheme3.css'
import { formatCurrency, getInvoiceViewModel } from './invoiceData'

const TaxTheme3 = ({ businessInfo, onCompanyClick, onLogoClick, logo, signature, onSignatureClick, selectedColor, terms, onTermsClick, invoiceData }) => {
  const view = getInvoiceViewModel(invoiceData)

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
          <p className="tax3-meta-value">{view.invoiceNo}</p>
        </div>

        <div className="tax3-cell tax3-meta-cell tax3-no-right-border">
          <p className="tax3-meta-label">Date</p>
          <p className="tax3-meta-value">{view.date}</p>
        </div>

      </div>

      {/* ROW 2: Bill To full width */}
      <div className="tax3-billto-row">
        <p className="tax3-billto-label">Bill To</p>
        <p className="tax3-billto-name">{view.billTo}</p>
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
          {view.items.map((item, index) => (
            <tr key={`${item.name}-${index}`}>
              <td className="tax3-td-left">{index + 1}</td>
              <td className="tax3-td-left"><strong>{item.name}</strong></td>
              <td className="tax3-td-right">{item.qty}</td>
              <td className="tax3-td-right">{formatCurrency(item.rate)}</td>
              <td className="tax3-td-right">{formatCurrency(item.amount)}</td>
            </tr>
          ))}
          <tr className="tax3-total-row">
            <td className="tax3-td-left"></td>
            <td className="tax3-td-left"><strong>Total</strong></td>
            <td className="tax3-td-right"><strong>{view.totalQty}</strong></td>
            <td className="tax3-td-right"></td>
            <td className="tax3-td-right"><strong>{formatCurrency(view.total)}</strong></td>
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
            <span>{formatCurrency(view.subtotal)}</span>
          </div>
          <div className="tax3-summary-row tax3-bold-row">
            <span>Total</span>
            <span>{formatCurrency(view.total)}</span>
          </div>
          <div className="tax3-summary-row">
            <span>Received</span>
            <span>{formatCurrency(view.received)}</span>
          </div>
          <div className="tax3-summary-row tax3-last-row">
            <span>Balance</span>
            <span>{formatCurrency(view.balance)}</span>
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
