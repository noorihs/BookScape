document.addEventListener('DOMContentLoaded', function () {
    const cartItemsContainer = document.getElementById('cartItems');
    const buyButton = document.getElementById('buyButton');
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Fonction pour calculer les totaux
    function calculateTotals() {
        let subtotal = 0;
        if (cart.length === 0) {
            document.getElementById('subtotal').textContent = '0.00 DZD';
            document.getElementById('tax').textContent = '0.00 DZD';
            document.getElementById('total').textContent = '0.00 DZD';
            return;
        }

        cart.forEach(book => {
            subtotal += book.price * book.quantity;
        });

        const deliveryPrice = 500; // Livraison
        const total = subtotal + deliveryPrice;

        document.getElementById('subtotal').textContent = `${subtotal.toFixed(2)} DZD`;
        document.getElementById('tax').textContent = `${deliveryPrice.toFixed(2)} DZD`;
        document.getElementById('total').textContent = `${total.toFixed(2)} DZD`;
    }

    // Fonction pour afficher le contenu du panier
    function displayCart() {
        cartItemsContainer.innerHTML = '';

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<tr><td colspan="4">Your cart is empty</td></tr>';
            calculateTotals();
            return;
        }

        cart.forEach((book, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${book.title}</td>
                <td>
                    <button class="quantity-btn" data-index="${index}" data-action="decrease">-</button>
                    <span class="quantity-text">${book.quantity}</span>
                    <button class="quantity-btn" data-index="${index}" data-action="increase">+</button>
                </td>
                <td>${(book.price * book.quantity).toFixed(2)} DZD</td>
                <td><button class="remove-btn" data-index="${index}">Remove</button></td>
            `;
            cartItemsContainer.appendChild(row);
        });

        calculateTotals();
        addRemoveEventListeners();
        addQuantityEventListeners();
    }

    function addRemoveEventListeners() {
        const removeButtons = document.querySelectorAll('.remove-btn');
        removeButtons.forEach(button => {
            button.addEventListener('click', function () {
                const index = this.dataset.index;
                cart.splice(index, 1);
                localStorage.setItem('cart', JSON.stringify(cart));
                displayCart();
            });
        });
    }

    function addQuantityEventListeners() {
        const quantityButtons = document.querySelectorAll('.quantity-btn');
        quantityButtons.forEach(button => {
            button.addEventListener('click', function () {
                const index = this.dataset.index;
                const action = this.dataset.action;

                if (action === 'increase') {
                    cart[index].quantity += 1;
                } else if (action === 'decrease' && cart[index].quantity > 1) {
                    cart[index].quantity -= 1;
                }
                localStorage.setItem('cart', JSON.stringify(cart));
                displayCart();
            });
        });
    }

    buyButton.addEventListener('click', function () {
        if (cart.length === 0) {
            alert('Your cart is empty.');
            return;
        }

        const formattedCart = cart.map(item => ({
            book_id: item.id, 
            quantity: item.quantity,
            price: item.price
        }));

        console.log('Formatted Cart Data:', formattedCart);

        fetch('place_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cart: formattedCart })
        })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message);
                }

                const orderId = data.id;

                return fetch('update_purchased_books.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: orderId, cart: formattedCart })
                });
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message);
                }

                alert('Thank you for your order!');
                localStorage.removeItem('cart');
                cart = [];
                displayCart();
                window.location.href = 'Books.php';
            })
            .catch(error => {
                console.error('Error:', error.message);
                alert(`An error occurred: ${error.message}`);
            });
    });

    displayCart();
});
