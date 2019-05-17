<style media="screen">
  .list li {
    margin-bottom: 10px;
  }
</style>
<ul class="list activities">
  <?php if($activities) { ?>
  <?php foreach ($activities as $key => $activitie) { ?>
  <li>
    <article>
      <a class="top"><?=$activitie->name?></a>
        <?php if($activitie->img) { ?>
          <img style="width:100%;" src="<?=$activitie->img?>" alt="discord"/>
          <hr />
        <?php } ?>
          <div style="text-align:left;padding:25px;">
            Host: <?=$activitie->hostName?><br/>
            Points Awarded: <?=$activitie->points?><br/>
            Time: <?=date("d/m/Y H:i:s", strtotime($activitie->timestamp));?><br/>
            <?=$activitie->description?>
          </div>
          <br/>
          <div>
            <?php if(@$this->session->userdata('user')->Id) { ?>
            <?php if(@$activitie->active) { ?>
            <?php if(@$activitie->joined_events) { ?>
              <button type="button" disabled class="nice_button" name="button">JOINED</button>
              <a style="font-size:12px;text-align:center;" href="<?=base_url()?>welcome/leave_event/<?=$activitie->Id?>"><button type="button" class="nice_button" name="button">LEAVE</button></a>
            <?php } else { ?>
              <a href="<?=base_url()?>welcome/join_event/<?=$activitie->Id?>"><button type="button" class="nice_button" name="button">JOIN</button></a>
            <?php } ?>
            <?php } else { ?>
              <?php if(@$this->session->userdata('role')->name == "Owner" || @$this->session->userdata('role')->name == "Moderator" || @$this->session->userdata('role')->name == "Admin") { ?>
                <form name="myform" action="<?=base_url()?>admin/joined_events" method="post">
                  <input type="hidden" value="<?=$activitie->Id?>" name="events" id="events"/>
                  <button type="submit" class="nice_button" name="button">VIEW ATTENDANCE</button>
                </form>
              <?php } ?>
            <?php } ?>
            <?php } else { ?>
              <a href="<?=base_url()?>login"><button type="button" class="nice_button" name="button">REGISTER</button></a>
            <?php } ?>
          </div>
    </article>
  </li>
<?php } ?>
<?php } else { ?>
  <h4>No Events</h4>
<?php } ?>
</ul>
