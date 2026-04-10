import './TaxTheme5.css'
import { formatCurrency, getInvoiceViewModel } from './invoiceData'

const TaxTheme6 = ({ selectedColor, businessInfo, signature, onCompanyClick, onSignatureClick, terms, onTermsClick, logo, onLogoClick, invoiceData }) => {
  const view = getInvoiceViewModel(invoiceData)

  return (
    <div className="tax2-wrapper">

      <div className="tax2-title-row">
        <h2 className="tax2-title">Invoice</h2>
      </div>

      <div className="tax2-header" onClick={onCompanyClick} style={{ cursor: 'pointer' }}>
        <div
          className="tax2-logo"
          onClick={(e) => { e.stopPropagation(); onLogoClick() }}
          style={{ cursor: 'pointer' }}
        >
          {logo
            ? <img src={logo} alt="logo" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
            : 'LOGO'
          }
        </div>
        <div className="tax2-company">
          <h1>{businessInfo.name}</h1>
          <p>Phone no.: {businessInfo.phone}</p>
        </div>
      </div>

      <div className="tax2-banner-row" style={{ backgroundColor: selectedColor }}>
        <span className="tax2-banner-left">Bill To</span>
        <span className="tax2-banner-right">Invoice Details</span>
      </div>

      <div className="tax2-info-row">
        <div className="tax2-bill-cell">
          <p className="tax2-bill-value">{view.billTo}</p>
        </div>
        <div className="tax2-details-cell">
          <p>Invoice No. : {view.invoiceNo}</p>
          <p>Date : {view.date}</p>
        </div>
      </div>

      <table className="tax2-table">
        <thead>
          <tr style={{ backgroundColor: selectedColor }}>
            <th className="tax2-th-left">#</th>
            <th className="tax2-th-left">Item name</th>
            <th className="tax2-th-right">Quantity</th>
            <th className="tax2-th-right">Price/ Unit</th>
            <th className="tax2-th-right">Amount</th>
          </tr>
        </thead>
        <tbody>
          {view.items.map((item, index) => (
            <tr key={`${item.name}-${index}`}>
              <td className="tax2-td-left">{index + 1}</td>
              <td className="tax2-td-left"><strong>{item.name}</strong></td>
              <td className="tax2-td-right">{item.qty}</td>
              <td className="tax2-td-right">{formatCurrency(item.rate)}</td>
              <td className="tax2-td-right">{formatCurrency(item.amount)}</td>
            </tr>
          ))}
          <tr className="tax2-total-row">
            <td className="tax2-td-left"></td>
            <td className="tax2-td-left"><strong>Total</strong></td>
            <td className="tax2-td-right"><strong>{view.totalQty}</strong></td>
            <td className="tax2-td-right"></td>
            <td className="tax2-td-right"><strong>{formatCurrency(view.total)}</strong></td>
          </tr>
        </tbody>
      </table>

      <div className="tax2-bottom">

        <div className="tax2-bottom-left">
          <div className="tax2-empty-spacer"></div>
          <div className="tax2-sub-banner" style={{ backgroundColor: selectedColor }}>
            <span>Invoice Amount In Words</span>
          </div>
          <div className="tax2-words-area">
            <p>{view.amountInWords}</p>
          </div>
          <div
            className="tax2-sub-banner"
            style={{ backgroundColor: selectedColor, cursor: 'pointer' }}
            onClick={onTermsClick}
          >
            <span>Terms and Conditions</span>
          </div>
          <div className="tax2-terms-area" onClick={onTermsClick} style={{ cursor: 'pointer' }}>
            <p>{terms}</p>
          </div>
        </div>

        <div className="tax2-bottom-right">
          <div className="tax2-amounts-section">
            <div className="tax2-amounts-banner" style={{ backgroundColor: selectedColor }}>
              <span>Amounts</span>
            </div>
            <div className="tax2-summary-row">
              <span>Sub Total</span>
              <span>{formatCurrency(view.subtotalPaid)}</span>
            </div>
            <div className="tax2-summary-row tax2-bold-row">
              <span>Total</span>
              <span>{formatCurrency(view.paidTotal)}</span>
            </div>
            <div className="tax2-summary-row">
              <span>Received</span>
              <span>{formatCurrency(view.received)}</span>
            </div>
            <div className="tax2-summary-row">
              <span>Balance</span>
              <span>{formatCurrency(view.balance)}</span>
            </div>
          </div>

          <div
            className="tax2-sign-section"
            onClick={onSignatureClick}
            style={{ cursor: 'pointer' }}
          >
            <p className="tax2-for-text">For : {businessInfo.name}</p>
            <div className="tax2-sign-box">
              {signature
                ? <img src={signature} alt="signature" style={{ height: '50px' }} />
                : <p className="tax2-signatory">Authorized Signatory</p>
              }
            </div>
          </div>
        </div>

      </div>

    </div>
  )
}

export default TaxTheme6
