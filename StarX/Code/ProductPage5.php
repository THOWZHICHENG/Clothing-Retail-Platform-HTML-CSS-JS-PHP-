<?php
    session_start();
    if (!isset($_SESSION['user_email'])) {
        header("Location: login page.html");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $user_email = $_SESSION['user_email'];

    $servername = "localhost";
    $username = "root";
    $password = "1234";
    $dbname = "StarX";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
        $color = $_POST["color"];
        $size = $_POST["size"];
        $quantity = $_POST["quantity"];

        $query = "SELECT productID FROM Product WHERE color = '$color' AND size = '$size'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $productId = $row["productID"];
            
            $checkCartQuery = "SELECT * FROM Cart WHERE productID = $productId";
            $checkCartResult = $conn->query($checkCartQuery);

            if ($checkCartResult->num_rows > 0) {
                $updateQuantityQuery = "UPDATE Cart SET quantity = quantity + $quantity WHERE productID = $productId";
                if ($conn->query($updateQuantityQuery) === TRUE) {
                    echo "<script>window.location.href = 'ProductPage5.php';</script>";
                } else {
                    echo "<script>alert('Error Updating');</script>";
                    echo "<script>window.location.href = 'ProductPage5.php';</script>";
                }
            } else {
                $sql = "INSERT INTO Cart (productID, quantity) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $productId, $quantity);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "<script>alert('Product Added');</script>";
                    echo "<script>window.location.href = 'ProductPage5.php';</script>";
                } else {
                    echo "<script>alert('Error Adding Product');</script>";
                    echo "<script>window.location.href = 'ProductPage5.php';</script>";
                }

                $stmt->close();
            }
        } else {
            echo "<script>alert('Product not found');</script>";
        }

        $conn->close();
    }
    ?>


<!DOCTYPE html>
<html>
<head>
    <title>Product Page</title>
    <script
      src="https://kit.fontawesome.com/1935d064dd.js"
      crossorigin="anonymous"
    ></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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

            .container{
                background: linear-gradient(120deg, #8e9cab, #393a41);
            }

            .logo {
                padding-top: 20pt;
                padding-left: 10%;
            }
            .logo img{
                margin-top: -5px;
                margin-left: -50px;
                margin-right: 45px;
            }

            .search-container {
                position: absolute;
                width: 300px;
            }

            .search-container input[type="text"] {
                margin: -20px 60px;
                width: 100%;
                padding: 30px;
                border: 1px solid #878787;
                border-radius: 5px;
                box-sizing: border-box;
            }

            .shopicon {
                position: absolute;
                top: -80%;
                left: 390%;
                width: 72px;
                height: 56px;
                transition: all 0.8s ease;
                color: #333;
                border-radius: 6px;
            }

            .shopicon:hover {
                transform: scale(1.2);
                color: rgb(230, 230, 230);
                background-color: #464646;
            }

            .icon {
                position: absolute;
                top: -30%;
                left: 300%;
            }

            .searchbar input{
                font-size: 16px;
                border: none;
                outline: none;
            }

            .search-container .icon {
                margin-top: -7.8px;
                margin-left: 100px;
                background-color: #c1c1c1;
                width: 50px; 
                height: 50px; 
                border: none;
                border-radius: 5px;
                cursor: pointer; 
            }

            .search-container .icon {
                padding: 10px;
                width: 50px;
                height: 50px;
            }

            .search-container .icon:hover {
                color: whitesmoke;
                background-color: #4b4b4b; 
                transition: all 0.8s ease;
            }

            .navbar {
                display: flex;
                align-items: center;
                padding: 30px 20px;
            }

            nav {
                flex: 1;
                text-align: center;
            }

    .container-2 {
        max-width: 1200px;
        max-height: 6000px;
        margin: 40px auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 90px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
    }

    .product-details {
        display: flex;
        flex-wrap: wrap;
    }

    .show-image img{
        width: 120px;
        height: 120px;
        margin-right: 1px;
        display: inline;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: 2px solid #fff;
    }

    .show-image img:hover{
        border: 2px solid black;
    }

    .product-image{
        width: 505px;
        height: 450px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .product-info {
        padding: 20px;
        flex: 1;
    }

    h1 {
        font-size: 30px; 
        font-weight: bold; 
        color: #2c3e50; 
        margin: 10px 0; 
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
        text-shadow: 2px 2px 4px lightgrey; 
    }

    .price {
        font-weight: bold; 
        margin: 10px 0;
        text-shadow: 2px 2px 4px lightgrey; 
    }

    .col p, .rating{
        position: relative;
        display: inline;
    }

    .review{
        font-size: 10px;
        margin-left: 70%;
    }

    a{
        text-decoration: none;
        color: #555;
    }

    .product-description {
        margin: 10px 0 60px 0;
    }

    .selection{
        font-weight: bolder;
        height: 30px;
        width: 150px;
    }

    td{
        padding: 20px;
    }

    .product-border{
        padding-top: 5px;
        border-top-color: rgb(194, 194, 194);
        border-top-style: solid;
        padding-bottom: 5px;
        border-bottom-color: rgb(194, 194, 194);
        border-bottom-style: solid;
        display: block;
    }

    .head{
        font-size: 24px;
        text-shadow: 2px 2px 4px lightgrey; 
        text-decoration: underline;
        font-weight: bolder;
    }

    .content {
        font-size: 18px; 
    }

    .button{
        margin-left: 60%;
        margin-top: 10px;
    }

    .button .submit, input{
        margin-top: 20px;   
        margin-right: 0%;
        padding: 10px;
    }

    .submit {
        color: rgb(80, 88, 105);
        background-color: white;
        font-weight: bolder;
        margin-top: 20px;
        margin-left: 0%;
        padding: 10px 44px;
    }

    .submit:hover {
        background: rgb(80, 88, 105);
        color: #e9f4fb;
        transition: .5s;
    }

    .product-details p {
        font-size: 16px;
        margin: 10px 0;
    }
    .container-3 a{
        font-size:15px;
        color: #a6a6a6;
            }
            .container-3 a:hover{
                text-decoration:underline;
                color: #a6a6a6;
            }
            .container-3 h1 {
                margin-top: 120px;
                margin-bottom: 20px;
            }
            .container-3 form {
                margin-bottom: 20px;
            }
            .container-3 label {
                display: block;
                margin-bottom: 5px;
            }
            .container-3 input[type="number"],
            textarea {
                width: 100%;
                padding: 8px;
                margin-bottom: 10px;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
            }
            .container-3 input[type="submit"] {
                background-color: #4caf50;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                padding: 10px 20px;
                font-size: 16px;
            }
            .container-3 input[type="submit"]:hover {
                background-color: #45a049;
            }
            .container-3 .review {
                background-color: #f9f9f9;
                padding: 10px;
                margin-bottom: 10px;
                margin-left:0;
                border-radius: 4px;
            }
            .container-3 .review p {
                margin: 0;
            }
            .container-3 .review .date {
                font-size: 12px;
                color: #888;
            }
        </style>
</head>
<body>
	<div class="container">
        <div class="navbar">
            <div class="logo">
                <a href="HomePage.php"><img src="./Images/StarX Logo.jpg" width="120px" height="90px" alt="StarX Logo"></a>
            </div>
            <nav>
                <div class="search-container">
                    <div class="searchbar">
                        <a href="ShoppingCart.php"><button class="shopicon" type="submit" ><i class="fa fa-shopping-cart" style="font-size:50px"></i></button></a>
                    </div>
                </div>
            </nav>
        </div>
    </div>

	<div class="container-2">
        <div class="product-details">
            <div>
                <img class="product-image" src="./Images/ProductImage5.1.jpeg" id="MainImg" alt="Product Image 1">
                <br>

                <div class="show-image">
                    <img src="./Images/ProductImage5.1.jpeg" class="small-img" alt="Product Image 1">
                    <img src="./Images/ProductImage5.2.jpeg" class="small-img" alt="Product Image 2">
                    <img src="./Images/ProductImage5.3.jpeg" class="small-img" alt="Product Image 3">
                    <img src="./Images/ProductImage5.4.jpeg" class="small-img" alt="Product Image 4">
                </div>
            </div>

            <div class="product-info">
                <h1>Thin Long-sleeved Hoodies</h1>
                <div class="col">
                    <p class="price" style="font-size: 21px;">RM22.90</p>

                    <div class="rating">
                            <div class="review">
                            <?php
                            $total_reviews_query = "SELECT COUNT(*) AS total_reviews FROM Review WHERE productID = ?";
                            $stmt_total_reviews = $conn->prepare($total_reviews_query);
                            $stmt_total_reviews->bind_param("i", $product_id); 
                            $product_id = 5; 
                            $stmt_total_reviews->execute();
                            $result_total_reviews = $stmt_total_reviews->get_result();
                            $total_reviews = 0;

                            if ($result_total_reviews && $row_total_reviews = $result_total_reviews->fetch_assoc()) {
                                $total_reviews = $row_total_reviews['total_reviews'];
                            }

                            $average_rating_query = "SELECT AVG(rating) AS average_rating FROM Review WHERE productID = ?";
                            $stmt_average_rating = $conn->prepare($average_rating_query);
                            $stmt_average_rating->bind_param("i", $product_id); 
                            $stmt_average_rating->execute();
                            $result_average_rating = $stmt_average_rating->get_result();
                            $average_rating = 0;

                            if ($result_average_rating && $row_average_rating = $result_average_rating->fetch_assoc()) {
                                $average_rating = $row_average_rating['average_rating'];
                            }

                            if ($average_rating !== null) {
                                echo "<p id='ratingValue'><a href='#product_reviews'>Rating (<span id='currentRating'>" . number_format($average_rating, 1) . "</span>) " . $total_reviews . " Reviews</a></p>";
                            } else {
                                echo "<p id='ratingValue'><a href='#product_reviews'>(0 rating) 0 review</a></p>";
                            }
                            ?>
                            </div>
                        </div>
                </div>

                <br><br>

                <form method="POST" onsubmit="return validateForm()">
                    <div class="product-border">  
                        <table class="product-description" width="600px">
                            <tr>
                                <td class="head">Product Details</td>
                            </tr>
                            <tr>
                                <td class="content"> Color </td>
                                <td> 
                                    :
                                    <select id="color" name="color" class="selection">
                                        <option>White</option>
                                        <option>Red</option>
                                        <option>Light Blue</option>
                                    </select>
                                </td>
                            </tr>
                    
                            <tr>
                                <td class="content"> Size </td> 
                                <td>
                                    :
                                    <select id="size" name="size" class="selection">
                                        <option>XS</option>
                                        <option>S</option>
                                        <option>M</option>
                                        <option>L</option>
                                        <option>XL</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                
                    <div class="button">
                        <input type="number" name="quantity" style="width:50px;" /> 
                        <button class="submit" name="add_to_cart" type="submit">ADD TO CART</button>
                    </div>
                </form>
            </div>
                        </div>

    <div class="container-3">
            <h1 id="product_reviews">Product Reviews<a href="#top"> <br> Back to Top </a></h1>

            <form action="" method="post">
                <input type="hidden" name="product_id" value="5"> 
                <label style="font-size:20px;" for="rating">Rating (1-5):</label>
                <input style="font-size:20px;" type="number" id="rating" name="rating" min="1" max="5" required>
                <label style="font-size:20px;" for="comment">Your Review:</label>
                <textarea style="font-size:20px;" id="comment" name="comment" rows="4" required></textarea>
                <input type="submit" name="submit_review" value="Submit Review">
            </form>

            <?php
            $servername = "localhost";
            $username = "root";
            $password = "1234";
            $dbname = "StarX";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $product_id = 5; 
            if (isset($_POST['submit_review'])) {
                $comment = $_POST["comment"];
                $rating = $_POST["rating"];
                $user_email = $_SESSION['user_email']; 

                $userIdQuery = "SELECT user_id FROM User WHERE email = ?";
                $stmt = $conn->prepare($userIdQuery);
                $stmt->bind_param("s", $user_email); 
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result && $row = $result->fetch_assoc()) {
                    $userId = $row['user_id'];
                } else {
                    echo "User ID not found or row is null";
                }

                $insertReviewQuery = "INSERT INTO Review (productID, userID, rating, comment, dateCreated) VALUES (?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($insertReviewQuery);
                $stmt->bind_param("iids", $product_id, $userId, $rating, $comment);

                if ($stmt->execute()) {
                    echo "<p>Review submitted successfully</p>";
                } else {
                    echo "<p>Error submitting review: " . $conn->error . "</p>";
                }

                $stmt->close();
            }

            if (isset($_POST["product_id"])) {
                $product_id = $_POST["product_id"];
                $sql = "SELECT Review.*, User.fName, User.lName FROM Review INNER JOIN User ON Review.userID = User.user_id WHERE Review.productID = ? ORDER BY Review.dateCreated DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='review'>";
                        echo "<p style='font-size:18px'><strong>User:</strong> {$row['fName']} {$row['lName']}</p>"; 
                        echo "<p style='font-size:18px'><strong>Rating:</strong> {$row['rating']}</p>";
                        echo "<p style='font-size:18px'>{$row['comment']}</p>";
                        echo "<p style='font-size:18px'class='date'>Date: {$row['dateCreated']}</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No reviews available</p>";
                }
            }
            $conn->close();
            ?>
        </div>
        </div>

    

<script>
    var MainImg = document.getElementById('MainImg');
        var smallImg = document.querySelectorAll('.small-img');

        smallImg[0].onclick = function () {
            MainImg.src = smallImg[0].src;
        };
        smallImg[1].onclick = function () {
            MainImg.src = smallImg[1].src;
        };
        smallImg[2].onclick = function () {
            MainImg.src = smallImg[2].src;
        };
        smallImg[3].onclick = function () {
            MainImg.src = smallImg[3].src;
        };


    function validateForm() {
        var quantity = document.getElementsByName('quantity')[0].value;

        var quantityValue = parseInt(quantity);

        if (isNaN(quantityValue) || quantityValue <= 0) {
            alert("Please enter a valid quantity.");
            return false;
        }
        return true;
    }

    const express = require('express');
    const bodyParser = require('body-parser');
    const mysql = require('mysql');

    const app = express();
    const port = 3000;

    const connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '1234',
    database: 'StarX'
    });

    connection.connect();

    app.use(bodyParser.json());
    app.use(bodyParser.urlencoded({ extended: true }));

    app.post('/addToCart', (req, res) => {
    const { color, size, material, quantity } = req.body;

    const query = `INSERT INTO Cart (productID, quantity) VALUES (?, ?)`;
    connection.query(query, [productId, quantity], (error, results, fields) => {
        if (error) {
        console.error(error);
        res.status(500).send('Error adding product to cart');
        } else {
        res.status(200).send('Product added to cart');
        }
    });
    });

    app.get('/cart', (req, res) => {
    const query = `SELECT * FROM Cart`;
    connection.query(query, (error, results, fields) => {
        if (error) {
        console.error(error);
        res.status(500).send('Error retrieving cart contents');
        } else {
        res.json(results);
        }
    });
    });

    app.listen(port, () => {
    console.log(`Server is running on http://localhost:3306`);
    });
</script>
</body>
</html>
