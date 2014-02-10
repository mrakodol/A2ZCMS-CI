<div id="content" class="col-lg-10 col-sm-11 ">
	<div class="row">
		<div class="page-header">
			<h1>List of login <?=$content['user']->name.' '.$content['user']->surname?></h1>
		</div>
		<div class="pull-right">
			<a class="btn btn-small btn-info" href="<?=base_url("admin/users/index")?>">
				<span class="icon-share-alt icon-white"></span> Back</a>
		</div>
		<?php if ($content['userlogins']->result_count() > 0) { ?>
			   
		<table class="table table-hover">
			<thead>
        <tr>
          <th>Login time</th>
        </tr>
	      </thead>
	      <tbody>
			<?
			foreach ($content['userlogins'] as $item) {
				echo '<tr>
				<td>'.$item->created_at.'</td>
				</tr>';
			}
			?>
	    	</tbody>
		</table>
	   <div class="dataTables_paginate paging_bootstrap">
	            <?php echo $content['pagination']->create_links(); ?>
	    </div>
<?php } else { ?>
    <div class="item_list_empty">
        No items found matching your search terms.
    </div>
<?php } ?>
	</div>
</div>
<script>
	$(".iframe").colorbox({
					iframe : true,
					width : "50%",
					height : "70%"
				});
</script>