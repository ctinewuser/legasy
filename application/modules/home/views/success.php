<section class="about pt-5 position-relative" style="background-color: #fefcf3;">
         <div class="container">
            <div class="row">
               <div class="col-lg-12 col-md-12 col-sm-6 my-5">
                  <div class="about__text">
                     <div class="section-title text-center">
                        <span>Thank you! Your payment was successful.</span>
                     </div>
                  </div>
               </div>
               <div class="col-md-12 col-sm-6">
                  <div class="about__text">
                       <div class="section-title">
                        <span>Payment Detail</span>
                        <div>Payer Name : <span><?php echo $address_name; ?></span></div>
                        <div>Payer Email : <span><?php echo $payer_email; ?></span></div>
                        <div>TXN ID : <span><?php echo $txn_id; ?></span></div>
                        <div>Amount Paid : <span>$<?php echo $currency_code.' '.$payment_gross; ?></span></div>
                        <div class="mb-4">Payment Status : <span><?php echo $payment_status; ?></span></div>
                       </div>
                  </div>
               </div>
            </div>
         </div>
 </section>

        