<?php
helper('function');
$data = [];
try {
    $data = get_settings('general', true);
} catch (Exception $e) {
    echo "<script>console.log('$e')</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    
    $settings = get_settings('general', true);
    $favicon = (isset($settings['favicon']) && $settings['favicon'] != "") ? $settings['favicon'] : "public/uploads/maareeye-fav.png";
    ?>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="description" content="<?= $meta_description ?>">
    <meta name="keywords" content="<?= $meta_keywords ?>">
    <title><?= $title ?></title>
    <link rel="icon" href="<?= base_url($favicon) ?>" type="image/png" sizes="16x16">

    <?php
    isset($data['primary_color']) && $data['primary_color'] != "" ?  $primary_color = $data['primary_color'] : $primary_color =  '#05a6e8';
    isset($data['secondary_color']) && $data['secondary_color'] != "" ?  $secondary_color = $data['secondary_color'] : $secondary_color =  '#003e64';
    isset($data['primary_shadow']) && $data['primary_shadow'] != "" ?  $primary_shadow = $data['primary_shadow'] : $primary_shadow =  '#05A6E8';
    ?>
    <style>
        body {
            --primary: <?= $primary_color ?>;
            --secondary: <?= $secondary_color ?>;
            --nav-link: <?= $secondary_color ?>;
            --primary-shadow: 0px 5px 30px <?= $primary_shadow ?>;
        }
    </style>
    <?= view("frontend/include-css"); ?>
    <script>
        var base_url = "<?= base_url() ?>";
        var site_url = "<?= base_url() ?>";
        let csrf_token = "<?= csrf_token(); ?>";
        let csrf_hash = "<?= csrf_hash();  ?>";
    </script>
</head>

<body class="d-flex flex-column min-vh-100">
    <div id="app">
        <div class="main-wrapper" data-aos="fade-up" data-aos-delay="100">
            <?= view("frontend/header"); ?>
            <main class="flex-shrink-0">
                <?php echo view("frontend/retro/pages/$page"); ?>
            </main>
            <?php echo view("frontend/footer"); ?>
        </div>
    </div>
    <?= view("frontend/include-scripts") ?>
</body>

</html>