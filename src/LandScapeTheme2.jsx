import './LandScapeTheme2.css'
import { formatCurrency, getInvoiceViewModel } from './invoiceData'

const LandScapeTheme2 = ({ businessInfo, onCompanyClick, onLogoClick, logo, signature, onSignatureClick, selectedColor, terms, onTermsClick, invoiceData }) => {
  const view = getInvoiceViewModel(invoiceData)

  return (
    <div className="tax4-wrapper">

      {/* TITLE */}
      <div className="tax4-title-row">
        <h2 className="tax4-title">Invoice</h2>
      </div>

      {/* ROW 1: Company | Invoice No | Date */}
      <div className="tax4-header-row">

        <div
          className="tax4-cell tax4-company-cell"
          onClick={onCompanyClick}
          style={{ cursor: 'pointer' }}
        >
          <div
            className="tax4-logo"
            onClick={(e) => { e.stopPropagation(); onLogoClick(); }}
            style={{ cursor: 'pointer' }}
          >
            {logo
              ? <img src={logo} alt="logo" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
              : 'LOGO'
            }
          </div>
          <div className="tax4-company-info">
            <h1>{businessInfo.name}</h1>
            <p>Phone: {businessInfo.phone}</p>
          </div>
        </div>

        <div className="tax4-cell tax4-meta-cell tax4-no-right-border">
          <p className="tax4-meta-label">Invoice No.:  <strong>{view.invoiceNo}</strong></p>
          <p className="tax4-meta-label">Date:  <strong>{view.date}</strong></p>
        </div>

      </div>

      {/* ROW 2: Bill To */}
      <div className="tax4-billto-row">
        <p className="tax4-billto-label">Bill To:</p>
        <p className="tax4-billto-name">{view.billTo}</p>
      </div>

      {/* ROW 3: Table */}
      <table className="tax4-table">
        <thead>
          <tr>
            <th className="tax4-th-left tax4-col-num">#</th>
            <th className="tax4-th-left tax4-col-name">Item name</th>
            <th className="tax4-th-right tax4-col-qty">Quantity</th>
            <th className="tax4-th-right tax4-col-price">Price/ Unit(Rs)</th>
            <th className="tax4-th-right tax4-col-amount">Amount(Rs)</th>
          </tr>
        </thead>
        <tbody>
          {view.items.map((item, index) => (
            <tr key={`${item.name}-${index}`}>
              <td className="tax4-td-left">{index + 1}</td>
              <td className="tax4-td-left"><strong>{item.name}</strong></td>
              <td className="tax4-td-right">{item.qty}</td>
              <td className="tax4-td-right">{formatCurrency(item.rate)}</td>
              <td className="tax4-td-right">{formatCurrency(item.amount)}</td>
            </tr>
          ))}
          <tr className="tax4-total-row">
            <td className="tax4-td-left"></td>
            <td className="tax4-td-left"><strong>Total</strong></td>
            <td className="tax4-td-right"><strong>{view.totalQty}</strong></td>
            <td className="tax4-td-right"></td>
            <td className="tax4-td-right"><strong>{formatCurrency(view.total)}</strong></td>
          </tr>
        </tbody>
      </table>

      {/* Summary rows */}
      <div className="tax4-summary-table">

        <div className="tax4-summary-row">
          <span className="tax4-summary-label">Sub Total</span>
          <span className="tax4-summary-colon">:</span>
          <span className="tax4-summary-value">{formatCurrency(view.subtotalPaid)}</span>
        </div>

        <div className="tax4-summary-row tax4-summary-bold">
          <span className="tax4-summary-label">Total</span>
          <span className="tax4-summary-colon">:</span>
          <span className="tax4-summary-value"><strong>{formatCurrency(view.paidTotal)}</strong></span>
        </div>

        <div className="tax4-summary-row">
          <span className="tax4-summary-label">Invoice Amount in Words</span>
          <span className="tax4-summary-colon">:</span>
          <span className="tax4-summary-value">One Hundred Rupees only</span>
        </div>

        <div className="tax4-summary-row tax4-received-balance">
          <span className="tax4-summary-label">
            <span className="tax4-rb-line">Received</span>
            <span className="tax4-rb-line">Balance</span>
          </span>
          <span className="tax4-summary-colon">
            <span className="tax4-rb-line">:</span>
            <span className="tax4-rb-line">:</span>
          </span>
          <span className="tax4-summary-value">
            <span className="tax4-rb-line">{formatCurrency(view.received)}</span>
            <span className="tax4-rb-line">{formatCurrency(view.balance)}</span>
          </span>
        </div>

      </div>

      {/* ROW: Terms | Signature */}
      <div className="tax4-footer-row">

        <div
          className="tax4-footer-left"
          onClick={onTermsClick}
          style={{ cursor: 'pointer', position: 'relative' }}
        >
          <p className="tax4-terms-label">Terms &amp; Conditions:</p>
          <p className="tax4-terms-text">{terms}</p>
          <span className="tax4-edit-icon">✎</span>
        </div>

        <div
          className="tax4-footer-right"
          onClick={onSignatureClick}
          style={{ cursor: 'pointer' }}
        >
          <p className="tax4-for-text">For {businessInfo.name}:</p>
          <div className="tax4-sign-box">
            {signature
              ? <img src={signature} alt="signature" style={{ height: '50px' }} />
              : <p className="tax4-signatory">Authorized Signatory</p>
            }
          </div>
        </div>

      </div>

    </div>
  )
}

export default LandScapeTheme2
