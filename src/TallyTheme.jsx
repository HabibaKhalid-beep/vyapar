import './TallyTheme.css'
import { getInvoiceViewModel } from './invoiceData'

const TallyTheme = ({ businessInfo, invoiceData, onCompanyClick, signature, onSignatureClick, selectedColor, terms, onTermsClick, logo, onLogoClick }) => {
  const view = getInvoiceViewModel(invoiceData)
  const items = view.items
  const totalQty = view.totalQty
  const total = view.total
  const received = view.received
  const balance = view.balance
  const billTo = view.billTo
  const invoiceNo = view.invoiceNo
  const invoiceDate = view.date
  const summaryTotal = view.paidTotal
  const summarySubtotal = view.subtotalPaid
  const amountInWords = view.amountInWords
  const isDeliveryChallan = String(view.documentType || view.title || '').toLowerCase().includes('delivery_challan')
    || String(view.title || '').toLowerCase().includes('delivery challan')
  const formatCurrency = (value) => `Rs ${Number(value || 0).toFixed(2)}`

  return (
    <div className="tally-wrapper">

      <h2 className="tally-title">{invoiceData?.title || 'Invoice'}</h2>

      <div className="tally-header" onClick={onCompanyClick} style={{ cursor: 'pointer' }}>
        <div
          className="tally-logo"
          onClick={(e) => { e.stopPropagation(); onLogoClick() }}
          style={{ cursor: 'pointer' }}
        >
          {logo
            ? <img src={logo} alt="logo" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
            : 'LOGO'
          }
        </div>
        <div className="tally-company">
          <h1>{businessInfo.name}</h1>
          <p>Phone: {businessInfo.phone}</p>
        </div>
      </div>

      {isDeliveryChallan ? (
        <div className="tally-transport-info">
          <div className="tally-transport-col">
            <p className="tally-label">Delivery Challan for</p>
            <p className="tally-value">{view.deliveryChallanFor}</p>
            <p className="tally-transport-line"><strong>Contact No.:</strong> {view.deliveryChallanPhone}</p>
          </div>
          <div className="tally-transport-col">
            <p className="tally-label">Transportation Details</p>
            <p className="tally-transport-line"><strong>BROKAR : :</strong> {view.transportBrokerName}</p>
            <p className="tally-transport-line"><strong>Transport Name/Goodz:</strong> {view.transportName}</p>
            <p className="tally-transport-line"><strong>Bilti no / Gari NO3#:</strong> {view.biltiGariNo || view.biltiNo}</p>
            <p className="tally-transport-line"><strong>City ::</strong> {view.transportCity}</p>
            {view.transportDetail ? <p className="tally-transport-line"><strong>Detail:</strong> {view.transportDetail}</p> : null}
          </div>
          <div className="tally-transport-col">
            <p className="tally-label">Challan Details</p>
            <p className="tally-transport-line"><strong>Challan No.</strong> {invoiceNo}</p>
            <p className="tally-transport-line"><strong>Date:</strong> {invoiceDate}</p>
            <p className="tally-transport-line"><strong>Delivery Persan:</strong> {view.deliveryPerson}</p>
            <p className="tally-transport-line">{view.transportBrokerCity}</p>
          </div>
        </div>
      ) : (
        <div className="tally-info">
          <div className="tally-bill">
            <p className="tally-label">Bill To:</p>
            <p className="tally-value">{billTo}</p>
          </div>
          <div className="tally-invoice-details">
            <p className="tally-label">Invoice Details:</p>
            <p>No: <strong>{invoiceNo}</strong></p>
            <p>Date: <strong>{invoiceDate}</strong></p>
            {invoiceData?.referenceNo ? <p>Ref: <strong>{invoiceData.referenceNo}</strong></p> : null}
          </div>
        </div>
      )}

      <table className="tally-table">
        <thead>
          <tr style={{ backgroundColor: selectedColor }}>
            <th>#</th>
            <th>Item name</th>
            <th>Quantity</th>
            <th>Price/ Unit(Rs)</th>
            <th>Amount(Rs)</th>
          </tr>
        </thead>
        <tbody>
          {items.map((item, index) => (
            <tr key={`${item.name}-${index}`}>
              <td>{index + 1}</td>
              <td><strong>{item.name}</strong></td>
              <td>{item.qty}</td>
              <td>{formatCurrency(item.rate)}</td>
              <td>{formatCurrency(item.amount ?? item.amt)}</td>
            </tr>
          ))}
          <tr className="tally-total-row">
            <td></td>
            <td><strong>Total</strong></td>
            <td><strong>{totalQty}</strong></td>
            <td></td>
            <td><strong>{formatCurrency(total)}</strong></td>
          </tr>
        </tbody>
      </table>

      <div className="tally-summary">
        <div className="tally-summary-row">
          <span>Sub Total</span>
          <span>:</span>
          <span>{formatCurrency(summarySubtotal)}</span>
        </div>
        <div className="tally-summary-row bold">
          <span>Total</span>
          <span>:</span>
          <span>{formatCurrency(summaryTotal)}</span>
        </div>
        <div className="tally-summary-row">
          <span>Received</span>
          <span>:</span>
          <span>{formatCurrency(received)}</span>
        </div>
        <div className="tally-summary-row">
          <span>Balance</span>
          <span>:</span>
          <span>{formatCurrency(balance)}</span>
        </div>
      </div>

      <div className="tally-words">
        <p className="tally-label">Invoice Amount in Words:</p>
        <p>{amountInWords}</p>
      </div>

      <div
        className="tally-terms"
        onClick={onTermsClick}
        style={{ cursor: 'pointer', position: 'relative' }}
      >
        <p className="tally-label">Terms & Conditions:</p>
        <p>{terms}</p>
        <span className="edit-icon">Edit</span>
      </div>

      <div className="tally-sign">
        <p className="tally-label">For {businessInfo.name}:</p>
        <div
          className="tally-sign-box"
          onClick={onSignatureClick}
          style={{ cursor: 'pointer' }}
        >
          {signature
            ? <img src={signature} alt="signature" style={{ height: '50px' }} />
            : <p>Authorized Signatory</p>
          }
        </div>
      </div>

    </div>
  )
}

export default TallyTheme
