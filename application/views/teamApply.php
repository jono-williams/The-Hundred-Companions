<?php if($team_questions) { ?>
<article>
  <a class="top"><?=$team->name?> Application</a>
  <form class="" action="<?=base_url()?>welcome/teamAppSubmit/<?=$team->Id?>" method="post">
    <div>
      <label for="Name">Character Name</label>
      <input type="text" name="Name" value="" />
    </div>
    <div>
      <label for="Realm">Character Realm</label>
      <input type="text" name="Realm" value="" />
    </div>
    <?php foreach ($team_questions as $key => $value): ?>
      <div>
        <label for="<?=$value->question?>"><?=$value->question?></label>
        <input type="text" name="<?=$value->question?>" value="" />
      </div>
    <?php endforeach; ?>
    <input type="submit" class="nice_button" value="Submit">
  </form>
</article>
<?php } else { ?>
<article>
  <a class="top">No Questions</a>
</article>
<?php } ?>
