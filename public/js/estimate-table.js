// Estimate Table Management Script

document.addEventListener('DOMContentLoaded', function() {
  // Initialize tooltips if using Bootstrap
  if (typeof bootstrap !== 'undefined') {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  }
});

function convertEstimate(url) {
  if (confirm('Estimate ko sale me convert karna hai?')) {
    window.location.href = url;
  }
}

function printEstimate(url) {
  const printWindow = window.open(url, '_blank');
  if (printWindow) {
    printWindow.focus();
  }
}

function previewEstimate(url) {
  const previewWindow = window.open(url, '_blank');
  if (previewWindow) {
    previewWindow.focus();
  }
}

function openPdf(url) {
  const pdfWindow = window.open(url, '_blank');
  if (pdfWindow) {
    pdfWindow.focus();
  }
}

function deleteEstimate(url, id) {
  if (confirm('Are you sure you want to delete this estimate? This action cannot be undone.')) {
    fetch(url, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'Content-Type': 'application/json',
      },
    })
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP Error: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        console.log('Estimate deleted successfully');
        // Remove the row from the table
        const row = document.querySelector(`[data-estimate-id="${id}"]`);
        if (row) {
          row.closest('tr').remove();
        }
        // Check if table is now empty
        const tbody = document.querySelector('table tbody');
        if (tbody.querySelectorAll('tr[data-estimate-id]').length === 0) {
          location.reload();
        }
      } else {
        alert('Error: ' + (data.message || 'Unable to delete estimate'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error deleting estimate: ' + error.message);
    });
  }
}

// Status badge color mapping
function getStatusColor(status) {
  const statusColors = {
    'open': 'success',
    'closed': 'secondary',
    'pending': 'warning'
  };
  return statusColors[status] || 'secondary';
}

// Format currency
function formatCurrency(amount) {
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount);
}
