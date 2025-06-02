

const swapItems = JSON.parse(localStorage.getItem('swapItems')) || [];
// Save swapitems to localStorage whenever it changes







//render all items in the swap  array

function renderSwap() {

const container = document.getElementById('swap-list'); // Get the container where items will be rendered
container.innerHTML = ''; // Clear the container before rendering

swapItems.forEach((swap) => {
    
    const html =`
    
      <div class="main-container">  
    
    
                <div class="img-container"> <img src="${swap.image}"  class="shirt-pic" > </div>
            
                <div class="desc-container"> ${swap.name}  </div> 
            
                <div class="button-container"> 
                    
                    <a href="https://wa.me/${swap.number}" target="_blank" class="btn btn-success w-100">
                        <i class="bi bi-whatsapp"></i> WhatsApp Seller
                      </a>
                      
                
                </div>
                  
            
                </div>
    `
    ;

    document.getElementById('swap-list').innerHTML += html;
    
   
    
    });


}


renderSwap(); // Call the function to render the swap items on page load