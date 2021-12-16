<!-- Begin Page Content -->
<div class="container-fluid">
  <!-- Page Heading -->
  <h1 class="h3 mb-2 text-gray-800"><?= $title; ?></h1>
  <!-- DataTales Example -->
  <div class="card shadow mb-4">
  
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>S.No.</th>
                  <th>Refer Fan Name</th>
                  <th>Artist Name</th>
                  <th>Credit Amount</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($credit)){ 
                foreach ($credit as $key => $value) { ?>
                  <tr>
                    <td><?= $key+1; ?></td>
                      <td>
                      <?= user_full_name($value['refer_id']); ?>
                    </td>  
              
                    <td>
                      <?= user_full_name($value['artist_id']); ?>
                    </td>
                    <td>
                      <?= $value['amount']; ?>
                    </td>
                        
                    <td>
                      <?=  date('d-m-Y',strtotime($value['created_at'])); ?>
                    </td>
                    
              
                                  <!-- Modal -->

  <!-- <div class="modal fade" id="myModal<?php echo $value['id']; ?>" role="dialog">
    <div class="modal-dialog">
    
     Modal content
      <div class="modal-content">
        <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal">&times;</button> 
          <h4 class="modal-title">Redeem Users</h4>
           <button type="button"
                                    class="close"
                                    data-dismiss="modal"
                                    aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
          <?php
         if(!empty($value['redeem_members']))
         {
          $profile_image = "";
          $test = explode(",", $value['redeem_members']);
          foreach ($test as $key => $value) {
              $userdetail = user_detail($value);
              if($userdetail['profile_image']){
                $profile_image = $userdetail['profile_image'];
              }else{
                  $profile_image = "dummy.png";
              }

            ?>
           
            <div class="row border-bottom pb-2 mb-2">
            <div class="col-lg-2">
              <div class="img-wrap">
                <img src="<?php echo base_url('/assets/userfile/profile/'); ?><?php echo $profile_image; ?>" alt="" class="rounded-circle img-thumbnail"> 
                </div>  


              </div>
              <div class="col-lg-10 d-flex align-items-center"><h6 class="text-dark m-0"><?php echo user_full_name($value); ?> </h6></div>
            </div>
            <?php }
           }
            ?>
          </div>
          </div>
          </div>
          </div> -->
                    </tr>

                  <?php }

            } ?>

         
              </tbody>
            </table>
            <!--Try Modal Code here-->

  
            <!--End-->
          </div>
        </div>
  </div>
</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->
  