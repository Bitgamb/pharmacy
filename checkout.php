<?php
include "includes/head.php";
?>

<body>

  <div class="site-wrap">

    <?php
    include "includes/header.php";

    // Check membership status directly using $_SESSION['user_id']
    $user_id = $_SESSION['user_id'];
    $membership_query = "SELECT user_id, user_fname, user_lname, email, user_address, membership_status FROM user WHERE user_id = $user_id";
    $user_data = query($membership_query);
    $membership_status = isset($user_data[0]['membership_status']) ? $user_data[0]['membership_status'] : '';

    // Define discount variable
    $discount = 0;

    // If membership is active, calculate the discount
    if ($membership_status == 'active') {
      $data = get_cart(); // Get cart data
      $discount = (delivery_fees($data) + total_price($data)) * 0.15; // 15% discount
    }
    ?>

    <div class="bg-light py-3">
      <div class="container">
        <div class="row">
          <div class="col-md-12 mb-0">
            <a href="index.php">Home</a> <span class="mx-2 mb-0">/</span>
            <strong class="text-black">Checkout</strong>
          </div>
        </div>
      </div>
    </div>

    <div class="site-section">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <div class="row mb-5">
              <div class="col-md-12">
                <h2 class="h3 mb-3 text-black">User Details</h2>
                <div class="p-3 p-lg-5 border">
                  <table class="table site-block-order-table mb-5">
                    <thead>
                      <th>User Details</th>
                    </thead>
                    <tbody>
                      <tr>
                        <td>User ID</td>
                        <td><?php echo $user_data[0]['user_id'] ?></td>
                      </tr>
                      <tr>
                        <td>First Name</td>
                        <td><?php echo $user_data[0]['user_fname'] ?></td>
                      </tr>
                      <tr>
                        <td>Last Name</td>
                        <td><?php echo $user_data[0]['user_lname'] ?></td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td><?php echo $user_data[0]['email'] ?></td>
                      </tr>
                      <tr>
                        <td>Address</td>
                        <td><?php echo $user_data[0]['user_address'] ?></td>
                      </tr>
                      <tr>
                        <td>Membership Status</td>
                        <td><?php echo ucfirst($membership_status) ?></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row mb-5">
              <div class="col-md-12">
                <h2 class="h3 mb-3 text-black">Your Order</h2>
                <div class="p-3 p-lg-5 border">
                  <table class="table site-block-order-table mb-5">
                    <thead>
                      <th>Product</th>
                      <th>Total</th>
                    </thead>
                    <tbody>
                      <?php
                      if (!empty($_SESSION['cart'])) {
                        $data = get_cart();
                        $num = sizeof($data);
                        for ($i = 0; $i < $num; $i++) {
                          if (isset($data[$i])) {
                      ?>
                            <tr>
                              <td><?php echo $data[$i][0]['item_title'] ?><strong class="mx-2">x</strong><?php echo $_SESSION['cart'][$i]['quantity'] ?></td>
                              <td>₹<?php echo ($data[$i][0]['item_price'] * $_SESSION['cart'][$i]['quantity'])  ?></td>
                            </tr>
                      <?php
                          }
                        }
                      }
                      ?>
                      <tr>
                        <td class="text-black font-weight-bold"><strong>Cart Subtotal</strong></td>
                        <td class="text-black">₹<?php echo total_price($data) ?></td>
                      </tr>
                      <tr>
                        <td class="text-black font-weight-bold"><strong>Delivery Fees</strong></td>
                        <td class="text-black">₹<?php echo delivery_fees($data) ?></td>
                      </tr>
                      <?php if ($membership_status == 'active') : ?>
                        <!-- Apply discount if membership is active -->
                        <tr>
                          <td class="text-black font-weight-bold"><strong>Membership Discount (15%)</strong></td>
                          <td class="text-black">₹<?php echo number_format($discount, 2) ?></td>
                        </tr>
                      <?php endif; ?>
                      <!-- Update order total with discount -->
                      <tr>
                        <td class="text-black font-weight-bold"><strong>Order Total</strong></td>
                        <td class="text-black font-weight-bold"><strong>₹<?php echo number_format((delivery_fees($data) + total_price($data)) - $discount, 2) ?></strong></td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="form-group">
                    <button id="rzp-button" class="btn btn-primary btn-lg btn-block">Proceed to Payment</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php include "includes/footer.php"; ?>

  </div>

  <!-- Include Razorpay script -->
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
    // Define Razorpay options
    var options = {
      "key": "rzp_test_5uPzGreHV3wiCs",
      "amount": <?php echo number_format((delivery_fees($data) + total_price($data)) - $discount, 2) ?>* 100, // amount in paisa
      "currency": "INR",
      "name": "Lifecare Pharmacy",
      "description": "Order Payment",
      "handler": function(response) {
        // Redirect to thank you page after successful payment
        window.location.href = 'thankyou.php?order=done';
      }
    };

    // Create a Razorpay instance with options
    var rzp = new Razorpay(options);

    // Attach click event to Razorpay payment button
    document.getElementById('rzp-button').onclick = function(e) {
      rzp.open();
      e.preventDefault();
    }
  </script>

</body>
</html>
