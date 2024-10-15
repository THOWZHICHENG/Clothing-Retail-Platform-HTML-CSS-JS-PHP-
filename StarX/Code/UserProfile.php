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

if ($result === false) {
    echo "Error: " . $conn->error;
} else {
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        $_SESSION['firstName'] = $row['fName'];
        $_SESSION['lastName'] = $row['lName'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['phoneNo'] = $row['phoneNo'];
        $_SESSION['gender'] = $row['gender'];
        $_SESSION['dob'] = $row['DOB'];
        $_SESSION['address'] = $row['address']; 
    } else {
        echo "<script>window.location.href = 'login.php';</script>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phoneNo = $_POST['phoneNo'];
    $gender = $_POST['gender'];
    $address = $_POST['address']; 
    
    $day = $_POST['day'];
    $month = $_POST['month'];
    $year = $_POST['year'];

    $dob = "$year-$month-$day"; 

    $sql = "UPDATE User SET fName='$firstName', lName='$lastName', phoneNo='$phoneNo', gender='$gender', DOB='$dob', address='$address' WHERE email='$email'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Details Saved!');</script>";
        echo "<script>window.location.href = 'UserProfile.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
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

        .func-list {
            font-size:18px;
            padding: 40px 100px;
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

        .profile-details {
            width: 1050px;
            height: 500px;
            margin: -240px 20px 80px 360px;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .profile-details table {
            padding: 100pt;
            width: 100%;
        }

        .profile-details td {
            padding: 20px 50px 20px 20px;
            text-align: left;
        }

        .profile-details input[type="text"]{
            font-size: 16px;
            font-family: Georgia, 'Times New Roman', Times, serif;
            width: 50%;
            padding: 10px 320px 10px 10px;
            border: 2px solid rgba(0, 0, 0, 0.1);
            -webkit-transition: 0.5s;
            transition: 0.5s;
        }

        input[type=text]:focus {
            border: 2px solid #48555d;
        }

        textarea{
            padding: 10px;
            font-family: Georgia, 'Times New Roman', Times, serif;
            font-size: 16px;
            width: 94.5%;
            resize: vertical;
        }

        textarea:focus {
            border: 2px solid #48555d;
        }

        .profile-details button {
            background-color: #337ab7;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        input[type="Submit"]{
            width: 100px;
            height: 30px;
            border: 1px solid;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 750;
            cursor: pointer;
            margin-right: 0px;
        }
        
        input[type="Submit"]:hover{
            background: #48555d;
            color: #e9f4fb;
            transition: .5s;
        }

        .mp,.mv {
            color: black;
            text-decoration: none;
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
		      <a href="UserProfile.php"><img src="./Image/userProfile.png" alt="Profile Icon"></td>
                    <td class="function" style="color:lightblue;"><a href="UserProfile.php" class="mp" style="color: blue;"> Profile </td>
                </tr>
                <tr>
                    <td class="function">
                      <a href="MyPurchase.php" class="mp"><img src="./Image/myPurchase.png" alt="My Purchase Icon"></a></td>
                    <td class="function"><a href="MyPurchase.php" class="mp"> My Purchase </a></td>
                </tr>
                <tr>
                    <td class="function"><a href="FAQ.html"> <img src="./Image/contactUs.png" alt="Contact Us Icon"> </a></td>
                    <td class="function"><a href="FAQ.html" class="mv"> Contact Us </td>
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
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="return validateForm()">
        <table class="profile-details">
        <tr>
            <td style="font-size:18px;"> First Name </td>
            <td> <input type="text" name="firstName" value="<?php echo isset($_SESSION['firstName']) ? $_SESSION['firstName'] : ''; ?>"/> </td>
        </tr>
        <tr>
            <td style="font-size:18px;"> Last Name </td>
            <td> <input type="text" name="lastName" value="<?php echo isset($_SESSION['lastName']) ? $_SESSION['lastName'] : ''; ?>"/> </td>
        </tr>
        <tr>
            <td style="font-size:18px;"> Email </td>
            <td style="font-size:16px;">&nbsp;&nbsp;<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>
            <input type="hidden" name="email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>"></td>
        </tr>
        <tr>
            <td style="font-size:18px;"> Phone Number</td>
            <td> <input type="text" name="phoneNo" value="<?php echo isset($_SESSION['phoneNo']) ? $_SESSION['phoneNo'] : ''; ?>"/></td>
        </tr>
        <tr>
            <td style="font-size:18px;"> Address</td>
            <td> 
                <textarea style="font-size:16px;" name="address" rows="4"><?php echo isset($_SESSION['address']) ? $_SESSION['address'] : ''; ?></textarea>
            </td>
        </tr>
            <td style="font-size:18px;"> Gender</td>
            <td style="font-size:16px;"> 
                <input type="radio" name="gender" value="Male" <?php echo isset($_SESSION['gender']) && $_SESSION['gender'] == 'Male' ? 'checked' : ''; ?>> Male 
                <input type="radio" name="gender" value="Female" <?php echo isset($_SESSION['gender']) && $_SESSION['gender'] == 'Female' ? 'checked' : ''; ?>> Female 
                <input type="radio" name="gender" value="Prefer not to state" <?php echo isset($_SESSION['gender']) && $_SESSION['gender'] == 'Prefer not to state' ? 'checked' : ''; ?>> Prefer not to state
            </td>
        </tr>
            <tr>
            <td style="font-size:18px;"> Date of Birth</td>
            <td style="font-size:16px;">
                Day
                <select name="day" id="daySelect">
                </select>
                Month
                <select name="month" id="monthSelect">
                </select>
                Year
                <select name="year" id="yearSelect">
                </select>
            </td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="submit" value="Save"></td>
            </tr>
        </table>
    </form>
    <script>
        var daySelect = document.getElementById('daySelect');
        var monthSelect = document.getElementById('monthSelect');
        var yearSelect = document.getElementById('yearSelect');

        function populateDayOptions() {
            for (var day = 1; day <= 31; day++) {
                var option = document.createElement('option');
                option.value = day;
                option.textContent = day;
                daySelect.appendChild(option);
            }
        }

        function populateMonthOptions() {
            var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            for (var i = 0; i < months.length; i++) {
                var option = document.createElement('option');
                option.value = i + 1; 
                option.textContent = months[i];
                monthSelect.appendChild(option);
            }
        }
        function populateYearOptions() {
            var currentYear = new Date().getFullYear();
            for (var year = currentYear; year >= 1923; year--) {
                var option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                yearSelect.appendChild(option);
            }
        }

        populateDayOptions();
        populateMonthOptions();
        populateYearOptions();

        function selectDateOfBirth() {
            var dob = "<?php echo isset($_SESSION['dob']) ? $_SESSION['dob'] : ''; ?>";
            if (dob !== "") {
                var dobDate = new Date(dob);
                var selectedDay = dobDate.getDate();
                var selectedMonth = dobDate.getMonth() + 1; 
                var selectedYear = dobDate.getFullYear();

                daySelect.value = selectedDay;
                monthSelect.value = selectedMonth;
                yearSelect.value = selectedYear;
            }
        }

        selectDateOfBirth();

        function search() {
            window.location.href = "";
        }

        function validateForm() {
            var confirmSave = confirm("Save Details?");

            if (confirmSave == true) {
                var firstName = document.getElementById('firstName').value;
                var lastName = document.getElementById('lastName').value;
                var email = document.getElementById('email').value;
                var phoneNo = document.getElementById('phoneNo').value;
                var gender = document.querySelector('input[name="gender"]:checked');
                return confirmSave;
            } else {
                alert("Changes Cancel!")
                return false; 
            }
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
