document.addEventListener('DOMContentLoaded', function () {
    const bookDetails = {
        book1: { 
            title: 'Divine Rivals', 
            price: 3200.00, 
            description: 'A historical fantasy that blends romance and conflict, following two journalists torn between love and war during a mystical conflict. 432 pages. Rated 4.5/5.' 
        },
        book2: { 
            title: 'Out On a Limb', 
            price: 2800.00, 
            description: 'A heartwarming romantic comedy exploring the complexities of relationships and the leaps of faith we take for love. 350 pages. Rated 4.2/5.' 
        },
        book3: { 
            title: 'A Curse for True Love', 
            price: 2500.00, 
            description: 'The thrilling conclusion to a magical series, where a curse threatens true love, and the characters must battle fate itself. 400 pages. Rated 4.7/5.' 
        },
        book4: { 
            title: 'Hopeless (Arabic version)', 
            price: 2200.00, 
            description: 'The Arabic edition of the emotional and heart-wrenching novel about a young woman uncovering the dark secrets of her past. 480 pages. Rated 4.3/5.' 
        },
        book5: { 
            title: 'It Starts With Us', 
            price: 3200.00, 
            description: 'A powerful sequel diving deeper into the lives of beloved characters, exploring themes of healing and forgiveness. 320 pages. Rated 4.6/5.' 
        },
        book6: { 
            title: 'Harry Potter and the Philosopher\'s Stone', 
            price: 1800.00, 
            description: 'The magical adventure that started it all, where Harry Potter discovers his true identity and enters the world of wizards. 352 pages. Rated 4.8/5.' 
        },
        book7: { 
            title: 'Atomic Habits', 
            price: 1800.00, 
            description: 'An insightful self-help book offering practical strategies to break bad habits and build good ones, changing your life step by step. 320 pages. Rated 4.9/5.' 
        },
        book8: { 
            title: 'The American Roommate Experience', 
            price: 2800.00, 
            description: 'A charming and witty romantic comedy about two people stuck in close quarters, navigating their feelings while sharing a small apartment. 370 pages. Rated 4.1/5.' 
        },
        book9: { 
            title: 'This Girl', 
            price: 2800.00, 
            description: 'The conclusion to the emotional journey of a couple who must fight for their love, revealing new perspectives and heart-stopping moments. 320 pages. Rated 4.4/5.' 
        },
        book10: { 
            title: 'Final Offer', 
            price: 2800.00, 
            description: 'A gripping romance about second chances and the ultimate gamble on love, where the stakes are higher than ever before. 420 pages. Rated 4.5/5.' 
        },
        book11: { 
            title: 'Maybe Someday', 
            price: 2800.00, 
            description: 'A touching love story of friendship, music, and complicated relationships that will pull at your heartstrings and challenge your ideas of love. 370 pages. Rated 4.3/5.' 
        },
        book12: { 
            title: 'The Zahir', 
            price: 2200.00, 
            description: 'A philosophical novel by Paulo Coelho, exploring love, freedom, and the search for spiritual fulfillment through a journey of self-discovery. 336 pages. Rated 4.0/5.' 
        }
    };
    

    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    function showBookDetails(bookId) {
        const book = bookDetails[bookId];
        if (!book) {
            console.error('Book not found!');
            return;
        }

        const bookTitleElement = document.getElementById('bookTitle');
        const bookPriceElement = document.getElementById('bookPrice');
        const bookDescriptionElement = document.getElementById('bookDescription');

        bookTitleElement.innerText = book.title;
        bookPriceElement.innerText = 'DZD' + book.price.toFixed(2);
        bookDescriptionElement.innerText = book.description;

        document.getElementById('bookOverlay').style.display = 'block';
        document.getElementById('bookDetails').style.display = 'block';
    }

    function hideBookDetails() {
        document.getElementById('bookOverlay').style.display = 'none';
        document.getElementById('bookDetails').style.display = 'none';
    }

    function addToCart(book) {
        cart.push(book);
        localStorage.setItem('cart', JSON.stringify(cart));
        alert('Product added to cart: ' + book.name);
    }

    const addToCartBtn = document.getElementById('addToCartBtn');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function () {
            const bookTitle = document.getElementById('bookTitle').innerText;
            const bookPrice = parseFloat(document.getElementById('bookPrice').innerText.replace('DZD', ''));

            if (!bookTitle || isNaN(bookPrice)) {
                alert('Could not retrieve book information. Please try again.');
                return;
            }

            const bookId = Date.now().toString();
            addToCart({
                id: bookId,
                name: bookTitle,
                price: bookPrice,
                quantity: 1
            });
        });
    }

    window.showBookDetails = showBookDetails;
    window.hideBookDetails = hideBookDetails;
});
