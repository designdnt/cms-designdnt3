<?php include "tpl_functions.php"; ?>
<?php get_top(); ?>
<?php include "top.php"; 
  $user = new User;
  $rest = new Rest;
  $type = $rest->get("type");
  $date_time_format		= date("d")."-".date("m")."-".date("Y");
?>
<div class="row">
<!-- BEGIN CUSTOM TABLE -->
<div class="col-md-12">
   <div class="grid no-border">
      <div class="grid-header">
		 <i class="fa fa-file-excel-o"></i>
		  <h5><a href="../dnt-view/data/uploads/generated-files/<?php echo $date_time_format; ?>/competition_<?php echo Vendor::getId(); ?>_competitors.csv?8169">Vygenerovať štatistiku do XLS</a></h5>
         <i class="fa fa-table"></i>
         <span class="grid-title">Používatelia</span>
         <div class="pull-right grid-tools">
            <a data-widget="collapse" title="Collapse"><i class="fa fa-chevron-up"></i></a>
            <a data-widget="reload" title="Reload"><i class="fa fa-refresh"></i></a>
            <a data-widget="remove" title="Remove"><i class="fa fa-times"></i></a>
         </div>
      </div>
	  <div class="row grid-body" style="    margin: 0px 20px;">
	  		 
		<li class="post_type">
			<a href="index.php?src=user">
				<span class="label label-primary bg-blue" style="padding: 5px;"><big>všetky</big></span>
			</a>
		</li>
	<?php foreach($user->getUserTypes() as $row){?>
		<li class="post_type">
			<a href="index.php?src=user&type=<?php echo $row['type']?>">
				<span class="label label-primary bg-blue" style="padding: 5px;"><big><?php echo $row['type']?></big></span>
			</a>
		</li>
	<?php } ?>
			</div>
		
	  
	  
	  
      <div class="grid-body">
         <table class="table table-hover">
            <thead>
               <tr>
                  <th>#</th>
                  <th>Meno, priezvisko</th>
                  <th>Email</th>
                  <th>Voucher</th>
                  <th>Dátum vytvorenia<br/>Dátum aktualizácie</th>
                  <th>img</th>
				  <th>Voucher</th>
                  <th>Akcia</th>
               </tr>
            </thead>
            <tbody>
               <?php
			   foreach($user->getUserByType($rest->get("type")) as $row){
				   $image 		= $user->getImage($row['img']);
				   $voucherId 	= $row['voucher'];
				   $postId 		= $row['id_entity'];
				   $voucherAssigneUrl = "index.php?src=user&type=".$rest->get("type")."&action=assign-voucher&post_id=$postId";
				?>
				<tr>
					<td><b><?php echo $postId ?></b></td>
					<td><b><?php echo $row['name']." ".$row['surname']; ?></b></td>
					<td><?php echo $row['email']; ?></td>
					<td><?php echo $row['voucher']; ?></td>
					<td><?php echo $row['datetime_creat']; ?><br/><b><?php echo $row['datetime_update']; ?></b></td>
					
					<td><b><?php if($image){echo '<a href="'.$image.'" target="_blank"><i class="fa fa-picture-o bg-green action"></i>';}?></td>
					<td>
						<?php if($voucherId){?>
							<i title="Voucher je priradený" class="fa fa-check bg-green action"></i></td>
						<?php }else{?>
							<a href="<?php echo $voucherAssigneUrl; ?>"><i title="Voucher čaká na priradenie, kliknutím priradíte." class="fa fa-times bg-blue action"></i></a>
						<?php } ?>
					</td>
					<td>
						<a title="Editovať používateľa" href="index.php?src=user&action=edit&post_id=<?php echo $row['id_entity'] ?>"><i class="fa fa-pencil bg-blue action"></i></a>
						<a <?php echo Dnt::confirmMsg("Naozaj chcete vymazať tohoto používateľa?"); ?> title="Zmazať používateľa" href="index.php?src=user&action=del&post_id=<?php echo $row['id_entity'] ?>"><i class="fa fa-times bg-red action"></i></a>
					</td>
				</tr>
               <?php
                  }
                  
                  ?>									
            </tbody>
         </table>
      </div>
   </div>
   
   <ul class="pagination">
      <li class="">
         <a href="<?php echo User::paginator($type, "prev");?>">
         &laquo;
         </a>
      </li>
      <li>
         <a href="<?php echo User::paginator($type, "first");?>">
         <?php echo User::getPage($type, "first");?>
         </a>
      </li>
      <li>
         <a href="<?php echo User::paginator($type, "last");?>">
         <?php echo User::getPage($type, "last");?>
         </a>
      </li>
      <li>
         <a href="<?php echo User::paginator($type, "next");?>">
         &raquo;
         </a>
      </li>
   </ul>
   
</div>
<!-- END CUSTOM TABLE -->
<?php include "bottom.php"; ?>
<?php get_bottom(); ?>