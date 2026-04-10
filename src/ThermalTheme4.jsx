import './ThermalTheme4.css'
import { getInvoiceViewModel } from './invoiceData'

const ThermalTheme4 = ({ businessInfo, onCompanyClick, onLogoClick, logo, signature, onSignatureClick, selectedColor, terms, onTermsClick, invoiceData }) => {
  const view = getInvoiceViewModel(invoiceData)
  return (
    <div className="tt4-wrapper">

      {/* HEADER */}
      <div className="tt4-header" onClick={onCompanyClick} style={{ cursor: 'pointer' }}>
        <h1 className="tt4-company-name">{businessInfo.name}</h1>
        <p className="tt4-company-phone">Ph.No.: {businessInfo.phone}</p>
      </div>

      <hr className="tt4-dashed" />

      {/* INVOICE TITLE */}
      <p className="tt4-title">Invoice</p>

      {/* INVOICE NO + DATE ROW */}
      <div className="tt4-meta-row">
        <span className="tt4-meta-left">Invoice No.: {view.invoiceNo}</span>
        <span className="tt4-meta-right">Date: {view.date}</span>
      </div>

      <hr className="tt4-dashed" />

      {/* BILL TO centered */}
      <p className="tt4-billto-name">{view.billTo}</p>

      <hr className="tt4-dashed" />

      {/* TABLE */}
      <table className="tt4-table">
        <thead>
          <tr>
            <th className="tt4-th-left tt4-col-num">#</th>
            <th className="tt4-th-left tt4-col-name">
              <span className="tt4-th-line">Item Name</span>
              <span className="tt4-th-line tt4-th-sub">Qty</span>
            </th>
            <th className="tt4-th-right tt4-col-price">Price</th>
            <th className="tt4-th-right tt4-col-amount">Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colSpan="4" className="tt4-dashed-cell"><hr className="tt4-dashed tt4-no-margin" /></td>
          </tr>

          {view.items.map((item, index) => (
            <tr key={`${item.name}-${index}`}>
              <td className="tt4-td-left">{index + 1}</td>
              <td className="tt4-td-left">
                <span className="tt4-item-name">{item.name}</span>
                <span className="tt4-item-qty">{item.qty}</span>
              </td>
              <td className="tt4-td-right">{item.rate.toFixed(2)}</td>
              <td className="tt4-td-right">{item.amount.toFixed(2)}</td>
            </tr>
          ))}

          <tr>
            <td colSpan="4" className="tt4-dashed-cell"><hr className="tt4-dashed tt4-no-margin" /></td>
          </tr>

          {/* Qty total row */}
          <tr className="tt4-qty-total-row">
            <td className="tt4-td-left" colSpan="2"><strong>Qty: {view.totalQty}</strong></td>
            <td className="tt4-td-right"></td>
            <td className="tt4-td-right"><strong>{view.total.toFixed(2)}</strong></td>
          </tr>

          <tr>
            <td className="tt4-td-left"></td>
            <td className="tt4-td-left tt4-sub-label"><strong>Total</strong></td>
            <td className="tt4-td-right tt4-sub-colon">:</td>
            <td className="tt4-td-right"><strong>{view.total.toFixed(2)}</strong></td>
          </tr>
          <tr>
            <td className="tt4-td-left"></td>
            <td className="tt4-td-left tt4-sub-label">Received</td>
            <td className="tt4-td-right tt4-sub-colon">:</td>
            <td className="tt4-td-right">{view.received.toFixed(2)}</td>
          </tr>
          <tr>
            <td className="tt4-td-left"></td>
            <td className="tt4-td-left tt4-sub-label">Balance</td>
            <td className="tt4-td-right tt4-sub-colon">:</td>
            <td className="tt4-td-right">{view.balance.toFixed(2)}</td>
          </tr>

          <tr>
            <td colSpan="4" className="tt4-dashed-cell"><hr className="tt4-dashed tt4-no-margin" /></td>
          </tr>
        </tbody>
      </table>

      {/* TERMS centered */}
      <div
        className="tt4-terms"
        onClick={onTermsClick}
        style={{ cursor: 'pointer', position: 'relative' }}
      >
        <p className="tt4-terms-label">Terms &amp; Conditions</p>
        <p className="tt4-terms-text">{terms}</p>
        <span className="tt4-edit-icon">✎</span>
      </div>

    </div>
  )
}

export default ThermalTheme4
