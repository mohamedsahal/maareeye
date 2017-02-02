<?php $data = get_settings('general', true); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $data['version'] = $version;
    $settings = get_settings('general', true);
    $data['logo'] = (isset($settings['logo']) && $settings['logo'] != "") ? $settings['logo'] : "public/uploads/maareeye-logo.png";
    $data['half_logo'] = (isset($settings['half_logo']) && $settings['half_logo'] != "") ? $settings['half_logo'] : "public/uploads/maareeye-logo.png";
    $data['company'] = (isset($settings['title'])) ? $settings['title'] : "Maareeye";
    $favicon = (isset($settings['favicon']) && $settings['favicon'] != "") ? $settings['favicon'] : "public/uploads/maareeye-fav.png";
    ?>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title><?= $title ?></title>
    <link rel="icon" href="<?= base_url($favicon) ?>" type="image/png" sizes="16x16">
    <meta name="description" content="<?= $meta_description ?>">
    <meta name="keywords" content="<?= $meta_keywords ?>">
    <?= view("admin/include-css") ?>
    <?php
    isset($data['primary_color']) && $data['primary_color'] != "" ?  $primary_color = $data['primary_color'] : $primary_color =  '#05a6e8';
    isset($data['secondary_color']) && $data['secondary_color'] != "" ?  $secondary_color = $data['secondary_color'] : $secondary_color =  '#003e64';
    isset($data['primary_shadow']) && $data['primary_shadow'] != "" ?  $primary_shadow = $data['primary_shadow'] : $primary_shadow =  '#05a6e8';
    ?>
    <style>
        body {
            --primary-color: <?= $primary_color ?>;
            --secondary-color: <?= $secondary_color ?>;
        }
    </style>
    
    <script>
        var base_url = "<?= base_url() ?>";
        var site_url = "<?= base_url() ?>";
        var csrf_token = "<?= csrf_token(); ?>";
        var csrf_hash = "<?= csrf_hash();  ?>";
    </script>
</head>

<body>
    <div id="app">
        <div class="main-wrapper">
            <?= view("admin/header_sidebar", $data) ?>
            <?= view("admin/pages/" . $page) ?>
            <?= view("admin/footer") ?>
        </div>
    </div>
    <?= view("admin/include-scripts") ?>
    <!-- Page Specific JS File -->
    <?php if (session()->has('message')): ?>
        <script>
            function showToastMessage(message, type) {
                console.log(message);
                
                switch (type) {
                    case "error":
                        $().ready(
                            iziToast.error({
                                title: "Error",
                                message: message,
                                position: "topRight",
                            })
                        );
                        break;
                    case "success":
                        $().ready(
                            iziToast.success({
                                title: "Success",
                                message: message,
                                position: "topRight",
                            })
                        );
                        break;
                }
            }
            showToastMessage("<?= session('message') ?>", "<?= session('type') ?>");
        </script>
    <?php endif; ?>
</body>

</html>