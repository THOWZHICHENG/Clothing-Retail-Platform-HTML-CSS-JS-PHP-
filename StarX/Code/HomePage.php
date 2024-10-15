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
$database = "StarX";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM User WHERE email = '$user_email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userName = $row['fName'] . " " . $row['lName']; 
} else {
    $userName = "Guest"; 
}

?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://kit.fontawesome.com/1935d064dd.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Home Page</title>
    <style>
        html {
            font-size: 10px;
        }

        body {
            font-family: "Open Sans", sans-serif;
            background-color: #EAEAEA;
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
            top: -36%;
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

        .welcome {
            color: white;
            text-align: center;
            padding-top: 100px;
            padding-bottom: 50px;
        }

        .welcome h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .welcome p {
            font-size: 2rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        a {
            font-size: 30px;
            color: #85B3FF;
            text-decoration: none;
        }

        a:hover{
            font-size: 36px;
        }

        .welcome a {
            font-size: 30px;
            color: #85B3FF;
            text-decoration: none;
            position: relative; 
        }

        .welcome a::after {
            content: "Click here to User Profile";
            font-size: 20px;
            color: lightgrey; 
            display: none;
            position: absolute;
            top: 100%; 
            left: -40px;
            white-space: pre;
        }

        .welcome a:hover::after {
            display: block; 
        }

        .search-result img{
            width: 300px;
            height: auto;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;           
        }

        .search-result img:hover{
            transform: scale(1.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #EAEAEA;
        }
        th {
            font-size: 4rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            padding: 15px 0;
            text-align: center;
        }
        .top-items {
            text-align: center;
        }
        .top-items img {
            width: 500px;
            height: auto;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .top-items img:hover {
            transform: scale(1.05);
        }
        .container-2 {
        max-width: 900px;
        margin: 10rem auto;
        padding: 0px 20px;
        }
        .container-2 h1{
            font-size: 4rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        .container-2 .text {
        text-align: center;
        margin-bottom: 2rem;
        }
        .container-2 .tabs {
        display: flex;
        border: 2px solid #393a41;
        }
        .container-2 .tab-links {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 25%;
        border-right: 2px solid #393a41;
        z-index: 1;
        }
        .container-2 button.tab-link {
        padding: 3rem;
        width: 100%;
        font-size: 2rem;
        border: none;
        outline: none;
        border-bottom: 1px solid #393a41;
        }
        .container-2 .active {
        background-color: white;
        }
        .container-2 .tab-contents {
        font-size: 1.8rem;
        padding: 1rem;
        }
        .tab-contents p {
        font-size: 1.8rem;  
        }
        .tab-contents a {
        font-size: 2rem;  
        }
        .tab-contents a:hover {
        font-size: 4rem;  
        }
        .container-2 .tab-content {
        display: none;
        animation: slide-right 0.5s ease 1;
        }
        .container-2 .about {
        display: block;
        }

        @keyframes slide-right {
        0% {
            transform: translateX(-10rem);
        }
        100% {
            transform: translateX(0);
        }
        }
        @media screen and (max-width: 600px) {
        .tab-links {
            width: 50%;
        }
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
                <form action="HomePage.php" method="POST">
                    <div class="searchbar">
                        <input type="text" name="query" style="width:1000px;height:50px;" placeholder="Browse in StarX (Specific Category: Hoodies, Sweaters, Jackets, T-shirts)">
                        <button class="icon" type="submit"><i class="fa fa-search" style="font-size:30px"></i></button>
                    </div>
                </form>
                <a href="ShoppingCart.php"><button class="shopicon" type="submit"><i class="fa fa-shopping-cart" style="font-size:50px"></i></button></a>
            </div>
            </nav>
        </div>

        <div class="welcome">
            <h1>Welcome to StarX</h1>
            <p>Find the best products for you!</p> 
            <br><br>
            <p>Welcome, <a href="UserProfile.php" title="Click here to User Profile"><?php echo $userName; ?></a></p>
        </div>
    </div>

    <?php 
        if(isset($_POST['query']) ) {
            if(!empty($_POST['query'])){
            $search_query = $_POST['query'];

            $sql = "SELECT * FROM Category WHERE name LIKE '%$search_query%'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<table> ";
                echo "<tr><th colspan='5'>Search Results for: $search_query</th></tr>";

                echo "<tr class='search-result'>";
                while ($row = $result->fetch_assoc()) {
                    echo "<td><center><a href='{$row['Page_link']}'><img src='{$row['category_image']}' alt='{$row['name']}'></a></center></td>";
                }
                echo "</tr>";

                echo "</table>";
                exit;
            } else {
                echo "<table> ";
                echo "<tr><th colspan='5'>No results found for '{$search_query}'</th></tr>";
                echo "</table>";
                exit;
            }
        }else{
            echo "<table> ";
            echo "<tr><th colspan='5'>Empty Result</th></tr>";
            echo "</table>";
            exit;
        }
        } else {
        }
    ?>


        <div class="container-2" >
        <div class="text">
          <h1>FAQs Section</h1>
          <p>Click on the tab buttons to know more.</p>
      </div>
      <div class="tabs">
          <div class="tab-links">
              <button class="tab-link active" data-filter="about">Who we are.</button>
              <button class="tab-link" data-filter="services">What we do.</button>
              <button class="tab-link" data-filter="contact">Get in touch.</button>
          </div>
          <div class="tab-contents">
              <div class="tab-content about">
                  <h2>Who We Are.</h2>
                  <p>
                  StarX is a new fashion clothing company in Malaysia, founded by three fashion IT student. <br>
                  Our slogan, 'Being a fashion icon with StarX,' encapsulates our vision. <br>
                  StarX represents the idea that everyone can be a star, and we are the 'X' that supports you.
                  </p>
              </div>
              <div class="tab-content services">
                  <h2>What we do.</h2>
                  <p>
                  Our website is an e-commerce platform that sells a variety of fashion clothing, <br>
                  including sweaters, hoodies, jackets, and T-shirts.
                  </p>
              </div>
              <div class="tab-content contact">
                  <h2>Get in touch.</h2>
                  <p> 
                  We are an active company that wants to know more about customer feedback to improve our services. <br>    
                  Feel free to share your thoughts with <a href="FAQ.html" >us</a>!
                  </p>
              </div>
          </div>
        </div>
      </div>

    <table>
        <tr>
            <th colspan="3">Hot Sales</th>
        </tr>
        <tr class = "top-items">
            <td><a href="ProductPage1.php"><img src="https://down-my.img.susercontent.com/file/194f2011d1074c02fa08a1a38b0334fa"></a></td>
            <td><a href="ProductPage2.php"><img src="https://img.ws.mms.shopee.com.my/cn-11134207-7r98o-lp0l8z61odljeb"></a></td>
            <td><a href="ProductPage3.php"><img src="https://down-my.img.susercontent.com/file/cbceac8bb0a5731a48f22eedcb732a5f"></a></td>
        </tr>
        <tr class = "top-items">
            <td><a href="ProductPage4.php"><img src="https://down-my.img.susercontent.com/file/a25230bf98758ba86fa39b8cfbf8fba7"></a></td>
            <td><a href="ProductPage5.php"><img src="https://down-my.img.susercontent.com/file/my-11134201-23020-p0flp36x2snv93"></a></td>
            <td><a href="ProductPage6.php"><img src="https://down-my.img.susercontent.com/file/ddee66fea89257fd5d82b4afccf416fb"></a></td>
        </tr>
    </table>
    </div>

    

    <script>
    const tabLinks = document.querySelectorAll(".tab-link");
    const allContent = document.querySelectorAll(".tab-content");

    tabLinks.forEach(tabLink => {
        tabLink.addEventListener("click", function (e) {    
            tabLinks.forEach(link => {
                link.classList.remove("active");
            });
            this.classList.add("active");

            const filter = this.dataset.filter;
            allContent.forEach(content => {
                if (content.classList.contains(filter)) {
                    content.style.display = "block";
                } else {
                    content.style.display = "none";
                }
            });
        });
    });
</script>
</body>
</html>
