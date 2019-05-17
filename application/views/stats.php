<style>
  table {
    color: #fff;
  }
</style>
<div class="container" style="padding-top:25px;">
  <div class="row">
    <div class="col-md-3">
      <h4>User Count:</h4>
      <h5><?=$user_count?></h5>
    </div>
    <div class="col-md-3">
      <h4>Active Activities Count:</h4>
      <h5><?=$active_activities_count?></h5>
    </div>
    <div class="col-md-3">
      <h4>Total Activities Count:</h4>
      <h5><?=$activities_count?></h5>
    </div>
    <div class="col-md-3">
      <h4>Total Points:</h4>
      <h5><?=$point_count?></h5>
    </div>
    <div class="col-md-12">
      <h4>Latest User:</h4>
      <table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>E-Mail</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?=$latest_users->name?></td>
            <td><?=$latest_users->email?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
