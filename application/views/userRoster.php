<style media="screen">
  .character {
    position: relative;
  }

  .table thead th {
    font-weight: 700;
  }

  .table td, .table th {
    border-top: 1px solid #786956;
  }

  .table thead th {
    border-bottom: 2px solid #786956;
  }
</style>
<a href="<?=base_url()?>admin/usersPoints"><button class="nice_button">Back</button></a>
  <article>
      <a class="top">Roster</a>
        <?php if($characters) { ?>
        <div class="col-md-12">
          <table class="table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Realm</th>
                <th>Level</th>
                <th>Race</th>
                <th>Class</th>
                <th>Role</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($characters as $key => $value) { ?>
                    <tr>
                      <td><?=stripslashes($value->name)?></td>
                      <td><?=str_replace(' ', '', $value->realm)?></td>
                      <td><?=$value->level?></td>
                      <td><?=$value->race?></td>
                      <td><?=$value->class?></td>
                      <td><?=$value->role?></td>
                    </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <?php } else { ?>
          <h4>Roster Empty</h4>
        <?php } ?>
</article>
<article>
    <a class="top">Events Joined</a>
      <?php if($joined_events) { ?>
      <div class="col-md-12">
        <table class="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Points Earned</th>
              <th>Activity Name</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($joined_events as $key => $value) { ?>
                  <tr>
                    <td><?=$value->name?>-<?=str_replace(' ', '', $value->realm)?></td>
                    <td><?=$value->points?></td>
                    <td><?=$value->aName?></td>
                    <td><?=date_format(date_create($value->timestamp),"d/m/Y H:i:s")?></td>
                  </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } else { ?>
        <h4>Empty</h4>
      <?php } ?>
</article>
