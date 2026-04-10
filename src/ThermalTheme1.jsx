import './ThermalTheme1.css'

const ThermalTheme1 = ({ businessInfo, onCompanyClick, onLogoClick, logo, signature, onSignatureClick, selectedColor, terms, onTermsClick }) => {
  const accent = selectedColor || '#111111'

  return (
    <div className="t5-wrapper">

      {/* HEADER: Company centered */}
      <div className="t5-header" onClick={onCompanyClick} style={{ cursor: 'pointer' }}>
        <h1 className="t5-company-name">{businessInfo.name}</h1>
        <p className="t5-company-phone">Ph.No.: {businessInfo.phone}</p>
      </div>

      {/* DASHED HR */}
      <hr className="t5-dashed" />

      {/* INVOICE TITLE */}
      <p className="t5-title">Invoice</p>

      {/* BILL TO + DATE/INVOICE NO */}
      <div className="t5-info-row">
        <p className="t5-billto-name">fida</p>
        <div className="t5-invoice-details">
          <p className="t5-details-line">Date: 09/04/2026</p>
          <p className="t5-details-line">Invoice No.: 3</p>
        </div>
      </div>

      {/* DASHED HR */}
      <hr className="t5-dashed" />

      {/* TABLE */}
      <table className="t5-table">
        <thead>
          <tr>
            <th className="t5-th-left t5-col-num">#</th>
            <th className="t5-th-left t5-col-name">Name</th>
            <th className="t5-th-right t5-col-qty">Qty</th>
            <th className="t5-th-right t5-col-price">Price</th>
            <th className="t5-th-right t5-col-amount">Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr className="t5-dashed-row">
            <td className="t5-td-left" colSpan="5"><hr className="t5-dashed t5-no-margin" /></td>
          </tr>
          <tr>
            <td className="t5-td-left">1</td>
            <td className="t5-td-left">Sample Item</td>
            <td className="t5-td-right">1</td>
            <td className="t5-td-right">100.00</td>
            <td className="t5-td-right">100.00</td>
          </tr>
          <tr>
            <td className="t5-td-left" colSpan="5"><hr className="t5-dashed t5-no-margin" /></td>
          </tr>

          {/* Total row */}
          <tr className="t5-total-main-row">
            <td className="t5-td-left"><strong>Total</strong></td>
            <td className="t5-td-left"></td>
            <td className="t5-td-right"><strong>1</strong></td>
            <td className="t5-td-right"></td>
            <td className="t5-td-right"><strong>100.00</strong></td>
          </tr>

          {/* Total / Received / Balance sub-rows */}
          {/* <tr>
            <td className="t5-td-left"></td>
            <td className="t5-td-left t5-sub-label"><strong>Total</strong></td>
            <td className="t5-td-right t5-sub-colon">:</td>
            <td className="t5-td-right"></td>
            <td className="t5-td-right"><strong>100.00</strong></td>
          </tr> */}
          <tr>
            <td className="t5-td-left"></td>
            <td className="t5-td-left t5-sub-label">Received</td>
            <td className="t5-td-right t5-sub-colon">:</td>
            <td className="t5-td-right"></td>
            <td className="t5-td-right">0.00</td>
          </tr>
          <tr>
            <td className="t5-td-left"></td>
            <td className="t5-td-left t5-sub-label">Balance</td>
            <td className="t5-td-right t5-sub-colon">:</td>
            <td className="t5-td-right"></td>
            <td className="t5-td-right">100.00</td>
          </tr>

          <tr>
            <td colSpan="5"><hr className="t5-dashed t5-no-margin" /></td>
          </tr>
        </tbody>
      </table>

      {/* TERMS */}
      <div
        className="t5-terms"
        onClick={onTermsClick}
        style={{ cursor: 'pointer', position: 'relative' }}
      >
        <p className="t5-terms-label">Terms &amp; Conditions</p>
        <p className="t5-terms-text">{terms}</p>
        <span className="t5-edit-icon">✎</span>
      </div>

    </div>
  )
}

export default ThermalTheme1