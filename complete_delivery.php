<?php
include 'includes/config.php';

$product_id = $_POST['product_id'];
$entered_code = $_POST['delivery_code'];

if ($entered_code == "654321") {
  mysqli_query($conn, "UPDATE products SET quantity = quantity - 1 WHERE id='$product_id'");

  // Auto delete if 0 after 1 minute
  mysqli_query($conn, "
    CREATE EVENT IF NOT EXISTS delete_zero_stock
    ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 1 MINUTE
    DO DELETE FROM products WHERE quantity <= 0
  ");

  echo "<script>alert('Delivery confirmed successfully!');window.location='shop.php';</script>";
} else {
  echo "<script>alert('Invalid code. Please check.');history.back();</script>";
}
?>
