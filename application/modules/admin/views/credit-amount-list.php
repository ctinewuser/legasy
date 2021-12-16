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
                  <th>Name</th>
                  <th>Credit to pay</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($amount)){ 
                foreach ($amount as $key => $value) { ?>
                  <tr>
                    <td><?= $key+1; ?></td>
                      <td>
                      <?= $value['name']; ?>
                    </td>  
              
            
                    <td>
                      <?= $value['amount']; ?>
                    </td>
                        
                    <td>
                      <?=  date('d-m-Y',strtotime($value['created_at'])); ?>
                    </td>
                    <td>
                     <a href="<?php echo base_url('admin/edit_amount/'.$value['id']); ?>"  class="btn btn-warning ml-0" title="" ><i class="fa fa-edit"></i></a>   
                    </td>
                    
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
  