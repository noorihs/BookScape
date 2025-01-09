
document.getElementById('searchButton').addEventListener('click', function () {
    const query = document.getElementById('searchInput').value.trim();

    if (!query) {
        alert('Please enter a search query.');
        return;
    }

    resetBookDetails();

    fetch('search.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ query: query }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const book = data.book;
                document.getElementById('bookImage').src = book.image_url || 'default-book-image.jpg';
                document.getElementById('bookTitle').textContent = book.title;
                document.getElementById('bookAuthor').textContent = `Author: ${book.author}`;
                document.getElementById('bookPrice').textContent = `${book.price} DZD`;
                document.getElementById('bookDescription').textContent = book.description || 'No description available.';
                document.getElementById('bookStock').textContent = `In Stock: ${book.stock || '0'}`;
                document.getElementById('bookDetails').style.display = 'block';
            } else {
                alert(data.message || 'Book not found.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while searching for the book.');
        });
});

function showBookDetails(bookId) {
    fetch(`get_book_details.php?id=${bookId}`)
        .then(response => response.json())
        .then(book => {
            if (book.error) {
                alert(book.error);
                return;
            }
            document.getElementById('bookTitle').textContent = book.title;
            document.getElementById('bookAuthor').textContent = `Author: ${book.author}`;
            document.getElementById('bookPrice').textContent = `${book.price} DZD`;
            document.getElementById('bookDescription').textContent = book.description;
            document.getElementById('bookStock').textContent = `In Stock: ${book.stock}`;
            document.getElementById('bookImage').src = book.image_url;

            fetch(`get_comments.php?book_id=${bookId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('commentsList').innerHTML = data;
                    document.getElementById('bookId').value = bookId;
                });

            document.getElementById('bookDetails').style.display = 'block';
        })
        .catch(err => console.error('Error fetching book details:', err));
}




// Fonction pour réinitialiser les données des détails du livre
function resetBookDetails() {
    document.getElementById('bookTitle').textContent = 'Loading...';
    document.getElementById('bookAuthor').textContent = '';
    document.getElementById('bookPrice').textContent = '';
    document.getElementById('bookDescription').textContent = '';
    document.getElementById('bookStock').textContent = '';
    document.getElementById('bookImage').src = 'default-book-image.jpg';
    document.getElementById('commentsList').innerHTML = '';
}

// Fonction pour cacher les détails du livre
function hideBookDetails() {
    document.getElementById('bookDetails').style.display = 'none';
}


    document.getElementById('addToCartBtn').addEventListener('click', function () {
    const bookId = document.getElementById('bookId').value;
    const bookTitle = document.getElementById('bookTitle').textContent;
    const bookPrice = parseFloat(document.getElementById('bookPrice').textContent);
    const cart = JSON.parse(localStorage.getItem('cart')) || [];

    
    const existingBook = cart.find(item => item.id === bookId);
    if (existingBook) {
        existingBook.quantity += 1; // Incrémenter la quantité
    } else {
        
        cart.push({ id: bookId, title: bookTitle, price: bookPrice, quantity: 1 });
    }

    localStorage.setItem('cart', JSON.stringify(cart)); 
    alert(`${bookTitle} has been added to the cart.`);
});

function addToCartAndCheckLogin(bookId) {
   
    fetch('check_login.php')
        .then(response => response.json())
        .then(data => {
            if (data.logged_in) {
             
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ book_id: bookId }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Book added to cart successfully!');

                       
                            let cart = JSON.parse(localStorage.getItem('cart')) || [];
                            const existingBookIndex = cart.findIndex(item => item.book_id === bookId);

                            if (existingBookIndex !== -1) {
                                cart[existingBookIndex].quantity += 1;
                            } else {
                                cart.push({
                                    book_id: bookId,
                                    title: "It Ends With Us", 
                                    price: 4500, 
                                    quantity: 1,
                                });
                            }

                            localStorage.setItem('cart', JSON.stringify(cart));
                            window.location.href = 'cart.html'; 
                        } else {
                            alert('Failed to add book to cart: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while adding the book to the cart.');
                    });
            } else {

                alert('Please log in to buy this book.');
                window.location.href = 'account.html';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while checking login status.');
        });
}
document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('.star');
    let selectedRating = 0;

    stars.forEach(star => {
        star.addEventListener('click', () => {
            selectedRating = parseInt(star.getAttribute('data-value'));
            updateStarColors(selectedRating);
        });

        star.addEventListener('mouseover', () => {
            const hoverRating = parseInt(star.getAttribute('data-value'));
            updateStarColors(hoverRating);
        });

        star.addEventListener('mouseout', () => {
            updateStarColors(selectedRating);
        });
    });

    function updateStarColors(rating) {
        stars.forEach(star => {
            const starValue = parseInt(star.getAttribute('data-value'));
            if (starValue <= rating) {
                star.classList.add('selected');
            } else {
                star.classList.remove('selected');
            }
        });
    }

    document.getElementById('addCommentForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const bookId = document.getElementById('bookId').value;
        const comment = document.getElementById('comment').value;

        if (!selectedRating) {
            alert('Please select a rating.');
            return;
        }

        const response = await fetch('add-comments.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ book_id: bookId, rating: selectedRating, comment })
        });

        const data = await response.json();
        if (data.success) {
            alert('Comment added successfully!');
            showBookDetails(bookId);
        } else {
            alert(data.message);
        }
    });
});




