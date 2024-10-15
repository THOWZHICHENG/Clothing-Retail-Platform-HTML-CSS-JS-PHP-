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

$checkPhoneNumberQuery = "SELECT phoneNo, address FROM User WHERE email = '$user_email'";
$result = $conn->query($checkPhoneNumberQuery);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $phone_number = $row['phoneNo'];
    $user_address = $row['address']; 
} else {
    $phone_number = "";
    $user_address = "";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['cancel_order'])) {
        $deleteOrderQuery = "DELETE FROM OrderDetail";
        if ($conn->query($deleteOrderQuery) !== TRUE) {
            echo "Error deleting order details: " . $conn->error;
        } else {
            header("Location: ShoppingCart.php");
            exit();
        }
    }

    if (isset($_POST['place_order'])) {
        $address = $_POST['address'];
        $payment = $_POST['payment'];
        $phoneNumber = $_POST['phoneNumber']; 
        
        if ($phoneNumber == "") { 
            echo "<script>alert('Please fill in your phone number!')</script>";
            echo "<script>window.location.href = 'CheckOut.php';</script>";
            exit;
        }

        if (!empty($address)) {
            $updateAddressQuery = "UPDATE User SET address = '$address' WHERE email = '$user_email'";
            if ($conn->query($updateAddressQuery) !== TRUE) {
                echo "Error updating address: " . $conn->error;
            }
        }
    
        $insertOrderQuery = $conn->prepare("INSERT INTO purchases (productID, product_name, product_image, price, quantity, purchase_date, user_email) 
                                            SELECT od.productID, p.name, p.product_image, od.price, od.quantity, NOW(), ? 
                                            FROM OrderDetail od 
                                            INNER JOIN Product p ON od.productID = p.productID");
    
        $insertOrderQuery->bind_param("s", $user_email);
    
        if ($insertOrderQuery->execute()) {
            $latestPurchaseId = $conn->insert_id;
    
            if($address != null){
                $insertDeliveryQuery = $conn->prepare("INSERT INTO Delivery (address, deliveryDate, purchaseID) VALUES (?, NOW(), ?)");
                $insertDeliveryQuery->bind_param("si", $address, $latestPurchaseId);
                $insertDeliveryQuery->execute();
            } else {
                echo "<script>alert('Please fill in your address!')</script>";
                echo "<script>window.location.href = 'CheckOut.php';</script>";
                exit;
            }
            $insertPaymentQuery = $conn->prepare("INSERT INTO Payment (paymentTime, paymentMethod, E_wallet_type, amount) VALUES (NOW(), ?, ?, ?)");
            $insertPaymentQuery->bind_param("ssd", $payment, $ewalletType, $final);
            $insertPaymentQuery->execute();

            echo "<script>alert('Thank you :)')</script>";
            echo "<script>alert('Enjoy you day !')</script>";
            echo "<script>window.location.href = 'MyPurchase.php';</script>";
        } else {
            echo "Error inserting data into purchases: " . $insertOrderQuery->error;
        }
    
        $deleteCartQuery = "DELETE FROM Cart";
        if ($conn->query($deleteCartQuery) !== TRUE) {
            echo "Error deleting cart items: " . $conn->error;
        }
    
        $deleteCartQuery = "DELETE FROM OrderDetail";
        if ($conn->query($deleteCartQuery) !== TRUE) {
            echo "Error deleting cart items: " . $conn->error;
        }
    }
}

$selectQuery = "SELECT od.*, p.name AS product_name
                FROM OrderDetail od
                INNER JOIN Product p ON od.productID = p.productID;";
$result = $conn->query($selectQuery);
$orders = [];
$totalPrice = 0;
$tax = 0;
$deliveryFee = 9.90;
$final = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
        $totalPrice += $row['totalAmount'];
    }
    $taxRate = 0.05;
    $tax = $totalPrice * $taxRate;
    $final = $totalPrice + $tax + $deliveryFee;
} else {
    echo "<script>window.location.href = 'ShoppingCart.php';</script>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        .order-summary {
            border-top: 2px solid #ccc;
            padding-top: 20px;
        }
        .order-summary table {
            width: 100%;
        }
        .order-summary th,
        .order-summary td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .order-details{
            text-align: center
        }
        .total-row, 
        .tax-row td,
        .delivery-fee-row,
        .final-total-row{
            text-align: right;
        }
        .place_order {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            margin-top: 20px;
        }
        .place_order:hover {
            background-color: #0056b3;
        }
        h2 {
            margin-top: 0;
            font-size: 24px;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-size: 16px;
            color: #555;
        }
        textarea {
            width: 98%;
            height: 100px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="tel"]{
            width: 98%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            display: block;
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .cancel_order {
            margin-left:41.5%;
            margin-right:auto;
            display: inline-block;
            padding: 10px 20px;
            color: red;
            border: none;
            background-color: #fff;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            margin-top: 0px;
        }

        .cancel_order:hover {
            background-color: #eee;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Checkout</h1>
    <div class="order-summary">
        <h2>Order Summary</h2>
        <table>
            <tr>
                <th>Order Date</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
            <?php foreach ($orders as $order): ?>
                <tr class="order-details">
                <td><?= isset($order['orderDate']) ? $order['orderDate'] : 'N/A' ?></td>
                    <td><?= isset($order['product_name']) ? $order['product_name'] : 'N/A' ?></td>
                    <td>RM <?= isset($order['price']) ? number_format($order['price'], 2) : '0.00' ?></td>
                    <td><?= isset($order['quantity']) ? $order['quantity'] : '0' ?></td>
                    <td style="text-align:right;">RM <?= isset($order['totalAmount']) ? number_format($order['totalAmount'], 2) : '0.00' ?></td>
                </tr>
            <?php endforeach; ?>


            <tr class="total-row">
                <td colspan="4"><strong>Total Price:</strong></td>
                <td>RM <?= number_format($totalPrice, 2) ?></td>
            </tr>
            <tr class="tax-row">
                <td colspan="4"><strong>Tax:</strong></td>
                <td>RM <?= number_format($tax, 2) ?></td>
            </tr>
            <tr class="delivery-fee-row">
                <td colspan="4"><strong>Delivery Fee:</strong></td>
                <td>RM <?= number_format($deliveryFee, 2) ?></td>
            </tr>
            <tr class="final-total-row">
                <td colspan="4"><strong>Final Total Amount:</strong></td>
                <td>RM <?= number_format($final, 2) ?></td>
            </tr>
        </table>
    </div>
    <br><br>
    <form id="checkoutForm" method="post" action="">
        <input type="hidden" name="orders" value='<?php echo json_encode($orders); ?>'>
        <input type="hidden" name="totalPrice" value='<?php echo $totalPrice; ?>'>
        <input type="hidden" name="tax" value='<?php echo $tax; ?>'>
        <input type="hidden" name="deliveryFee" value='<?php echo $deliveryFee; ?>'>
        <input type="hidden" name="final" value='<?php echo $final; ?>'>
        <label style="font-size:20px" for="address">Address:</label>
        <textarea style="font-size:20px" id="address" name="address" placeholder="Enter your address here..."><?php echo !empty($user_address) ? $user_address : ''; ?></textarea>


        <label style="font-size:20px" for="payment">Payment Method:</label>
        <select style="font-size:20px" id="payment" name="payment">
            <option value="Cash">Cash</option>
            <option value="E-wallet">E-wallet</option>
        </select>
        <br><br>
        <div id="ewalletOptions" style="display: none;">
            <label style="font-size:20px" for="ewalletType">Select E-wallet:</label>
            <select style="font-size:20px" id="ewalletType" name="ewalletType">
                <option value="Tng">Touch 'n Go</option>
                <option value="GrabPay">GrabPay</option>
                <option value="Boost">Boost</option>
            </select>
            <br><br>
        </div>

        <label style="font-size:20px" for="phoneNumber">Phone Number:</label>
        <input type="tel" style="font-size:20px" id="phoneNumber" name="phoneNumber" placeholder="Enter your phone number here..." value="<?php echo $phone_number; ?>">

        <button type="submit" class="place_order" name="place_order">Place Order</button>
        <br>
        <button type="submit" class="cancel_order" name="cancel_order" onclick="return confirm('Are you sure you want to cancel the order?')">Cancel Order</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var paymentSelect = document.getElementById('payment');
        var ewalletOptions = document.getElementById('ewalletOptions');
        var phoneNumberInput = document.getElementById('phoneNumber');
        var placeOrderForm = document.getElementById('checkoutForm');

        paymentSelect.addEventListener('change', function() {
            if (paymentSelect.value === 'E-wallet') {
                ewalletOptions.style.display = 'block';
            } else {
                ewalletOptions.style.display = 'none';
            }
        });
    });
</script>


</body>
</html>
