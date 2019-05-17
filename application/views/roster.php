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
<article class="">
  <a class="top">Search</a>
  <input type="text" name="search" id="search" />
</article>
  <article>
      <a class="top">Roster</a>
        <?php if($characters) { ?>
        <div class="col-md-12">
          <table id="roster" class="table">
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
                      <td><?=$value->name?></td>
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
<script>
     $(document).ready(function(){
          $('#search').keyup(function(){
               search_table($(this).val());
          });
          function search_table(value){
               $('#roster tbody tr').each(function(){
                    var found = 'false';
                    $(this).each(function(){
                         if($(this).text().toLowerCase().indexOf(value.toLowerCase()) >= 0)
                         {
                              found = 'true';
                         }
                    });
                    if(found == 'true')
                    {
                         $(this).show();
                    }
                    else
                    {
                         $(this).hide();
                    }
               });
          }
     });
</script>
