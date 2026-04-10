const fallbackItem = { name: 'Sample Item', hsn: '', qty: 1, unit: '', rate: 100, discount: 0, amount: 100 }

export function getInvoiceViewModel(invoiceData = {}) {
  const items = Array.isArray(invoiceData.items) && invoiceData.items.length
    ? invoiceData.items.map((item) => ({
      name: item.name || 'Item',
      hsn: item.hsn || '',
      qty: Number(item.qty || 0),
      unit: item.unit || '',
      rate: Number(item.rate || 0),
      discount: Number(item.discount || 0),
      amount: Number(item.amount || 0),
    }))
    : [fallbackItem]

  const totalQty = items.reduce((sum, item) => sum + Number(item.qty || 0), 0)
  const subtotal = Number(invoiceData.subtotal ?? items.reduce((sum, item) => sum + Number(item.amount || 0), 0))
  const discount = Number(invoiceData.discount ?? 0)
  const taxAmount = Number(invoiceData.taxAmount ?? 0)
  const total = Number(invoiceData.total ?? Math.max(subtotal + taxAmount - discount, 0))
  const rawBalance = Number(invoiceData.balance ?? invoiceData.balance_amount ?? 0)
  const receivedFromBalance = Math.max(total - rawBalance, 0)
  const received = Number(invoiceData.received ?? invoiceData.received_amount ?? receivedFromBalance ?? 0)
  const paidAmount = received > 0 ? received : total
  const subtotalPaid = paidAmount

  const amountInWords = numberToRupeesWords(paidAmount)
  const balance = Number(invoiceData.balance ?? invoiceData.balance_amount ?? Math.max(total - received, 0))

  return {
    title: invoiceData.title || 'Invoice',
    invoiceNo: invoiceData.invoiceNo || '3',
    date: invoiceData.date || '09/04/2026',
    time: invoiceData.time || '',
    dueDate: invoiceData.dueDate || invoiceData.date || '09/04/2026',
    billTo: invoiceData.billTo || 'Walk-in Customer',
    billAddress: invoiceData.billAddress || '',
    billPhone: invoiceData.billPhone || '',
    shipTo: invoiceData.shipTo || invoiceData.billAddress || '',
    description: invoiceData.description || 'Thanks for doing business with us!',
    bankName: invoiceData.bankName || '',
    bankAccountNumber: invoiceData.bankAccountNumber || '',
    bankAccountHolder: invoiceData.bankAccountHolder || '',
    items,
    totalQty,
    subtotal,
    subtotalPaid,
    discount,
    taxAmount,
    total,
    paidTotal: paidAmount,
    amountInWords,
    received,
    balance,
  }
}

export function formatCurrency(value) {
  return `Rs ${Number(value || 0).toFixed(2)}`
}

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
