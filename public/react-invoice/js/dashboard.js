/**
 * ═══════════════════════════════════════════
 *  VYAPAR — Dashboard Page Logic
 * ═══════════════════════════════════════════
 */

$(document).ready(function () {

  // ─── Animate summary cards on load ────────────
  $('.summary-card').each(function (i) {
    $(this).css({ opacity: 0, transform: 'translateY(12px)' })
      .delay(i * 120)
      .animate({ opacity: 1 }, 400)
      .css('transform', 'translateY(0)');
  });

});
