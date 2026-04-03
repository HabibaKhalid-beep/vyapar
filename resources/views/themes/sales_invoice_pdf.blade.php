@include('themes.index', [
    'sale' => $sale,
    'invoicePreviewData' => $invoicePreviewData ?? [],
    'pageTitle' => $pageTitle ?? 'Invoice PDF',
    'browserTabLabel' => $browserTabLabel ?? 'Invoice PDF',
    'saveCloseUrl' => $saveCloseUrl ?? route('sale.invoice-preview', $sale),
    'pdfMode' => $pdfMode ?? true,
    'initialMode' => $initialMode ?? 'regular',
    'initialRegularThemeId' => $initialRegularThemeId ?? 1,
    'initialThermalThemeId' => $initialThermalThemeId ?? 1,
    'initialAccent' => $initialAccent ?? '#1f4e79',
    'initialAccent2' => $initialAccent2 ?? '#ff981f',
])
