<div class="carousel slide">
	<!--div>We’re making it easier to find best professionals for your work.</div-->
	<div class="box-wrapper span10">
		<div class="searchPanel row-fluid">
		<?=form_open(array('action'=>root_url('request')))?>
				<div class="input-append span12 margin0">
					<div class="span2"><label class="searchTitle">FIND A PRO</label></div>
					<div class="span4"><input type="text" name="role_name" placeholder="<?=__('What service do you need ?')?>" id="request_title" class="span12 searchInput" /><!--<br><span class="onlineCount">37594 Pros currently online.</span>--></div>
					<div class="span4"><input type="text" name="zipcode" placeholder="<?=__('Enter your zip code or full address.')?>" id="request_zipcoe" class="span12 searchInput" /></div>
					<div class="span2"><input type="submit" value="<?=__('SUBMIT')?>" class="btn btn-primary btn-search" /></div>
				</div>
			<script> Page.bindRoleSelect('#request_title'); </script>
		<?=form_close()?>
		</div>
	</div>
</div><!--carousel-->