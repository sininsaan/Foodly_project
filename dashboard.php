<?php
include('includes/db.php');

// fetch from db
$query = "SELECT * FROM menu WHERE is_available = 1";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodly | Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { height: 100vh; background: #2d3436; color: white; position: fixed; width: 250px; }
        .main-content { margin-left: 250px; padding: 30px; }
        .food-card { border: none; border-radius: 15px; transition: all 0.3s ease-in-out; height: 100%; display: flex; flex-direction: column; background: white; }
        .food-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .food-card img { height: 160px; object-fit: cover; width: 100%; border-radius: 12px; background-color: #e9ecef; }
        .card-body { display: flex; flex-direction: column; justify-content: space-between; flex-grow: 1; padding: 15px; }
        .btn-add { background-color: #ff7675; color: white; border-radius: 10px; border: none; }
        .btn-add:hover { background-color: #d63031; color: white; }
        .cart-badge { position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; }
    </style>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body>

    <div class="sidebar d-flex flex-column p-3">
        <h3 class="text-center fw-bold mb-4 mt-2">Foodly</h3>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="#" class="nav-link text-white active bg-danger"><i class="bi bi-house-door me-2"></i> Menu</a>
            </li>
            <li>
                <a href="#" class="nav-link text-white"><i class="bi bi-bag-check me-2"></i> My Orders</a>
            </li>
            <li>
                <a href="#" class="nav-link text-white"><i class="bi bi-clock-history me-2"></i> History</a>
            </li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="text-white text-decoration-none"><i class="bi bi-person-circle me-2"></i> <strong>Roll No: 8104</strong></a>
        </div>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Welcome, Janvi!</h2>
                <p class="text-muted">What would you like to eat today?</p>
            </div>
            <button class="btn btn-outline-dark position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartSidebar">
                <i class="bi bi-cart3"></i> Cart
                <span class="cart-badge">3</span>
            </button>
        </div>

        <div class="mb-4">
            <button class="btn btn-dark rounded-pill px-4 me-2">All</button>
            <button class="btn btn-white border rounded-pill px-4 me-2 shadow-sm">Snacks</button>
            <button class="btn btn-white border rounded-pill px-4 me-2 shadow-sm">Beverages</button>
            <button class="btn btn-white border rounded-pill px-4 me-2 shadow-sm">Meals</button>
        </div>

    <div class="row">
    <?php 
    // if items present in the db, loop runs
    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) { 
    ?>
            <div class="col-md-4 mb-4">
                <div class="card food-card h-100 p-2">

<img src="assets/images/<?php echo $row['image']; ?>" 
     class="card-img-top rounded-4" 
     style="height: 180px; object-fit: cover;"
     onerror="this.src='https://via.placeholder.com/200x150?text=Food'">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1"><?php echo $row['item_name']; ?></h5>
                        <p class="text-muted small">Available in Canteen</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="fw-bold fs-5">₹<?php echo $row['price']; ?></span>
								<button class="btn btn-add px-3" > Add </button>
                        </div>
                    </div>
                </div>
            </div>
    <?php 
        } 
    } else {
        echo "<p class='text-center'>No items found in menu.</p>";
    }
    ?>
</div>
    </div> <div class="offcanvas offcanvas-end show" tabindex="-1" id="cartSidebar" aria-labelledby="cartSidebarLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="cartSidebarLabel">Your Order Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="mb-4">
                <p class="fw-bold mb-2">Selected Items:</p>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        Veg Samosa (x2)
                        <span>₹30.00</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        Masala Tea (x1)
                        <span>₹10.00</span>
                    </li>
                </ul>
            </div>

            <hr>

            <div class="mb-4">
                <label class="fw-bold mb-2">Select Pickup Time Slot:</label>
                <div class="d-grid gap-2">
                    <input type="radio" class="btn-check" name="timeslot" id="slot1" autocomplete="off" checked>
                    <label class="btn btn-outline-dark text-start p-3" for="slot1">
                        <div class="d-flex justify-content-between">
                            <span><i class="bi bi-clock me-2"></i> 10:00 AM - 11:00 AM</span>
                            <small class="text-success">Available</small>
                        </div>
                    </label>

                    <input type="radio" class="btn-check" name="timeslot" id="slot2" autocomplete="off" disabled>
                    <label class="btn btn-outline-secondary text-start p-3 opacity-50" for="slot2">
                        <div class="d-flex justify-content-between">
                            <span><i class="bi bi-clock me-2"></i> 11:00 AM - 12:00 PM</span>
                            <small class="text-danger">Full (30/30)</small>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mt-auto border-top pt-3">
                <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                    <span>Total Amount:</span>
                    <span>₹40.00</span>
                </div>
                <button class="btn btn-danger w-100 py-3 fw-bold rounded-3" onclick="placeOrder('Final Order', true)">
    Place Pre-Order
</button>
            </div>
        </div>
    </div>
	
	<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body text-center p-5" id="receipt-content">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                <h2 class="fw-bold mt-3">Order Placed!</h2>
                <div class="bg-light p-3 my-4 rounded-3 border">
                    <span class="text-uppercase small fw-bold text-muted d-block">Your Token Number</span>
                    <span class="display-4 fw-bold text-danger">#402</span>
                </div>
                <p class="mb-0 text-muted">Show this at the counter for pickup.</p>
            </div>
            <div class="modal-footer border-0 pb-4 justify-content-center">
                <button type="button" class="btn btn-outline-dark px-4 rounded-pill" onclick="downloadReceipt()">Download Receipt</button>
                <button type="button" class="btn btn-dark px-4 rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
		<script>
function placeOrder(itemName, showAlert = false) {
    let timeLeft = 5;

    
    if (showAlert) {
        alert("Starting final preparation for your order... Please wait 5 seconds.");
    }

    const timerAlert = setInterval(function() {
        if(timeLeft <= 0) {
            clearInterval(timerAlert);
            var myModal = new bootstrap.Modal(document.getElementById('orderModal'));
            myModal.show();
        } else {
            timeLeft--;
        }
    }, 1000);
}

function downloadReceipt() {
    const receipt = document.getElementById('receipt-content');
    html2canvas(receipt).then(canvas => {
        const link = document.createElement('a');
        link.download = 'Receipt_402.png';
        link.href = canvas.toDataURL();
        link.click();
    });
}
</script>
</body>
</html>