import './Theme3.css'
import { formatCurrency, getInvoiceViewModel } from './invoiceData'

const Theme3 = ({ businessInfo, onCompanyClick, onLogoClick, logo, signature, onSignatureClick, selectedColor, terms, onTermsClick, invoiceData }) => {
  const accent = selectedColor || '#888888'
  const view = getInvoiceViewModel(invoiceData)

  return (
    <div className="t1-wrapper">

      {/* HEADER: Logo left | Company name right */}
      <div className="t1-header" style={{backgroundColor:accent}}>
        <div
          className="t1-logo"
          onClick={onLogoClick}
          style={{ cursor: 'pointer', backgroundColor: accent }}
        >
          {logo
            ? <img src={logo} alt="logo" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
            : 'LOGO'
          }
        </div>
        <div
          className="t1-company-info"
          onClick={onCompanyClick}
          style={{ cursor: 'pointer' }}
        >
          <h1 className="t1-company-name" style={{color:'white'}}>{businessInfo.name}</h1>
          <p className="t1-company-phone">Phone no.: {businessInfo.phone}</p>
        </div>
      </div>

      {/* DIVIDER + INVOICE TITLE */}
      <div className="t1-title-row">
        <hr className="t1-hr" style={{ borderTopColor: accent }} />
        <p className="t11-title" style={{ color: accent }}>Invoice</p>
        <hr className="t1-hr" style={{ borderTopColor: accent }} />
      </div>

      {/* BILL TO + INVOICE DETAILS */}
      <div className="t1-info-row" style={{ borderTopColor: accent }}>
        <div className="t1-billto-block">
          <p className="t1-billto-label" style={{backgroundColor:accent,color:'white'}}>Bill To</p>
          <p className="t1-billto-name">{view.billTo}</p>
        </div>
        <div className="t1-invoice-details">
          <p className="t1-details-heading" style={{backgroundColor:accent,color:'white'}}>Invoice Details</p>
          <p className="t1-details-line">Invoice No. : {view.invoiceNo}</p>
          <p className="t1-details-line">Date : {view.date}</p>
        </div>
      </div>

      {/* TABLE */}
      <table className="t1-table">
        <thead>
          <tr>
            <th className="t1-th-left t1-col-num" style={{ backgroundColor: accent }}>#</th>
            <th className="t1-th-left t1-col-name" style={{ backgroundColor: accent }}>Item name</th>
            <th className="t1-th-right t1-col-qty" style={{ backgroundColor: accent }}>Quantity</th>
            <th className="t1-th-right t1-col-price" style={{ backgroundColor: accent }}>Price/ Unit</th>
            <th className="t1-th-right t1-col-amount" style={{ backgroundColor: accent }}>Amount</th>
          </tr>
        </thead>
        <tbody>
          {view.items.map((item, index) => (
            <tr key={`${item.name}-${index}`}>
              <td className="t1-td-left">{index + 1}</td>
              <td className="t1-td-left"><strong>{item.name}</strong></td>
              <td className="t1-td-right">{item.qty}</td>
              <td className="t1-td-right">{formatCurrency(item.rate)}</td>
              <td className="t1-td-right">{formatCurrency(item.amount)}</td>
            </tr>
          ))}
          <tr className="t1-total-row">
            <td className="t1-td-left"></td>
            <td className="t1-td-left"><strong>Total</strong></td>
            <td className="t1-td-right"><strong>{view.totalQty}</strong></td>
            <td className="t1-td-right"></td>
            <td className="t1-td-right"><strong>{formatCurrency(view.total)}</strong></td>
          </tr>
        </tbody>
      </table>

      {/* BOTTOM SECTION: Left col | Right col */}
      <div className="t1-bottom">

        {/* LEFT: Invoice Amount in Words + Terms */}
        <div className="t1-bottom-left">

          <div className="t1-section-header" style={{ backgroundColor: accent }}>Invoice Amount In Words</div>
          <div className="t1-section-body t1-words-body">
            <p className="t1-words-text">{view.amountInWords}</p>
          </div>

          <div
            className="t1-section-header"
            style={{ backgroundColor: accent, cursor: 'pointer' }}
            onClick={onTermsClick}
          >
            Terms and Conditions
          </div>
          <div
            className="t1-section-body t1-terms-body"
            onClick={onTermsClick}
            style={{ cursor: 'pointer', position: 'relative' }}
          >
            <p className="t1-terms-text">{terms}</p>
            <span className="t1-edit-icon">✎</span>
          </div>

        </div>

        {/* RIGHT: Amounts + Signature */}
        <div className="t1-bottom-right">

          <div className="t1-section-header" style={{ backgroundColor: accent }}>Amounts</div>
          <div className="t1-amounts-body">
            <div className="t1-amount-row">
              <span>Sub Total</span>
              <span>{formatCurrency(view.subtotalPaid)}</span>
            </div>
            <div className="t1-amount-row t1-amount-bold">
              <span>Total</span>
              <span>{formatCurrency(view.paidTotal)}</span>
            </div>
            <div className="t1-amount-row">
              <span>Received</span>
              <span>{formatCurrency(view.received)}</span>
            </div>
            <div className="t1-amount-row t1-amount-last">
              <span>Balance</span>
              <span>{formatCurrency(view.balance)}</span>
            </div>
          </div>

          {/* Signature */}
          <div
            className="t1-sign-area"
            onClick={onSignatureClick}
            style={{ cursor: 'pointer' }}
          >
            <p className="t1-for-text">For : {businessInfo.name}</p>
            <div className="t1-sign-space">
              {signature
                ? <img src={signature} alt="signature" style={{ height: '50px' }} />
                : null
              }
            </div>
            <p className="t1-signatory">Authorized Signatory</p>
          </div>

        </div>

      </div>

    </div>
  )
}

export default Theme3
