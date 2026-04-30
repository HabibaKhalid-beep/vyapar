@extends('layouts.app')

@section('title', 'Utilities - Close Financial Year')
@section('description', 'Close the current financial year and prepare accounts for the next year.')
@section('page', 'close-financial-year')

@push('styles')
  <style>
    .close-fy-page {
      padding: 28px;
      background: #f4f4f7;
      min-height: calc(100vh - 20px);
    }

    .close-fy-grid {
      display: grid;
      grid-template-columns: 1fr 0.92fr;
      gap: 0;
      background: #fff;
      border: 1px solid #e8edf6;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 18px 34px rgba(46, 74, 121, 0.08);
    }

    .close-fy-panel {
      padding: 26px 28px 30px;
      background: #fff;
    }

    .close-fy-panel + .close-fy-panel {
      border-left: 1px solid #edf1f7;
    }

    .close-fy-panel h2 {
      margin: 0 0 10px;
      color: #333a49;
      font-size: 2rem;
      font-weight: 700;
    }

    .close-fy-panel > p {
      margin: 0 0 18px;
      color: #7b8395;
      font-size: 1rem;
      line-height: 1.5;
    }

    .info-box {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 14px 16px;
      margin-bottom: 22px;
      border-radius: 8px;
      background: #e8f4ff;
      color: #2d6ea7;
      font-weight: 600;
      line-height: 1.45;
    }

    .info-box i {
      font-size: 1.15rem;
      margin-top: 2px;
    }

    .prefix-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 10px;
    }

    .prefix-table th {
      padding: 0 6px 8px;
      color: #727b8d;
      font-size: 0.96rem;
      font-weight: 700;
      text-align: left;
    }

    .prefix-table td {
      padding: 4px 6px;
      vertical-align: middle;
      color: #4b5264;
      font-size: 1rem;
      font-weight: 600;
    }

    .prefix-input,
    .preview-box,
    .closing-date-input {
      width: 100%;
      height: 44px;
      border: 1px solid #d8deea;
      border-radius: 4px;
      padding: 10px 12px;
      outline: none;
      font-size: 1rem;
    }

    .prefix-input:focus,
    .closing-date-input:focus {
      border-color: #5aa6f4;
      box-shadow: 0 0 0 3px rgba(90, 166, 244, 0.14);
    }

    .preview-box {
      background: #dfdfdf;
      color: #4b4f58;
      display: flex;
      align-items: center;
      font-weight: 600;
    }

    .button-row {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: 18px;
    }

    .action-btn {
      border: 0;
      border-radius: 4px;
      padding: 12px 18px;
      font-size: 0.96rem;
      font-weight: 700;
      color: #fff;
      min-width: 170px;
    }

    .action-btn.secondary {
      background: #1d99d3;
    }

    .action-btn.primary {
      background: #148ec7;
    }

    .action-btn.gray {
      background: #a8a8a8;
    }

    .backup-label {
      margin: 20px 0 8px;
      color: #678094;
      font-size: 0.95rem;
      font-weight: 700;
      text-transform: uppercase;
    }

    .backup-row {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 10px;
      align-items: center;
      max-width: 360px;
    }

    .date-field {
      position: relative;
    }

    .date-field i {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #7c879b;
      pointer-events: none;
    }

    .status-chip {
      display: inline-block;
      margin-top: 16px;
      padding: 10px 12px;
      border-radius: 8px;
      background: #f4f8fb;
      color: #5d6b82;
      font-size: 0.95rem;
      font-weight: 600;
    }

    .alert-space {
      margin-bottom: 16px;
    }

    @media (max-width: 1199.98px) {
      .close-fy-grid {
        grid-template-columns: 1fr;
      }

      .close-fy-panel + .close-fy-panel {
        border-left: 0;
        border-top: 1px solid #edf1f7;
      }
    }

    @media (max-width: 767.98px) {
      .close-fy-page {
        padding: 16px;
      }

      .close-fy-panel {
        padding: 18px 16px 22px;
      }

      .close-fy-panel h2 {
        font-size: 1.55rem;
      }

      .backup-row {
        grid-template-columns: 1fr;
      }

      .action-btn {
        width: 100%;
      }
    }
  </style>
@endpush

@section('content')
  <div class="close-fy-page">
    @if (session('success'))
      <div class="alert alert-success alert-space">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger alert-space">{{ $errors->first() }}</div>
    @endif

    <div class="close-fy-grid">
      <section class="close-fy-panel">
        <h2>Restart Transaction Numbers</h2>
        <p>Your data remains as it is in your company and only the invoice prefixes are reset for new financial year after closing date.</p>

        <div class="info-box">
          <i class="bi bi-info-circle"></i>
          <div>Invoice prefixes empty reh sakte hain. Letters, numbers aur hyphen allowed hain, is liye date-style values jaise 2026-04 bhi use kar sakte hain.</div>
        </div>

        <form method="POST" action="{{ route('utilities.close-financial-year.prefixes') }}" id="prefixForm">
          @csrf
          <table class="prefix-table">
            <thead>
              <tr>
                <th>Type</th>
                <th>Prefix</th>
                <th>Transaction No Preview</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($labels as $key => $label)
                <tr>
                  <td>{{ $label }}@if ($key === 'invoice')*@endif</td>
                  <td>
                    <input
                      class="prefix-input js-prefix-input"
                      type="text"
                      name="prefixes[{{ $key }}]"
                      value="{{ old('prefixes.' . $key, $prefixes[$key] ?? '') }}"
                      data-preview-target="preview_{{ $key }}"
                      data-sequence="{{ preg_replace('/^[^0-9]*/', '', $previews[$key] ?? '') ?: '1' }}"
                    >
                  </td>
                  <td>
                    <div class="preview-box" id="preview_{{ $key }}">{{ $previews[$key] ?? '' }}</div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>

          <div class="button-row">
            <button type="button" class="action-btn gray" id="suggestPrefixBtn">Suggestion</button>
            <button type="submit" class="action-btn primary">Update</button>
          </div>
        </form>
      </section>

      <section class="close-fy-panel">
        <h2>Backup all data and start fresh.</h2>
        <p>All the transaction data upto the closing date will be backed up. You can always access your data later from the generated backup file.</p>

        <div class="info-box">
          <i class="bi bi-info-circle"></i>
          <div>
            @if ($lastBackupDate)
              You have closed books on {{ \Carbon\Carbon::parse($lastBackupDate)->format('d/m/Y') }}. Backup file:
              <strong>{{ $lastBackupFile }}</strong>
            @else
              Select a closing date to create a backup snapshot before starting a new financial year.
            @endif
          </div>
        </div>

        <form method="POST" action="{{ route('utilities.close-financial-year.backup') }}">
          @csrf
          <div class="backup-label">Select Closing Date</div>
          <div class="backup-row">
            <div class="date-field">
              <input
                class="closing-date-input"
                type="date"
                name="closing_date"
                value="{{ old('closing_date', $lastBackupDate ?? now()->toDateString()) }}"
              >
              <i class="bi bi-calendar-event"></i>
            </div>
            <button type="submit" class="action-btn gray">Start Fresh</button>
          </div>
        </form>

        <div class="status-chip">Backup saves transaction snapshot and financial year closing date in your company settings.</div>
      </section>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const defaultPrefixes = @json(\App\Support\TransactionNumberPrefix::defaults());

      function updatePreview(input) {
        const target = document.getElementById(input.dataset.previewTarget);
        const sequence = input.dataset.sequence || '1';
        if (!target) {
          return;
        }

        target.textContent = (input.value || '') + sequence;
      }

      document.querySelectorAll('.js-prefix-input').forEach(function (input) {
        input.addEventListener('input', function () {
          updatePreview(input);
        });
        updatePreview(input);
      });

      const suggestBtn = document.getElementById('suggestPrefixBtn');
      if (suggestBtn) {
        suggestBtn.addEventListener('click', function () {
          document.querySelectorAll('.js-prefix-input').forEach(function (input) {
            const nameMatch = input.name.match(/\[([^\]]+)\]/);
            const key = nameMatch ? nameMatch[1] : null;
            if (key && Object.prototype.hasOwnProperty.call(defaultPrefixes, key)) {
              input.value = defaultPrefixes[key];
              updatePreview(input);
            }
          });
        });
      }
    });
  </script>
@endpush
