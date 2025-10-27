<?php
include 'includes/config.php';
$product_id = $_GET['product_id'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Delivery Verification</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card p-4 shadow">
    <h4>Enter Delivery Code</h4>
    <p>The delivery code for this product is: <strong>654321</strong> (simulation)</p>

    <form action="complete_delivery.php" method="POST">
      <input type="hidden" name="product_id" value="<?= $product_id ?>">
      <input type="text" name="delivery_code" class="form-control" placeholder="Enter code">
      <button type="submit" class="btn btn-success mt-3 w-100">Confirm Delivery</button>
    </form>
  </div>
</div>
</body>
</html>
