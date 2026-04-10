<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Vyapar - Invoice Builder</title>
  <meta name="description" content="Create and preview invoice themes in the React invoice builder.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

  @if ($reactCss)
    <link rel="stylesheet" href="{{ $reactCss }}">
  @endif

  <style>
    html,
    body {
      margin: 0;
      min-height: 100%;
      background: #f3f4f6;
    }

    #root {
      min-height: 100vh;
    }

    #root .app-container {
      min-height: 100vh;
      height: auto;
    }

    .bundle-missing {
      max-width: 720px;
      margin: 40px auto;
      padding: 16px 20px;
      border: 1px solid #f1c40f;
      border-radius: 10px;
      background: #fff8db;
      color: #6b5a00;
      font-family: Roboto, sans-serif;
    }
  </style>
</head>
<body>
  @if (!empty($paymentIn))
    <script>
      window.paymentInInvoice = @json($paymentIn);
    </script>
  @endif

  @if (!empty($allPaymentIns))
    <script>
      window.allPaymentInInvoices = @json($allPaymentIns);
    </script>
  @endif

  @if ($reactJs)
    <div id="root"></div>
    <script>
      window.invoiceAppData = @json($invoiceAppData ?? null);
    </script>
  @else
    <div class="bundle-missing">
      React invoice bundle not found. Run the React build and copy the generated dist/assets files into
      public/react-invoice/assets.
    </div>
  @endif

  @if ($reactJs)
    <script type="module" src="{{ $reactJs }}"></script>
  @endif
</body>
</html>
