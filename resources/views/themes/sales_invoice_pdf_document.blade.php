@php
    $invoice = $invoicePreviewData ?? [];
    $theme = $themeConfig ?? ['mode' => 'regular', 'variant' => 'classicA', 'name' => 'Telly Theme'];
    $accent = $accent ?? '#1f4e79';
    $accent2 = $accent2 ?? '#ff981f';
    $items = $invoice['items'] ?? [];
    $subtotal = (float) ($invoice['subtotal'] ?? collect($items)->sum('amt'));
    $discount = (float) ($invoice['discount'] ?? 0);
    $tax = (float) ($invoice['taxAmount'] ?? 0);
    $total = (float) ($invoice['total'] ?? max($subtotal + $tax - $discount, 0));
    $received = (float) ($invoice['received'] ?? 0);
    $balance = (float) ($invoice['balance'] ?? max($total - $received, 0));
    $totalQty = collect($items)->sum(fn ($item) => (float) ($item['qty'] ?? 0));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice['invoiceNo'] ?? 'Invoice' }}</title>
    <style>
        @page { margin: 18px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; margin: 0; }
        .doc { width: 100%; }
        .title { text-align: center; font-size: 30px; font-weight: 700; color: {{ $accent }}; margin-bottom: 14px; }
        .head { width: 100%; border: 1px solid #64748b; border-collapse: collapse; margin-bottom: 12px; }
        .head td { border: 1px solid #64748b; padding: 10px; vertical-align: top; }
        .logo { width: 70px; height: 48px; border: 1px solid #cbd5e1; color: #94a3b8; text-align: center; line-height: 48px; }
        .company { font-size: 18px; font-weight: 700; color: {{ $accent }}; }
        .label { font-weight: 700; color: {{ $accent }}; }
        .meta strong { color: {{ $accent }}; }
        .grid { width: 100%; border: 1px solid #64748b; border-collapse: collapse; margin-bottom: 12px; }
        .grid td { border: 1px solid #64748b; padding: 8px; vertical-align: top; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .table th, .table td { border: 1px solid #64748b; padding: 7px 6px; }
        .table th { background: {{ $accent }}; color: #fff; text-align: center; }
        .amount-box { width: 100%; border-collapse: collapse; }
        .amount-box td { border: 1px solid #64748b; padding: 7px 8px; }
        .amount-box .highlight td { background: {{ $accent }}; color: #fff; font-weight: 700; }
        .two-col { width: 100%; }
        .two-col td { vertical-align: top; width: 50%; }
        .section-title { font-size: 16px; font-weight: 700; color: {{ $accent }}; margin: 0 0 8px; }
        .double-head { position: relative; height: 120px; margin-bottom: 16px; }
        .double-left { position: absolute; left: 0; top: 0; width: 58%; height: 118px; background: {{ $accent }}; color: #fff; border-bottom-right-radius: 48px; padding: 18px 22px; }
        .double-right { position: absolute; right: 0; top: 0; width: 72%; height: 74px; background: {{ $accent2 }}; color: #fff; padding: 24px 30px 0 0; text-align: right; border-bottom-left-radius: 64px; }
        .elite-banner { background: {{ $accent }}; color: #fff; padding: 10px 12px; font-size: 28px; font-weight: 800; }
        .elite-store { color: {{ $accent }}; font-size: 28px; font-weight: 700; margin: 10px 0; }
        .thermal { width: 280px; margin: 0 auto; border: 1px solid {{ $accent }}; padding: 12px; }
        .thermal .line { border-top: 2px dotted {{ $accent }}; margin: 8px 0; }
        .thermal table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .thermal th { text-align: left; border-bottom: 2px dotted {{ $accent }}; padding-bottom: 6px; }
        .thermal td { padding: 6px 0; }
        .right { text-align: right; }
        .accent-block { background: {{ in_array($theme['variant'], ['doubleDivine']) ? $accent2 : $accent }}; color: #fff; }
    </style>
</head>
<body>
@if (($theme['mode'] ?? 'regular') === 'thermal')
    <div class="thermal">
        <div style="text-align:center;font-weight:700;">{{ $invoice['businessName'] ?? 'My Company' }}</div>
        <div style="text-align:center;">Ph.No.: {{ $invoice['phone'] ?? '' }}</div>
        <div class="line"></div>
        <div style="display:flex;justify-content:space-between;"><span>{{ $invoice['billTo'] ?? '' }}</span><span style="font-weight:700;">Invoice</span></div>
        <div style="display:flex;justify-content:space-between;font-size:11px;"><span>Invoice No.: {{ $invoice['invoiceNo'] ?? '' }}</span><span>{{ $invoice['date'] ?? '' }}</span></div>
        <div class="line"></div>
        <table>
            <thead><tr><th>#</th><th>Item</th><th class="right">Qty</th><th class="right">Amt</th></tr></thead>
            <tbody>
            @foreach ($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['name'] ?? '' }}</td>
                    <td class="right">{{ $item['qty'] ?? '' }}</td>
                    <td class="right">{{ number_format((float) ($item['amt'] ?? 0), 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="line"></div>
        <div style="display:flex;justify-content:space-between;"><strong>Total</strong><strong>{{ number_format($total, 2) }}</strong></div>
        <div style="display:flex;justify-content:space-between;"><span>Received</span><span>{{ number_format($received, 2) }}</span></div>
        <div style="display:flex;justify-content:space-between;"><span>Balance</span><span>{{ number_format($balance, 2) }}</span></div>
    </div>
@elseif (($theme['variant'] ?? '') === 'doubleDivine')
    <div class="doc">
        <div class="double-head">
            <div class="double-left">
                <div class="logo">LOGO</div>
                <div style="font-size:20px;">{{ $invoice['businessName'] ?? 'My Company' }}</div>
            </div>
            <div class="double-right">{{ $invoice['phone'] ?? '' }}</div>
        </div>
        <table class="two-col" style="margin-bottom:12px;">
            <tr>
                <td>
                    <div class="section-title" style="color:{{ $accent2 }}">Bill To:</div>
                    <div style="font-size:22px;font-weight:700;">{{ $invoice['billTo'] ?? '' }}</div>
                    <div>{{ $invoice['billAddress'] ?? '' }}</div>
                    <div><strong>Contact No:</strong> {{ $invoice['billPhone'] ?? '' }}</div>
                </td>
                <td style="padding-left:18px;">
                    <div style="font-size:26px;margin-bottom:8px;">Invoice</div>
                    <div><strong>Invoice No.:</strong> {{ $invoice['invoiceNo'] ?? '' }}</div>
                    <div><strong>Date:</strong> {{ $invoice['date'] ?? '' }}</div>
                </td>
            </tr>
        </table>
        <table class="table">
            <thead><tr><th>#</th><th>Item name</th><th>Quantity</th><th>Price / Unit</th><th>Amount</th></tr></thead>
            <tbody>
            @foreach ($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['name'] ?? '' }}</td>
                    <td class="right">{{ $item['qty'] ?? '' }}</td>
                    <td class="right">{{ number_format((float) ($item['rate'] ?? 0), 2) }}</td>
                    <td class="right">{{ number_format((float) ($item['amt'] ?? 0), 2) }}</td>
                </tr>
            @endforeach
            <tr class="accent-block"><td></td><td><strong>Total</strong></td><td class="right"><strong>{{ $totalQty }}</strong></td><td></td><td class="right"><strong>{{ number_format($total, 2) }}</strong></td></tr>
            </tbody>
        </table>
@else
    <div class="doc">
        <div class="title">{{ in_array($theme['variant'] ?? '', ['modernPurple','theme3','classicSale','theme4']) ? 'Sale' : ($invoice['title'] ?? 'Invoice') }}</div>
        @if (($theme['variant'] ?? '') === 'frenchElite')
            <div class="elite-banner">TAX INVOICE</div>
            <div class="elite-store">{{ $invoice['businessName'] ?? 'My Company' }}</div>
        @endif
        <table class="head">
            <tr>
                <td style="width:90px;"><div class="logo">Image</div></td>
                <td>
                    <div class="company">{{ $invoice['businessName'] ?? 'My Company' }}</div>
                    <div>Phone: {{ $invoice['phone'] ?? '' }}</div>
                </td>
                <td class="meta">
                    <div><strong>Invoice No:</strong> {{ $invoice['invoiceNo'] ?? '' }}</div>
                    <div><strong>Date:</strong> {{ $invoice['date'] ?? '' }}</div>
                    <div><strong>Time:</strong> {{ $invoice['time'] ?? '' }}</div>
                    <div><strong>Due Date:</strong> {{ $invoice['dueDate'] ?? '' }}</div>
                </td>
            </tr>
        </table>
        <table class="grid">
            <tr>
                <td>
                    <div class="label">Bill To:</div>
                    <div>{{ $invoice['billTo'] ?? '' }}</div>
                    <div>{{ $invoice['billAddress'] ?? '' }}</div>
                    <div>Phone: {{ $invoice['billPhone'] ?? '' }}</div>
                </td>
                <td>
                    <div class="label">Shipping To</div>
                    <div>{{ $invoice['shipTo'] ?? '' }}</div>
                </td>
                <td>
                    <div class="label">Invoice Details</div>
                    <div>Invoice No.: {{ $invoice['invoiceNo'] ?? '' }}</div>
                </td>
            </tr>
        </table>
        <table class="table">
            <thead>
            <tr>
                <th>#</th><th>Item name</th><th>HSC/SAC</th><th>Quantity</th><th>Price/unit</th><th>Discount</th><th>GST</th><th>Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['name'] ?? '' }}</td>
                    <td>{{ $item['hsn'] ?? '' }}</td>
                    <td>{{ $item['qty'] ?? '' }}</td>
                    <td>{{ number_format((float) ($item['rate'] ?? 0), 2) }}</td>
                    <td>{{ $item['disc'] ?? '' }}</td>
                    <td>{{ $item['gst'] ?? '' }}</td>
                    <td>{{ number_format((float) ($item['amt'] ?? 0), 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td></td><td><strong>Total</strong></td><td></td><td><strong>{{ $totalQty }}</strong></td><td></td><td><strong>{{ number_format($discount, 2) }}</strong></td><td><strong>{{ number_format($tax, 2) }}</strong></td><td><strong>{{ number_format($total, 2) }}</strong></td>
            </tr>
            </tbody>
        </table>
        <table class="two-col">
            <tr>
                <td style="padding-right:10px;">
                    <div class="section-title">Description</div>
                    <div>{{ $invoice['description'] ?? 'Thanks for doing business with us!' }}</div>
                    <div style="height:16px;"></div>
                    <div class="section-title">Bank Details</div>
                    <div>Bank Name: {{ $invoice['bankName'] ?? '' }}</div>
                    <div>Account Holder: {{ $invoice['bankAccountHolder'] ?? '' }}</div>
                    <div>Bank Account No: {{ $invoice['bankAccountNumber'] ?? '' }}</div>
                </td>
                <td style="padding-left:10px;">
                    <table class="amount-box">
                        <tr><td>Sub Total</td><td class="right">{{ number_format($subtotal, 2) }}</td></tr>
                        <tr><td>Discount</td><td class="right">{{ number_format($discount, 2) }}</td></tr>
                        <tr><td>Tax</td><td class="right">{{ number_format($tax, 2) }}</td></tr>
                        <tr class="highlight"><td>Total</td><td class="right">{{ number_format($total, 2) }}</td></tr>
                        <tr><td>Received</td><td class="right">{{ number_format($received, 2) }}</td></tr>
                        <tr><td>Balance</td><td class="right">{{ number_format($balance, 2) }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
@endif
</body>
</html>
