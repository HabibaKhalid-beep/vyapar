/**
 * ═══════════════════════════════════════════
 *  VYAPAR — Sale Page Logic
 * ═══════════════════════════════════════════
 */

$(document).ready(function () {
  const storageKey = 'vyaparBusinessName';
  const $badge = $('#businessNameBadge');
  const $badgeText = $('#businessNameText');
  const $cardMeta = $('#saleBusinessMeta');

  function getBusinessName() {
    return localStorage.getItem(storageKey) || '';
  }

  function setBusinessName(name) {
    const trimmed = (name || '').trim();
    if (trimmed) {
      localStorage.setItem(storageKey, trimmed);
      $badgeText.text(trimmed);
      $cardMeta.html('<span class="sale-card-meta-label">Business:</span> ' + trimmed);
    } else {
      localStorage.removeItem(storageKey);
      $badgeText.text('Enter Business Name');
      $cardMeta.html('');
    }
  }

  // Initialize
  setBusinessName(getBusinessName());

  $badge.on('click', function () {
    const current = getBusinessName();
    const newName = prompt('Enter your business name', current);
    if (newName === null) {
      return; // cancelled
    }
    setBusinessName(newName);
  });
});
