<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            background: #f3f3f3;
            color: #334;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .top-strip {
            height: 42px;
            background: #e9e9e9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 18px 0 0;
            border-bottom: 1px solid #dbdbdb;
        }

        .fake-tab {
            height: 100%;
            min-width: 330px;
            background: #fff;
            border-radius: 0 0 14px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 18px 0 16px;
            color: #333;
            font-size: 16px;
            font-weight: 400;
        }

        .tab-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 18px;
            color: #6f7680;
        }

        .tab-close {
            font-size: 16px;
            font-weight: 600;
        }

        .tab-plus {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background: #0d76de;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            line-height: 1;
        }

        .top-icons {
            display: flex;
            align-items: center;
            gap: 30px;
            color: #60717e;
            font-size: 24px;
        }

        .top-icons span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
        }

        .page-head {
            height: 79px;
            background: #fff;
            border-bottom: 1px solid #d9d9d9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
        }

        .page-head h1 {
            margin: 0;
            font-size: 27px;
            font-weight: 600;
            color: #164b74;
        }

        .page-head-right {
            display: flex;
            align-items: center;
            gap: 11px;
            color: #244b69;
            font-size: 16px;
            font-weight: 400;
        }

        .page-head-right input {
            width: 24px;
            height: 24px;
            margin: 0;
        }

        .save-close {
            color: #006fe7;
            font-weight: 600;
            text-decoration: none;
        }

        .layout {
            display: grid;
            grid-template-columns: 300px 1fr 300px;
            min-height: calc(100vh - 120px);
        }

        .sidebar {
            background: #efefef;
            border-right: 1px solid #dddddd;
            display: flex;
            flex-direction: column;
        }

        .sidebar-section-title {
            padding: 20px 24px;
            font-size: 16px;
            font-weight: 600;
            color: #545454;
            border-bottom: 1px solid #dedede;
            background: #fafafa;
        }

        .group-header {
            padding: 18px 20px;
            background: #fff;
            border-top: 1px solid #e3e3e3;
            border-bottom: 1px solid #e3e3e3;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 16px;
            font-weight: 600;
            color: #2f536a;
            cursor: pointer;
        }

        .theme-list {
            list-style: none;
            margin: 0;
            padding: 0;
            background: #fff;
            max-height: 339px;
            overflow-y: auto;
            transition: max-height 0.25s ease, opacity 0.25s ease;
        }

        .theme-list::-webkit-scrollbar {
            width: 8px;
        }

        .theme-list::-webkit-scrollbar-thumb {
            background: #9b9b9b;
            border-radius: 10px;
        }

        .theme-list li {
            padding: 16px 28px;
            border-bottom: 1px solid #e5e8ec;
            font-size: 16px;
            font-weight: 600;
            color: #355368;
            background: #fff;
        }

        .theme-list li.active {
            background: #dce8f2;
        }

        .theme-dropdown.closed .theme-list {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
        }

        .dropdown-arrow {
            width: 16px;
            height: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.25s ease;
            color: #6e7697;
        }

        .theme-dropdown.closed .dropdown-arrow {
            transform: rotate(-90deg);
        }

        .dropdown-arrow svg {
            width: 14px;
            height: 14px;
            display: block;
        }

        .tip-box {
            margin-top: 6px;
            padding: 0 0 18px;
        }

        .tip-card {
            background: #fff;
            border-top: 1px solid #e1e1e1;
            border-radius: 6px;
            padding: 16px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #335266;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.25;
        }

        .tip-bulb {
            color: #f1b400;
            font-size: 24px;
        }

        .preview-wrap {
            padding: 15px 22px 18px;
            background: #f3f3f3;
            overflow: auto;
        }

        .sheet {
            width: 100%;
            max-width: 1068px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 7px 0 0 #9c9c9c, 0 2px 12px rgba(0, 0, 0, 0.14);
            padding: 18px 22px 26px;
            border: 1px solid #e5e5e5;
            position: relative;
            min-height: 828px;
        }

        .sheet-expand {
            position: absolute;
            top: 1px;
            right: 13px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #a2a2a2;
            font-size: 20px;
        }

        .invoice-title {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #414760;
            margin: 11px 0 14px;
        }

        table.invoice {
            width: 100%;
            border-collapse: collapse;
            color: #454b63;
            table-layout: fixed;
        }

        table.invoice td,
        table.invoice th {
            border: 1px solid #4c5570;
            padding: 6px 6px;
            vertical-align: top;
            font-size: 15px;
        }

        .company-box {
            height: 154px;
        }

        .company-flex {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .logo-box {
            width: 138px;
            height: 136px;
            background: #9d9999;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .company-name {
            font-size: 30px;
            font-weight: 700;
            color: #454a67;
            margin-bottom: 10px;
        }

        .company-meta {
            padding-left: 0;
        }

        .line-bold {
            font-weight: 700;
        }

        .thead th {
            font-weight: 700;
            background: #fafafa;
            text-align: left;
        }

        .align-right {
            text-align: right;
        }

        .muted-row {
            color: #4a5068;
            line-height: 1.45;
        }

        .word-row {
            font-size: 14px;
            padding: 10px 6px;
        }

        .right-panel {
            background: #fafafa;
            border-left: 1px solid #dddddd;
            display: flex;
            flex-direction: column;
        }

        .right-title {
            padding: 15px 22px;
            font-size: 17px;
            font-weight: 700;
            color: #333;
            border-bottom: 1px solid #ededed;
            background: #fff;
        }

        .share-row {
            display: flex;
            justify-content: center;
            gap: 36px;
            padding: 42px 16px 30px;
            background: #fff;
            border-bottom: 1px solid #ededed;
        }

        .share-item {
            text-align: center;
            color: #333;
            font-size: 16px;
            min-width: 70px;
        }

        .share-icon {
            width: 54px;
            height: 54px;
            margin: 0 auto 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .share-icon svg {
            width: 48px;
            height: 48px;
            display: block;
        }

        .share-label {
            font-size: 17px;
            font-weight: 400;
            color: #333;
        }

        .action-row {
            display: flex;
            justify-content: center;
            gap: 16px;
            padding: 28px 10px 22px;
            background: #fff;
            margin-top: 0;
        }

        .action-item {
            border: 0;
            background: transparent;
            padding: 0;
            text-align: center;
            color: #373737;
            width: 82px;
            font-size: 14px;
            line-height: 1.2;
            cursor: pointer;
        }

        .action-icon {
            width: 61px;
            height: 61px;
            border: 1px solid #1275ea;
            border-radius: 13px;
            color: #1275ea;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 31px;
            margin: 0 auto 13px;
            background: #fff;
        }

        .action-icon.primary {
            background: #1275ea;
            color: #fff;
        }

        .action-icon svg {
            width: 30px;
            height: 30px;
            display: block;
        }

        .action-label {
            font-size: 14px;
            font-weight: 400;
            color: #333;
        }

        .action-subtle {
            color: #5b6284;
        }

        .invoice-subtle {
            color: #474d65;
            font-weight: 400;
        }

        .printer-modal {
            position: fixed;
            inset: 0;
            background: rgba(17, 24, 39, 0.32);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 24px;
        }

        .printer-modal.open {
            display: flex;
        }

        .printer-dialog {
            width: 100%;
            max-width: 520px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.25);
        }

        .printer-dialog-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px;
            border-bottom: 1px solid #e6e9ef;
        }

        .printer-dialog-title {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #2a2d34;
        }

        .printer-dialog-close {
            border: 0;
            background: #f2f4f8;
            color: #6c7390;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
        }

        .printer-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .printer-option {
            width: 100%;
            border: 0;
            background: #fff;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 22px;
            cursor: pointer;
            text-align: left;
            color: #474d65;
            font-size: 15px;
            font-weight: 600;
        }

        .printer-option:hover,
        .printer-option.active {
            background: #dbeaf8;
        }

        .printer-icon {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #a7acb6;
            flex-shrink: 0;
        }

        .printer-icon svg {
            width: 26px;
            height: 26px;
            display: block;
        }

        .printer-dialog-foot {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding: 16px 22px 22px;
        }

        .printer-btn {
            border: 1px solid #c9d2df;
            background: #fff;
            color: #384152;
            border-radius: 6px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .printer-btn.primary {
            background: #1473e6;
            border-color: #1473e6;
            color: #fff;
        }

        @media print {
            body * {
                visibility: hidden !important;
            }

            .sheet,
            .sheet * {
                visibility: visible !important;
            }

            .sheet {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: none;
                margin: 0;
                box-shadow: none;
                border: 0;
                min-height: auto;
                padding: 0;
            }

            .sheet-expand {
                display: none !important;
            }

            body.print-thermal .sheet {
                width: 80mm;
                max-width: 80mm;
                padding: 0;
            }

            body.print-thermal .invoice-title {
                font-size: 22px;
                margin: 4px 0 8px;
            }

            body.print-thermal table.invoice td,
            body.print-thermal table.invoice th {
                font-size: 10px;
                padding: 3px;
            }

            body.print-thermal .logo-box {
                width: 72px;
                height: 72px;
                font-size: 15px;
            }

            body.print-thermal .company-name {
                font-size: 18px;
                margin-bottom: 4px;
            }

            body.print-thermal .company-box {
                height: auto;
            }
        }

        @media (max-width: 1400px) {
            .layout {
                grid-template-columns: 260px 1fr 260px;
            }
        }

        @media (max-width: 1100px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .sidebar,
            .right-panel {
                border: none;
            }

            .share-row,
            .action-row {
                justify-content: flex-start;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    @php
        $classicThemes = [
            'Tally Theme',
            'Tax Theme 1',
            'Tax Theme 3',
            'Double Divine',
            'French Elite',
            'Landscape Theme 1',
            'Landscape Theme 2',
        ];

        $vintageThemes = [
            'Tax Theme 2',
            'Tax Theme 4',
            'Tax Theme 5',
            'Tax Theme 6',
            'Theme 1',
            'Theme 2',
            'Theme 3',
            'Theme 4',
            'Thermal Theme 1',
            'Thermal Theme 2',
            'Thermal Theme 3',
            'Thermal Theme 4',
            'Thermal Theme 5',
        ];
    @endphp

    <div class="top-strip">
        <div class="fake-tab">
                <span>fddfhhfd</span>
            <div class="tab-actions">
                <span class="tab-close">×</span>
                <span class="tab-plus">+</span>
            </div>
        </div>
        <div class="top-icons">
            <span>⌘</span>
            <span>▦</span>
            <span>⚙</span>
            <span>✕</span>
        </div>
    </div>

    <div class="page-head">
        <h1>Preview</h1>
        <div class="page-head-right">
            <input type="checkbox">
            <span>Do not show invoice preview again</span>
            <span style="color:#b8b8b8;">|</span>
            <a href="#" class="save-close">Save &amp; Close</a>
        </div>
    </div>

    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-section-title">Select Theme</div>

            <div class="theme-dropdown" data-dropdown>
                <div class="group-header" data-toggle>
                    <span>Classic Themes</span>
                    <span class="dropdown-arrow" aria-hidden="true">
                        <svg viewBox="0 0 16 16">
                            <path d="M3 10.5L8 5.5l5 5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </span>
                </div>
                <ul class="theme-list">
                    @foreach ($classicThemes as $index => $theme)
                        <li class="{{ $index === 0 ? 'active' : '' }}">{{ $theme }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="theme-dropdown closed" data-dropdown>
                <div class="group-header" data-toggle>
                    <span>Vintage Themes</span>
                    <span class="dropdown-arrow" aria-hidden="true">
                        <svg viewBox="0 0 16 16">
                            <path d="M3 10.5L8 5.5l5 5" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </span>
                </div>
                <ul class="theme-list">
                    @foreach ($vintageThemes as $theme)
                        <li>{{ $theme }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="tip-box">
                <div class="tip-card">
                    <span class="tip-bulb">💡</span>
                    <span>Use this theme for a clean and professional look</span>
                </div>
            </div>
        </aside>

        <main class="preview-wrap">
            <div class="sheet">
                <div class="sheet-expand">↗</div>
                <div class="invoice-title">Invoice</div>

                <table class="invoice" id="invoiceTable">
                    <tr>
                        <td colspan="6" class="company-box">
                            <div class="company-flex">
                                <div class="logo-box">LOGO</div>
                                <div class="company-meta">
                                    <div class="company-name">My Company</div>
                                    <div class="invoice-subtle">Phone:&nbsp; <span class="line-bold">3714346914</span></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="line-bold">Bill To:</td>
                        <td colspan="3" class="line-bold">Invoice Details:</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="line-bold muted-row" style="margin-bottom:8px;">fddfhhfd</div>
                            <div class="invoice-subtle muted-row" style="margin-bottom:8px;">23123</div>
                            <div class="invoice-subtle muted-row">Contact No:&nbsp; <span class="line-bold">213123</span></div>
                        </td>
                        <td colspan="3">
                            <div class="invoice-subtle muted-row" style="margin-bottom:8px;">No:&nbsp; <span class="line-bold">3</span></div>
                            <div class="invoice-subtle muted-row">Date:&nbsp; <span class="line-bold">02/04/2026</span></div>
                        </td>
                    </tr>
                    <tr class="thead">
                        <th style="width:54px;">#</th>
                        <th>Item name</th>
                        <th class="align-right" style="width:155px;">Quantity</th>
                        <th class="align-right" style="width:138px;">Unit</th>
                        <th class="align-right" style="width:155px;">Price/ Unit(Rs)</th>
                        <th class="align-right" style="width:163px;">Amount(Rs)</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td class="line-bold">hasnain</td>
                        <td class="align-right invoice-subtle">1</td>
                        <td class="align-right invoice-subtle">Ltr</td>
                        <td class="align-right invoice-subtle">Rs 123.00</td>
                        <td class="align-right invoice-subtle">Rs 123.00</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="line-bold">Total</td>
                        <td class="align-right line-bold">1</td>
                        <td></td>
                        <td></td>
                        <td class="align-right line-bold">Rs 123.00</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="muted-row">Sub Total <span style="float:right;">:</span></td>
                        <td class="align-right invoice-subtle">Rs 123.00</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="line-bold">Total <span style="float:right;">:</span></td>
                        <td class="align-right line-bold">Rs 123.00</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="line-bold">Invoice Amount in Words:</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="word-row">One Hundred Twenty Three Rupees only</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="muted-row">Received <span style="float:right;">:</span></td>
                        <td class="align-right invoice-subtle">Rs 0.00</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="muted-row">Balance <span style="float:right;">:</span></td>
                        <td class="align-right invoice-subtle">Rs 123.00</td>
                    </tr>
                    <tr>
                        <td colspan="6" style="height:13px; background:#fbfbfb;"></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="line-bold">Terms &amp; Conditions:</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="invoice-subtle">Thanks for doing business with us!</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="height:70px;"></td>
                        <td colspan="3" class="line-bold" style="vertical-align:bottom;">For My Company:</td>
                    </tr>
                </table>
            </div>
        </main>

        <aside class="right-panel">
            <div class="right-title">Share Invoice</div>
            <div class="share-row">
                <div class="share-item">
                    <div class="share-icon">
                        <svg viewBox="0 0 64 64" aria-hidden="true">
                            <circle cx="32" cy="32" r="22" fill="#36c95f"></circle>
                            <path d="M24 19c1.5-1.4 3-1.2 4.1.3l2.5 3.8c.8 1.2.8 2.3-.3 3.2l-1.6 1.3c-.6.5-.7 1-.3 1.7 1.7 2.8 3.9 5 6.7 6.7.7.4 1.2.3 1.7-.3l1.3-1.6c.9-1.1 2-1.1 3.2-.3l3.8 2.5c1.5 1 1.7 2.6.3 4.1-1.4 1.5-3.2 2.3-5.6 2.1-4.4-.3-8.6-2.7-12.6-6.8-4.1-4.1-6.4-8.2-6.8-12.6-.2-2.4.6-4.2 2.1-5.6z" fill="#fff"></path>
                        </svg>
                    </div>
                    <div class="share-label">Whatsapp</div>
                </div>
                <div class="share-item">
                    <div class="share-icon">
                        <svg viewBox="0 0 64 64" aria-hidden="true">
                            <path d="M12 18h40v28H12z" fill="#fff"></path>
                            <path d="M12 18l20 16 20-16" fill="none" stroke="#db493b" stroke-width="6"></path>
                            <path d="M12 46V18l12 10" fill="none" stroke="#db493b" stroke-width="6"></path>
                            <path d="M52 46V18L40 28" fill="none" stroke="#db493b" stroke-width="6"></path>
                        </svg>
                    </div>
                    <div class="share-label">Gmail</div>
                </div>
            </div>

            <div class="action-row">
                <button type="button" class="action-item" id="downloadPdfBtn">
                    <div class="action-icon">
                        <svg viewBox="0 0 64 64" aria-hidden="true">
                            <path d="M32 14v24" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"></path>
                            <path d="M22 30l10 10 10-10" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M18 48h28" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"></path>
                        </svg>
                    </div>
                    <div class="action-label">Download PDF</div>
                </button>
                <button type="button" class="action-item" id="thermalPrintBtn">
                    <div class="action-icon">
                        <svg viewBox="0 0 64 64" aria-hidden="true">
                            <rect x="20" y="12" width="24" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="4"></rect>
                            <rect x="16" y="24" width="32" height="24" rx="4" fill="none" stroke="currentColor" stroke-width="4"></rect>
                            <path d="M24 34h16M24 40h12" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
                        </svg>
                    </div>
                    <div class="action-label">Print Invoice <span class="action-subtle">(Thermal)</span></div>
                </button>
                <button type="button" class="action-item" id="normalPrintBtn">
                    <div class="action-icon primary">
                        <svg viewBox="0 0 64 64" aria-hidden="true">
                            <rect x="20" y="10" width="24" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="4"></rect>
                            <rect x="14" y="24" width="36" height="18" rx="4" fill="none" stroke="currentColor" stroke-width="4"></rect>
                            <rect x="20" y="38" width="24" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="4"></rect>
                            <circle cx="45" cy="30" r="2.5" fill="currentColor"></circle>
                        </svg>
                    </div>
                    <div class="action-label">Print Invoice <span class="action-subtle">(Normal)</span></div>
                </button>
            </div>
        </aside>
    </div>

    <div class="printer-modal" id="thermalPrinterModal">
        <div class="printer-dialog" role="dialog" aria-modal="true" aria-labelledby="thermalPrinterTitle">
            <div class="printer-dialog-head">
                <h2 class="printer-dialog-title" id="thermalPrinterTitle">Choose Thermal Printer</h2>
                <button type="button" class="printer-dialog-close" id="closePrinterModal" aria-label="Close">×</button>
            </div>
            <ul class="printer-list">
                <li>
                    <button type="button" class="printer-option active" data-printer="OneNote for Windows 10">
                        <span class="printer-icon" aria-hidden="true">
                            <svg viewBox="0 0 64 64">
                                <rect x="20" y="10" width="24" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="4"></rect>
                                <rect x="14" y="24" width="36" height="18" rx="4" fill="none" stroke="currentColor" stroke-width="4"></rect>
                                <rect x="20" y="38" width="24" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="4"></rect>
                            </svg>
                        </span>
                        <span>OneNote for Windows 10</span>
                    </button>
                </li>
                <li>
                    <button type="button" class="printer-option" data-printer="Microsoft XPS Document Writer">
                        <span class="printer-icon" aria-hidden="true">
                            <svg viewBox="0 0 64 64">
                                <rect x="20" y="10" width="24" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="4"></rect>
                                <rect x="14" y="24" width="36" height="18" rx="4" fill="none" stroke="currentColor" stroke-width="4"></rect>
                                <rect x="20" y="38" width="24" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="4"></rect>
                            </svg>
                        </span>
                        <span>Microsoft XPS Document Writer</span>
                    </button>
                </li>
                <li>
                    <button type="button" class="printer-option" data-printer="Microsoft Print to PDF">
                        <span class="printer-icon" aria-hidden="true">
                            <svg viewBox="0 0 64 64">
                                <rect x="20" y="10" width="24" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="4"></rect>
                                <rect x="14" y="24" width="36" height="18" rx="4" fill="none" stroke="currentColor" stroke-width="4"></rect>
                                <rect x="20" y="38" width="24" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="4"></rect>
                            </svg>
                        </span>
                        <span>Microsoft Print to PDF</span>
                    </button>
                </li>
                <li>
                    <button type="button" class="printer-option" data-printer="Fax">
                        <span class="printer-icon" aria-hidden="true">
                            <svg viewBox="0 0 64 64">
                                <rect x="20" y="10" width="24" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="4"></rect>
                                <rect x="14" y="24" width="36" height="18" rx="4" fill="none" stroke="currentColor" stroke-width="4"></rect>
                                <rect x="20" y="38" width="24" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="4"></rect>
                            </svg>
                        </span>
                        <span>Fax</span>
                    </button>
                </li>
            </ul>
            <div class="printer-dialog-foot">
                <button type="button" class="printer-btn" id="cancelPrinterBtn">Cancel</button>
                <button type="button" class="printer-btn primary" id="confirmThermalPrintBtn">Print</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        document.querySelectorAll('[data-dropdown]').forEach(function (dropdown) {
            var toggle = dropdown.querySelector('[data-toggle]');
            if (!toggle) return;

            toggle.addEventListener('click', function () {
                dropdown.classList.toggle('closed');
            });
        });

        var body = document.body;
        var sheet = document.querySelector('.sheet');
        var downloadPdfBtn = document.getElementById('downloadPdfBtn');
        var normalPrintBtn = document.getElementById('normalPrintBtn');
        var thermalPrintBtn = document.getElementById('thermalPrintBtn');
        var thermalPrinterModal = document.getElementById('thermalPrinterModal');
        var closePrinterModal = document.getElementById('closePrinterModal');
        var cancelPrinterBtn = document.getElementById('cancelPrinterBtn');
        var confirmThermalPrintBtn = document.getElementById('confirmThermalPrintBtn');
        var printerOptions = Array.from(document.querySelectorAll('.printer-option'));

        function setPrintMode(mode) {
            body.classList.remove('print-normal', 'print-thermal');
            if (mode) {
                body.classList.add(mode);
            }
        }

        function doPrint(mode) {
            setPrintMode(mode);
            window.print();
            setTimeout(function () {
                setPrintMode('');
            }, 300);
        }

        function openPrinterModal() {
            if (thermalPrinterModal) {
                thermalPrinterModal.classList.add('open');
            }
        }

        function closePrinterChooser() {
            if (thermalPrinterModal) {
                thermalPrinterModal.classList.remove('open');
            }
        }

        printerOptions.forEach(function (option) {
            option.addEventListener('click', function () {
                printerOptions.forEach(function (item) {
                    item.classList.remove('active');
                });
                option.classList.add('active');
            });
        });

        if (downloadPdfBtn) {
            downloadPdfBtn.addEventListener('click', function () {
                if (window.html2pdf && sheet) {
                    var exportNode = sheet.cloneNode(true);
                    var expandIcon = exportNode.querySelector('.sheet-expand');
                    if (expandIcon) {
                        expandIcon.remove();
                    }

                    window.html2pdf()
                        .set({
                            margin: [8, 8, 8, 8],
                            filename: 'invoice.pdf',
                            image: { type: 'jpeg', quality: 0.98 },
                            html2canvas: { scale: 2, useCORS: true },
                            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                        })
                        .from(exportNode)
                        .save();
                } else {
                    doPrint('print-normal');
                }
            });
        }

        if (normalPrintBtn) {
            normalPrintBtn.addEventListener('click', function () {
                doPrint('print-normal');
            });
        }

        if (thermalPrintBtn) {
            thermalPrintBtn.addEventListener('click', openPrinterModal);
        }

        if (closePrinterModal) {
            closePrinterModal.addEventListener('click', closePrinterChooser);
        }

        if (cancelPrinterBtn) {
            cancelPrinterBtn.addEventListener('click', closePrinterChooser);
        }

        if (confirmThermalPrintBtn) {
            confirmThermalPrintBtn.addEventListener('click', function () {
                closePrinterChooser();
                doPrint('print-thermal');
            });
        }

        if (thermalPrinterModal) {
            thermalPrinterModal.addEventListener('click', function (event) {
                if (event.target === thermalPrinterModal) {
                    closePrinterChooser();
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closePrinterChooser();
            }
        });
    </script>
</body>
</html>
