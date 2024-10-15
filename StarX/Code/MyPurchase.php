<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login page.html");
    exit;
}

$user_email = $_SESSION['user_email'];

$host = 'localhost'; 
$dbname = 'StarX'; 
$username = 'root'; 
$password = '1234'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT * FROM purchases WHERE user_email = ?"; 
    
    $statement = $pdo->prepare($query);
    
    $statement->bindParam(1, $user_email);
    
    $statement->execute();
    
    $purchases = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
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

h1{
    margin-bottom: 15px;
}

.delivery{
    font-weight: bolder;
    font-size: 16px;
    padding: 10px 10px;
    right: 12px;
    margin-top: -52px;
    position: absolute;
}

.delivery:hover{
    color: white;
    border: 2px solid #f4f4f4;
    background-color: #393a41;
}

.content table{
    width: 1120px;
    max-height: 3000px;
}

.content th{
    border: 2px solid black;
    padding: 27px;
    font-size: 18px;
    text-align: center;  
}

.content td{
    border: 2px solid black;
    padding: 30px;
    font-size: 16px;
    text-align: center;  
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
                      <a href="login page.html" class="mp"><img src="./Image/signOut.png" alt="Sign Out Icon"></a></td>
                    <td class="function"><a onclick="signOut()" class="mv"> Sign Out </a></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="content">
        <h1>My Purchase History</h1>
        <button onclick="delivery()" class="delivery"> Delivery Details </button>
        <table>
            <tr>
                <th>Product Image</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Purchase Date</th>
                <th>Total Price</th>
            </tr>
            <?php
            $total_price = 0; 
            foreach ($purchases as $purchase) {
                echo "<tr>";
                echo "<td><img src='" . $purchase['product_image'] . "' alt='" . $purchase['product_name'] . "' style='width: 180px;'></td>";
                echo "<td>" . $purchase['product_name'] . "</td>";
                echo "<td>" . $purchase['quantity'] . "</td>";
                echo "<td>" . $purchase['price'] . "</td>";
                echo "<td>" . $purchase['purchase_date'] . "</td>";
                echo "<td>" . $total_price = $purchase['price']*$purchase['quantity'] . "</td>";
                echo "</tr>";
                
            }
            ?>
        </table>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch("get_purchases.php")
        .then(response => response.json())
        .then(data => {
            const purchaseList = document.getElementById("purchaseList");
            data.forEach(purchase => {
                const purchaseItem = document.createElement("div");
                purchaseItem.classList.add("purchase-item");
                purchaseItem.innerHTML = `
                    <h2>${purchase.product_name}</h2>
                    <p>Price: ${purchase.price}</p>
                    <p>Date: ${purchase.purchase_date}</p>
                `;
                purchaseList.appendChild(purchaseItem);
            });
        })
        .catch(error => console.error("Error fetching data:", error));
    });

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

    function delivery() {
        window.location.href="Delivery.php";
    }
    </script>
</body>
</html>
