<div class="container" style="padding-top:25px;">
<?php if($this->session->flashdata('importResult')) { ?>
  <div class="alert <?=($this->session->flashdata('importResult') == "Import Was Successfully" ? 'alert-success' : 'alert-danger')?>">
    <?=$this->session->flashdata('importResult')?>
  </div>
<?php } ?>
  <form class="form" action="<?=current_url()?>" method="POST" enctype="multipart/form-data">
    <label for="user_id">Select User Account</label>
    <select name="user_id" id="user_id">
      <?php foreach ($users as $key => $user) { ?>
        <option value="<?=$user->Id?>"><?=$user->email?></option>
      <?php } ?>
    </select>
    <label for="name">Old Character Name</label>
    <input type="text" name="name" id="name"/>
    <label for="realm">Old Character Realm</label>
    <input type="text" name="realm" id="realm"/>
    <button type="submit" class="nice_button" name="button">Submit</button>
  </form>
</div>
