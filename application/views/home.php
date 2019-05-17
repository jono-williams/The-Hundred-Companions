<?php if($articles) { ?>
<?php foreach ($articles as $key => $a) { ?>
  <article>
    <a class="top"><?=$a->title?></a>
    <section class="body">
      <?php if($a->thumbnail) { ?>
        <div class="avatar">
          <img src="//render-eu.worldofwarcraft.com/character/<?=$a->thumbnail?>" alt="avatar" height="85" width="85">
        </div>
      <?php } ?>
      <p class="bigtext"><?=$a->content?></p>
      <div class="clear"></div>

      <div class="news_bottom">
        Posted by <?=$a->name?>-<?=$a->realm?> on <?=date_format(date_create($a->date),"d/m/Y")?>
      </div>
    </section>
  </article>
<?php } ?>
<?php } else { ?>
  <article>
    <a class="top">No Articles</a>
  </article>
<?php } ?>