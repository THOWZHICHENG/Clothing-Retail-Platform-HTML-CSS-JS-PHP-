<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login page.html");
    exit;
}

$user_email = $_SESSION['user_email'];

$servername = 'localhost'; 
$dbname = 'StarX'; 
$username = 'root'; 
$password = '1234'; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT Delivery.*, purchases.product_name, DATE_ADD(Delivery.deliveryDate, INTERVAL 12 DAY) AS estimatedArrivedDate 
        FROM Delivery 
        INNER JOIN purchases ON Delivery.purchaseID = purchases.id"; 
$result = $conn->query($sql);

if ($result === false) {
    echo "Error fetching delivery information: " . $conn->error;
} else {
    $delivery = $result->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delivery_ids'])) {
    foreach ($_POST['delivery_ids'] as $delivery_id) {
        $delivery_id = $conn->real_escape_string($delivery_id);

        $update_query = "UPDATE Delivery SET status = 'received' WHERE deliveryID = '$delivery_id'";

        if ($conn->query($update_query) === TRUE) {
        } else {
            echo "Error updating delivery: " . $conn->error;
        }
    }
}
?>

    <!DOCTYPE html>
    <html>
    <head>
        <script
        src="https://kit.fontawesome.com/1935d064dd.js"
        crossorigin="anonymous"
        ></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <title>My Purchase</title>
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
            text-align: center;
        }

        .func-list {
            position: relative;
            padding: 40px 100px;
	        font-size: 18px;

        }

        .function {
            cursor: pointer;
            padding: 10px;
            font-weight: bolder;
        }

        .func-list img {
            width: 24px;
            height: 24px;
        }

        button {
            cursor: pointer;
        }

        .mp,.mv {
            color: black;
            text-decoration: none;
        }

    .content{
        position: absolute; 
        top: 160px; 
        right: 210px; 
        width: 1150px;
        margin: 50px auto;
        padding: 20px;
        border: 1px solid #ccc;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
    }
    .content a{
        color: #8e9cab;
        font-size: 18px;
        text-decoration: none;
    }
    .content a:hover{
        text-decoration: underline;
    }
    .content h1 {
        text-align: center;
        margin-top: 20px;
    }
    .content table {
        width: 1000px;
        max-height: 3000px;
        margin: 20px auto;
        border-collapse: collapse;
    }
    .content th,.content td {
        font-size: 16px;
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    .content th {
        background-color: #f2f2f2;
    }
    .content tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .content tr:hover {
        background-color: #f2f2f2;
    }
    .content .receive_button {
        margin-left:1000px;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    .content .receive_button:hover {
        background-color: #0056b3;
    }
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
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

            <div class="func-list">
                <table>
                    <tr>
                        <td class="function">
                <a href="./UserProfile.php"><img src="./Image/userProfile.png" alt="Profile Icon"></a></td>
                        <td class="function" style="color:#1E90FF;"><a href="./UserProfile.php" class="mp"> Profile </a></td>
                    </tr>
                    <tr>
                        <td class="function">
                        <a href="MyPurchase.php" class="mp"><img src="./Image/myPurchase.png" alt="My Purchase Icon"></a></td>
                        <td class="function"><a href="MyPurchase.php" class="mp" style="color: blue;"> My Purchase </a></td>
                    </tr>
                    <tr>
                        <td class="function"><a href="FAQ.html"> <img src="./Image/contactUs.png" alt="Contact Us Icon"></a></td>
                        <td class="function"><a href="FAQ.html" class="mp"> Contact Us </a></td>
                    </tr>
                    <tr>
                        <td class="function">
                        <a href="login page.html" class="mp"><img src="./Image/signOut.png" alt="Sign Out Icon"></a>
                    </td>
                        <td class="function"><a onclick="signOut()" class="mv"> Sign Out </a></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="content">
        <a href="MyPurchase.php">Back To Purchase</a><h1>Delivery Information</h1>
        <form id="delivery" method="post" action="">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Delivery ID</th>
                        <th>Product Name</th>
                        <th>Address</th>
                        <th>Delivery Date</th>
                        <th>Estimated Arrived Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($delivery as $row): ?>
                        <tr>
                            <td><input type="checkbox" class="delivery-checkbox" name="delivery_ids[]" value="<?= $row['deliveryID'] ?>" /></td>
                            <td><?= $row['deliveryID'] ?></td>
                            <td><?= $row['product_name'] ?></td>
                            <td><?= $row['address'] ?></td>
                            <td><?= $row['deliveryDate'] ?></td>
                            <td><?= $row['estimatedArrivedDate'] ?></td> 
                            <td><?= $row['status'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
        <button class="receive_button" onclick="hideSelectedDeliveries()"> Receive </button>
    </div>

        <script>
        function hideSelectedDeliveries() {
            document.getElementById("delivery").submit();
        }

        function signOut() {
            if (confirm("Are you sure you want to sign out?")) {
                byeMessage();
                window.location.href="login page.html";
            } else {
                alert("You're back!")
            }
        }

        function byeMessage() {
            alert("Bye!");
            seeYouNextTime();
        }

        function seeYouNextTime() {
            alert("See you next time!");
        }
    </script>
    </body>
    </html>
