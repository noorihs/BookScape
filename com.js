document.addEventListener('DOMContentLoaded', function () {
    function showBookDetails(bookId) {
        switch (bookId) {
            case 'book1':
                document.getElementById('bookTitle').innerText = 'Divine Rivals';
                document.getElementById('bookPrice').innerText = '3200.00 DZD';
                document.getElementById('bookDescription').innerText = 'This is the description of Divine Rivals.';
                break;
            case 'book2':
                document.getElementById('bookTitle').innerText = 'Out On a Limb';
                document.getElementById('bookPrice').innerText = '2800.00 DZD';
                document.getElementById('bookDescription').innerText = 'This is the description of Out On a Limb.';
                break;
            case 'book3':
                document.getElementById('bookTitle').innerText = 'A Curse for True Love';
                document.getElementById('bookPrice').innerText = '2500.00 DZD';
                document.getElementById('bookDescription').innerText = 'This is the description of A Curse for True Love.';
                break;
            case 'book4':
                document.getElementById('bookTitle').innerText = 'Hopeless (Arabic version)';
                document.getElementById('bookPrice').innerText = '2200.00 DZD';
                document.getElementById('bookDescription').innerText = 'This is the description of Hopeless.';
                break;
            case 'book5':
                document.getElementById('bookTitle').innerText = 'It Starts With Us';
                document.getElementById('bookPrice').innerText = '3200.00 DZD';
                document.getElementById('bookDescription').innerText = 'This is the description of It Starts With Us.';
                break;
            case 'book6':
                document.getElementById('bookTitle').innerText = 'Harry Potter and the Philosopher\'s Stone';
                document.getElementById('bookPrice').innerText = '1800.00 DZD';
                document.getElementById('bookDescription').innerText = 'This is the description of Harry Potter and the Philosopher\'s Stone.';
                break;
            case 'book7':
                document.getElementById('bookTitle').innerText = 'Atomic Habits';
                document.getElementById('bookPrice').innerText = '$1800.00 DZD';
                document.getElementById('bookDescription').innerText = 'This is the description of Atomic Habits.';
                break;
            case 'book8':
                document.getElementById('bookTitle').innerText = 'The American Roommate Experience';
                document.getElementById('bookPrice').innerText = '2800.00 DZD';
                document.getElementById('bookDescription').innerText = 'This is the description of The American Roommate Experience.';
                break;
            case 'book9':
                document.getElementById('bookTitle').innerText = 'This Girl';
                document.getElementById('bookPrice').innerText = '2800.00 DZD';
                document.getElementById('bookDescription').innerText = 'This is the description of This Girl.';
                break;
            case 'book10':
                document.getElementById('bookTitle').innerText = 'Final Offer';
                document.getElementById('bookPrice').innerText = '2800.00 DZD';
                document.getElementById('bookDescription').innerText = 'This is the description of Final Offer.';
                break;
            case 'book11':
                document.getElementById('bookTitle').innerText = 'Maybe Someday';
                document.getElementById('bookPrice').innerText = '2800.00 DZD';
                document.getElementById('bookDescription').innerText = 'This is the description of Maybe Someday.';
                break;
            case 'book12':
                document.getElementById('bookTitle').innerText = 'The Zahir';
                document.getElementById('bookPrice').innerText = '2200.00 DZD';
                document.getElementById('bookDescription').innerText = 'This is the description of The Zahir.';
                break;
            default:
                document.getElementById('bookTitle').innerText = 'Unknown Book';
                document.getElementById('bookPrice').innerText = '0.00 DZD';
                document.getElementById('bookDescription').innerText = 'No description available.';
                break;
        }

        
        document.getElementById('bookOverlay').style.display = 'block';
        document.getElementById('bookDetails').style.display = 'block';
    }

    function hideBookDetails() {
        document.getElementById('bookOverlay').style.display = 'none';
        document.getElementById('bookDetails').style.display = 'none';
    }

    window.showBookDetails = showBookDetails;
    window.hideBookDetails = hideBookDetails;
    document.addEventListener('DOMContentLoaded', function () {
    const cartItemsContainer = document.getElementById('cartItems');
    const sortingSelect = document.getElementById('sortingSelect');
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    function sortCart(sortType) {
        if (sortType === 'price') {
            cart.sort((a, b) => a.price - b.price); 
        } else if (sortType === 'popularity') {
           
            
            cart.sort((a, b) => b.popularity - a.popularity); 
        } 
        
    }

    function displayCart() {
        cartItemsContainer.innerHTML = ''; 
        

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<tr><td colspan="5">Your cart is empty</td></tr>';
            return;
        }

        cart.forEach((book, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${book.name}</td>
                <td>${book.quantity}</td>
                <td>${book.price.toFixed(2)} DZD</td>
                <td><button class="remove-btn" data-index="${index}">Remove</button></td>
            `;
            cartItemsContainer.appendChild(row);
        });

        calculateTotals();
        addRemoveEventListeners();
    }

    function calculateTotals() {
        let subtotal = 0;
        cart.forEach(book => {
            subtotal += book.price * book.quantity;
        });
        const tax = 500; 
        
        const total = subtotal + tax;

        document.getElementById('subtotal').textContent = `${subtotal.toFixed(2)} DZD`;
        document.getElementById('tax').textContent = `${tax.toFixed(2)} DZD`;
        document.getElementById('total').textContent = `${total.toFixed(2)} DZD`;
    }

    sortingSelect.addEventListener('change', function() {
        const selectedValue = this.value;
        sortCart(selectedValue); 
        displayCart(); 
    });

    displayCart();
});

});
