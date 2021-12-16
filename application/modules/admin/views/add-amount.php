

<div class="container-fluid">

          <!-- Page Heading -->

          <h1 class="h3 mb-2 text-gray-800"><?= $title; ?></h1>

          <!-- DataTales Example -->

          <div class="card shadow mb-4">

            <div class="card-body">

<div class="col-md-12"> 

        <div class="alertfailurfile"></div>

    <?php echo $this->session->userdata('msg'); ?> 

<form class="form-horizontal" method="post"  action="<?php if(!empty($amount)){ echo site_url('admin/edit_amount/'.$this->uri->segment(3));}else{

          echo  base_url('admin/add_amount/');

        } ?>"  enctype="multipart/form-data" >

        <h3 class="text-center"><?= $title; ?></h3><br>


         <div class="form-group">

          <label class="col-sm-2 control-label">credit amount</label>

          <div class="col-sm-8">

            <input type="text" name="amount"  class="form-control"  placeholder="amount" 

            value="<?php if(!empty($amount)){ echo $amount['amount']; } ?>"

            > 
         <p><?php echo form_error('credit_amount', '<span class="error_msg">', '</span>'); ?></p> 

          </div>

        </div>

      
        <div class="col-sm-offset-2">

          <?php if(!empty($amount)){ ?>

  <input type="hidden" name="id" value="<?php echo $amount['id']; ?>">

                <input type="submit" name="submit" value="Update" class="btn btn-success">

                <?php } else { ?>

                <input type="submit" name="submit" value="Add" class="btn btn-success">

                <?php } ?>

            </div>

       

      </form>

</div>

</div>

        </div>

        </div>







