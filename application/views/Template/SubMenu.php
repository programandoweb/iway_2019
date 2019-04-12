<nav class="navbar navbar-expand-lg navbar-light bg-light p-3">
  <a class="navbar-brand" href="#"><?php echo $this->util->get_title()?></a>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
				<?php echo links($this->uri->segment(1).'/Add/0?view=iframe',"add_".$this->uri->segment(1),"Agregar ".$this->uri->segment(1),"<i class='fas fa-plus'></i>","pgrw_iframe");?>
      </li>
    </ul>
  </div>
</nav>
