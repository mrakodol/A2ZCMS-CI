<div id="content" class="col-lg-10 col-sm-11 ">
	<div class="row">
		<div class="page-header">
			<h1>List of blog comments</h1>
		</div>
		<?php if ($content['blogcomments']->result_count() > 0) { ?>
			   
		<table class="table table-hover">
			<thead>
        <tr>
          <th>Comment</th>
          <th>Created at</th>          
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
		<?
		foreach ($content['blogcomments'] as $item) {
			echo '<tr>
			<td>'.$item->content.'</td>
			<td>'.$item->created_at.'</td>
			<td class="">
				<a class="iframe btn btn-sm btn-default cboxElement" href="'.base_url("admin/blogs/blogcomments_create/".$item->id).'"><i class="icon-edit "></i></a>
				<a class="iframe btn btn-sm btn-danger cboxElement" href="'.base_url("admin/blogs/blogcomments_delete/".$item->id).'"><i class="icon-trash "></i></a>
            </td>
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
        No items found. 
    </div>
<?php } ?>
	</div>
</div>
<script>
	$(".iframe").colorbox({
					iframe : true,
					width : "50%",
					height : "80%"
				});
</script>