<article>
  <a class="top">Attendance Overview</a>
  <form class="form" action="<?=current_url()?>" method="post">
    <select name="events">
      <?php foreach ($events as $key => $value) { ?>
        <option <?=(@$_POST['events'] == $value->Id) ? 'selected="selected"' : '' ?> value="<?=$value->Id?>"><?=stripslashes($value->name)?></option>
      <?php } ?>
    </select>
    <input type="submit" name="" class="nice_button" value="Submit">
  </form>
</article>
<?php if(@$eventM) { ?>
<article>
  <a class="top">Event Details</a>
  <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Realm</th>
        <th>Points</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($eventM as $key => $value): ?>
        <tr>
          <td><?=stripslashes($value->character_name)?></td>
          <td><?=str_replace(' ', '', stripslashes($value->character_realm))?></td>
          <td><?=$value->points?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</article>
<?php } ?>
