<div class="searchPanel rowfluid">
<?=form_open(array('action'=>root_url('request')))?>
		<div class="input-append span12">
			<div class="span2"><label>Find a Pros</label></div>
			<div class="span4"><input type="text" name="role_name" placeholder="<?=__('What service do you need ?')?>" id="request_title" class="span11" /></div>
			<div class="span4"><input type="text" name="zipcode" placeholder="<?=__('Enter zipcode here.')?>" id="request_zipcoe" class="span11" /></div>
			<div class="span2"><input type="submit" value="<?=__('Get a Quote')?>" class="btn btn-primary" /></div>
		</div>
	<script> Page.bindRoleSelect('#request_title'); </script>
<?=form_close()?>
</div>