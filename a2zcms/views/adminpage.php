<?php $this->load->view('includes/adminheader'); ?>

<div class="container">
	<div class="row">
		<?php 
		echo modules::run("adminmenu/adminmenu/head_navigation");
		echo '<div class="col-lg-2 col-sm-1 " id="sidebar-left" style="min-height: 559px;">
		'.modules::run("adminmenu/adminmenu/left_navigation").'</div>';
		if(isset($main_content)) $this->load->view($main_content); 
		?>
	</div>
</div>
<?php $this->load->view('includes/adminfooter'); ?>
