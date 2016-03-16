

<?php if($message){ ?>
<div class="alert alert-warning"><?=$message?></div>
<?php } ?>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Settings</h3>
  </div>
  <div class="panel-body">
	
	
	
	<?php echo form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cloudflare_mk'.AMP.'method=update_settings', array('class' => 'form-horizontal'))?>
 
	  <div class="form-group">
	 	 <div class="col-sm-10 col-sm-offset-2">
			<p>Please enter your CloudFlare information below. For assistance in locating your CloudFlare api key, please review the <a href="https://support.cloudflare.com/hc/en-us/articles/200167836-Where-do-I-find-my-CloudFlare-API-key-" target="_blank">CloudFlare Support Documentation</a>.</p>
		</div>
	    <label for="cloudflare_email" class="col-sm-2 control-label">CloudFlare Email:</label>
	    <div class="col-sm-10">
	      	<input 
	      		type="email" 
	      		class="form-control" 
	      		name="cloudflare_email" 
	      		id="cloudflare_email" 
	      		placeholder="CloudFlare Email" 
	      		value="<?php echo $cloudflare_email;?>"
	      	>
	    </div>
	  </div>
	  <div class="form-group">
	    <label for="cloudflare_key" class="col-sm-2 control-label">CloudFlare API Key:</label>
	    <div class="col-sm-10">
	      	<input 
		      	type="password" 
		      	class="form-control" 
		      	name="cloudflare_key" 
		      	id="cloudflare_key" 
		      	placeholder="CloudFlare Key" 
		      	value="<?php echo $cloudflare_key;?>"
	     	> 
	    </div>
	  </div> 
	  <div class="form-group">
	    <div class="col-sm-offset-2 col-sm-10">
	      <button type="submit" class="btn btn-default">Save Settings</button>
	    </div>
	  </div>
	<?=form_close()?>

  </div>
</div> 
 
 
 
 
	

 

 


 








 