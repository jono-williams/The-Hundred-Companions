<article>
  <a class="top">Applications Overview</a>
  <form class="form" action="<?=current_url()?>" method="post">
    <select name="application">
      <?php foreach ($applications as $key => $value) { ?>
        <option <?=(@$_POST['application'] == $value->Id) ? 'selected="selected"' : '' ?> value="<?=$value->Id?>,<?=$value->team_id?>,<?=$value->user_id?>"><?=stripslashes($value->tName)?>: <?=stripslashes($value->name)?>-<?=stripslashes($value->realm)?></option>
      <?php } ?>
    </select>
    <input type="submit" name="" class="nice_button" value="Submit">
  </form>
</article>
<?php if(@$application_questions) { ?>
<article>
  <a class="top">Event Details</a>
  <table class="table">
    <thead>
      <tr>
        <th>Question</th>
        <th>Answer</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($application_questions as $key => $value): ?>
        <tr>
          <td><?=str_replace('_', ' ', $value->question)?></td>
          <td><?=$value->answer?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <a href="<?=base_url()?>admin/acceptApp/<?=$search[0]?>"><button type="button" class="nice_button">Accept</button></a>
  <a href="<?=base_url()?>admin/declineApp/<?=$search[0]?>"><button type="button" class="nice_button">Declined</button></a>
</article>
<?php } ?>
