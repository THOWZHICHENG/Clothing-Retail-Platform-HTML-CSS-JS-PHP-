<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login page.html");
    exit;
}

$user_email = $_SESSION['user_email'];

$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "StarX";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['productId']) && isset($_POST['action'])) {
        $productId = $_POST['productId'];
        $action = $_POST['action'];

        if ($action === 'increase') {
            $sql = "UPDATE Cart SET quantity = quantity + 1 WHERE productID = ?";
        } elseif ($action === 'decrease') {
            $checkZero = "SELECT quantity FROM Cart WHERE productID = ?";
            $stmt = $conn->prepare($checkZero);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if ($row['quantity'] == 1) {
                    $deleteSql = "DELETE FROM Cart WHERE productID = ?";
                    $stmt = $conn->prepare($deleteSql);
                    $stmt->bind_param("i", $productId);
                    if (!$stmt->execute()) {
                        die("Error in deleting product: " . $conn->error);
                    }
                } else {
                    $sql = "UPDATE Cart SET quantity = GREATEST(quantity - 1, 0) WHERE productID = ?";
                }
            }
        }

        if (isset($sql)) {
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Error in preparing query: " . $conn->error);
            }
            $stmt->bind_param("i", $productId);
            if (!$stmt->execute()) {
                die("Error in executing query: " . $stmt->error);
            }
        }
    } elseif (isset($_POST['checkout'])) {

        $userId = 1; 
        $orderDate = date("Y-m-d H:i:s"); 

        $insertQuery = "INSERT INTO OrderDetail (productID, price, quantity, totalAmount, orderDate)
                        SELECT c.productID, p.price, c.quantity, p.price * c.quantity, NOW()
                        FROM Cart c
                        INNER JOIN Product p ON c.productID = p.productID";

        if ($conn->query($insertQuery) === TRUE) { 
            echo "<script>window.location.href = 'CheckOut.php';</script>";
        } else {
            echo "Error transferring data to OrderDetail: " . $conn->error;
        }
        
    }
}
?>




<!DOCTYPE html>
<html>
<head>
    <script src="https://kit.fontawesome.com/1935d064dd.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Shopping Cart</title>
    <style>
        html {
            font-size: 10px;
        }

        body {
            font-family: "Open Sans", sans-serif;
            background-color: #f4f4f4;
            color: #393a41;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        p {
            font-size: 1.6rem;
            line-height: 1.5;
        }

        img {
            width: 100%;
        }

        .container {
            background: linear-gradient(120deg, #8e9cab, #393a41);
        }

        .logo {
            padding-top: 20pt;
            padding-left: 10%;
        }

        .logo img {
            margin-top: -5px;
            margin-left: -50px;
            margin-right: 45px;
        }

        .navbar {
            display: flex;
            align-items: center;
            padding: 30px 20px;
        }

        .content {
            position: absolute;
            top: 160px;
            right: 100px;
            width: 1400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
        }

        h1 {
            border-bottom: 2px solid grey;
        }

        .back_button {
            margin-top: 36px;
            margin-left: 100px;
        }

        .back_button a {
            text-decoration: none;
            font-size: 16px;
            background-color: #f1f1f1;
            color: black;
            padding: 10px 20px;
            display: inline-block;
        }

        .back_button a:hover {
            background-color: #ddd;
            color: black; 
        }

        .content table {
            text-align:center;
            width: 1300px;
            max-height: 3000px;
        }

        .content th {
            margin: 40px;
            font-size: 24px;
            padding-top: 20px;
        }

        .content td {
            padding: 20px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .checkout-button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #393a41;
            color: #fff;
            border-radius: 10px;
            cursor: pointer;
        }

        .checkout-button:hover {
            background-color: white;
            color: black; 
        }

        .quantity-buttons {
            display: flex;
            align-items: center;
        }

        .plus,
        .minus {
            border: 2px solid black;
            color: #4a4b52;
            background-color: white;
            padding: 2px 6px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 10px;
        }

        .plus:hover,
        .minus:hover {
            background-color: #393a41;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <div class="logo">
                <a href="HomePage.php"><img src="./Images/StarX Logo.jpg" width="120px" height="90px" alt="StarX Logo"></a>
            </div>
        </div>
    </div>
    <div class="back_button">
        <a href="javascript:history.back()">&#8249; Back</a>
    </div>

    <div class="content">
        <h1>My Shopping Cart</h1>
        <table>
            <tr>
                <th>Product Image</th>
                <th>Product Name</th>
                <th>Details</th> 
                <th>Quantity</th>
                <th>Price</th>
                <th>Total Price</th>
            </tr>
            <?php
            $sql = "SELECT c.cartID, p.product_image, p.name AS product_name, p.size, p.color, p.price, c.quantity, (p.price * c.quantity) AS total_price, c.productID
                    FROM Cart c
                    INNER JOIN Product p ON c.productID = p.productID";
            
            $result = $conn->query($sql);

            $purchases = array();
            $totalPrice = 0;

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $purchases[] = $row;
                    $totalPrice += $row['total_price'];
                }
            } else {
                echo "<td colspan='7' style='font-size:16px; padding: 30px 30px;'>0 results</td>";
            }
            foreach ($purchases as $purchase) {
                echo "<tr>";
                echo "<td style='font-size:20px; padding: 30px 60px;'>
                        <a href='ProductPage.php?pageNumber=" . $purchase['productID'] . "'>
                            <img src='" . $purchase['product_image'] . "' alt='" . htmlentities($purchase['product_name']) . "' style='width: 180px;'>
                        </a>
                    </td>";

                echo "<td style='font-size:20px; '>" . $purchase['product_name'] . "</td>";
                echo "<td style='font-size:20px; '> Size: " . $purchase['size'] . "<br> Color: " . $purchase['color'] . "</td>"; 
                echo "<td style='font-size:20px; padding: 10px;'> 
                        <form method='post' style='display: inline;'>
                            <input type='hidden' name='cartId' value='" . $purchase['cartID'] . "'>
                            <input type='hidden' name='productId' value='" . $purchase['productID'] . "'>
                            <input type='hidden' name='action' value='decrease'>
                            <button type='submit' class='minus' style='display: inline;'>-</button>
                        </form>
                        <span class='quantity' style='display: inline; '>" . $purchase['quantity'] . "</span>
                        <form method='post' style='display: inline;'>
                            <input type='hidden' name='cartId' value='" . $purchase['cartID'] . "'>
                            <input type='hidden' name='productId' value='" . $purchase['productID'] . "'>
                            <input type='hidden' name='action' value='increase'>
                            <button type='submit' class='plus' style='display: inline;'>+</button>
                        </form>
                    </td>";
            
                echo "<td class='price' style='font-size:20px; padding: 30px 28px;'>" . $purchase['price'] . "</td>";
                echo "<td class='total-price-for-item' style='font-size:20px; padding: 30px 28px;'>" . $purchase['total_price'] . "</td>"; 
                echo "</tr>";
            }            

            echo "<tr class='total-row'>";
            echo "<td colspan='5' style='font-size:24px; padding: 15px 72px; text-align:left;'>Total Amount</td>";
            echo "<td class='total-price' style='font-size:20px; padding: 30px 28px;'>" . $totalPrice . "</td>";
            ?>
            <tr>
                <td colspan="5"></td>
                <form method="post" action="">
                    <td>
                        <div style="text-align: center; margin-top: 20px;">
                            <button type="submit" name="checkout" class="checkout-button">Checkout</button>
                        </div>
                    </td>
                </form> 
        </tr>
        </table>
    </div>

<script>
function increaseQuantity(productId) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_quantity.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var newQuantity = parseInt(xhr.responseText);
            var quantitySpan = document.querySelector('input[name="productId"][value="' + productId + '"]')
                .parentElement.querySelector('.quantity');
            if (quantitySpan) {
                quantitySpan.textContent = newQuantity;
            }
        }
    };
    xhr.send("productId=" + productId + "&action=increase");
}

function decreaseQuantity(productId) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_quantity.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var newQuantity = parseInt(xhr.responseText);
            var quantitySpan = document.querySelector('input[name="productId"][value="' + productId + '"]')
                .parentElement.querySelector('.quantity');
            if (quantitySpan) {
                quantitySpan.textContent = newQuantity;
            }
        }
    };
    xhr.send("productId=" + productId + "&action=decrease");
}
</script>
</body>
</html>
