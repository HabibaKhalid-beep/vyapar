@extends('layouts.app')

@section('title', 'Products')
@section('page', 'items')

@push('styles')
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

.vp-page {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 100vh;
    background: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
}

/* ── TABS ── */
.vp-tabs {
    display: flex;
    border-bottom: 1.5px solid #e5e7eb;
    background: #fff;
    flex-shrink: 0;
}
.vp-tab {
    flex: 1;
    text-align: center;
    padding: 18px 0 16px;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: .09em;
    color: #b0b8c4;
    cursor: pointer;
    border-bottom: 2.5px solid transparent;
    margin-bottom: -1.5px;
    transition: color .15s, border-color .15s;
    user-select: none;
    text-transform: uppercase;
}
.vp-tab:hover { color: #6b7280; }
.vp-tab.active { color: #2563eb; border-bottom-color: #2563eb; }

/* ── EMPTY STATE ── */
.vp-empty {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    padding: 20px;
}
.vp-empty-inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 26px;
    text-align: center;
}
.vp-empty-text {
    font-size: 15px;
    color: #8a8a8a;
    max-width: 500px;
    line-height: 1.65;
    font-weight: 400;
}
.vp-add-btn {
    display: inline-flex;
    align-items: center;
    background: #f59e0b;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 14px 44px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    letter-spacing: .02em;
    box-shadow: 0 2px 10px rgba(245,158,11,.30);
    transition: background .18s, transform .15s, box-shadow .18s;
}
.vp-add-btn:hover {
    background: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(245,158,11,.40);
}

/* ── LIST STATE ── */
.vp-list-body { flex:1; display:flex; flex-direction:column; overflow:hidden; }
.vp-list-toolbar {
    display:flex; justify-content:space-between; align-items:center;
    padding:13px 22px; border-bottom:1px solid #f0f0f0; background:#fff;
}
.vp-list-title { font-size:14px; font-weight:600; color:#374151; }
.vp-add-btn-sm {
    display:inline-flex; align-items:center; gap:6px;
    background:#f59e0b; color:#fff; border:none; border-radius:6px;
    padding:8px 18px; font-size:13px; font-weight:600; cursor:pointer;
    transition:background .15s;
}
.vp-add-btn-sm:hover { background:#d97706; }
</style>
@endpush

@section('content')
<div class="vp-page">

    {{-- TABS --}}
    <div class="vp-tabs">
        <div class="vp-tab active">PRODUCTS</div>
        <div class="vp-tab" onclick="window.location.href='/items/services'">SERVICES</div>
        <div class="vp-tab" onclick="window.location.href='/items/category'">CATEGORY</div>
        <div class="vp-tab" onclick="window.location.href='/items/units'">UNITS</div>
    </div>

    @if(count($products) === 0)

    <div class="vp-empty">
        <div class="vp-empty-inner">

            {{-- ═══════════════════════════════════
                 ILLUSTRATION — matches screenshot exactly
                 Coordinate system: 340 wide × 280 tall
                 overflow=visible so workers poke outside oval
            ════════════════════════════════════ --}}
            <svg width="340" height="280" viewBox="0 0 340 280"
                 xmlns="http://www.w3.org/2000/svg" overflow="visible"
                 style="display:block;">

                <defs>
                    {{-- Red arrowhead --}}
                    <marker id="redArrow" markerWidth="9" markerHeight="9"
                            refX="8" refY="4.5" orient="auto">
                        <path d="M0,0 L9,4.5 L0,9 Z" fill="#c0392b"/>
                    </marker>
                    {{-- Skin gradient for faces --}}
                    <radialGradient id="skinGrad" cx="45%" cy="40%" r="55%">
                        <stop offset="0%" stop-color="#fdd5a8"/>
                        <stop offset="100%" stop-color="#f4a96a"/>
                    </radialGradient>
                </defs>

                {{-- ══ BACKGROUND OVAL ══
                     Wide horizontal ellipse, light blue, sits centre-bottom
                ══ --}}
                <ellipse cx="170" cy="185" rx="140" ry="95" fill="#dbeafe"/>

                {{-- ══════════════════════════════════════
                     BOXES — stacked 3 high, right-centre
                     Sitting ON the oval, boxes go from y=108 to y=198
                ══════════════════════════════════════ --}}

                {{-- Box 3 (bottom, largest, on ground) --}}
                <rect x="198" y="162" width="54" height="40" rx="3" fill="#d4a843"/>
                <rect x="198" y="162" width="54" height="40" rx="3" fill="none" stroke="#b8892a" stroke-width="1.4"/>
                <line x1="225" y1="162" x2="225" y2="202" stroke="#b8892a" stroke-width="1.2"/>
                <line x1="198" y1="182" x2="252" y2="182" stroke="#b8892a" stroke-width="1.2"/>
                {{-- tape/ribbon on box --}}
                <rect x="217" y="162" width="16" height="40" rx="0" fill="#c99430" opacity="0.4"/>

                {{-- Box 2 (middle) --}}
                <rect x="202" y="126" width="48" height="37" rx="3" fill="#e0b355"/>
                <rect x="202" y="126" width="48" height="37" rx="3" fill="none" stroke="#b8892a" stroke-width="1.4"/>
                <line x1="226" y1="126" x2="226" y2="163" stroke="#b8892a" stroke-width="1.2"/>
                <line x1="202" y1="144" x2="250" y2="144" stroke="#b8892a" stroke-width="1.2"/>
                <rect x="219" y="126" width="14" height="37" rx="0" fill="#c99430" opacity="0.4"/>

                {{-- Box 1 (top, smallest) --}}
                <rect x="207" y="95" width="40" height="32" rx="3" fill="#f0c46a"/>
                <rect x="207" y="95" width="40" height="32" rx="3" fill="none" stroke="#b8892a" stroke-width="1.4"/>
                <line x1="227" y1="95" x2="227" y2="127" stroke="#b8892a" stroke-width="1.2"/>
                <line x1="207" y1="111" x2="247" y2="111" stroke="#b8892a" stroke-width="1.2"/>
                <rect x="220" y="95" width="14" height="32" rx="0" fill="#c99430" opacity="0.4"/>

                {{-- ══════════════════════════════════════
                     WORKER 1 — standing top-left of boxes
                     NO hat. Dark hair. Clipboard in left hand.
                     Feet ~y=205, head ~y=325 (above oval top)
                ══════════════════════════════════════ --}}

                {{-- Boots W1 --}}
                <rect x="151" y="194" width="16" height="9" rx="3" fill="#6b3a1f"/>
                <ellipse cx="159" cy="203" rx="11" ry="4.5" fill="#4a2510"/>
                <rect x="167" y="194" width="16" height="9" rx="3" fill="#6b3a1f"/>
                <ellipse cx="175" cy="203" rx="11" ry="4.5" fill="#4a2510"/>

                {{-- Legs W1 --}}
                <rect x="154" y="170" width="13" height="28" rx="5" fill="#e08020"/>
                <rect x="169" y="170" width="13" height="28" rx="5" fill="#e08020"/>

                {{-- Torso W1 --}}
                <rect x="148" y="126" width="42" height="48" rx="8" fill="#f59e0b"/>
                {{-- Belt --}}
                <rect x="148" y="162" width="42" height="7" rx="2" fill="#d97706"/>
                {{-- Chest pocket --}}
                <rect x="152" y="132" width="11" height="9" rx="2" fill="#d97706" opacity="0.55"/>

                {{-- Left arm (HOLDING CLIPBOARD — arm goes forward-left) --}}
                <path d="M148 138 Q134 145 128 160"
                      stroke="#f59e0b" stroke-width="11" stroke-linecap="round" fill="none"/>

                {{-- CLIPBOARD --}}
                <rect x="112" y="154" width="22" height="30" rx="3"
                      fill="#f8f8f0" stroke="#c8c8b0" stroke-width="1.5"/>
                {{-- Clip at top --}}
                <rect x="119" y="150" width="8" height="8" rx="2" fill="#9ca3af"/>
                {{-- Lines on clipboard --}}
                <line x1="116" y1="165" x2="130" y2="165" stroke="#aaa" stroke-width="1.2"/>
                <line x1="116" y1="170" x2="130" y2="170" stroke="#aaa" stroke-width="1.2"/>
                <line x1="116" y1="175" x2="125" y2="175" stroke="#aaa" stroke-width="1.2"/>

                {{-- Right arm (down at side) --}}
                <path d="M190 138 Q200 150 198 168"
                      stroke="#f59e0b" stroke-width="11" stroke-linecap="round" fill="none"/>

                {{-- Neck W1 --}}
                <rect x="161" y="114" width="14" height="15" rx="4" fill="#f4a96a"/>

                {{-- Head W1 --}}
                <ellipse cx="168" cy="97" rx="22" ry="24" fill="url(#skinGrad)"/>

                {{-- Ears W1 --}}
                <ellipse cx="146" cy="97" rx="4.5" ry="5.5" fill="#f4a96a"/>
                <ellipse cx="190" cy="97" rx="4.5" ry="5.5" fill="#f4a96a"/>

                {{-- Hair W1 — dark, short, swept --}}
                <path d="M146 90 Q147 68 168 64 Q189 68 190 90 Q185 70 168 68 Q151 70 146 90Z"
                      fill="#2c1a0a"/>
                {{-- Hair sides --}}
                <path d="M146 90 Q144 80 148 73" fill="none" stroke="#2c1a0a" stroke-width="3" stroke-linecap="round"/>
                <path d="M190 90 Q192 80 188 73" fill="none" stroke="#2c1a0a" stroke-width="3" stroke-linecap="round"/>

                {{-- Eyes W1 --}}
                <ellipse cx="161" cy="94" rx="3.5" ry="4" fill="#fff"/>
                <ellipse cx="175" cy="94" rx="3.5" ry="4" fill="#fff"/>
                <circle cx="162" cy="95" r="2.2" fill="#1a0a00"/>
                <circle cx="176" cy="95" r="2.2" fill="#1a0a00"/>
                {{-- Eyebrows W1 --}}
                <path d="M157 89 Q161 86 165 89" fill="none" stroke="#2c1a0a" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M171 89 Q175 86 179 89" fill="none" stroke="#2c1a0a" stroke-width="1.8" stroke-linecap="round"/>
                {{-- Nose W1 --}}
                <path d="M166 101 Q168 105 170 101" fill="none" stroke="#c07040" stroke-width="1.4" stroke-linecap="round"/>
                {{-- Mouth W1 — slight smile --}}
                <path d="M162 110 Q168 115 174 110" fill="none" stroke="#a04020" stroke-width="1.6" stroke-linecap="round"/>

                {{-- ══ CURVED ARROW — TOP RIGHT ══
                     Goes from right side of boxes, curves UP and RIGHT
                     (clockwise cycle — top half)
                ══ --}}
                <path d="M 250 110 Q 290 75 270 135"
                      fill="none" stroke="#c0392b" stroke-width="2.8"
                      stroke-linecap="round"
                      marker-end="url(#redArrow)"/>

                {{-- ══════════════════════════════════════
                     WORKER 2 — bottom right, pushing dolly/platform trolley
                     Smaller figure. Feet at ~y=248 (below oval bottom edge)
                ══════════════════════════════════════ --}}

                {{-- Platform trolley (flat bed with 2 wheels) --}}
                {{-- Flat platform bed --}}
                <rect x="228" y="228" width="60" height="8" rx="3" fill="#8b7355"/>
                {{-- Wheels --}}
                <circle cx="238" cy="242" r="9" fill="#2d2420"/>
                <circle cx="238" cy="242" r="4"  fill="#6b5e54"/>
                <circle cx="278" cy="242" r="9" fill="#2d2420"/>
                <circle cx="278" cy="242" r="4"  fill="#6b5e54"/>
                {{-- Handle pole (goes back-left to worker's hands) --}}
                <line x1="228" y1="224" x2="208" y2="205"
                      stroke="#8b7355" stroke-width="4" stroke-linecap="round"/>

                {{-- Box on trolley --}}
                <rect x="232" y="200" width="48" height="30" rx="3" fill="#d4a843"/>
                <rect x="232" y="200" width="48" height="30" rx="3" fill="none" stroke="#b8892a" stroke-width="1.3"/>
                <line x1="256" y1="200" x2="256" y2="230" stroke="#b8892a" stroke-width="1.1"/>
                <line x1="232" y1="215" x2="280" y2="215" stroke="#b8892a" stroke-width="1.1"/>

                {{-- Worker 2 boots --}}
                <rect x="192" y="236" width="14" height="9" rx="3" fill="#6b3a1f"/>
                <ellipse cx="199" cy="245" rx="10" ry="4" fill="#4a2510"/>
                <rect x="208" y="236" width="14" height="9" rx="3" fill="#6b3a1f"/>
                <ellipse cx="215" cy="245" rx="10" ry="4" fill="#4a2510"/>

                {{-- Worker 2 legs (walking forward, slight stride) --}}
                <rect x="195" y="212" width="11" height="27" rx="4"
                      fill="#e08020" transform="rotate(5 200 225)"/>
                <rect x="209" y="212" width="11" height="27" rx="4"
                      fill="#e08020" transform="rotate(-5 214 225)"/>

                {{-- Worker 2 torso (slightly leaning forward) --}}
                <rect x="190" y="174" width="36" height="42" rx="7"
                      fill="#f59e0b" transform="rotate(-5 208 195)"/>
                {{-- Belt --}}
                <rect x="189" y="205" width="36" height="6" rx="2"
                      fill="#d97706" transform="rotate(-5 207 208)"/>

                {{-- Worker 2 right arm (reaching forward to handle) --}}
                <path d="M224 185 Q218 196 210 205"
                      stroke="#f59e0b" stroke-width="10" stroke-linecap="round" fill="none"/>
                {{-- Worker 2 left arm (at side) --}}
                <path d="M191 184 Q182 194 182 208"
                      stroke="#f59e0b" stroke-width="10" stroke-linecap="round" fill="none"/>

                {{-- Worker 2 neck --}}
                <rect x="202" y="163" width="12" height="13" rx="4" fill="#f4a96a"/>

                {{-- Worker 2 head (smaller) --}}
                <ellipse cx="208" cy="149" rx="18" ry="20" fill="url(#skinGrad)"/>

                {{-- Worker 2 ears --}}
                <ellipse cx="190" cy="149" rx="4" ry="5" fill="#f4a96a"/>
                <ellipse cx="226" cy="149" rx="4" ry="5" fill="#f4a96a"/>

                {{-- Worker 2 hair — dark short --}}
                <path d="M190 145 Q191 127 208 124 Q225 127 226 145 Q222 129 208 127 Q194 129 190 145Z"
                      fill="#2c1a0a"/>

                {{-- Worker 2 eyes --}}
                <ellipse cx="202" cy="147" rx="3" ry="3.5" fill="#fff"/>
                <ellipse cx="214" cy="147" rx="3" ry="3.5" fill="#fff"/>
                <circle cx="203" cy="148" r="1.9" fill="#1a0a00"/>
                <circle cx="215" cy="148" r="1.9" fill="#1a0a00"/>
                {{-- Eyebrows W2 --}}
                <path d="M199 143 Q202 140 205 143" fill="none" stroke="#2c1a0a" stroke-width="1.6" stroke-linecap="round"/>
                <path d="M211 143 Q214 140 217 143" fill="none" stroke="#2c1a0a" stroke-width="1.6" stroke-linecap="round"/>
                {{-- Nose W2 --}}
                <path d="M206 154 Q208 157 210 154" fill="none" stroke="#c07040" stroke-width="1.2" stroke-linecap="round"/>
                {{-- Mouth W2 --}}
                <path d="M204 161 Q208 165 212 161" fill="none" stroke="#a04020" stroke-width="1.4" stroke-linecap="round"/>

                {{-- ══ CURVED ARROW — BOTTOM LEFT ══
                     Goes from bottom-left area, curves DOWN and LEFT
                     (clockwise cycle — bottom half)
                ══ --}}
                <path d="M 140 215 Q 100 245 118 185"
                      fill="none" stroke="#c0392b" stroke-width="2.8"
                      stroke-linecap="round"
                      marker-end="url(#redArrow)"/>

            </svg>

            <p class="vp-empty-text">
                Add Products/Items you sell or purchase to manage your full Stock Inventory.
            </p>

            <button class="vp-add-btn" onclick="window.location.href='/items/create?type=product'">
                Add Your First Product
            </button>

        </div>
    </div>

    @else

    <div class="vp-list-body">
        <div class="vp-list-toolbar">
            <span class="vp-list-title">All Products</span>
            <button class="vp-add-btn-sm" onclick="window.location.href='/items/create?type=product'">
                + Add Product
            </button>
        </div>
        <div style="flex:1; overflow-y:auto;">
            <div style="padding:40px; text-align:center; color:#9ca3af; font-size:14px;">
                Product list coming soon…
            </div>
        </div>
    </div>

    @endif

</div>
@endsection