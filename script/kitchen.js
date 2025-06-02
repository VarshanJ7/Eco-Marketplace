

const clothes = JSON.parse(localStorage.getItem('clothes')) || 

[{
    image:'../images/shirt.jpeg',
    name:'t-shirt',
    priceCents:1090,   
    number:123456  ,

    
    //js got prob with maths on floats
                     //best practice is to use cents instead of dollars
    
    }, 
    {
        image:'../images/hoodie.jpeg',
        name:'hoodie',
        priceCents:2000,   
        number:123456
    },
     {
        image:'../images/sweater.jpeg',
        name:'sweater',
        priceCents:1500,  
        number:123456
     },];
// Save clothes to localStorage whenever it changes







//render all items in the clothes array

function renderClothes() {

const container = document.getElementById('clothes-list');
container.innerHTML = ''; // Clear the container before rendering

clothes.forEach((clothing) => {
    
    const html =`
    
      <div class="main-container">  
    
    
                <div class="img-container"> <img src="${clothing.image}"  class="shirt-pic" > </div>
            
                <div class="desc-container"> ${clothing.name} <br> ${(clothing.priceCents/100).toFixed(2)} </div> 
            
                <div class="button-container"> 
                    
                    <a href="https://wa.me/${clothing.number}" target="_blank" class="btn btn-success w-100">
                        <i class="bi bi-whatsapp"></i> WhatsApp Seller
                      </a>
                      
                
                </div>
                  
            
                </div>
    `
    ;

    document.getElementById('clothes-list').innerHTML += html;
    
   
    
    });


}

renderClothes();









    
    
    







