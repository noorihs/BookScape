<?php
include 'session.php';
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books</title>
    <link rel="stylesheet" href="Books.css">
    <link id="icon" rel="icon" type="image/png" href="pics/booklogo.png">
    <script src="com.js" defer></script>
    <script src="cart.js" defer></script>
</head>
<body>


<header class="header">
    <div class="welcome">
        <h1>Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>!</h1>
    </div>
    <nav class="navbar">
        <ul id="MenuItems">
            <li><a href="com.html">Home</a></li>
            <li><a href="Books.php">Books</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="account.html">Account</a></li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="cart.html">My cart</a></li>
        </ul>
    </nav>
</header>

<div class="search">
    <input type="text" id="searchInput" placeholder="Search by book-name..">
    <button id="searchButton">Search</button>
</div>




<div class="container">
    <!-- Book List Section -->
    <div class="book-list row">
        <?php
        $sql = "SELECT id, title, author, price, description, stock, image_url FROM books ORDER BY id ASC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='col-3 book-item' onclick='showBookDetails({$row['id']})'>
                    <img src='{$row['image_url']}' alt='{$row['title']}'>
                   
                </div>";
            }
        } else {
            echo "<p>No books found in the database.</p>";
        }
        ?>
    </div>

    <!-- Book Details Overlay -->
    <div id="bookDetails" class="book-details">
        <div class="close-btn" onclick="hideBookDetails()">&times;</div>
        <img id="bookImage" src="" alt="Book Image">
        <h1 id="bookTitle">Book Title</h1>
        <h3 id="bookAuthor">Author: Author Name</h3>
        <h4 id="bookPrice"> Price:50.00 DZD</h4>
        <h3 id="desc">Description:</h3>
        <p id="bookDescription">Lorem ipsum dolor sit amet.</p>
        <p id="bookStock">In Stock: 0</p>
        <button id="addToCartBtn" class="btn">Add to Cart</button>
        <!-- Comments Section -->
        <div id="commentsSection" class="comments-section">
            <h3 id="rating">Ratings & Comments:</h3>
            <div id="commentsList"></div>

            <!-- Comment Form -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <form id="addCommentForm">
                    <input type="hidden" id="bookId" value="">
                    <div class="stars" id="ratingStars">
                        <span class="star" data-value="1">★</span>
                        <span class="star" data-value="2">★</span>
                        <span class="star" data-value="3">★</span>
                        <span class="star" data-value="4">★</span>
                        <span class="star" data-value="5">★</span>
                    </div>
                    <textarea name="comment" id="comment" placeholder="Write your comment..." required></textarea>
                    <button type="submit" class="btn" >Submit</button>
                </form>
            <?php else: ?>
                <p>You need to <a href="account.html">sign in</a> to leave a comment.</p>
            <?php endif; ?>
            
        </div>
        
     
    </div>
</div>

<script>
  

</script>
<div class="footer">
    <div class="container">
      <div class="row">
        <div class="footer-col-2">
            <div class="logo2">
                <h1>BookScape</h1>
            </div>
        </div>
<section >
    <a name="about"></a>
        <div class="footer-col-3">
          <h3>Useful Links</h3>
          <ul>
            <li>Coupons</li>
            <li>Return Policy</li>
          </ul>
        </div>
</section> 
<section >
    <a name="contact"></a>
        <div class="footer-col-4">
          <h3>Follow us</h3>
          <ul>
            <li><a href="https://www.facebook.com" target="_blank">Facebook</a></li>
            <li><a href="https://www.instagram.com" target="_blank">Instagram</a></li>
            
    </section>          
          </ul>
        </div>
      </div>
    
      <p class="copyright">Copyright &copy; 2024 - BookScape</p>
    </div>
  </div>

</body>
</html>
