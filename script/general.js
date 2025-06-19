//handle form submission to add a new item
 




// disable the type dropdown if the purpose is "Swap"
  const purposeSelect = document.getElementById('item-purpose');
  const typeSelect = document.getElementById('item-type');
  const priceInput = document.getElementById('item-price');

  function handlePurposeChange() {
    if (purposeSelect.value === 'Swap') {
      typeSelect.disabled = true;
      typeSelect.style.textDecoration = 'line-through';
  
      priceInput.disabled = true;
      priceInput.style.textDecoration = 'line-through';
      priceInput.style.color = 'gray';
  
    } else {
      typeSelect.disabled = false;
      typeSelect.style.textDecoration = 'none';
  
      priceInput.disabled = false;
      priceInput.style.textDecoration = 'none';
      priceInput.style.color = 'black';
    }
  }
  


  handlePurposeChange(); // Initial call to set the state based on the current selection if incase form is pre-filled with "Swap"

  purposeSelect.addEventListener('change', handlePurposeChange);



