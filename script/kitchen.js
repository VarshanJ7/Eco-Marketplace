

const sellKitchen= JSON.parse(localStorage.getItem('sellKitchen')) || [];
// Save clothes to localStorage whenever it changes







//render all items in the clothes array

function renderKitchen() {

const container = document.getElementById('kitchen-list');
container.innerHTML = ''; // Clear the container before rendering

sellKitchen.forEach((Kitchen) => {
    
    const html =`
    
      <div class="main-container">  
    
    
                <div class="img-container"> <img src="${Kitchen.image}"  class="shirt-pic" > </div>
            
                <div class="desc-container"> ${Kitchen.name} <br> ${(Kitchen.priceCents/100).toFixed(2)} </div> 
            
                <div class="button-container"> 
                    
                    <a href="https://wa.me/${Kitchen.number}" target="_blank" class="btn btn-success w-100">
                        <i class="bi bi-whatsapp"></i> WhatsApp Seller
                      </a>
                      
                
                </div>
                  
            
                </div>
    `
    ;

    document.getElementById('kitchen-list').innerHTML += html;
    
   
    
    });


}

renderKitchen();









    
    
    







