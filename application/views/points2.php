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
  <article>
      <a class="top">Leaderboard</a>
        <?php if($users) { ?>
        <div class="col-md-12">
          <table class="table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Points</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $key => $value) { ?>
                    <tr>
                      <td><?=stripslashes($value->name)?></td>
                      <td><?=(@$value->points ? $value->points : '0')?></td>
                    </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <?php } else { ?>
          <h4>Roster Empty</h4>
        <?php } ?>
</article>
