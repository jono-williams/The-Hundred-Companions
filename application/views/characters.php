<style media="screen">
  .character {
    position: relative;
  }

  table, .table {
    color:#fff;
  }
</style>
<?php if($this->session->flashData('loginSuccess')) { ?>
  <article>
    <a class="top">Result</a>
    <div class="alert alert-success">
      <?=$this->session->flashData('loginSuccess')?>
    </div>
  </article>
<?php } ?>
<?php if($characters) { ?>
<?php foreach ($characters as $key => $value) { ?>
  <?php if($value->main_character) { ?>
    <article style="box-shadow: inset 0 0 0 1px rgba(255,255,255,.3), inset 0 0 0 0px #ffc107, inset 0 0 0 4px #000, 0 0 10px #ffc107; height: 150px; margin:1%; width: 48%; <?=($key % 2) ? 'float: right;' : 'float: left;' ?>">
  <?php } else { ?>
    <article style="height: 150px; margin:1%; width: 48%; <?=($key % 2) ? 'float: right;' : 'float: left;' ?>">
  <?php } ?>
      <a class="top"><?=$value->name?>-<?=$value->realm?></a>
      <div style="text-align:left;padding:5px;">
        <?=@json_decode($value->spec)->name?><br/>
        <?=@json_decode($value->spec)->role?><br/>
        <?php if(!$value->main_character) { ?>
          <a href="<?=base_url()?>welcome/set_main/<?=$value->Id?>"><button type="button" class="nice_button" name="button">Set Main</button></a>
        <?php } ?>
      </div>
</article>
<?php } ?>
<?php } else { ?>
  <h4>No Characters</h4>
<?php } ?>
<?php if(@$this->session->userdata('user')->Id) { ?>
<article style="width:100%; float:left;">
  <a class="top">Resync Characters</a>
  <a href="<?=base_url()?>auth/blizzard_oauth"><button type="button" class="nice_button" name="button">Resync</button></a>
</article>
<?php } ?>
