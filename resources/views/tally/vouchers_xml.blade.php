<ENVELOPE>
  <HEADER>
    <TALLYREQUEST>Import Data</TALLYREQUEST>
  </HEADER>
  <BODY>
    <IMPORTDATA>
      <REQUESTDESC>
        <REPORTNAME>Vouchers</REPORTNAME>
      </REQUESTDESC>
      <REQUESTDATA>
@foreach ($rows as $row)
        @php
          $amount = number_format((float) ($row['amount'] ?? 0), 2, '.', '');
          $type = (string) ($row['normalized_type'] ?? 'sale');
          $primaryAmount = '-' . $amount;
          $counterAmount = $amount;

          if (in_array($type, ['credit_note', 'purchase'], true)) {
              $primaryAmount = $amount;
              $counterAmount = '-' . $amount;
          }
        @endphp
        <TALLYMESSAGE xmlns:UDF="TallyUDF">
          <VOUCHER VCHTYPE="{{ e($row['voucher_type'] ?? 'Sales') }}" ACTION="Create" OBJVIEW="Accounting Voucher View">
            <DATE>{{ e($row['date_ymd'] ?? now()->format('Ymd')) }}</DATE>
            <NARRATION>{{ e($row['narration'] ?? '') }}</NARRATION>
            <VOUCHERTYPENAME>{{ e($row['voucher_type'] ?? 'Sales') }}</VOUCHERTYPENAME>
            <VOUCHERNUMBER>{{ e($row['invoice_no'] ?? '') }}</VOUCHERNUMBER>
            <PARTYLEDGERNAME>{{ e($row['party_name'] ?? 'Walk-in Customer') }}</PARTYLEDGERNAME>
            <PERSISTEDVIEW>Accounting Voucher View</PERSISTEDVIEW>
            <ISINVOICE>No</ISINVOICE>
            <REFERENCE>{{ e($row['invoice_no'] ?? '') }}</REFERENCE>

            <ALLLEDGERENTRIES.LIST>
              <LEDGERNAME>{{ e($row['primary_ledger'] ?? 'Sales') }}</LEDGERNAME>
              <ISDEEMEDPOSITIVE>{{ strpos($primaryAmount, '-') === 0 ? 'Yes' : 'No' }}</ISDEEMEDPOSITIVE>
              <AMOUNT>{{ $primaryAmount }}</AMOUNT>
            </ALLLEDGERENTRIES.LIST>

            <ALLLEDGERENTRIES.LIST>
              <LEDGERNAME>{{ e($row['counter_ledger'] ?? 'Cash') }}</LEDGERNAME>
              <ISDEEMEDPOSITIVE>{{ strpos($counterAmount, '-') === 0 ? 'Yes' : 'No' }}</ISDEEMEDPOSITIVE>
              <AMOUNT>{{ $counterAmount }}</AMOUNT>
            </ALLLEDGERENTRIES.LIST>
          </VOUCHER>
        </TALLYMESSAGE>
@endforeach
      </REQUESTDATA>
    </IMPORTDATA>
  </BODY>
</ENVELOPE>
