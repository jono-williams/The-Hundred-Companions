<div class="container" style="padding-top:25px;">
<?php if($this->session->flashdata('importResult')) { ?>
  <div class="alert <?=($this->session->flashdata('importResult') == "Import Was Successfully" ? 'alert-success' : 'alert-danger')?>">
    <?=$this->session->flashdata('importResult')?>
  </div>
<?php } ?>
  <h2><?=$title?></h2>
  <h5><?=$importLikeThis?></h5>
  <form class="form" action="<?=current_url()?>" method="POST" enctype="multipart/form-data">
    <input type="file" id="file" name="file">
    <button type="submit" class="nice_button" name="button">Submit</button>
  </form>
</div>
