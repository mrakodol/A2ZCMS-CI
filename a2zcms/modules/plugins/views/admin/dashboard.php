<div id="content" class="col-lg-10 col-sm-11 ">
	<div class="row">
		<div class="page-header">
			<h1>Admin panel</h1>
			<b>Welcome to admin panel in A2Z CMS!</b>
		</div>
			<?php
			foreach($content['navigation'] as $item){				
			?>
			<div class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12">
				<div class="smallstat box">
					<i class="<?=$item->icon.' '.$item->background_color?>">&nbsp;</i>
					<span class="title"><?=$item->title?><br><br></span>
					<a href="<?=base_url('admin/'.$item->name)?>" class="more">
						<span>View More</span>
						<i class="icon-chevron-right"></i>
					</a>
				</div>
			</div>
			<?php
				}
			?>
	</div>
</div>