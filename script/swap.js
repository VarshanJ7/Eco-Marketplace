

const swapItems = JSON.parse(localStorage.getItem('swapItems')) || [];
// Save swapitems to localStorage whenever it changes







//render all items in the swap  array

function renderSwap() {

const container = document.getElementById('clothes-list');
container.innerHTML = ''; // Clear the container before rendering

clothes.forEach((clothing) => {
    
    const html =`
    
      <div class="main-container">  
    
    
                <div class="img-container"> <img src="${swapItems.image}"  class="shirt-pic" > </div>
            
                <div class="desc-container"> ${swapItems.name} <br> ${(swapItems.priceCents/100).toFixed(2)} </div> 
            
                <div class="button-container"> 
                    
                    <a href="https://wa.me/${swapItems.number}" target="_blank" class="btn btn-success w-100">
                        <i class="bi bi-whatsapp"></i> WhatsApp Seller
                      </a>
                      
                
                </div>
                  
            
                </div>
    `
    ;

    document.getElementById('swap-list').innerHTML += html;
    
   
    
    });


}