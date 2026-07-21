<?php
// store/customer-logout.php
require_once 'customer-session.php';

logoutCustomer();
header("Location: index.php?success=" . urlencode("You have been logged out successfully"));
exit();
