<?php 
session_start(); 
include 'includes/config.php'; 


// ✅ 2. Automatically remove sold-out products (quantity = 0 or less)
mysqli_query($conn, "
    DELETE FROM products 
    WHERE quantity <= 0
");


// Detect login
$isLoggedIn = isset($_SESSION['user']) || isset($_SESSION['company']); 
$role = $isLoggedIn ? ($_SESSION['role'] ?? null) : null; 
$userData = $isLoggedIn ? ($_SESSION['user'] ?? $_SESSION['company'] ?? null) : null; 
?>
<!DOCTYPE html>
<html lang="en">
<head>  
<meta charset="UTF-8">
<title>💄 Glamour Beauty | Shop</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
/* ✅ Image Modal Styles */
.image-modal {
  display: none;
  position: fixed;
  z-index: 1050;
  padding-top: 60px;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.8);
}

.image-modal img {
  display: block;
  margin: auto;
  max-width: 90%;
  max-height: 80%;
  border: 4px solid #fff;
  border-radius: 10px;
  animation: zoomIn 0.3s ease;
}

.image-modal .close {
  position: absolute;
  top: 20px;
  right: 35px;
  color: #fff;
  font-size: 40px;
  font-weight: bold;
  cursor: pointer;
}

@keyframes zoomIn {
  from {transform: scale(0.8); opacity: 0;}
  to {transform: scale(1); opacity: 1;}
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: #fff0f6;
    overflow-x: hidden;
}

/* NAVBAR */
.navbar {
    background: linear-gradient(90deg, #ff3ebf, #ff69b4);
    box-shadow: 0 4px 10px rgba(255, 62, 191, 0.3);
}
.navbar-brand { font-weight: bold; color: white !important; letter-spacing: 1px; font-size: 1.4rem; }
.nav-link { color: white !important; }
.nav-link:hover { color: #ffe6f2 !important; text-shadow: 0 0 5px white; }

/* MENU ICON */
.menu-icon { background: none; border: none; color: white; font-size: 1.8rem; cursor: pointer; transition: all 0.3s ease; }
.menu-icon:hover { color: #ffe6f2; transform: scale(1.1); }

/* SLIDING SIDEBAR */
.sidebar { height: 100%; width: 0; position: fixed; top: 0; right: 0; background: linear-gradient(180deg, #ff69b4, #ff3ebf); overflow-x: hidden; transition: 0.4s; padding-top: 80px; box-shadow: -3px 0 10px rgba(255, 62, 191, 0.4); z-index: 9999; }
.sidebar a { padding: 12px 24px; text-decoration: none; font-size: 1.1rem; color: white; display: block; transition: 0.3s; font-weight: 500; }
.sidebar a:hover { background-color: rgba(255,255,255,0.2); color: #fff; padding-left: 30px; }
.sidebar .closebtn { position: absolute; top: 15px; right: 25px; font-size: 2rem; color: white; cursor: pointer; }

/* CATEGORY BAR */
.category-bar { background-color: #fff; border-bottom: 2px solid #ff3ebf; padding: 12px 0; display: flex; justify-content: center; flex-wrap: wrap; gap: 10px; }
.category-bar button { background: linear-gradient(135deg, #ff3ebf, #ff69b4); color: white; border: none; padding: 12px 10px; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 3px 8px rgba(255, 62, 191, 0.3); }
.category-bar button:hover { transform: translateY(-1px); box-shadow: 0 5px 12px rgba(255, 62, 191, 0.5); }
.category-bar button.active { background: #ff1493; box-shadow: 0 0 12px rgba(255, 62, 191, 0.6); }

/* PRODUCT GRID */
.product-grid { padding: 40px 5%; display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 25px; justify-items: center; background-color: #fff0f6; }

/* PRODUCT CARD */
.card { width: 100%; max-width: 220px; background: #fff; border: 1px solid #f1f1f1; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: all 0.25s ease; cursor: pointer; }
.card:hover { transform: translateY(-4px); box-shadow: 0 4px 12px rgba(255, 62, 191, 0.25); }

/* IMAGE AREA */
.image-container { width: 100%; height: 200px; background: #fff; display: flex; justify-content: center; align-items: center; overflow: hidden; }
.image-container img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
.card:hover .image-container img { transform: scale(1.05); }

/* CARD BODY */
.card-body { padding: 10px 12px 12px; text-align: left; }
.card-body h6 { font-size: 0.9rem; font-weight: 600; color: #333; line-height: 1.3; height: 2.5em; overflow: hidden; margin-bottom: 6px; }

/* PRICE */
.price { color: #e91e63; font-weight: 700; font-size: 1rem; margin-bottom: 4px; }
.discount { display: inline-block; background: #ffe6f2; color: #e91e63; font-size: 0.75rem; border: 1px solid #e91e63; border-radius: 4px; padding: 2px 5px; margin-left: 5px; }

/* FOOTER */
footer { text-align: center; padding: 20px; background: linear-gradient(90deg, #ff3ebf, #ff69b4); color: white; margin-top: 50px; font-weight: 500; letter-spacing: 0.5px; }

/* TOAST */
.toast-message {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    background: rgba(40,167,69,0.95);
    color: #fff;
    padding: 18px 30px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 500;
    text-align: center;
    z-index: 9999;
    opacity: 0;
    transition: all 0.3s ease;
    display: none;
}
.toast-message.show {
    display: block;
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}
</style>
</head>

<body>
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg" style="padding: 10px 20px;">
  <div class="container-fluid">
    <a class="navbar-brand text-white fw-bold" href="#">💄 Glamour Beauty</a>
    <form class="d-flex search-bar mx-auto" role="search">
      <input class="form-control me-2" type="search" id="searchInput" placeholder="Search makeup..." aria-label="Search">
    </form>
    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
      <?php if(!$isLoggedIn): ?>
        <li class="nav-item">
          <button class="menu-icon" onclick="openSidebar()">&#9776;</button>
        </li>
      <?php else: ?>
        <li class="nav-item me-3 text-white">
          Welcome, <strong><?= htmlspecialchars($role == 'company' ? $userData['company_name'] : $userData['username']); ?></strong>
        </li>
        <li class="nav-item">
          <a href="../auth/logout.php" class="btn btn-light fw-bold px-3 py-1 rounded-pill text-danger">Logout</a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<!-- SLIDING SIDEBAR -->
<div id="sidebar" class="sidebar">
  <span class="closebtn" onclick="closeSidebar()">&times;</span>
  <a href="auth/login.php">Login</a>
  <a href="auth/user_register.php">User Register</a>
  <a href="auth/company_register.php">Company Register</a>
</div>

<!-- CATEGORY BAR -->
<div class="category-bar sticky-top">
  <button class="active" onclick="filterCategory('all', this)">Home</button>
  <button onclick="filterCategory('lipstick', this)">Lipstick</button>
  <button onclick="filterCategory('foundation', this)">Foundation</button>
  <button onclick="filterCategory('skincare', this)">Skincare</button>
  <button onclick="filterCategory('accessories', this)">Accessories</button>
</div>
<!-- ✅ Image Modal -->
<div id="imageModal" class="image-modal">
  <span class="close" onclick="closeImageModal()">&times;</span>
  <img id="modalImg" src="">
</div>

<!-- PRODUCT GRID -->
<div class="product-grid" id="productGrid">
<?php
$result = mysqli_query($conn, "
    SELECT p.*, c.company_name
    FROM products p
    JOIN companies c ON p.company_id = c.id
    WHERE p.status='approved' OR p.status='out_of_sold'
    ORDER BY p.created_at DESC
");

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $available = (int)$row['quantity'];
        $isOutOfSold = $available <= 0;

        echo "
        <div class='card product {$row['category']}' style='position:relative;'>
            <div class='image-container'>
                <img src=\"func/{$row['image']}\" 
                    alt=\"{$row['productName']}\" 
                    class=\"img-fluid\" 
                    style=\"cursor:pointer;\" 
                    onclick=\"openImageModal('func/{$row['image']}')\">

            </div>"
            . ($isOutOfSold ? "<span class='badge bg-danger position-absolute top-0 end-0 m-2'>Out of Sold</span>" : "") .
            "<div class='card-body'>
                <h6 class='fw-bold'>{$row['productName']}</h6>
                <div class='price mb-2'>
                    ₱" . number_format($row['price']) . "
                    <span class='discount text-danger'>-" . rand(10, 40) . "%</span>
                </div>
                <div class='quantity mb-2'>
                    <small>Available: <strong>{$available}</strong></small>
                </div>
                <small class='d-block mb-2 text-muted'>Rating:
                    <i class='bi bi-star-fill text-warning'></i> 4." . rand(5, 9) . "⭐
                </small>
                <div class='card-footer bg-transparent border-0'>
                    <small class='text-muted d-block mb-2'>Company: {$row['company_name']}</small>";

        // Buttons
        echo "<form class='cart-form' action='../func/add_to_cart.php' method='POST' style='margin-top:10px;'>
                <input type='hidden' name='product_id' value='{$row['id']}'>
                <input type='hidden' name='product_name' value='{$row['productName']}'>
                <input type='hidden' name='price' value='{$row['price']}'>
                <input type='hidden' name='image' value='{$row['image']}'>
                <button type='submit' class='btn btn-sm btn-primary w-100' " . ($isOutOfSold ? 'disabled' : '') . ">
                    <i class='bi bi-cart-plus'></i> Add to Cart
                </button>
            </form>";

        echo "<form action='../func/checkout.php' method='POST' onsubmit='return handleBuyNow(event, this, {$available})' style='margin-top:5px;'>
                <input type='hidden' name='product_id' value='{$row['id']}'>
                <input type='hidden' name='product_name' value='{$row['productName']}'>
                <input type='hidden' name='price' value='{$row['price']}'>
                <input type='hidden' name='image' value='{$row['image']}'>
                <input type='hidden' name='quantity' value='1'>
                <button type='submit' class='btn btn-sm btn-success w-100' " . ($isOutOfSold ? 'disabled' : '') . ">
                    <i class='bi bi-bag-check'></i> Buy Now
                </button>
            </form>";

        echo "<div class='add-message text-success mt-2' style='display:none;font-size:14px;'>
                <i class='bi bi-check-circle'></i> Added to cart!
            </div>
            </div>
        </div>
        </div>";
    }
} else {
    echo "<p class='text-center w-100'>No products available yet. ✨</p>";
}
?>
</div>

<div id="toast" class="toast-message">
    <i class="bi bi-check-circle-fill"></i> You must log in to perform this action!
</div>

<footer>
  © 2025 Glamour Beauty — Radiate Confidence, Shine Brighter ✨
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openSidebar() { document.getElementById("sidebar").style.width = "250px"; }
function closeSidebar() { document.getElementById("sidebar").style.width = "0"; }

document.getElementById('searchInput').addEventListener('keyup', function(){
    let filter = this.value.toLowerCase();
    document.querySelectorAll('.product').forEach(card => {
        let title = card.querySelector('h6').textContent.toLowerCase();
        card.style.display = title.includes(filter) ? 'block' : 'none';
    });
});

function filterCategory(cat, btn) {
    document.querySelectorAll('.category-bar button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const products = document.querySelectorAll('.product');
    products.forEach(p => {
        p.style.display = (cat === 'all' || p.classList.contains(cat)) ? 'block' : 'none';
    });
}

// Logged-in check
const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;

// Add to Cart
document.querySelectorAll('.cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        if(!isLoggedIn){
            e.preventDefault();
            showToast("❌ You must log in to add items to your cart!");
        }
    });
});

// Buy Now
document.querySelectorAll('form[action="../func/checkout.php"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        if(!isLoggedIn){
            e.preventDefault();
            showToast("❌ You must log in to buy products!");
        }
    });
});

function handleBuyNow(e, form, available){
    if(!isLoggedIn) return false;
    e.preventDefault();
    let qty = parseInt(prompt(`Enter quantity (Available: ${available}):`, "1"));
    if(!qty || qty < 1){
        alert("❌ Invalid quantity!");
        return false;
    }
    if(qty > available){
        alert(`❌ Only ${available} item(s) available!`);
        return false;
    }
    form.querySelector("input[name='quantity']").value = qty;
    form.submit();
}

// Toast function
function showToast(msg){
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => { toast.classList.remove('show'); }, 2000);
}
</script>
</body>
</html>
<script>
function openImageModal(src) {
  document.getElementById("modalImg").src = src;
  document.getElementById("imageModal").style.display = "block";
}

function closeImageModal() {
  document.getElementById("imageModal").style.display = "none";
}

// Optional: close modal when clicking outside the image
window.onclick = function(event) {
  const modal = document.getElementById("imageModal");
  if (event.target === modal) {
    modal.style.display = "none";
  }
}
</script>
