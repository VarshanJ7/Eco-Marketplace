// "Add item" form, shared by every page with the navbar.
// - loads the modal markup from project-folder/add_item_form.php
// - Sell shows "Item Value (RM)", Swap shows "What do you want in exchange?"
// - validates in the browser (same rules as project-folder/submit_item.php)
// - submits with fetch so the form keeps what was typed if the server finds a problem
// - after a successful post, the target page shows a "posted" toast (?added=<id>)
(function () {
  const scriptUrl = document.currentScript ? document.currentScript.src : location.href;
  const formUrl = new URL('../project-folder/add_item_form.php', scriptUrl);
  const MAX_IMAGE_BYTES = 5 * 1024 * 1024;
  const IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

  let modalReady = null; // Promise for the loaded modal element
  let modal = null;
  let formTouched = false;

  function loadModal() {
    if (!modalReady) {
      modalReady = fetch(formUrl, { credentials: 'same-origin' })
        .then(function (res) {
          if (!res.ok) throw new Error('HTTP ' + res.status);
          return res.text();
        })
        .then(function (html) {
          document.body.insertAdjacentHTML('beforeend', html);
          const el = document.getElementById('addItemModal');
          setUpForm(el);
          modal = new bootstrap.Modal(el);
          return el;
        })
        .catch(function (err) {
          modalReady = null;
          throw err;
        });
    }
    return modalReady;
  }

  function openModal() {
    loadModal()
      .then(function () {
        if (!formTouched) preselectFromPage();
        modal.show();
      })
      .catch(function () {
        alert('Sorry, the Add item form could not be loaded. Please refresh the page and try again.');
      });
  }

  // On a browse page, start the form with that page's purpose / type.
  function preselectFromPage() {
    const page = location.pathname.split('/').pop();
    const purpose = document.getElementById('item-purpose');
    const type = document.getElementById('item-type');
    const presets = {
      'swap.php': ['swap', null],
      'kitchen.php': ['sell', 'Kitchen'],
      'clothes.php': ['sell', 'Clothes'],
      'others.php': ['sell', 'Others']
    };
    const preset = presets[page] || ['sell', null];
    purpose.value = preset[0];
    if (preset[1]) type.value = preset[1];
    purpose.dispatchEvent(new Event('change'));
  }

  // ---------- Validation (mirrors submit_item.php) ----------
  function normalizePhone(value) {
    let digits = value.replace(/\D+/g, '');
    if (digits.startsWith('60')) digits = digits.slice(1);
    return /^01(1\d{8}|[02-9]\d{7})$/.test(digits) ? digits.slice(0, 3) + '-' + digits.slice(3) : null;
  }

  function validate(form) {
    const v = function (name) { return (form.elements[name].value || '').trim(); };
    const errors = {};
    const purpose = v('purpose');

    if (!v('itemName')) errors.itemName = 'Please enter the item name.';
    if (!v('itemCondition')) errors.itemCondition = 'Choose the item condition.';

    if (purpose === 'sell') {
      const price = Number(v('price'));
      if (!v('price') || !isFinite(price) || price <= 0) errors.price = 'Enter a price greater than RM 0.';
      else if (price > 99999.99) errors.price = 'Price must be below RM 100,000.';
    } else if (!v('wantsInExchange')) {
      errors.wantsInExchange = 'Tell others what you want in exchange.';
    }

    if (!v('description')) errors.description = 'Please add a short description.';
    else if (v('description').length > 200) errors.description = 'Description must be 200 characters or less.';

    if (!v('ownerName')) errors.ownerName = 'Please enter your name.';
    if (!normalizePhone(v('phoneNumber'))) errors.phoneNumber = 'Enter a Malaysian mobile number, e.g. 012-3456789.';

    const file = form.elements.itemImage.files[0];
    if (file) {
      if (IMAGE_TYPES.indexOf(file.type) === -1) errors.itemImage = 'Only JPG, PNG or WEBP images are allowed.';
      else if (file.size > MAX_IMAGE_BYTES) errors.itemImage = 'That image is too large. The maximum size is 5MB.';
    }
    return errors;
  }

  function showErrors(form, errors) {
    form.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
    const general = document.getElementById('add-item-error');
    general.hidden = !errors.form;
    general.textContent = errors.form || '';

    let first = null;
    Object.keys(errors).forEach(function (name) {
      const input = form.elements[name];
      const feedback = form.querySelector('[data-error-for="' + name + '"]');
      if (!input || !feedback || input.disabled) return;
      input.classList.add('is-invalid');
      feedback.textContent = errors[name];
      if (!first) first = input;
    });
    if (first) first.focus();
    else if (errors.form) general.scrollIntoView({ block: 'nearest' });
  }

  // ---------- Form behaviour ----------
  function setUpForm(modalEl) {
    const form = modalEl.querySelector('#add-item-form');
    const purpose = form.elements.purpose;
    const priceGroup = document.getElementById('item-price-group');
    const wantsGroup = document.getElementById('item-wants-group');
    const description = form.elements.description;
    const counter = document.getElementById('item-description-count');
    const imageInput = form.elements.itemImage;
    const preview = document.getElementById('item-image-preview');
    const submitBtn = document.getElementById('add-item-submit');

    function togglePurpose() {
      const isSwap = purpose.value === 'swap';
      priceGroup.hidden = isSwap;
      form.elements.price.disabled = isSwap;
      wantsGroup.hidden = !isSwap;
      form.elements.wantsInExchange.disabled = !isSwap;
    }
    purpose.addEventListener('change', togglePurpose);
    togglePurpose();

    function updateCounter() {
      counter.textContent = description.value.length + ' / 200';
    }
    description.addEventListener('input', updateCounter);

    preview.addEventListener('error', function () { preview.hidden = true; }); // not a real image

    imageInput.addEventListener('change', function () {
      const file = imageInput.files[0];
      if (preview.src) URL.revokeObjectURL(preview.src);
      if (file && IMAGE_TYPES.indexOf(file.type) !== -1) {
        preview.src = URL.createObjectURL(file);
        preview.hidden = false;
      } else {
        preview.removeAttribute('src');
        preview.hidden = true;
      }
    });

    // Clear a field's error as soon as the user edits it.
    form.addEventListener('input', function (e) {
      formTouched = true;
      e.target.classList.remove('is-invalid');
    });
    form.addEventListener('change', function (e) {
      formTouched = true;
      e.target.classList.remove('is-invalid');
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const errors = validate(form);
      if (Object.keys(errors).length) {
        showErrors(form, errors);
        return;
      }
      showErrors(form, {});

      submitBtn.disabled = true;
      submitBtn.textContent = 'Submitting…';

      fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-Requested-With': 'fetch' },
        credentials: 'same-origin'
      })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (data.ok) {
            location.href = new URL(data.redirect, form.action).href;
            return;
          }
          showErrors(form, data.errors || { form: 'Your item could not be added. Please try again.' });
          resetButton();
        })
        .catch(function () {
          showErrors(form, { form: 'Could not reach the server. Check that the site is running and try again.' });
          resetButton();
        });

      function resetButton() {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit';
      }
    });

    updateCounter();
  }

  // ---------- "Item posted" toast after a successful submit ----------
  function showPostedToast() {
    const params = new URLSearchParams(location.search);
    const addedId = params.get('added');
    if (!addedId || !/^\d+$/.test(addedId)) return;

    const card = document.querySelector('.item-card[data-id="' + addedId + '"]');
    const name = card ? card.querySelector('.item-card-name').textContent : '';

    const container = document.createElement('div');
    container.className = 'toast-container position-fixed end-0 p-3 add-item-toast-container';
    container.innerHTML =
      '<div class="toast align-items-center text-bg-success border-0" role="status" aria-live="polite" aria-atomic="true">' +
      '<div class="d-flex"><div class="toast-body"></div>' +
      '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>';
    container.querySelector('.toast-body').textContent =
      name ? '✓ "' + name + '" has been posted!' : '✓ Your item has been posted!';
    document.body.appendChild(container);
    new bootstrap.Toast(container.querySelector('.toast'), { delay: 5000 }).show();

    if (card) {
      card.classList.add('item-card-new');
      card.scrollIntoView({ block: 'center', behavior: 'smooth' });
    }

    // Don't show the toast again on refresh.
    params.delete('added');
    const query = params.toString();
    history.replaceState(null, '', location.pathname + (query ? '?' + query : '') + location.hash);
  }

  document.addEventListener('DOMContentLoaded', function () {
    const trigger = document.getElementById('add-item-trigger');
    if (trigger) {
      trigger.addEventListener('click', openModal);
      trigger.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          openModal();
        }
      });
      loadModal().catch(function () { /* retried on click */ });
    }
    showPostedToast();
  });
})();
