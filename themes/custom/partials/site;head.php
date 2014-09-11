<meta name="description" content="<?= h($this->page->description) ?>" />
<meta name="keywords" content="<?= h($this->page->keywords) ?>" />
<meta name="author" content="Scripts Ahoy!" />
<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<?=$this->css_include(array(
	// Framework
		// Ahoy
		'@/assets/stylesheets/css/shared/framework.css',

	// Extras
	'@/assets/extras/font-awesome/css/font-awesome.css',
	'@/assets/extras/aristo/css/aristo.css',
	'@/assets/extras/carousel/css/skin.css',

	// Theme
	'@/assets/stylesheets/css/layouts/application_layout/templates/default_template.css',
	'@/assets/stylesheets/css/layouts/application_layout/templates/home_template.css',
	'@/assets/stylesheets/css/layouts/application_layout/templates/content_template.css',
	'@/assets/stylesheets/css/layouts/application_layout/templates/full_width_template.css',
	'@/assets/stylesheets/css/layouts/application_layout/templates/full_width_background_template.css',

	// Shared / Controls
	'@/assets/stylesheets/css/layouts/application_layout/shared/box.css',
	'@/assets/stylesheets/css/layouts/application_layout/shared/forms.css',
	'@/assets/stylesheets/css/layouts/application_layout/shared/badge_control.css',
	'@/assets/stylesheets/css/layouts/application_layout/shared/provider_form.css',
	'@/assets/stylesheets/css/layouts/application_layout/shared/offer_request_control.css',
	'@/assets/stylesheets/css/layouts/application_layout/shared/conversation_control.css',
), array(
	'skip_cache' => true,
	'src_mode' => true,
)) ?>
<?=$this->js_include(array(
	// Framework
		// Core
		'jquery',
		'jquery-helper',
		'phpr-core',
		// Ahoy
		'@/assets/scripts/js/shared/framework.js',

	// Extras
	'@/assets/extras/carousel/js/jquery.jcarousel.js',

	// Theme
	'@/assets/scripts/js/layouts/application_layout.js',

), array(
	'skip_cache' => true,
	'src_mode' => true,
)) ?>

<link rel="alternate" type="application/rss+xml" href="<?= root_url('blog/feed') ?>" title="<?= c('site_name') ?> RSS">

<?=$this->display_head()?>
