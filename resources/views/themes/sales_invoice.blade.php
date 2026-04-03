@include('themes.index', [
    'invoicePreviewData' => $invoicePreviewData ?? [],
    'pageTitle' => $pageTitle ?? 'Preview',
    'browserTabLabel' => $browserTabLabel ?? (($invoicePreviewData['billTo'] ?? null) ?: 'Invoice Preview'),
    'saveCloseUrl' => $saveCloseUrl ?? route('sale.index'),
])
