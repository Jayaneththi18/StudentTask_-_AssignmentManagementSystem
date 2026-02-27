
document.addEventListener('DOMContentLoaded', function () {

  document.querySelectorAll('.alert').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity .5s';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 500);
    }, 5000);
  });

  var form = document.querySelector('form[data-validate="register"]');
  if (form) {
    form.addEventListener('submit', function (e) {
      var pw  = document.getElementById('password');
      var cpw = document.getElementById('confirm_password');
      if (pw && cpw && pw.value !== cpw.value) {
        e.preventDefault();
        showInlineError(cpw, 'Passwords do not match.');
      }
    });
  }

  function showInlineError(input, message) {
    var existing = input.parentElement.querySelector('.inline-error');
    if (existing) existing.remove();
    var err = document.createElement('div');
    err.className = 'inline-error';
    err.style.cssText = 'color:#ef4444;font-size:12px;font-weight:600;margin-top:5px;';
    err.textContent = '⚠ ' + message;
    input.parentElement.appendChild(err);
    input.style.borderColor = '#ef4444';
    input.focus();
  }

});
