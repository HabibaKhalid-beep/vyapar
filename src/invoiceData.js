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
  const received = Number(invoiceData.received ?? 0)
  const balance = Number(invoiceData.balance ?? Math.max(total - received, 0))

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
    discount,
    taxAmount,
    total,
    received,
    balance,
  }
}

export function formatCurrency(value) {
  return `Rs ${Number(value || 0).toFixed(2)}`
}
