<?php
//session_start(); // Uncommented: Essential for using $_SESSION

// Sanitize username for display
$username = isset($_SESSION['username']) ? htmlentities($_SESSION['username']) : '';
$isLoggedIn = isset($_SESSION['login']) && !empty($_SESSION['login']);
?>

<div class="top-bar animate-dropdown">
    <div class="container">
        <div class="header-top-inner">
            <div class="cnt-account">
                <ul class="list-unstyled">
                    <?php if ($isLoggedIn): ?>
                        <li><a href="#"><i class="icon fa fa-user"></i>Welcome - <?php echo $username; ?></a></li>
                    <?php endif; ?>

                    <?php if ($isLoggedIn): // Optionally, only show these if logged in ?>
                        <li><a href="my-account.php"><i class="icon fa fa-user"></i>My Account</a></li>
                        <li><a href="my-wishlist.php"><i class="icon fa fa-heart"></i>Wishlist</a></li>
                        <li><a href="my-cart.php"><i class="icon fa fa-shopping-cart"></i>My Cart</a></li>
                        <li><a href="logout.php"><i class="icon fa fa-sign-out"></i>Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php"><i class="icon fa fa-sign-in"></i>Login</a></li>
                    <?php endif; ?>
                </ul>
            </div><!-- /.cnt-account -->

            <div class="cnt-block">
                <ul class="list-unstyled list-inline">
                    <li class="dropdown dropdown-small">
                        <a href="track-orders.php" class="dropdown-toggle"><span class="key">Track Order</span></a>
                    </li>
                </ul>
            </div>

            <div class="clearfix"></div>
        </div><!-- /.header-top-inner -->
    </div><!-- /.container -->
</div><!-- /.header-top -->
