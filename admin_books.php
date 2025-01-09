<?php
session_start();
include 'db.php'; 


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn->set_charset("utf8");


if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die('Access denied. Only admins are allowed.');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'add') {
           
            $title = $conn->real_escape_string($_POST['title']);
            $author = $conn->real_escape_string($_POST['author']);
            $price = floatval($_POST['price']);
            $description = $conn->real_escape_string($_POST['description']);
            $stock = intval($_POST['stock']);
            $imageUrl = $conn->real_escape_string($_POST['image_url']);

          
            $sql = "INSERT INTO books (title, author, price, description, stock, image_url) 
                    VALUES ('$title', '$author', $price, '$description', $stock, '$imageUrl')";

            if ($conn->query($sql)) {
                header("Location: " . $_SERVER['PHP_SELF']); // Redirige pour éviter le double envoi
                exit();
            } else {
                echo "Erreur lors de l'ajout : " . $conn->error;
            }
        } elseif ($action === 'edit') {
          
            $id = intval($_POST['id']);
            $title = $conn->real_escape_string($_POST['title']);
            $author = $conn->real_escape_string($_POST['author']);
            $price = floatval($_POST['price']);
            $description = $conn->real_escape_string($_POST['description']);
            $stock = intval($_POST['stock']);
            $imageUrl = $conn->real_escape_string($_POST['image_url']);

           
            $sql = "UPDATE books 
                    SET title = '$title', author = '$author', price = $price, 
                        description = '$description', stock = $stock, image_url = '$imageUrl' 
                    WHERE id = $id";

            if ($conn->query($sql)) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "Erreur lors de la mise à jour : " . $conn->error;
            }
        } elseif ($action === 'delete') {
            $id = intval($_POST['id']);

            // Suppression du livre
            $sql = "DELETE FROM books WHERE id = $id";

            if ($conn->query($sql)) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "Erreur lors de la suppression : " . $conn->error;
            }
        }
    }
}


$result = $conn->query("SELECT * FROM books");


$orderQuery = "
    SELECT o.id AS order_id, o.user_id, o.total_price, o.order_date, 
           pb.book_id, pb.quantity, b.title 
    FROM orders o
    JOIN purchased_books pb ON o.id = pb.order_id
    JOIN books b ON pb.book_id = b.id
    ORDER BY o.order_date DESC
";
$orderResult = $conn->query($orderQuery);
$orders = [];
while ($row = $orderResult->fetch_assoc()) {
    $orders[$row['order_id']]['details'] = [
        'user_id' => $row['user_id'],
        'total_price' => $row['total_price'],
        'order_date' => $row['order_date']
    ];
    $orders[$row['order_id']]['books'][] = [
        'title' => $row['title'],
        'quantity' => $row['quantity']
    ];
}


$userQuery = "SELECT id, username, email, is_admin FROM users ORDER BY id ASC";
$userResult = $conn->query($userQuery);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Books</title>
    <link id="icon" rel="icon" type="image/png" href="pics/pngwing.com.png">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="container">
        <h1>Admin - Manage Books</h1>
        <h2>Add a new book</h2>
        <form class="form1" method="POST" id="addBookForm">
            <input type="hidden" name="action" value="add">
            <input type="text" name="title" placeholder="Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <input type="number" step="0.01" name="price" placeholder="Price (DZD)" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="number" name="stock" placeholder="Stock" required>
            <input type="text" name="image_url" placeholder="Image URL" required>
            <button type="button" onclick="confirmAddBook()">Add Book</button>
        </form>

        <h2>Existing books</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                    <td><?php echo $row['price']; ?> DZD</td>
                    <td><?php echo $row['stock']; ?></td>
                    <td>
                        <button id="edit" type="button" onclick="fillEditForm(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                        
                        <form method="POST" id="deleteForm<?php echo $row['id']; ?>" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button  type="button" onclick="confirmDelete(<?php echo $row['id']; ?>)" id="delete" >Delete</button>
                          
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    <h2>Order Management</h2>
<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Total Price</th>
            <th>Order Date</th>
            <th>Books Purchased</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $orderId => $order): ?>
            <tr>
                <td><?php echo $orderId; ?></td>
                <td><?php echo $order['details']['user_id']; ?></td>
                <td><?php echo $order['details']['total_price']; ?> DZD</td>
                <td><?php echo $order['details']['order_date']; ?></td>
                <td>
                    <ul>
                        <?php foreach ($order['books'] as $book): ?>
                            <li><?php echo htmlspecialchars($book['title']); ?> (x<?php echo $book['quantity']; ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<h2>User Management</h2>
<table>
    <thead>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($user = $userResult->fetch_assoc()): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo $user['is_admin'] == 1 ? 'Admin' : 'Client'; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

        <div class="container">
        <h2>Edit book</h2>
    <form class="form1" id="editForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" id="editId">
        <div class="form-group">
            <label for="editTitle"></label>
            <input type="text" name="title" id="editTitle" placeholder="Title" required>
        </div>
        <div class="form-group">
            <label for="editAuthor"></label>
            <input type="text" name="author" id="editAuthor" placeholder="Author" required>
        </div>
        <div class="form-group">
            <label for="editPrice"></label>
            <input type="number" step="0.01" name="price" id="editPrice" placeholder="Price (DZD)" required>
        </div>
        <div class="form-group">
            <label for="editDescription"></label>
            <textarea name="description" id="editDescription" placeholder="Description" required></textarea>
        </div>
        <div class="form-group">
            <label for="editStock"></label>
            <input type="number" name="stock" id="editStock" placeholder="Stock" required>
        </div>
        <div class="form-group">
            <label for="editImageUrl"></label>
            <input type="text" name="image_url" id="editImageUrl" placeholder="Image URL" required>
        </div>
        <button type="submit" class="btn">Save Changes</button>
    </form>
</div>

  

    <script>
function confirmAddBook() {
    const confirmation = confirm('Are you sure you want to add this book?');
    if (confirmation) {
        document.getElementById('addBookForm').submit();
    }
}

function confirmDelete(id) {
    const confirmation = confirm('Are you sure you want to delete this book?');
    if (confirmation) {
        document.getElementById('deleteForm' + id).submit();
    }
}

function fillEditForm(book) {
    console.log(book); // Debug : affiche les données pour vérification
    const editForm = document.getElementById('editForm');
    editForm.style.display = 'block';
    document.getElementById('editId').value = book.id;
    document.getElementById('editTitle').value = book.title;
    document.getElementById('editAuthor').value = book.author;
    document.getElementById('editPrice').value = book.price;
    document.getElementById('editDescription').value = book.description;
    document.getElementById('editStock').value = book.stock;
    document.getElementById('editImageUrl').value = book.image_url;

    editForm.scrollIntoView({ behavior: 'smooth' });
    alert('Edit form is ready. Please make your changes.');
}
    </script>
</body>
</html>
