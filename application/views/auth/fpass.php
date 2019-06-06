<link href="<?=base_url()?>assets/css/login.css" rel="stylesheet" >
<?php if($this->session->userdata('loginSuccess')) { ?>
  <article>
    <a class="top">Success</a>
    <div class="alert alert-success">
      <?=$this->session->userdata('loginSuccess')?>
    </div>
  </article>
<?php } ?>
<?php if($this->session->userdata('loginError')) { ?>
  <article>
    <a class="top">Failed</a>
    <div class="alert alert-danger">
      <?=$this->session->userdata('loginError')?>
    </div>
  </article>
<?php } ?>
<article>
  <a class="top">Sign Up!</a>
  <section class="body">
<div id="logreg-forms">
    <form action="<?=base_url()?>auth/reset_password" method="POST" class="form-signup">
        <input type="text" style="width:45.5%; margin:1%; float:left;" id="auth-code" name="auth-code" class="form-control" placeholder="Auth Code" required="" autofocus="" autocomplete="off">
        <input type="email" style="width:45.5%; margin:1%; float:left;" id="user-email" name="user-email" class="form-control" placeholder="Email address" required autofocus="" autocomplete="off">
        <input type="password" style="width:95.3%; margin:1%; float:left;" id="user-pass" name="user-pass" class="form-control" placeholder="New Password" required autofocus="" autocomplete="off">
        <button class="nice_button" type="submit">Reset Password</button>
    </form>
</div>
</section>
</article>
