<article>
  <a class="top">Team Overview</a>
  <form class="form" action="<?=current_url()?>" method="post">
    <select name="team">
      <?php foreach ($teams as $key => $value) { ?>
        <option <?=(@$_POST['teams'] == $value->Id) ? 'selected="selected"' : '' ?> value="<?=$value->Id?>"><?=stripslashes($value->name)?></option>
      <?php } ?>
    </select>
    <input type="submit" name="" class="nice_button" value="Submit">
  </form>
</article>
<?php if(@$teamDetails) { ?>
<article>
  <a class="top">Team Info</a>
  <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Realm</th>
        <th>Is Leader?</th>
        <th>Remove</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($teamDetails as $key => $value): ?>
        <tr>
          <td><?=stripslashes($value->character_name)?></td>
          <td><?=str_replace(' ', '', stripslashes($value->character_realm))?></td>
          <td><?=($value->isLeader ? 'Yes' : 'No')?></td>
          <td>
            <a href="<?=base_url()?>admin/teamRemoveMember/<?=$value->Id?>">
              <button class="nice_button">Remove</button>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</article>
<?php } ?>
