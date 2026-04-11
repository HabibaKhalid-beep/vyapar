import './ThermalTheme3.css'
import { getInvoiceViewModel } from './invoiceData'

const ThermalTheme3 = ({ businessInfo, onCompanyClick, onLogoClick, logo, signature, onSignatureClick, selectedColor, terms, onTermsClick, invoiceData }) => {
  const view = getInvoiceViewModel(invoiceData)
  return (
    <div className="tt3-wrapper">

      {/* HEADER: Company centered */}
      <div className="tt3-header" onClick={onCompanyClick} style={{ cursor: 'pointer' }}>
        <h1 className="tt3-company-name">{businessInfo.name}</h1>
        <p className="tt3-company-phone">Ph.No.: {businessInfo.phone}</p>
      </div>

      {/* DASHED HR */}
      <hr className="tt3-dashed" />

      {/* INVOICE TITLE */}
      <p className="tt3-title">Invoice</p>

      {/* BILL TO + DATE/INVOICE NO */}
      <div className="tt3-info-row">
        <p className="tt3-billto-name">{view.billTo}</p>
        <div className="tt3-invoice-details">
          <p className="tt3-details-line">Date: {view.date}</p>
          <p className="tt3-details-line">Invoice No.: {view.invoiceNo}</p>
        </div>
      </div>

      {/* DASHED HR */}
      <hr className="tt3-dashed" />

      {/* TABLE */}
      <table className="tt3-table">
        <thead>
          <tr>
            <th className="tt3-th-left tt3-col-num">#</th>
            <th className="tt3-th-left tt3-col-name">
              <span className="tt3-th-line">Item Name</span>
              <span className="tt3-th-line tt3-th-sub">Qty</span>
            </th>
            <th className="tt3-th-right tt3-col-price">Price</th>
            <th className="tt3-th-right tt3-col-amount">Amount</th>
          </tr>
        </thead>
        <tbody>
          {/* dashed separator */}
          <tr>
            <td colSpan="4" className="tt3-dashed-cell"><hr className="tt3-dashed tt3-no-margin" /></td>
          </tr>

          {/* Item row */}
          {view.items.map((item, index) => (
            <tr key={`${item.name}-${index}`}>
              <td className="tt3-td-left">{index + 1}</td>
              <td className="tt3-td-left">
                <span className="tt3-item-name">{item.name}</span>
                <span className="tt3-item-qty">{item.qty}</span>
              </td>
              <td className="tt3-td-right">{item.rate.toFixed(2)}</td>
              <td className="tt3-td-right">{item.amount.toFixed(2)}</td>
            </tr>
          ))}

          {/* dashed separator */}
          <tr>
            <td colSpan="4" className="tt3-dashed-cell"><hr className="tt3-dashed tt3-no-margin" /></td>
          </tr>

          {/* Qty total row */}
          <tr className="tt3-qty-total-row">
            <td className="tt3-td-left" colSpan="2"><strong>Qty: {view.totalQty}</strong></td>
            <td className="tt3-td-right"></td>
            <td className="tt3-td-right"><strong>{view.paidTotal.toFixed(2)}</strong></td>
          </tr>

          {/* Total / Received / Balance sub-rows */}
          <tr>
            <td className="tt3-td-left"></td>
            <td className="tt3-td-left tt3-sub-label"><strong>Total</strong></td>
            <td className="tt3-td-right tt3-sub-colon">:</td>
            <td className="tt3-td-right"><strong>{view.paidTotal.toFixed(2)}</strong></td>
          </tr>
          <tr>
            <td className="tt3-td-left"></td>
            <td className="tt3-td-left tt3-sub-label">Received</td>
            <td className="tt3-td-right tt3-sub-colon">:</td>
            <td className="tt3-td-right">{view.received.toFixed(2)}</td>
          </tr>
          <tr>
            <td className="tt3-td-left"></td>
            <td className="tt3-td-left tt3-sub-label">Balance</td>
            <td className="tt3-td-right tt3-sub-colon">:</td>
            <td className="tt3-td-right">{view.balance.toFixed(2)}</td>
          </tr>

          {/* dashed separator */}
          <tr>
            <td colSpan="4" className="tt3-dashed-cell"><hr className="tt3-dashed tt3-no-margin" /></td>
          </tr>
        </tbody>
      </table>

      {/* TERMS */}
      <div
        className="tt3-terms"
        onClick={onTermsClick}
        style={{ cursor: 'pointer', position: 'relative' }}
      >
        <p className="tt3-terms-label">Terms &amp; Conditions</p>
        <p className="tt3-terms-text">{terms}</p>
        <span className="tt3-edit-icon">✎</span>
      </div>

    </div>
  )
}

export default ThermalTheme3
