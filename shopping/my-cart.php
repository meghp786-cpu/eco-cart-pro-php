<?php
session_start();
// error_reporting(0); // Removed for debugging; re-enable in production if needed
include('includes/config.php');

// Update cart quantities
if (isset($_POST['submit']) && !empty($_SESSION['cart'])) {
    if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $key => $val) {
            if ($val == 0) {
                unset($_SESSION['cart'][$key]);
            }  else {
                $_SESSION['cart'][$key]['quantity'] = (int)$val; // Cast to int for safety
            } 
        }
        echo "<script>alert('Your Cart has been Updated');</script>";
    } else {
        // Optional: Handle case where quantity array is invalid
        echo "<script>alert('No quantities to update.');</script>";
    }
}

// Bulk remove products from cart (via checkboxes)
if (isset($_POST['remove_code']) && !empty($_SESSION['cart'])) {
    if (isset($_POST['remove_code']) && is_array($_POST['remove_code'])) {
        foreach ($_POST['remove_code'] as $productId) {
            $productId = (int)$productId; // Sanitize
            if (isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
            }
        }
        echo "<script>alert('Your Cart has been Updated');</script>";
    } else {
        // Optional: Handle case where remove_code array is invalid
        echo "<script>alert('No items selected for removal.');</script>";
    }
}

// Single remove product from cart (new feature)
if (isset($_POST['remove_single']) && !empty($_SESSION['cart'])) {
    $productId = (int)$_POST['product_id']; // Sanitize
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        echo "<script>alert('Product removed from cart');</script>";
        // Refresh the page to update display and totals
        header('location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Insert orders (only if logged in and cart not empty)
if (isset($_POST['ordersubmit'])) {
    if (strlen($_SESSION['login']) == 0) {
        header('location:login.php');
        exit();
    }
    if (empty($_SESSION['cart'])) {
        echo "<script>alert('Your cart is empty. Cannot place order.');</script>";
    } else {
        foreach ($_SESSION['cart'] as $productId => $item) {
            $quantity = (int)$item['quantity'];
            if ($quantity > 0) {
                $stmt = mysqli_prepare($con, "INSERT INTO orders (userId, productId, quantity) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "iii", $_SESSION['id'], $productId, $quantity);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        // Clear cart after order (optional; adjust as needed)
        // unset($_SESSION['cart']);
        header('location:payment-method.php');
        exit();
    }
}

// Update billing address
if (isset($_POST['update'])) {
    $baddress = mysqli_real_escape_string($con, $_POST['billingaddress']);
    $bstate = mysqli_real_escape_string($con, $_POST['billingstate']);
    $bcity = mysqli_real_escape_string($con, $_POST['billingcity']);
    $bpincode = mysqli_real_escape_string($con, $_POST['billingpincode']);
    
    $stmt = mysqli_prepare($con, "UPDATE users SET billingAddress = ?, billingState = ?, billingCity = ?, billingPincode = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sss si", $baddress, $bstate, $bcity, $bpincode, $_SESSION['id']);
    $query = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    if ($query) {
        echo "<script>alert('Billing Address has been updated');</script>";
    }
}

// Update shipping address
if (isset($_POST['shipupdate'])) {
    $saddress = mysqli_real_escape_string($con, $_POST['shippingaddress']);
    $sstate = mysqli_real_escape_string($con, $_POST['shippingstate']);
    $scity = mysqli_real_escape_string($con, $_POST['shippingcity']);
    $spincode = mysqli_real_escape_string($con, $_POST['shippingpincode']);
    
    $stmt = mysqli_prepare($con, "UPDATE users SET shippingAddress = ?, shippingState = ?, shippingCity = ?, shippingPincode = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sss si", $saddress, $sstate, $scity, $spincode, $_SESSION['id']);
    $query = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    if ($query) {
        echo "<script>alert('Shipping Address has been updated');</script>";
    }
}

// Fetch user data once for address forms (only if logged in)
$userRow = null;
if (isset($_SESSION['id']) && strlen($_SESSION['login']) > 0) {
    $userStmt = mysqli_prepare($con, "SELECT * FROM users WHERE id = ?");
    mysqli_stmt_bind_param($userStmt, "i", $_SESSION['id']);
    mysqli_stmt_execute($userStmt);
    $userQuery = mysqli_stmt_get_result($userStmt);
    $userRow = mysqli_fetch_array($userQuery);
    mysqli_stmt_close($userStmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keywords" content="MediaCenter, Template, eCommerce">
    <meta name="robots" content="all">

    <title>My Cart</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/red.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.css">
    <link rel="stylesheet" href="assets/css/owl.transitions.css">
    <link href="assets/css/lightbox.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/rateit.css">
    <link rel="stylesheet" href="assets/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="assets/css/config.css"> <!-- Demo only -->
    <link href="assets/css/green.css" rel="alternate stylesheet" title="Green color">
    <link href="assets/css/blue.css" rel="alternate stylesheet" title="Blue color">
    <link href="assets/css/red.css" rel="alternate stylesheet" title="Red color">
    <link href="assets/css/orange.css" rel="alternate stylesheet" title="Orange color">
    <link href="assets/css/dark-green.css" rel="alternate stylesheet" title="Darkgreen color">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- HTML5 shim and Respond.js for IE8 support -->
    <!--[if lt IE 9]>
        <script src="assets/js/html5shiv.js"></script>
        <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>
<body class="cnt-home">

    <!-- Header -->
    <header class="header-style-1">
        <?php include('includes/top-header.php'); ?>
        <?php include('includes/main-header.php'); ?>
        <?php include('includes/menu-bar.php'); ?>
    </header>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <div class="breadcrumb-inner">
                <ul class="list-inline list-unstyled">
                    <li><a href="#">Home</a></li>
                    <li class='active'>Shopping Cart</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="body-content outer-top-xs">
        <div class="container">
            <div class="row inner-bottom-sm">
                <div class="shopping-cart">
                    <div class="col-md-12 col-sm-12 shopping-cart-table">
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <form name="cart" method="post">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="cart-romove item">Remove</th>
                                                <th class="cart-description item">Image</th>
                                                <th class="cart-product-name item">Product Name</th>
                                                <th class="cart-qty item">Quantity</th>
                                                <th class="cart-sub-total item">Price Per unit</th>
                                                <th class="cart-sub-total item">Shipping Charge</th>
                                                <th class="cart-total last-item">Grand Total</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <td colspan="7">
                                                    <div class="shopping-cart-btn">
                                                        <span class="">
                                                            <a href="index.php" class="btn btn-upper btn-primary outer-left-xs">Continue Shopping</a>
                                                            <input type="submit" name="submit" value="Update shopping cart" class="btn btn-upper btn-primary pull-right outer-right-xs">
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php
                                            $pdtid = array();
                                            $productIds = implode(',', array_keys($_SESSION['cart']));
                                            $sql = "SELECT * FROM products WHERE id IN ($productIds) ORDER BY id ASC";
                                            $query = mysqli_query($con, $sql) or die(mysqli_error($con));
                                            
                                            $totalprice = 0;
                                            $totalqunty = 0;
                                            if (mysqli_num_rows($query) > 0) {
                                                while ($row = mysqli_fetch_array($query)) {
                                                    $quantity = $_SESSION['cart'][$row['id']]['quantity'];
                                                    $subtotal = $quantity * $row['productPrice'] + $row['shippingCharge'];
                                                    $totalprice += $subtotal;
                                                    $totalqunty += $quantity;
                                                    
                                                    array_push($pdtid, $row['id']);
                                                    $pd = $row['id']; // For reviews and links
                                            ?>
                                            <tr>
                                                <td class="romove-item">
                                                    <!-- Checkbox for bulk removal (optional) -->
                                                    <input type="checkbox" name="remove_code[]" value="<?php echo htmlspecialchars($row['id']); ?>">
                                                    <!-- Individual remove button -->
                                                    <form method="post" style="display: inline;">
                                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                                        <button type="submit" name="remove_single" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to remove this item?');">
                                                            <i class="fa fa-trash"></i> Remove
                                                        </button>
                                                    </form>
                                                </td>
                                                <td class="cart-image">
                                                    <a class="entry-thumbnail" href="product-details.php?pid=<?php echo htmlspecialchars($pd); ?>">
                                                        <img src="admin/productimages/<?php echo htmlspecialchars($row['id']); ?>/<?php echo htmlspecialchars($row['productImage1']); ?>" alt="<?php echo htmlspecialchars($row['productName']); ?>" width="114" height="146">
                                                    </a>
                                                </td>
                                                <td class="cart-product-name-info">
                                                    <h4 class='cart-product-description'>
                                                        <a href="product-details.php?pid=<?php echo htmlspecialchars($pd); ?>"><?php echo htmlspecialchars($row['productName']); ?></a>
                                                    </h4>
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <div class="rating rateit-small"></div>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <?php
                                                            $stmt = mysqli_prepare($con, "SELECT * FROM productreviews WHERE productId = ?");
                                                            mysqli_stmt_bind_param($stmt, "i", $pd);
                                                            mysqli_stmt_execute($stmt);
                                                            $rt = mysqli_stmt_get_result($stmt);
                                                            $num = mysqli_num_rows($rt);
                                                            mysqli_stmt_close($stmt);
                                                            ?>
                                                            <div class="reviews">( <?php echo htmlspecialchars($num); ?> Reviews )</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="cart-product-quantity">
                                                    <div class="quant-input">
                                                        <div class="arrows">
                                                            <div class="arrow plus gradient"><span class="ir"><i class="icon fa fa-sort-asc"></i></span></div>
                                                            <div class="arrow minus gradient"><span class="ir"><i class="icon fa fa-sort-desc"></i></span></div>
                                                        </div>
                                                        <input type="text" value="<?php echo htmlspecialchars($quantity); ?>" name="quantity[<?php echo htmlspecialchars($row['id']); ?>]">
                                                    </div>
                                                </td>
                                                <td class="cart-product-sub-total">
                                                    <span class="cart-sub-total-price">Rs <?php echo htmlspecialchars($row['productPrice']); ?>.00</span>
                                                </td>
                                                <td class="cart-product-sub-total">
                                                    <span class="cart-sub-total-price">Rs <?php echo htmlspecialchars($row['shippingCharge']); ?>.00</span>
                                                </td>
                                                <td class="cart-product-grand-total">
                                                    <span class="cart-grand-total-price">Rs <?php echo $subtotal; ?>.00</span>
                                                </td>
                                            </tr>
                                            <?php
                                                }
                                            }
                                            $_SESSION['pid'] = $pdtid;
                                            $_SESSION['qnty'] = $totalqunty;
                                            $_SESSION['tp'] = $totalprice . ".00";
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Cart total and checkout button -->
                                <div class="col-md-4 col-sm-12 cart-shopping-total">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="cart-grand-total">
                                                        Grand Total <span class="inner-left-md">Rs <?php echo $_SESSION['tp']; ?></span>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="cart-checkout-btn pull-right">
                                                        <button type="submit" name="ordersubmit" class="btn btn-primary">PROCEED TO CHECKOUT</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                <h4>Your shopping Cart is empty.</h4>
                                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Address Forms (shown if user is logged in, regardless of cart) -->
                    <?php if ($userRow): ?>
                        <!-- Billing Address Form -->
                        <div class="col-md-4 col-sm-12 estimate-ship-tax">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><span class="estimate-title">Billing Address</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <form method="post">
                                                <div class="form-group">
                                                    <label class="info-title" for="billingaddress">Billing Address <span>*</span></label>
                                                    <textarea class="form-control unicase-form-control text-input" name="billingaddress" required><?php echo htmlspecialchars($userRow['billingAddress']); ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label class="info-title" for="billingstate">Billing State <span>*</span></label>
                                                    <input type="text" class="form-control unicase-form-control text-input" name="billingstate" value="<?php echo htmlspecialchars($userRow['billingState']); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label class="info-title" for="billingcity">Billing City <span>*</span></label>
                                                    <input type="text" class="form-control unicase-form-control text-input" name="billingcity" value="<?php echo htmlspecialchars($userRow['billingCity']); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label class="info-title" for="billingpincode">Billing Pincode <span>*</span></label>
                                                    <input type="text" class="form-control unicase-form-control text-input" name="billingpincode" value="<?php echo htmlspecialchars($userRow['billingPincode']); ?>" required>
                                                </div>
                                                <button type="submit" name="update" class="btn-upper btn btn-primary checkout-page-button">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Shipping Address Form -->
                        <div class="col-md-4 col-sm-12 estimate-ship-tax">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
										 <th><span class="estimate-title">Shipping Address</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <form method="post">
                                                <div class="form-group">
                                                    <label class="info-title" for="shippingaddress">Shipping Address <span>*</span></label>
                                                    <textarea class="form-control unicase-form-control text-input" name="shippingaddress" required><?php echo htmlspecialchars($userRow['shippingAddress']); ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label class="info-title" for="shippingstate">Shipping State <span>*</span></label>
                                                    <input type="text" class="form-control unicase-form-control text-input" name="shippingstate" value="<?php echo htmlspecialchars($userRow['shippingState']); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label class="info-title" for="shippingcity">Shipping City <span>*</span></label>
                                                    <input type="text" class="form-control unicase-form-control text-input" name="shippingcity" value="<?php echo htmlspecialchars($userRow['shippingCity']); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label class="info-title" for="shippingpincode">Shipping Pincode <span>*</span></label>
                                                    <input type="text" class="form-control unicase-form-control text-input" name="shippingpincode" value="<?php echo htmlspecialchars($userRow['shippingPincode']); ?>" required>
                                                </div>
                                                <button type="submit" name="shipupdate" class="btn-upper btn btn-primary checkout-page-button">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; // End of address forms if user logged in ?>
                </div> <!-- /.shopping-cart -->
            </div> <!-- /.row -->
        </div> <!-- /.container -->
    </div> <!-- /.body-content -->

    <?php include('includes/brands-slider.php'); ?>

    <?php include('includes/footer.php'); ?>

    <!-- Scripts -->
    <script src="assets/js/jquery-1.11.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/echo.min.js"></script>
    <script src="assets/js/jquery.easing-1.3.min.js"></script>
    <script src="assets/js/bootstrap-slider.min.js"></script>
    <script src="assets/js/jquery.rateit.min.js"></script>
    <script type="text/javascript" src="assets/js/lightbox.min.js"></script>
    <script src="assets/js/bootstrap-select.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/scripts.js"></script>

    <!-- For demo purposes – can be removed on production -->
    <script src="switchstylesheet/switchstylesheet.js"></script>
    <script>
        $(document).ready(function(){ 
            $(".changecolor").switchstylesheet( { seperator:"color"} );
            $('.show-theme-options').click(function(){
                $(this).parent().toggleClass('open');
                return false;
            });
        });

        $(window).bind("load", function() {
           $('.show-theme-options').delay(2000).trigger('click');
        });
    </script>
    <!-- For demo purposes – can be removed on production : End -->

</body>
</html>
