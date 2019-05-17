<style media="screen">
  #ajax_list {
    background: white;
  }
</style>
<?php if(@$css_files) { foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; } ?>
<style media="screen">
  .flexigrid div.mDiv div, #quickSearchBox, .flexigrid div.fbutton span.export, .print-anchor, .btnseparator, .fbutton, .pcontrol {
    display: none;
  }

  .flexigrid table tr.hDiv, #ajax_list, .flexigrid div.bDiv, .flexigrid div.bDiv tr:hover td,
  .flexigrid div.bDiv tr:hover td.sorted, .flexigrid div.bDiv tr.trOver td.sorted, .flexigrid div.bDiv tr.trOver td,
  .flexigrid tr.erow td, .flexigrid tr td.sorted, .flexigrid div.tDiv, .flexigrid div.fbutton span.add, .form-field-box.odd,
  .flexigrid div.form-div, .form-field-box.even, #crudForm .pDiv {
    background: transparent !important;
  }

  .flexigrid div.pDiv {
    background: none !important;
  }

  .form-display-as-box {
    color: white !important;
  }

  .flexigrid table tr.hDiv {
    border-bottom: 2px solid #ccc;
  }

  .flexigrid div.form-div {
    border-top: 1px solid #ccc;
  }

  .flexigrid input[type=text].form-control {
    width: 455px;
    background: #fff;
  }

  .flexigrid div.fbutton span.add {
    padding:0px;
    color: #fff;
  }

  .flexigrid div.bDiv table {
    margin:0;
  }

  .crud-form .pDiv {
    display: block;
  }

  #form-button-save {
    margin:0;
  }
</style>
<div class="container" style="padding-top:25px;">
  <?=$output?>
</div>
<?php if(@$js_files) { foreach($js_files as $file): ?>
    <script src="<?php echo $file; ?>"></script>
<?php endforeach;} ?>
