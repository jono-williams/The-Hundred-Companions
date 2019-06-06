<?php if($team_members) { ?>
<article>
  <a class="top"><?=$team->name?> Info</a>
  <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Realm</th>
        <th>Is Leader?</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($team_members as $key => $value): ?>
        <tr>
          <td><?=stripslashes($value->character_name)?></td>
          <td><?=str_replace(' ', '', stripslashes($value->character_realm))?></td>
          <td><?=($value->isLeader ? 'Yes' : 'No')?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</article>
<?php } ?>
