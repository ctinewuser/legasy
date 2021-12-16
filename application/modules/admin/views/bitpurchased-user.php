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
                       <th>User Name</th>
                    <th>Bit Pack Name</th>
                    <th>Bit Pack Amount</th>
                    <th>Remaining Amount</th>
                  
                    <th>Created Date</th>  

                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($buyed)){ 

                    foreach ($buyed as $key => $value) { ?>
                    <tr>
                    <td><?= $key+1; ?></td>
                     <td>
                    <?= user_full_name($value['user_id']); ?>
                    </td>
                    <td>

                    <?= bitpack_name($value['bit_pack_id']); ?>
                    </td>
                     <td>
                    <?= $value['bit_pack_amount']; ?>
                    </td> 
                    <td>
                    <?= $value['total_donate_bit']; ?>
                    </td>
                   

                    <td>
                    <?= date('d-M-Y',strtotime($value['created_at'])); ?>
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
