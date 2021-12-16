            <!-- Begin Page Content -->
            <div class="container-fluid">
            <!-- Page Heading -->
            <h1 class="h3 mb-2 text-gray-800"><?= $title; ?></h1>

            <?php echo $this->session->userdata('msg'); ?>    

            <div class="col-md-2 pl-0 mb-4 mt-3">
            <a href="<?php echo base_url('admin/uploadfolder')?>" id="" class="btn btn-success"><i class="plus"></i> Upload Songs</a>
            </div>

            <!-- DataTales Example -->
            <div class="card shadow mb-4">

            <div class="card-body">
            <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
            <tr>

            <th>S.No</th>
            <th>Song Name</th>
            <th>Action</th> 
            </tr>
            </thead>
            <tbody>
            <?php 
            if (!empty($songs)){ 
            foreach ($songs as $key => $value) { ?>
            <tr>
            <td><?=  $key+1; ?></td>
            <td><?= $value['song_name']; ?></td>
             <td>  
            <a href="javascript:void(0)" class="btn btn-danger" onclick="delete_song(this,'<?php echo $value['id'];?>')"><i class="fa fa-trash"></i></a>
            </td>
            </tr>
            <?php } } ?>
            </tbody>
            </table>
            </div>
            </div>
            </div>
            </div>
            <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->
            <script type="text/javascript">
            function delete_song(t,id){
            if (confirm('Are you sure you want to delete this song?')) {
            $.ajax({
            type: "POST",     
            url:"<?php echo base_url('admin/delete_songlist');?>",
            data:{id:id},
            success: function(data){

            if(data){
            $(t).closest('tr').remove();

            }else{
            alert('failure');
            }
            }
            });
            }
            else
            { 
            return false;
            }
            }
            </script>