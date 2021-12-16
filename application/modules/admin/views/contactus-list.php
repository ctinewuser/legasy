

            <div class="container-fluid">

            <!-- Page Heading -->

            <h1 class="h3 mb-2 text-gray-800"><?= $title; ?></h1>

            <!-- DataTales Example -->

            <div class="card shadow mb-4">

              <div class="card-body">

      <div class="col-md-12"> 

          <div class="alertfailurfile"></div>

      <?php echo $this->session->userdata('msg'); ?> 

      <form class="form-horizontal" method="post"  action="<?php if(!empty($contact)){ echo site_url('admin/contactUs/');}

           ?>"  enctype="multipart/form-data" >

          <h3 class="text-center"><?= $title; ?></h3><br>


           <div class="form-group">

            <label class="col-sm-2 control-label">Email</label>

            <div class="col-sm-8">

              <input type="text" name="email"  class="form-control"  placeholder="email" 

              value="<?php if(!empty($contact)){ echo $contact['email']; } ?>"

              > 

               <p><?php echo form_error('email', '<span class="error_msg">', '</span>'); ?></p> 

            </div>

          </div>
          

        <div class="form-group">

            <label class="col-sm-2 control-label">Address</label>

            <div class="col-sm-8">

              <input type="text" name="address"  class="form-control"  placeholder="address" 

              value="<?php if(!empty($contact)){ echo $contact['address']; } ?>"

              > 

            </div>


          </div>

           <div class="form-group">

            <label class="col-sm-2 control-label">Contact</label>

            <div class="col-sm-8">

              <input type="text" name="contactnumber"  class="form-control"  placeholder="contact number" 

              value="<?php if(!empty($contact)){ echo $contact['contactnumber']; } ?>"

              > 

            </div>


          </div> 
            
          <div class="col-sm-offset-2">

            <?php if(!empty($contact)){ ?>

          <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">

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







