<!-- <nav class="navbar navbar-default">
  <div class="container-fluid">
    Brand and toggle get grouped for better mobile display
  </div>
</nav> -->

<?php if($message){ ?>
<div class="alert alert-warning"><?=$message?></div>
<?php } ?>

<div class="panel panel-default">

  	<div class="panel-heading" style="overflow: hidden">
   		<h3 class="panel-title pull-left" style="margin-bottom:0;padding:5px 0">Cloudflare domain: <?=$cloudflare_domain?></h3>
		<a style="float:right;font-size: 13px;margin-top: 5px;" href="<?=$base_url?>&method=settings"><span class="glyphicon glyphicon-cog"></span> Settings</a>
   	</div>
  	<div class="panel-body">
  		<div class="col-sm-8">
			<h2 class="large">Purge Cache</h3>
			<p class="small">Clear cached files to force CloudFlare to fetch a fresh version of those files from your web server.<br> You can purge files selectively or all at once.</p>
			<p><strong>Note:</strong> Purging the cache may temporarily degrade performance for your website.</p>
			
		</div>
		<hr class="col-sm-12" style="margin-top:10px;margin-bottom: 10px;">
		<div class="col-sm-5">
			<h3>Purge Individual Files</h3>
			<p>You can purge up to 30 files at a time.</p>
			<p><b>Note:</b> Wildcards are not supported with single file purge at this time. You will need to specify the full path to the file.</p>
		<?php echo form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cloudflare_mk'.AMP.'method=update_purge_urls', array('class' => 'form-vertical'))?>
		<label>Separate URL(s) with spaces, or list one per line.</label>
			<div class="form-group">
				<textarea 
					rows="5" 
					placeholder="http://www.<?=$cloudflare_domain?>/articles" 
					class="width-full user-success" 
					name="purge_urls"
				></textarea>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-refresh"></span> Purge Individual Files</button>
			</div>
		<?=form_close()?>
		<?php $purge_urls_array = array_filter($purge_urls_array); ?>
		<?php if(!empty($purge_urls_array)) : ?>
		<br>
		<p><b>Urls you recently submitted:</b></p>
		<ol>
			<?php foreach ($purge_urls_array as $purge_url) : 
			?>
				<li><?=$purge_url?></li>
			<?php endforeach; ?>
		</ol> 
		<?php endif; ?>
		</div>

		<div class="col-sm-5 col-sm-offset-1">
			<h3>Purge Everything</h3>
			<p>Purge all cached files.</p>
			<p><b>Note:</b> Purging your cache may slow your website temporarily.</p>
			<div class="row">
			<div class="col-sm-4">
			 <a class="btn btn-default btn-sm btn-block small" href="<?=$base_url;?>&method=purge_everything"><span class="glyphicon glyphicon-refresh"></span> Purge Everything</a>
			 </div>
			 </div>
		</div>

		
 	</div>

</div>






 






 