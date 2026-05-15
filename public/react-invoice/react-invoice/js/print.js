(function () {
  const templates = [
    { id: 1, name: "Telly Theme", variant: "classicA", thumb: "classic" },
    { id: 2, name: "Landscape Theme 1", variant: "purpleA", thumb: "purple" },
    { id: 3, name: "Landscape Theme 2", variant: "classicB", thumb: "classic" },
    { id: 4, name: "Tax Theme 1", variant: "purpleB", thumb: "purple" },
    { id: 5, name: "Tax Theme 2", variant: "classicC", thumb: "classic" },
    { id: 6, name: "Tax Theme 3", variant: "modernPurple", thumb: "minimal" },
    { id: 7, name: "Tax Theme 4", variant: "purpleC", thumb: "purple" },
    { id: 8, name: "Tax Theme 5", variant: "classicSale", thumb: "classic" },
    { id: 9, name: "Tax Theme 6", variant: "taxTheme6", thumb: "purple" },
    { id: 10, name: "Double Divine", variant: "doubleDivine", thumb: "red" },
    { id: 11, name: "French Elite", variant: "frenchElite", thumb: "minimal" },
    { id: 12, name: "Theme 1", variant: "theme1", thumb: "classic" },
    { id: 13, name: "Theme 2", variant: "theme2", thumb: "purple" },
    { id: 14, name: "Theme 3", variant: "theme3", thumb: "minimal" },
    { id: 15, name: "Theme 4", variant: "theme4", thumb: "classic" }
  ];

  const invoiceData = Array.from({ length: 15 }, (_, i) => {
    const seed = i + 1;
    return {
      id: seed,
      title: "Tax Invoice",
      businessName: `Grocery Store ${seed}`,
      phone: `3023308${(500 + seed).toString().padStart(3, "0")}`,
      invoiceNo: `INV-${2026}${(100 + seed).toString()}`,
      date: `0${(seed % 9) + 1}-04-2026`,
      time: `${10 + (seed % 8)}:${(seed * 7 % 60).toString().padStart(2, "0")} PM`,
      dueDate: `1${seed % 9}-04-2026`,
      billTo: `Classic Enterprises ${seed}, Plot No. ${seed}, Koramangala, Bangalore`,
      shipTo: `Metro Textiles ${seed}, Marathalli Road, Bangalore, Karnataka`,
      items: [
        { name: "ITEM 1", hsn: 1234, qty: "1+1", rate: 10 + seed, disc: `${seed % 4}.0`, gst: "5%", amt: (10 + seed) * 1.05 },
        { name: "ITEM 2", hsn: 6325, qty: "1+1", rate: 30 + seed, disc: `${seed % 5}.0`, gst: "18%", amt: (30 + seed) * 1.18 }
      ]
    };
  });

  let templateStart = 0;
  let activeTemplate = 1;
  let activeData = 1;

  const themeList = document.getElementById("themeList");
  const prevBtn = document.getElementById("themePrev");
  const nextBtn = document.getElementById("themeNext");
  const invoiceCanvas = document.getElementById("invoiceCanvas");
  const dataSetSelect = document.getElementById("dataSetSelect");
  const tabButtons = document.querySelectorAll(".printer-tab");
  const signatureChangeBtn = document.getElementById("signatureChangeBtn");
  const signatureFileInput = document.getElementById("signatureFileInput");
  const signatureTextInput = document.getElementById("signatureTextInput");
  const changeLayoutTab = document.getElementById("changeLayoutTab");
  const changeColorsTab = document.getElementById("changeColorsTab");
  const colorStrip = document.getElementById("colorStrip");
  const themeStrip = document.getElementById("themeStrip");
  const regularConfig = document.getElementById("regularConfig");
  const thermalConfig = document.getElementById("thermalConfig");
  const printerTabsWrap = document.querySelector(".printer-tabs");
  const thermalThemeList = document.getElementById("thermalThemeList");
  const thermalNext = document.getElementById("thermalNext");
  const thermalSize = document.getElementById("thermalSize");

  const SIGNATURE_STORAGE_KEY = "vyapar_signature_image";
  const ACCENT_STORAGE_KEY = "vyapar_invoice_accent";
  const PRINTER_MODE_KEY = "vyapar_printer_mode";
  const THERMAL_THEME_KEY = "vyapar_thermal_theme";
  const THERMAL_WIDTH_KEY = "vyapar_thermal_width";

  const thermalThemes = [
    { id: 1, name: "Theme 1", variant: "thermal1" },
    { id: 2, name: "Theme 2", variant: "thermal2" },
    { id: 3, name: "Theme 3", variant: "thermal3" },
    { id: 4, name: "Theme 4", variant: "thermal4" },
    { id: 5, name: "Theme 5", variant: "thermal5" }
  ];

  function applySignatureToPreview() {
    if (!invoiceCanvas) return;
    const signatureImage = localStorage.getItem(SIGNATURE_STORAGE_KEY);
    const signatureText = signatureTextInput?.value?.trim() || "Authorized Signatory";

    const signEls = invoiceCanvas.querySelectorAll(".inv-sign");
    signEls.forEach((el) => {
      // Clear prior image
      el.querySelectorAll(".inv-sign-img").forEach((img) => img.remove());
      // Ensure text is present
      if (!el.textContent || !el.textContent.trim()) el.textContent = signatureText;
      else el.textContent = signatureText;

      if (signatureImage) {
        const img = document.createElement("img");
        img.className = "inv-sign-img";
        img.src = signatureImage;
        img.alt = "Signature";
        el.prepend(img);
      }
    });
  }

  function setAccent(color) {
    if (!invoiceCanvas) return;
    invoiceCanvas.style.setProperty("--inv-accent", color);
    try {
      localStorage.setItem(ACCENT_STORAGE_KEY, color);
    } catch {
      // ignore
    }
  }

  function fmt(n) {
    return `Rs ${(Math.round(n * 100) / 100).toFixed(2)}`;
  }

  function renderThemeList() {
    if (!themeList) return;
    const visible = templates.slice(templateStart, templateStart + 4);
    themeList.innerHTML = visible
      .map((t) => `
        <button class="theme-item ${t.id === activeTemplate ? "active" : ""}" data-template-id="${t.id}" type="button">
          <div class="theme-thumb theme-thumb--${t.thumb}"></div>
          <div class="theme-name">${t.name}</div>
        </button>
      `)
      .join("");

    themeList.querySelectorAll(".theme-item").forEach((btn) => {
      btn.addEventListener("click", () => {
        activeTemplate = Number(btn.dataset.templateId);
        renderThemeList();
        renderInvoice();
      });
    });
  }

  function renderThermalThemes(activeId) {
    if (!thermalThemeList) return;
    const start = Math.min(Math.max(0, activeId - 1), 1); // show 4 at a time (0..1)
    const visible = thermalThemes.slice(start, start + 4);
    thermalThemeList.innerHTML = visible.map((t) => `
      <button type="button" class="thermal-theme-item ${t.id === activeId ? "is-active" : ""}" data-id="${t.id}">
        <div class="thermal-theme-thumb thermal-theme-thumb--${t.id}"></div>
        <div class="thermal-theme-name">${t.name}</div>
      </button>
    `).join("");

    thermalThemeList.querySelectorAll(".thermal-theme-item").forEach((btn) => {
      btn.addEventListener("click", () => {
        const id = Number(btn.dataset.id);
        try { localStorage.setItem(THERMAL_THEME_KEY, String(id)); } catch {}
        renderThermalThemes(id);
        renderInvoice();
      });
    });
  }

  function renderDataSelect() {
    if (!dataSetSelect) return;
    dataSetSelect.innerHTML = invoiceData
      .map((d) => `<option value="${d.id}" ${d.id === activeData ? "selected" : ""}>Invoice Sample ${d.id}</option>`)
      .join("");
    dataSetSelect.addEventListener("change", (e) => {
      activeData = Number(e.target.value);
      renderInvoice();
    });
  }

  function renderInvoice() {
    if (!invoiceCanvas) return;
    const mode = (() => {
      try { return localStorage.getItem(PRINTER_MODE_KEY) || "regular"; } catch { return "regular"; }
    })();
    const data = invoiceData.find((x) => x.id === activeData) || invoiceData[0];
    const tpl = templates.find((x) => x.id === activeTemplate) || templates[0];
    const subtotal = data.items.reduce((s, i) => s + i.rate, 0);
    const tax = data.items.reduce((s, i) => s + i.rate * (parseFloat(i.gst) / 100), 0);
    const total = subtotal + tax;

    const base = `
      <div class="inv-doc">
        <div class="inv-title">${data.title}</div>
        <div class="inv-head">
          <div class="inv-logo">Image</div>
          <div>
            <div class="inv-company">${data.businessName}</div>
            <div class="inv-phone">Phone: ${data.phone}</div>
          </div>
          <div class="inv-meta">
            <div><strong>Invoice No:</strong> ${data.invoiceNo}</div>
            <div><strong>Date:</strong> ${data.date}</div>
            <div><strong>Time:</strong> ${data.time}</div>
            <div><strong>Due Date:</strong> ${data.dueDate}</div>
          </div>
        </div>
        <div class="inv-grid">
          <div class="inv-col">
            <h5>Bill To:</h5>
            <p>${data.billTo}</p>
          </div>
          <div class="inv-col">
            <h5>Invoice Details:</h5>
            <p>Tax Type: GST</p>
            <p>State: Karnataka</p>
          </div>
        </div>
        <div class="inv-ship"><strong>Ship To:</strong> ${data.shipTo}</div>
        <table class="inv-table">
          <thead>
            <tr>
              <th>#</th><th>Item Name</th><th>HSC/SAC</th><th>Quantity</th><th>Price/Unit</th><th>Discount</th><th>GST</th><th>Amount</th>
            </tr>
          </thead>
          <tbody>
            ${data.items.map((it, idx) => `
              <tr>
                <td>${idx + 1}</td><td>${it.name}</td><td>${it.hsn}</td><td>${it.qty}</td><td>${fmt(it.rate)}</td><td>${it.disc}%</td><td>${it.gst}</td><td>${fmt(it.amt)}</td>
              </tr>`).join("")}
            <tr>
              <td colspan="7" style="text-align:right;"><strong>Total</strong></td>
              <td><strong>${fmt(total)}</strong></td>
            </tr>
          </tbody>
        </table>
        <div class="inv-bottom">
          <div class="box">
            <strong>Description:</strong>
            <p>Sale Description</p>
            <br />
            <strong>Bank Details:</strong>
            <p>Bank Name: 123123123123</p>
            <p>Bank Account No: 12312312312</p>
          </div>
          <div class="box">
            <strong>Terms & Conditions:</strong>
            <p>Thanks for doing business with us!</p>
            <br />
            <div class="inv-sign">Authorized Signatory</div>
          </div>
        </div>
      </div>
    `;

    const purple = `
      <div class="inv-doc inv-purple">
        <div class="inv-title inv-title--sale">Sale</div>
        <div class="inv-head inv-head--sale">
          <div class="inv-logo">Image</div>
          <div>
            <div class="inv-company">${data.businessName}</div>
            <div class="inv-phone">Ph. no.: ${data.phone}</div>
          </div>
          <div class="inv-meta">
            <div><strong>Invoice No:</strong> ${data.invoiceNo}</div>
            <div><strong>Date:</strong> ${data.date}</div>
            <div><strong>Time:</strong> ${data.time}</div>
            <div><strong>Due Date:</strong> ${data.dueDate}</div>
          </div>
        </div>
        <div class="inv-grid inv-grid--purple">
          <div class="inv-col"><h5>Bill To:</h5><p>${data.billTo}</p></div>
          <div class="inv-col"><h5>Shipping To</h5><p>${data.shipTo}</p></div>
          <div class="inv-col"><h5>Invoice Details</h5><p>Invoice No.: ${data.invoiceNo}</p></div>
        </div>
        <table class="inv-table">
          <thead>
            <tr><th>#</th><th>Item name</th><th>HSC/SAC</th><th>Quantity</th><th>Price/unit</th><th>Discount</th><th>GST</th><th>Amount</th></tr>
          </thead>
          <tbody>
            ${data.items.map((it, idx) => `
            <tr>
              <td>${idx + 1}</td><td>${it.name}</td><td>${it.hsn}</td><td>${it.qty}</td><td>${fmt(it.rate)}</td><td>Rs ${it.disc} (${it.disc}%)</td><td>Rs ${(it.rate * (parseFloat(it.gst) / 100)).toFixed(2)} (${it.gst})</td><td>${fmt(it.amt)}</td>
            </tr>`).join("")}
            <tr><td></td><td><strong>Total</strong></td><td></td><td><strong>2 + 1</strong></td><td></td><td><strong>Rs 0.10</strong></td><td><strong>Rs 5.90</strong></td><td><strong>${fmt(total)}</strong></td></tr>
          </tbody>
        </table>
        <div class="inv-bottom inv-bottom--purple">
          <div class="box">
            <strong>Tax type</strong>
            <p>SGST</p><p>CGST</p><p>SGST</p><p>CGST</p>
            <strong>Invoice Amount In Words</strong>
            <p>Forty Two Rupees and Thirty Two Paisa only</p>
            <strong>Bank Details</strong>
            <p>Bank Name: 123123123123</p>
            <p>Bank Account No: 12312312312</p>
          </div>
          <div class="box">
            <strong>Amounts</strong>
            <p>Sub Total <span>${fmt(subtotal)}</span></p>
            <p>Discount (12%) <span>Rs 5.50</span></p>
            <p>Tax (5%) <span>Rs 2.02</span></p>
            <p><strong>Total <span>${fmt(total)}</span></strong></p>
            <p>Received <span>Rs 12.00</span></p>
            <p>Balance <span>Rs 30.32</span></p>
            <p><strong>You Saved <span>Rs 30.32</span></strong></p>
            <strong>Terms and conditions</strong>
            <p>Thanks for doing business with us!</p>
            <div class="inv-sign">Authorized Signatory</div>
          </div>
        </div>
      </div>
    `;

    const modern = `
      <div class="inv-doc inv-modern">
        <div class="inv-head inv-head--modern">
          <div><div class="inv-company">${data.businessName}</div><div class="inv-phone">Ph. no.: ${data.phone}</div></div>
          <div class="inv-logo">Image</div>
        </div>
        <div class="inv-title inv-title--sale">Sale</div>
        <div class="inv-grid inv-grid--modern">
          <div class="inv-col"><h5>Bill To:</h5><p>${data.billTo}</p></div>
          <div class="inv-col"><h5>Shipping To</h5><p>${data.shipTo}</p></div>
          <div class="inv-col"><h5>Invoice Details</h5><p>Invoice No.: ${data.invoiceNo}</p><p>Date: ${data.date}</p><p>Time: ${data.time}</p></div>
        </div>
        <table class="inv-table">
          <thead><tr><th>#</th><th>Item name</th><th>HSC/SAC</th><th>Quantity</th><th>Price/unit</th><th>Discount</th><th>GST</th><th>Amount</th></tr></thead>
          <tbody>
            ${data.items.map((it, idx) => `<tr><td>${idx + 1}</td><td>${it.name}</td><td>${it.hsn}</td><td>${it.qty}</td><td>${fmt(it.rate)}</td><td>Rs ${it.disc}</td><td>${it.gst}</td><td>${fmt(it.amt)}</td></tr>`).join("")}
            <tr><td></td><td><strong>Total</strong></td><td></td><td><strong>2 + 1</strong></td><td></td><td><strong>Rs 0.10</strong></td><td><strong>Rs 5.90</strong></td><td><strong>${fmt(total)}</strong></td></tr>
          </tbody>
        </table>
        <div class="inv-bottom">
          <div class="box"><strong>Description</strong><p>Sale Description</p><strong>INVOICE AMOUNT IN WORDS</strong><p>Forty Two Rupees and Thirty Two Paisa only</p><strong>TERMS AND CONDITIONS</strong><p>Thanks for doing business with us!</p></div>
          <div class="box"><p>Sub Total <span>${fmt(subtotal)}</span></p><p>Discount <span>Rs 0.10</span></p><p>SGST@2.5% <span>Rs 0.25</span></p><p>CGST@2.5% <span>Rs 0.25</span></p><p><strong>Total <span>${fmt(total)}</span></strong></p><p>Received <span>Rs 12.00</span></p><p>Balance <span>Rs 30.32</span></p><p><strong>You Saved <span>${fmt(total)}</span></strong></p></div>
        </div>
      </div>
    `;

    const saleClassic = `
      <div class="inv-doc inv-sale-classic">
        <div class="inv-title inv-title--sale">Sale</div>
        <div class="inv-head inv-head--saleclassic">
          <div class="inv-logo">Image</div>
          <div><div class="inv-company">${data.businessName}</div><div class="inv-phone">Ph. no.: ${data.phone}</div></div>
          <div class="inv-meta inv-meta--grid">
            <div><strong>Invoice No.</strong><br>${data.invoiceNo}</div>
            <div><strong>Date</strong><br>${data.date}, ${data.time}</div>
            <div><strong>Due Date</strong><br>${data.dueDate}</div>
            <div><strong>Transport Name</strong><br>Transport-Ltd</div>
          </div>
        </div>
        <div class="inv-grid"><div class="inv-col"><h5>Bill To</h5><p>${data.billTo}</p></div><div class="inv-col"><h5>Ship To</h5><p>${data.shipTo}</p></div></div>
        <table class="inv-table"><thead><tr><th>#</th><th>Item name</th><th>HSC/SAC</th><th>Quantity</th><th>Price/unit</th><th>Discount</th><th>GST</th><th>Amount</th></tr></thead><tbody>
        ${data.items.map((it, idx) => `<tr><td>${idx + 1}</td><td>${it.name}</td><td>${it.hsn}</td><td>${it.qty}</td><td>${fmt(it.rate)}</td><td>Rs ${it.disc} (1%)</td><td>Rs ${(it.rate * (parseFloat(it.gst) / 100)).toFixed(2)} (${it.gst})</td><td>${fmt(it.amt)}</td></tr>`).join("")}
        <tr><td></td><td><strong>Total</strong></td><td></td><td><strong>2 + 1</strong></td><td></td><td><strong>Rs 0.10</strong></td><td><strong>Rs 5.90</strong></td><td><strong>${fmt(total)}</strong></td></tr>
        </tbody></table>
        <div class="inv-bottom"><div class="box"><strong>Invoice Amount In Words</strong><p>Forty Two Rupees and Thirty Two Paisa only</p><strong>Description</strong><p>Sale Description</p></div><div class="box"><strong>Amounts</strong><p>Sub Total <span>${fmt(subtotal)}</span></p><p>Discount (12%) <span>Rs 5.50</span></p><p>Tax (5%) <span>Rs 2.02</span></p><p><strong>Total <span>${fmt(total)}</span></strong></p></div></div>
      </div>
    `;

    const doubleDivine = `
      <div class="inv-doc inv-double-divine">
        <div class="divine-top">
          <div class="divine-logo">Image</div>
          <div class="divine-phone"><i class="fa fa-phone-alt"></i> ${data.phone}</div>
        </div>
        <div class="divine-headline">
          <div class="divine-store">${data.businessName}</div>
          <div class="divine-title">Tax Invoice</div>
        </div>
        <div class="inv-grid inv-grid--modern">
          <div class="inv-col"><h5>Bill To:</h5><p>${data.billTo}</p></div>
          <div class="inv-col"><h5>Transportation Details:</h5><p>Transport Name: ARYION interstate Transport service</p><p>Delivery Date: ${data.dueDate}</p></div>
          <div class="inv-col"><h5>Invoice No.</h5><p>${data.invoiceNo}</p><p>Date: ${data.date}</p><p>PO date: ${data.date}</p></div>
        </div>
        <table class="inv-table"><thead><tr><th>#</th><th>Item name</th><th>HSN/SAC</th><th>Quantity</th><th>Price / unit</th><th>Discount</th><th>GST</th><th>Amount</th></tr></thead>
        <tbody>${data.items.map((it, idx) => `<tr><td>${idx + 1}</td><td>${it.name}</td><td>${it.hsn}</td><td>${it.qty}</td><td>${fmt(it.rate)}</td><td>Rs ${it.disc}</td><td>${it.gst}</td><td>${fmt(it.amt)}</td></tr>`).join("")}
        <tr><td></td><td><strong>Total</strong></td><td></td><td><strong>9 + 1</strong></td><td></td><td><strong>Rs 2,312.72</strong></td><td><strong>Rs 10,702.11</strong></td><td><strong>Rs 1,02,201.89</strong></td></tr>
        </tbody></table>
        <div class="inv-bottom"><div class="box"><strong>Pay To:</strong><p>Bank Name: ICICI BANK, Branch - HSR LAYOUT</p><p>Bank Account No.: 1234567890</p><strong>Invoice Amount In Words</strong><p>One Lakh Two Thousand Four Hundred Fifty Two Rupees only</p></div><div class="box"><p><strong>Total</strong> <span>Rs 1,02,452.00</span></p><p>Received <span>Rs 50,000.00</span></p><p>Balance <span>Rs 52,452.00</span></p><p><strong>You Saved</strong> <span>Rs 30.32.00</span></p><div class="inv-sign">Authorized Signatory</div></div></div>
      </div>
    `;

    const frenchElite = `
      <div class="inv-doc inv-french-elite">
        <div class="elite-banner">
          <div class="elite-title">TAX INVOICE</div>
          <div class="inv-logo">Image</div>
        </div>
        <div class="elite-store">${data.businessName}</div>
        <div class="inv-grid inv-grid--modern">
          <div class="inv-col"><h5>Invoice No.: #1</h5><p>Invoice Date: ${data.date}</p><p>Invoice Time: ${data.time}</p></div>
          <div class="inv-col"><h5>Bill To:</h5><p>${data.billTo}</p></div>
          <div class="inv-col"><h5>Transportation Details:</h5><p>Transport Name: ARYION interstate Transport</p><p>Vehicle Number: KA BABA 7878</p></div>
        </div>
        <table class="inv-table"><thead><tr><th>#</th><th>Item name</th><th>HSN/SAC</th><th>Quantity</th><th>Price / unit</th><th>Discount</th><th>GST</th><th>Amount</th></tr></thead>
        <tbody>${data.items.map((it, idx) => `<tr><td>${idx + 1}</td><td>${it.name}</td><td>${it.hsn}</td><td>${it.qty}</td><td>${fmt(it.rate)}</td><td>Rs ${it.disc}</td><td>${it.gst}</td><td>${fmt(it.amt)}</td></tr>`).join("")}
        <tr><td></td><td><strong>Total</strong></td><td></td><td><strong>9 + 1</strong></td><td></td><td><strong>Rs 2,312.72</strong></td><td><strong>Rs 10,702.11</strong></td><td><strong>Rs 1,02,201.89</strong></td></tr>
        </tbody></table>
        <div class="inv-bottom"><div class="box"><strong>Pay To:</strong><p>Bank Name: ICICI BANK, Branch - HSR LAYOUT</p><strong>Terms And Conditions</strong><p>Thanks for doing business with us!</p></div><div class="box"><p><strong>Total</strong> <span>Rs 1,02,452.00</span></p><p>Received <span>Rs 50,000.00</span></p><p>Balance <span>Rs 52,452.00</span></p><p><strong>You Saved</strong> <span>Rs 32.32</span></p><div class="inv-sign">Authorized Signatory</div></div></div>
      </div>
    `;

    const byVariant = {
      classicA: base,
      classicB: base.replace('<div class="inv-title">', '<div class="inv-title inv-title--large">'),
      classicC: base.replace('<div class="inv-bottom">', '<div class="inv-bottom inv-bottom--compact">'),
      purpleA: purple,
      purpleB: purple.replace('GST</th>', 'CGST</th><th>SGST</th>').replace('<th>Amount</th>', '<th>Amount</th>'),
      purpleC: purple.replace('inv-bottom--purple', 'inv-bottom--purple inv-bottom--compact'),
      modernPurple: modern,
      classicSale: saleClassic,
      taxTheme6: purple.replace('inv-bottom--purple', 'inv-bottom--purple tax-theme-6'),
      doubleDivine: doubleDivine,
      frenchElite: frenchElite,
      theme1: base.replace('<div class="inv-title">', '<div class="inv-title inv-title--sale">'),
      theme2: purple.replace('inv-doc inv-purple', 'inv-doc inv-purple theme-two'),
      theme3: modern.replace('inv-doc inv-modern', 'inv-doc inv-modern theme-three'),
      theme4: saleClassic.replace('inv-doc inv-sale-classic', 'inv-doc inv-sale-classic theme-four')
    };

    if (mode === "thermal") {
      const thermalId = Number((() => { try { return localStorage.getItem(THERMAL_THEME_KEY) || "1"; } catch { return "1"; } })());
      const thermalVariant = thermalThemes.find((t) => t.id === thermalId)?.variant || "thermal1";

      // thermal width
      const w = (() => { try { return localStorage.getItem(THERMAL_WIDTH_KEY) || "260"; } catch { return "260"; } })();
      invoiceCanvas.style.setProperty("--thermal-width", `${w}px`);

      const thermalTopA = `
        <div class="t-center t-bold">${data.businessName}</div>
        <div class="t-center t-small">Ph.No.: 3023308556</div>
        <div class="t-line"></div>
        <div class="t-row"><span>Vyapar tech solutions (Sample Party Name)</span><span class="t-bold">Invoice</span></div>
        <div class="t-row t-small"><span>Ph. No.: (+91) 9333 911 911</span><span>Date: 01/04/2026</span></div>
        <div class="t-row t-small"><span>Bill To:</span><span>Invoice No.: Inv12345</span></div>
        <div class="t-row t-small"><span>Sarjapur Road, Banglore</span><span></span></div>
        <div class="t-line"></div>
      `;

      const thermalTopB = `
        <div class="t-center t-bold">${data.businessName}</div>
        <div class="t-center t-small">Ph.No.: 3023308556</div>
        <div class="t-line"></div>
        <div class="t-row t-small"><span>Invoice No.: Inv12345</span><span>Date: 01/04/2026</span></div>
        <div class="t-center t-bold">Invoice</div>
        <div class="t-line"></div>
        <div class="t-center t-bold">Vyapar tech solutions (Sample Party Name)</div>
        <div class="t-center t-small">Ph. No.: (+91) 9333 911 911</div>
        <div class="t-line"></div>
        <div class="t-row t-small"><span class="t-bold">Bill To:</span><span></span></div>
        <div class="t-row t-small"><span>Sarjapur Road, Banglore</span><span></span></div>
        <div class="t-line"></div>
      `;

      const rowsClassic = `
        <tr>
          <td>1</td>
          <td>Britannia Chocolate Cake<div class="t-muted t-small">100 + 0 Box</div><div class="t-muted t-small t-italic">Britannia Chocolate Cake description</div><div class="t-muted t-small">Batch No.: N1234, Model No.: A12345, Exp. Date: 04/2027, Mfg. Date: 01/04/2026, Size: Mzd/32</div></td>
          <td class="t-right">100.00</td>
          <td class="t-right">100.00</td>
          <td class="t-right">10,000.00</td>
        </tr>
        <tr>
          <td>2</td>
          <td>Cadbury Chocolate<div class="t-muted t-small">50 + 1Pac</div><div class="t-muted t-small t-italic">Cadbury cake description</div><div class="t-muted t-small">MRP: 150.00</div></td>
          <td class="t-right">150.00</td>
          <td class="t-right">150.00</td>
          <td class="t-right">7,500.00</td>
        </tr>
      `;

      const tableTheme1 = `
        <table>
          <thead><tr><th>#</th><th>Name<div class="t-small">Qty</div></th><th class="t-right">Qty</th><th class="t-right">Price</th><th class="t-right">Amount</th></tr></thead>
          <tbody>${rowsClassic}</tbody>
        </table>
      `;

      const tableTheme2 = `
        <table>
          <thead><tr><th>#</th><th>Item Name<div class="t-small">Qty</div><div class="t-small">Description</div></th><th class="t-right">MRP</th><th class="t-right">Price</th><th class="t-right">Amount</th></tr></thead>
          <tbody>${rowsClassic}</tbody>
        </table>
      `;

      const totalsA = `
        <div class="t-line"></div>
        <div class="t-row"><span class="t-bold">Qty: 150 + 1</span><span class="t-bold">17,500.00</span></div>
        <div class="t-row t-small t-indent"><span>Disc.(0%)</span><span>-500.00</span></div>
        <div class="t-row t-small t-indent"><span>Tax(0%)</span><span>500.00</span></div>
        <div class="t-row t-small t-indent"><span>Total Disc.</span><span>-1,350.00</span></div>
        <div class="t-row t-small t-indent"><span class="t-bold">Total</span><span class="t-bold">20,000.00</span></div>
        <div class="t-row t-small t-indent"><span>Received</span><span>20,000.00</span></div>
        <div class="t-row t-small t-indent"><span>Balance</span><span>0.00</span></div>
      `;

      const totalsB = `
        <div class="t-line"></div>
        <div class="t-row t-row--triple"><span class="t-bold">Qty: 150 + 1</span><span class="t-bold t-center">Items: 2</span><span class="t-bold t-right">17,500.00</span></div>
        <div class="t-row t-small t-indent"><span>Disc.(0%)</span><span>-500.00</span></div>
        <div class="t-row t-small t-indent"><span>Tax(0%)</span><span>500.00</span></div>
        <div class="t-row t-small t-indent"><span>Total Disc.</span><span>-1,350.00</span></div>
        <div class="t-row t-small t-indent"><span class="t-bold">Total</span><span class="t-bold">20,000.00</span></div>
        <div class="t-row t-small t-indent"><span>Received</span><span>20,000.00</span></div>
        <div class="t-row t-small t-indent"><span>Balance</span><span>0.00</span></div>
      `;

      const footer = `
        <div class="t-line"></div>
        <div class="t-center t-small">Balance to be paid in 3 days</div>
        <div class="t-line"></div>
        <div class="t-center t-bold t-small">Terms & Conditions</div>
        <div class="t-center t-small">Thanks for doing business with us!</div>
      `;

      const thermalHtmlByVariant = {
        thermal1: `<div class="inv-thermal inv-thermal--t1">${thermalTopA}${tableTheme1}${totalsA}${footer}</div>`,
        thermal2: `<div class="inv-thermal inv-thermal--t2">${thermalTopA}${tableTheme2}${totalsA}${footer}</div>`,
        thermal3: `<div class="inv-thermal inv-thermal--t3">${thermalTopA}${tableTheme1}${totalsA}${footer}</div>`,
        thermal4: `<div class="inv-thermal inv-thermal--t4">${thermalTopB}${tableTheme2}${totalsB}${footer}</div>`,
        thermal5: `<div class="inv-thermal inv-thermal--t5">${thermalTopB}${tableTheme1}${totalsB}${footer}</div>`
      };

      invoiceCanvas.innerHTML = thermalHtmlByVariant[thermalVariant] || thermalHtmlByVariant.thermal1;
      // signature not used in thermal receipt preview
    } else {
      invoiceCanvas.innerHTML = byVariant[tpl.variant] || base;
      applySignatureToPreview();
    }
  }

  prevBtn?.addEventListener("click", () => {
    templateStart = Math.max(0, templateStart - 1);
    renderThemeList();
  });
  nextBtn?.addEventListener("click", () => {
    templateStart = Math.min(templates.length - 4, templateStart + 1);
    renderThemeList();
  });

  renderThemeList();
  renderDataSelect();
  renderInvoice();

  // Config tabs: Change layout vs Change colours
  if (changeLayoutTab && changeColorsTab && colorStrip) {
    const setTab = (mode) => {
      const isColors = mode === "colors";
      changeLayoutTab.classList.toggle("is-active", !isColors);
      changeColorsTab.classList.toggle("is-active", isColors);
      themeStrip?.classList.toggle("d-none", isColors);
      colorStrip.classList.toggle("d-none", !isColors);
    };
    changeLayoutTab.addEventListener("click", () => setTab("layout"));
    changeColorsTab.addEventListener("click", () => setTab("colors"));
    setTab("layout");

    // Palette click => update invoice heading accent
    colorStrip.querySelectorAll(".color-dot").forEach((btn) => {
      btn.addEventListener("click", () => {
        const color = btn.dataset.color;
        if (!color) return;
        colorStrip.querySelectorAll(".color-dot").forEach((b) => b.classList.remove("is-selected"));
        btn.classList.add("is-selected");
        setAccent(color);
      });
    });
  }

  // Restore accent from storage
  const savedAccent = (() => {
    try { return localStorage.getItem(ACCENT_STORAGE_KEY); } catch { return null; }
  })();
  if (savedAccent) setAccent(savedAccent);

  // Signature upload (keeps UI unchanged; only wires behavior)
  if (signatureChangeBtn && signatureFileInput) {
    signatureChangeBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      signatureFileInput.click();
    });

    signatureFileInput.addEventListener("change", () => {
      const file = signatureFileInput.files?.[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = () => {
        try {
          localStorage.setItem(SIGNATURE_STORAGE_KEY, String(reader.result));
        } catch {
          // ignore storage failures
        }
        // Optional: set input text to filename; doesn't change UI layout.
        if (signatureTextInput) signatureTextInput.value = file.name;
        renderInvoice();
      };
      reader.readAsDataURL(file);
    });
  }

  // Printer tab switching: regular vs thermal
  const setPrinterMode = (mode) => {
    const safeMode = mode === "thermal" ? "thermal" : "regular";
    try { localStorage.setItem(PRINTER_MODE_KEY, safeMode); } catch {}
    tabButtons.forEach((b) => b.classList.toggle("active", b.dataset.tab === safeMode));
    if (regularConfig) {
      regularConfig.classList.toggle("d-none", safeMode !== "regular");
      regularConfig.style.display = safeMode === "regular" ? "" : "none";
    }
    if (thermalConfig) {
      thermalConfig.classList.toggle("d-none", safeMode !== "thermal");
      thermalConfig.style.display = safeMode === "thermal" ? "" : "none";
    }
    // hide colors UI in thermal mode (matches screenshot)
    changeColorsTab?.classList.toggle("d-none", safeMode === "thermal");
    colorStrip?.classList.add("d-none");
    themeStrip?.classList.toggle("d-none", safeMode === "thermal");
    if (safeMode === "thermal") {
      renderThermalThemes(Number((() => { try { return localStorage.getItem(THERMAL_THEME_KEY) || "1"; } catch { return "1"; } })()));
    }
    renderInvoice();
  };

  if (printerTabsWrap) {
    printerTabsWrap.addEventListener("click", (e) => {
      const btn = e.target.closest(".printer-tab");
      if (!btn) return;
      setPrinterMode(btn.dataset.tab);
    });
  } else {
    // fallback if wrapper is unavailable
    tabButtons.forEach((btn) => {
      btn.addEventListener("click", () => setPrinterMode(btn.dataset.tab));
    });
  }

  // Thermal themes + next button
  if (thermalNext) {
    thermalNext.addEventListener("click", () => {
      const current = Number((() => { try { return localStorage.getItem(THERMAL_THEME_KEY) || "1"; } catch { return "1"; } })());
      const next = current >= 5 ? 1 : current + 1;
      try { localStorage.setItem(THERMAL_THEME_KEY, String(next)); } catch {}
      renderThermalThemes(next);
      renderInvoice();
    });
  }

  // Thermal size buttons -> preview width
  if (thermalSize) {
    thermalSize.querySelectorAll(".thermal-size__btn").forEach((b) => {
      b.addEventListener("click", () => {
        thermalSize.querySelectorAll(".thermal-size__btn").forEach((x) => x.classList.remove("is-active"));
        b.classList.add("is-active");
        const mm = Number(b.dataset.size || "68");
        // quick px mapping similar to screenshot proportions
        const px = mm === 58 ? 240 : mm === 68 ? 260 : mm === 88 ? 320 : 220;
        try { localStorage.setItem(THERMAL_WIDTH_KEY, String(px)); } catch {}
        renderInvoice();
      });
    });
  }

  // Init mode + thermal UI
  const initialMode = (() => {
    try {
      const mode = localStorage.getItem(PRINTER_MODE_KEY);
      return mode === "thermal" ? "thermal" : "regular";
    } catch {
      return "regular";
    }
  })();
  renderThermalThemes(Number((() => { try { return localStorage.getItem(THERMAL_THEME_KEY) || "1"; } catch { return "1"; } })()));
  setPrinterMode(initialMode);
})();

