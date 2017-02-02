<meta content="<?= $meta_description; ?>" name="description" />
<meta content="<?= $meta_keywords; ?>" name="keywords" />
<!-- Favicons -->
<?php
$data = [];
try {
    helper('function');
    $data = get_settings('general', true);
} catch (Exception $e) {
    echo "<script>console.log('$e')</script>";
}
?>

<!-- Vendor CSS Files -->
<link href="<?= base_url('public/frontend/assets/retro/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet" />
<link href="<?= base_url("public/frontend/assets/retro/vendor/bootstrap-icons/bootstrap-icons.css") ?>" rel="stylesheet" />
<link href="<?= base_url("public/frontend/assets/retro/vendor/aos/aos.css") ?>" rel="stylesheet" />
<link rel="stylesheet" href="<?= base_url("public/backend/assets/module/css/iziToast.css") ?>">
<!-- Template CSS -->
<link rel="stylesheet" href="<?= base_url("public/frontend/assets/retro/css/custom.css") ?>">
<script src="<?= base_url("public/backend/assets/js/jquery-3.3.1.min.js") ?>"> </script>