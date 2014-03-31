<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Connect 4</title>
	<link href="<?php echo base_url();?>css/site.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url();?>css/bootstrap.min.css" rel="stylesheet" media="screen">
	<noscript>
		Javascript is not enabled! Please turn on Javascript to use this site.
	</noscript>
</head>

<body>
<header>
	<?php $this->load->view('shared/header');?>
</header>

<div class="wrapper">
	<?php $this->load->view($partial);?>
</div>

<footer> 
	<?php $this->load->view('shared/footer');?>
</footer>

<input id="base_url" type="hidden" value="<?= base_url() ?>"/>
<script src="https://code.jquery.com/jquery-latest.js"></script>
<script src="<?= base_url() ?>js/jquery.timers.js"></script>
<script src="<?= base_url() ?>js/bootstrap.min.js"></script>
<script src="<?= base_url() ?>js/scripts.js"></script>
<?php
	if(isset($js)) {
	    $this->load->helper('js');
	    js($js);
	}
?>
</body>
</html>
