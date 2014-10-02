<?php

include('disqusapi.php');

  $disqus = new Disqusapi($disqus_vars['PCI1V6gJ1XB6un7TJn2xXRREpvoSg2tCVeoJFbFus1HvOBsYgB4TTJedp4Y953Jm']);
  $forums = $disqus->get_forum_list($disqus_vars['PCI1V6gJ1XB6un7TJn2xXRREpvoSg2tCVeoJFbFus1HvOBsYgB4TTJedp4Y953Jm']);
  
  foreach ($forums as $forum) {
    echo $forum->shortname." ".$forum->id."<p>";
  }
  
  ?>