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
      <a class="top">Teams</a>
        <?php if($teams) { ?>
        <div class="col-md-12">
          <table class="table">
            <thead>
              <tr>
                <th colspan="3">Name</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($teams as $key => $value) { ?>
                    <tr>
                      <td><?=stripslashes($value->name)?></td>
                      <td><a href="<?=base_url()?>welcome/viewTeam/<?=$value->Id?>"><button class="nice_button">View</button></a></td>
                      <?php
                        $applied = $this->db->query("SELECT * FROM team_applicants WHERE team_id = {$value->Id} AND user_id = {$this->session->userdata('user')->Id}")->row();
                        $already_in_team = $this->db->query("SELECT * FROM team_members WHERE team_id = {$value->Id} AND user_id = {$this->session->userdata('user')->Id}")->row();
                        if(!$applied && !$already_in_team) {
                      ?>
                      <td><a href="<?=base_url()?>welcome/teamApply/<?=$value->Id?>"><button class="nice_button">Apply</button></a></td>
                      <?php } ?>
                    </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <?php } else { ?>
          <h4>No Teams</h4>
        <?php } ?>
</article>
