//handle form submission to add a new item
document.getElementById("add-item-form").addEventListener("submit", function(e) {
    e.preventDefault();
    

    const name = document.getElementById("item-name").value;
    const price = parseFloat(document.getElementById("item-price").value) * 100; // Convert to cents
    const file = document.getElementById("item-image").files[0]; // Get the file input
    const number = document.getElementById("item-number").value; // Generate a random number for the item
    const purpose = document.getElementById("item-purpose").value; // Get the purpose of the item
    const type = document.getElementById("item-type").value; // Get the type of the item

    const reader = new FileReader();//prepare to read the file


    
reader.onload = function () {  // function runs after the file is done loading
const imageUrl = reader.result; // final image URL after reading the file



    const newItem = {
        name: name,
        priceCents: price,
        image: imageUrl,
        number: number
      }; 
  
      

    if (purpose === "Swap"){
     const swapItems = JSON.parse(localStorage.getItem('swapItems')) || [];   // Get existing swap items or initialize an empty array
     swapItems.push(newItem); // Add the new item to the swap items array
     localStorage.setItem('swapItems', JSON.stringify(swapItems)); // Save the updated swap items array to localStorage
     window.location.href = "swap.html"; // Redirect to the swap page
    }else if (purpose === "Sell") {
        if (type === "Clothes") {
            const clothes = JSON.parse(localStorage.getItem('clothes')) || []; // Get existing clothes or initialize an empty array
              clothes.push(newItem); // Add the new item to the clothes array
            localStorage.setItem('clothes', JSON.stringify(clothes)); // Save the updated clothes array to localStorage
            window.location.href = "clothes.html"; // Redirect to the clothes page
        } else if (type === "Kitchen") {
            const sellKitchen = JSON.parse(localStorage.getItem("sellKitchen")) || [];
            sellKitchen.push(newItem);
            localStorage.setItem("sellKitchen", JSON.stringify(sellKitchen));
            window.location.href = "sell_kitchen.html";
        }

    }


      
      
      
     
     
      e.target.reset(); // Reset the form fields
    
};

reader.readAsDataURL(file); // Read this file and convert it to a base64 data URL
});




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



