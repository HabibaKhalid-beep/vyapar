import './Theme4.css'
import { formatCurrency, getInvoiceViewModel } from './invoiceData'

const Theme4 = ({ businessInfo, onCompanyClick, onLogoClick, logo, signature, onSignatureClick, selectedColor, terms, onTermsClick, invoiceData }) => {
  const accent = selectedColor || '#888888'
  const view = getInvoiceViewModel(invoiceData)

  return (
    <div className="t4-wrapper">

      {/* HEADER: Company name LEFT | Logo RIGHT */}
      <div className="t4-header">
        <div
          className="t4-company-info"
          onClick={onCompanyClick}
          style={{ cursor: 'pointer' }}
        >
          <h1 className="t4-company-name">{businessInfo.name}</h1>
          <p className="t4-company-phone">Phone no. : {businessInfo.phone}</p>
        </div>
        <div
          className="t4-logo"
          onClick={onLogoClick}
          style={{ cursor: 'pointer', backgroundColor: accent }}
        >
          {logo
            ? <img src={logo} alt="logo" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
            : 'LOGO'
          }
        </div>
      </div>

      {/* FULL WIDTH HR + INVOICE TITLE CENTERED */}
      <div className="t4-title-section">
        <hr className="t4-hr" style={{ borderTopColor: accent }} />
        <p className="t4-title" style={{ color: accent }}>Invoice</p>
      </div>

      {/* BILL TO + INVOICE DETAILS */}
      <div className="t4-info-row">
        <div className="t4-billto-block">
          <p className="t4-billto-label">Bill To</p>
          <p className="t4-billto-name">{view.billTo}</p>
        </div>
        <div className="t4-invoice-details">
          <p className="t4-details-heading">Invoice Details</p>
          <p className="t4-details-line">Invoice No. : {view.invoiceNo}</p>
          <p className="t4-details-line">Date : {view.date}</p>
        </div>
      </div>

      {/* TABLE */}
      <table className="t4-table">
        <thead>
          <tr>
            <th className="t4-th-left t4-col-num" style={{ backgroundColor: accent }}>#</th>
            <th className="t4-th-left t4-col-name" style={{ backgroundColor: accent }}>Item name</th>
            <th className="t4-th-right t4-col-qty" style={{ backgroundColor: accent }}>Quantity</th>
            <th className="t4-th-right t4-col-price" style={{ backgroundColor: accent }}>Price/ Unit</th>
            <th className="t4-th-right t4-col-amount" style={{ backgroundColor: accent }}>Amount</th>
          </tr>
        </thead>
        <tbody>
          {view.items.map((item, index) => (
            <tr key={`${item.name}-${index}`}>
              <td className="t4-td-left">{index + 1}</td>
              <td className="t4-td-left"><strong>{item.name}</strong></td>
              <td className="t4-td-right">{item.qty}</td>
              <td className="t4-td-right">{formatCurrency(item.rate)}</td>
              <td className="t4-td-right">{formatCurrency(item.amount)}</td>
            </tr>
          ))}
          <tr className="t4-total-row">
            <td className="t4-td-left"></td>
            <td className="t4-td-left"><strong>Total</strong></td>
            <td className="t4-td-right"><strong>{view.totalQty}</strong></td>
            <td className="t4-td-right"></td>
            <td className="t4-td-right"><strong>{formatCurrency(view.total)}</strong></td>
          </tr>
        </tbody>
      </table>

      {/* BOTTOM SECTION */}
      <div className="t4-bottom">

        {/* LEFT: Words + Terms inline */}
        <div className="t4-bottom-left">
          <p className="t4-inline-row">
            <strong>Invoice Amount in Words:</strong> One Hundred Rupees only
          </p>
          <p
            className="t4-inline-row"
            onClick={onTermsClick}
            style={{ cursor: 'pointer' }}
          >
            <strong>Terms and Conditions</strong> {terms}
          </p>
        </div>

        {/* RIGHT: Amounts + Signature */}
        <div className="t4-bottom-right">

          <div className="t4-amounts-body">
            <div className="t4-amount-row">
              <span>Sub Total</span>
              <span>{formatCurrency(view.subtotal)}</span>
            </div>
            <div className="t4-amount-row t4-amount-bold">
              <span>Total</span>
              <span>{formatCurrency(view.total)}</span>
            </div>
            <div className="t4-amount-row">
              <span>Received</span>
              <span>{formatCurrency(view.received)}</span>
            </div>
            <div className="t4-amount-row t4-amount-last">
              <span>Balance</span>
              <span>{formatCurrency(view.balance)}</span>
            </div>
          </div>

          {/* Signature */}
          <div
            className="t4-sign-area"
            onClick={onSignatureClick}
            style={{ cursor: 'pointer' }}
          >
            <p className="t4-for-text">For : {businessInfo.name}</p>
            <div className="t4-sign-space">
              {signature
                ? <img src={signature} alt="signature" style={{ height: '50px' }} />
                : null
              }
            </div>
            <p className="t4-signatory">Authorized Signatory</p>
          </div>

        </div>

      </div>

    </div>
  )
}

export default Theme4
