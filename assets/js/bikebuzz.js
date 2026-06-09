document.addEventListener('DOMContentLoaded', () => {
  if (window.BIKEBUZZ_FLASH && window.Swal) {
    const typeMap = {
      success: 'success',
      error: 'error',
      warning: 'warning',
      info: 'info'
    };

    Swal.fire({
      toast: true,
      position: 'top-end',
      timer: 2600,
      showConfirmButton: false,
      timerProgressBar: true,
      icon: typeMap[window.BIKEBUZZ_FLASH.type] || 'info',
      title: window.BIKEBUZZ_FLASH.message
    });
  }

  document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (!window.Swal) return;
      event.preventDefault();
      Swal.fire({
        title: form.dataset.confirm || 'Xac nhan thao tac?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Dong y',
        cancelButtonText: 'Huy',
        confirmButtonColor: '#18a058'
      }).then((result) => {
        if (result.isConfirmed) form.submit();
      });
    });
  });
});
