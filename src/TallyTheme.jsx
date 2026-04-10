import './TallyTheme.css'

const TallyTheme = ({ businessInfo, invoiceData, onCompanyClick, signature, onSignatureClick, selectedColor, terms, onTermsClick, logo, onLogoClick }) => {
  const items = invoiceData?.items?.length ? invoiceData.items : [
    { name: 'Sample Item', qty: 1, rate: 100, amount: 100 }
  ]
  const totalQty = items.reduce((sum, item) => sum + Number(item.qty || 0), 0)
  const subtotal = Number(invoiceData?.subtotal ?? items.reduce((sum, item) => sum + Number(item.amount || 0), 0))
  const total = Number(invoiceData?.total ?? subtotal)
  const rawBalance = Number(invoiceData?.balance ?? invoiceData?.balance_amount ?? 0)
  const receivedFromBalance = Math.max(total - rawBalance, 0)
  const received = Number(invoiceData?.received ?? invoiceData?.received_amount ?? receivedFromBalance ?? 0)
  const balance = Number(invoiceData?.balance ?? invoiceData?.balance_amount ?? Math.max(total - received, 0))
  const billTo = invoiceData?.billTo || 'Walk-in Customer'
  const invoiceNo = invoiceData?.invoiceNo || '3'
  const invoiceDate = invoiceData?.date || '09/04/2026'
  const summaryTotal = received > 0 ? received : total
  const summarySubtotal = summaryTotal
  const amountInWords = numberToRupeesWords(summaryTotal)
  const formatCurrency = (value) => `Rs ${Number(value || 0).toFixed(2)}`

  function numberToRupeesWords(value) {
    const amount = Number(value || 0)
    if (!isFinite(amount)) return 'Zero Rupees only'

    const integerPart = Math.floor(Math.abs(amount))
    const decimalPart = Math.round((Math.abs(amount) - integerPart) * 100)

    const words = integerPart === 0 ? 'Zero' : numberToIndianWords(integerPart)
    const paisaWords = decimalPart > 0 ? ` and ${numberToIndianWords(decimalPart)} Paisa` : ''
    const rupeesWord = integerPart === 1 ? 'Rupee' : 'Rupees'

    return `${words} ${rupeesWord}${paisaWords} only`
  }

  function numberToIndianWords(num) {
    const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen']
    const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety']

    const toWordsBelowHundred = (n) => {
      if (n < 20) return ones[n]
      const t = Math.floor(n / 10)
      const o = n % 10
      return `${tens[t]}${o ? ' ' + ones[o] : ''}`.trim()
    }

    const toWordsBelowThousand = (n) => {
      const h = Math.floor(n / 100)
      const rest = n % 100
      const head = h ? `${ones[h]} Hundred` : ''
      const tail = rest ? `${head ? ' ' : ''}${toWordsBelowHundred(rest)}` : ''
      return `${head}${tail}`.trim()
    }

    const parts = []
    let remaining = num

    const crore = Math.floor(remaining / 10000000)
    if (crore) {
      parts.push(`${toWordsBelowThousand(crore)} Crore`)
      remaining %= 10000000
    }

    const lakh = Math.floor(remaining / 100000)
    if (lakh) {
      parts.push(`${toWordsBelowThousand(lakh)} Lakh`)
      remaining %= 100000
    }

    const thousand = Math.floor(remaining / 1000)
    if (thousand) {
      parts.push(`${toWordsBelowThousand(thousand)} Thousand`)
      remaining %= 1000
    }

    if (remaining) {
      parts.push(toWordsBelowThousand(remaining))
    }

    return parts.join(' ').trim()
  }

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
              <td>{formatCurrency(item.amount)}</td>
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
