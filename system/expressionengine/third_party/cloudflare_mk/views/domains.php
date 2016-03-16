<!-- <nav class="navbar navbar-default">
  <div class="container-fluid">
    Brand and toggle get grouped for better mobile display
  </div>
</nav> -->

<?php if($message){ ?>
<div class="alert alert-warning"><?=$message?></div>
<?php } ?>

<div class="panel panel-default">
  <div class="panel-heading">
	<?php 
		$cf_num = count($get_cloudflare_domains);
		if ($cf_num > 1) : 
	?>

    <h3 class="panel-title">Cloudflare domains</h3>

    <?php endif; ?>
  </div>
  <div class="panel-body">
	<?php
		if ($cf_num > 1) : ?>
		<?php echo form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cloudflare_mk'.AMP.'method=update_domain', array('class' => 'form-horizontal'))?>
		<div class="form-group">
		    <label for="cloudflare_domain" class="col-sm-2 control-label">Choose for which cloudflare domain the cache will be cleared:</label>
				<div class="col-sm-10">
					<?php foreach ($get_cloudflare_domains as $domain) : ?>
					<?php $domain_count = count($domain); ?>
					 
					<p>
						<input 
							type="radio" 
							name="cloudflare_domain"  
		      				placeholder="CloudFlare Domain"
		      				id="<?php echo $domain; ?>"
							value="<?php echo $domain; ?>" 
						>
						<label for="<?php echo $domain; ?>"><?php echo $domain; ?></label>
					</p>
					<?php endforeach; ?>
				</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-default">Save domain</button>
			</div>
		</div>
		<?=form_close()?>
		
	<?php endif; ?>
 </div>

 </div>







 






 