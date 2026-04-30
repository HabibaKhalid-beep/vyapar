<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Agarri List</title>
    <style>
        body {
            margin: 0;
            padding: 22px;
            font-family: "Noto Naskh Arabic", "Segoe UI", Tahoma, Arial, sans-serif;
            color: #16263d;
            background: #ffffff;
            font-size: 14px;
            line-height: 1.65;
        }
        .header {
            text-align: center;
            margin-bottom: 14px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            color: #17365d;
        }
        .header p {
            margin: 6px 0 0;
            color: #5f6f85;
            font-size: 12px;
        }
        .filters {
            margin-bottom: 16px;
            padding: 10px 12px;
            border: 1px solid #dbe4ef;
            border-radius: 10px;
            background: #f7faff;
            font-size: 12px;
            color: #42546b;
        }
        .city-block {
            margin-bottom: 22px;
            page-break-inside: avoid;
        }
        .city-title {
            margin: 0 0 10px;
            padding: 10px 14px;
            background: #17365d;
            color: #fff;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 700;
        }
        .party-block {
            margin-bottom: 14px;
            border: 1px solid #dce5ef;
            border-radius: 12px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .party-head {
            padding: 12px 14px;
            background: #f8fbff;
            border-bottom: 1px solid #e7edf5;
        }
        .party-name {
            font-size: 21px;
            font-weight: 800;
            margin-bottom: 8px;
            color: #14253b;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .info-grid td {
            width: 25%;
            padding: 6px 8px;
            vertical-align: top;
            font-size: 12px;
        }
        .label {
            display: block;
            font-size: 11px;
            color: #64748b;
            margin-bottom: 2px;
        }
        .value {
            display: block;
            font-weight: 700;
            color: #14253b;
            word-break: break-word;
        }
        .record {
            padding: 12px 14px;
            border-bottom: 1px dashed #e4eaf2;
        }
        .record:last-child {
            border-bottom: 0;
        }
        .record-top {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .record-top > div {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .record-title {
            font-size: 15px;
            font-weight: 800;
            color: #17365d;
            margin-bottom: 4px;
        }
        .items {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .items li {
            margin-bottom: 3px;
        }
        .tone-line {
            font-size: 13px;
            font-weight: 800;
            color: #17365d;
            margin-top: 6px;
        }
        .record-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .record-grid td {
            width: 25%;
            padding: 6px 8px;
            vertical-align: top;
            border: 1px solid #e5ebf3;
            background: #fff;
        }
        .tone {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
        }
        .tone-soft {
            background: #e7f8ee;
            color: #0b8b50;
        }
        .tone-medium {
            background: #fff3da;
            color: #b7791f;
        }
        .tone-normal {
            background: #eef4ff;
            color: #2b6cb0;
        }
        .tone-strict {
            background: #fde7e7;
            color: #c53030;
        }
        .tone-very-strict {
            background: #ffd9d9;
            color: #9b1c1c;
        }
        .party-total {
            padding: 10px 14px;
            background: #f8fbff;
            border-top: 1px solid #e7edf5;
            text-align: left;
            font-size: 14px;
            font-weight: 800;
        }
        .empty-state {
            padding: 30px;
            text-align: center;
            border: 1px dashed #dce5ef;
            border-radius: 12px;
            color: #5c6b80;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>اگرری لسٹ / کھاتہ</h1>
        <p>تیار کردہ: {{ $generatedAt->format('d/m/Y h:i A') }}</p>
    </div>

    <div class="filters">
        تاریخ: {{ $filters['from'] ?: 'تمام' }} تا {{ $filters['to'] ?: 'تمام' }}
        |
        پارٹی: {{ $filters['party_name'] ?: ($filters['party_id'] ?: 'تمام') }}
        |
        بروکر: {{ $filters['broker_id'] ?: 'تمام' }}
        |
        شہر: {{ $filters['city'] ?: 'تمام' }}
    </div>

    @forelse($sales as $cityGroup)
        <section class="city-block">
            <h2 class="city-title">شہر: {{ $cityGroup['city_name'] }}</h2>

            @foreach($cityGroup['parties'] as $partyGroup)
                <article class="party-block">
                    <div class="party-head">
                        <div class="party-name">{{ $partyGroup['party_name'] }}</div>
                        <table class="info-grid">
                            <tr>
                                <td>
                                    <span class="label">پارٹی موبائل</span>
                                    <span class="value">{{ $partyGroup['party_phone'] ?: 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="label">واٹس ایپ</span>
                                    <span class="value">{{ $partyGroup['party_whatsapp'] ?: 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="label">پی ٹی سی ایل</span>
                                    <span class="value">{{ $partyGroup['party_ptcl'] ?: 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="label">پارٹی</span>
                                    <span class="value">{{ $partyGroup['party_name'] }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <span class="label">ایڈریس</span>
                                    <span class="value">{{ $partyGroup['party_address'] ?: 'N/A' }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    @foreach($partyGroup['rows'] as $row)
                        <div class="record">
                            <div class="record-top">
                                <div>
                                    <div class="record-title">بل نمبر: {{ $row['bill_number'] }}</div>
                                    <div>
                                        @if(!empty($row['items']))
                                            <ul class="items">
                                                @foreach($row['items'] as $item)
                                                    <li>{{ $item['name'] }} (Rs {{ number_format($item['rate'], 2) }})</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <div class="record-title">بروکر</div>
                                    <div>{{ $row['broker_name'] ?: '-' }} | {{ $row['broker_phone'] ?: '-' }}</div>
                                    <div class="tone-line">
                                        <span class="tone tone-{{ $row['tone_class'] }}">
                                            {{ $row['tone_label'] }}
                                        </span>
                                        | لیٹ دن: {{ (int) $row['late_days'] }}
                                    </div>
                                </div>
                            </div>

                            <table class="record-grid">
                                <tr>
                                    <td>
                                        <span class="label">سودا تاریخ</span>
                                        <span class="value">{{ $row['sale_date'] ?: '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="label">ڈیو تاریخ</span>
                                        <span class="value">{{ $row['due_date'] ?: '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="label">ڈیل ڈیز</span>
                                        <span class="value">{{ $row['deal_days'] ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="label">لیٹ دن</span>
                                        <span class="value">{{ (int) $row['late_days'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="label">کل رقم</span>
                                        <span class="value">Rs {{ number_format($row['grand_total'], 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="label">وصول</span>
                                        <span class="value">Rs {{ number_format($row['received_amount'], 2) }}</span>
                                    </td>
                                    <td colspan="2">
                                        <span class="label">بیلنس</span>
                                        <span class="value">Rs {{ number_format($row['balance'], 2) }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    @endforeach

                    <div class="party-total">
                        کل بقایا: Rs {{ number_format($partyGroup['total_balance'], 2) }}
                    </div>
                </article>
            @endforeach
        </section>
    @empty
        <div class="empty-state">اس فلٹر کے مطابق کوئی pending bill موجود نہیں ہے۔</div>
    @endforelse
</body>
</html>
