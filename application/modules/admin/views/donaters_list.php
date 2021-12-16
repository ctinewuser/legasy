<!--Begin Page Content -->
<div class="container-fluid">
  <!-- Page Heading -->
  <h1 class="h3 mb-2 text-gray-800"><?= $title; ?></h1>

               <?php echo $this->session->userdata('msg'); ?>    
          
          
  <!-- DataTales Example -->
  <div class="card shadow mb-4">
  
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
               
                  <th>S.No.</th>
                  <th>Donate To</th>
                  <th>Donate By</th> 
                    <th>Messages</th>
                     <th>Donate Name</th>
                     <th>Created Date</th>  
                
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($donate)){ 

                foreach ($donate as $key => $value) { ?>
                  <tr>
                    <td><?= $key+1; ?></td>
                    <td>
                      <?= user_full_name($value['donate_to']); ?>
                    </td>
                  <td>
                      <?= user_full_name($value['donate_by']); ?>
                    </td> 
                    <td>
                      <?= $value['message']; ?>
                    </td>
                    <td>
                      <?= $value['donate_name']; ?>
                    </td>

                     <td>
                      <?= date('d-m-Y',strtotime($value['created_at'])); ?>
                    </td>
                   
                  
                    </tr>
                  <?php }

            } ?>
              </tbody>
            </table>
          </div>
        </div>
  </div>
</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->
