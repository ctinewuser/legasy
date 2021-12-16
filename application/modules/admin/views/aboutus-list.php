<div class="container-fluid">
<div class="height20 clear"></div>
<div class="col-sm-12"> 
    <?php echo $this->session->userdata('msg'); ?> 
  
    <form class="form-horizontal" method="post" action="<?php if(!empty($about)){ echo base_url('admin/aboutUs_page');}else{
          echo  base_url('admin/addcategory');
        } ?>" >
      <fieldset>
        <h1 class="h3 mb-2 page-title">About Us</h1>
        <div class="form-group" id="main">
          <textarea class="form-control ckeditor" rows="20" name="editor" required><?php echo $about['data']; ?></textarea>
        </div>
        <div class="form-group">
          <div class="text-right">
           <?php if(!empty($about)){ ?>
          <input type="hidden" name="id" value="<?php echo $about['id']; ?>">
                <input type="submit" name="submit" value="Update" class="btn btn-success" style="min-width: 180px;">
                <?php } else { ?>
                <input type="submit" name="submit" value="Add" class="btn btn-success" style="min-width: 180px;">
                <?php } ?>
          </div>
        </div>
      </fieldset>
    </form>
</div>
</div>
</div>