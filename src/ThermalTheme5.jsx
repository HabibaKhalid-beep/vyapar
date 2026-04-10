import './ThermalTheme5.css'
import { getInvoiceViewModel } from './invoiceData'

const ThermalTheme5 = ({ businessInfo, onCompanyClick, onLogoClick, logo, signature, onSignatureClick, selectedColor, terms, onTermsClick, invoiceData }) => {
  const view = getInvoiceViewModel(invoiceData)
  return (
    <div className="tt5-wrapper">

      {/* HEADER */}
      <div className="tt5-header" onClick={onCompanyClick} style={{ cursor: 'pointer' }}>
        <h1 className="tt5-company-name">{businessInfo.name}</h1>
        <p className="tt5-company-phone">Ph.No.: {businessInfo.phone}</p>
      </div>

      <hr className="tt5-dashed" />

      {/* INVOICE TITLE */}
      <p className="tt5-title">Invoice</p>

      {/* INVOICE NO + DATE ROW */}
      <div className="tt5-meta-row">
        <span className="tt5-meta-left">Invoice No.: {view.invoiceNo}</span>
        <span className="tt5-meta-right">Date: {view.date}</span>
      </div>

      <hr className="tt5-dashed" />

      {/* BILL TO centered */}
      <p className="tt5-billto-name">{view.billTo}</p>

      <hr className="tt5-dashed" />

      {/* TABLE */}
      <table className="tt5-table">
        <thead>
          <tr>
            <th className="tt5-th-left tt5-col-num">#</th>
            <th className="tt5-th-left tt5-col-name">
              <span className="tt5-th-line">Item Name</span>
              <span className="tt5-th-line tt5-th-sub">Qty</span>
            </th>
            <th className="tt5-th-right tt5-col-price">Price</th>
            <th className="tt5-th-right tt5-col-amount">Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colSpan="4" className="tt5-dashed-cell"><hr className="tt5-dashed tt5-no-margin" /></td>
          </tr>

          {view.items.map((item, index) => (
            <tr key={`${item.name}-${index}`}>
              <td className="tt5-td-left">{index + 1}</td>
              <td className="tt5-td-left">
                <span className="tt5-item-name">{item.name}</span>
                <span className="tt5-item-qty">{item.qty}</span>
              </td>
              <td className="tt5-td-right">{item.rate.toFixed(2)}</td>
              <td className="tt5-td-right">{item.amount.toFixed(2)}</td>
            </tr>
          ))}

          <tr>
            <td colSpan="4" className="tt5-dashed-cell"><hr className="tt5-dashed tt5-no-margin" /></td>
          </tr>

          {/* Qty: 1   Items: 1   100.00 */}
          <tr className="tt5-qty-total-row">
            <td className="tt5-td-left" colSpan="1"><strong>Qty: {view.totalQty}</strong></td>
            <td className="tt5-td-center"><strong>Items: {view.items.length}</strong></td>
            <td className="tt5-td-right"></td>
            <td className="tt5-td-right"><strong>{view.total.toFixed(2)}</strong></td>
          </tr>

          <tr>
            <td colSpan="4" className="tt5-dashed-cell"><hr className="tt5-dashed tt5-no-margin" /></td>
          </tr>

          {/* Total / Received / Balance — no leading empty cell */}
          <tr>
            <td className="tt5-td-left tt5-sub-label" colSpan="1"><strong>Total</strong></td>
            <td className="tt5-td-left"></td>
            <td className="tt5-td-right tt5-sub-colon">:</td>
            <td className="tt5-td-right"><strong>{view.total.toFixed(2)}</strong></td>
          </tr>
          <tr>
            <td className="tt5-td-left tt5-sub-label">Received</td>
            <td className="tt5-td-left"></td>
            <td className="tt5-td-right tt5-sub-colon">:</td>
            <td className="tt5-td-right">{view.received.toFixed(2)}</td>
          </tr>
          <tr>
            <td className="tt5-td-left tt5-sub-label">Balance</td>
            <td className="tt5-td-left"></td>
            <td className="tt5-td-right tt5-sub-colon">:</td>
            <td className="tt5-td-right">{view.balance.toFixed(2)}</td>
          </tr>

          <tr>
            <td colSpan="4" className="tt5-dashed-cell"><hr className="tt5-dashed tt5-no-margin" /></td>
          </tr>
        </tbody>
      </table>

      {/* TERMS centered */}
      <div
        className="tt5-terms"
        onClick={onTermsClick}
        style={{ cursor: 'pointer', position: 'relative' }}
      >
        <p className="tt5-terms-label">Terms &amp; Conditions</p>
        <p className="tt5-terms-text">{terms}</p>
        <span className="tt5-edit-icon">✎</span>
      </div>

    </div>
  )
}

export default ThermalTheme5
