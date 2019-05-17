<?php

/**
 * /application/core/MY_Loader.php
 *
 */
class MY_Loader extends CI_Loader {
    public function template($template_name, $vars = array(), $return = FALSE)
    {
      $vars['body'] = $this->view($template_name, $vars)->output->final_output;
      $this->view('templates/header', $vars);
    }
}
