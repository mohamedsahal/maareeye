<?php $data = get_settings('general', true); ?>
<!-- primary color #1679c5 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $settings = $data;
    $data['logo'] = (isset($settings['logo']) && $settings['logo'] != "") ? $settings['logo'] : "public/uploads/maareeye-logo.png";
    $data['half_logo'] = (isset($settings['half_logo']) && $settings['half_logo'] != "") ? $settings['half_logo'] : "public/uploads/maareeye-logo.png";
    $favicon = (isset($settings['favicon']) && $settings['favicon'] != "") ? $settings['favicon'] : "public/uploads/maareeye-fav.png";
    $data['company'] = (isset($settings['title'])) ? $settings['title'] : "Maareeye";
    $id = $_SESSION['user_id'];

    $team_member = fetch_details('team_members', ['user_id' => $id]);

    if (empty($team_member)) {
        $businesses = fetch_details('businesses', ['user_id' => $id]);
        $data['businesses'] = (isset($businesses)) ? $businesses : $businesses = [];
    } else {
        $businesses = [];

        foreach ($team_member as $key) {
            // Fetch the business details and extract the first element of the array
            $business_ids = $key['business_ids'];
            $business_ids = json_decode($business_ids, true);

            foreach ($business_ids as $key) {
                $business = fetch_details('businesses', ['id' => $key]);

                if (!empty($business)) {
                    $businesses[] = $business[0]; // Assuming fetch_details returns an array with a single business
                }
            }
        }

        $data['businesses'] = (isset($businesses)) ? $businesses : $businesses = [];
    }

    ?>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>
        <?= $title ?>
    </title>
    <link rel="icon" href="<?= base_url($favicon) ?>" type="image/png" sizes="16x16">
    <meta name="description" content="<?= $meta_description ?>">
    <meta name="keywords" content="<?= $meta_keywords ?>">
    <?php include("include-css.php") ?>
    <?php
    $primary_color = (isset($data['primary_color']) && $data['primary_color'] != "") ? $data['primary_color'] : '#05a6e8';
    $secondary_color = (isset($data['secondary_color']) && $data['secondary_color'] != "") ? $data['secondary_color'] : '#003e64';
    $primary_shadow = (isset($data['primary_shadow']) && $data['primary_shadow'] != "") ? $data['primary_shadow'] : '#05a6e8';
    ?>
    <style>
        body {
            --primary-color:
                <?= $primary_color ?>;
            --secondary-color:
                <?= $secondary_color ?>;
            --shadow:
                <?= $primary_shadow ?>;
        }
    </style>
    <script>
        var base_url = "<?= base_url() ?>";
        var site_url = "<?= base_url() ?>";
        var csrf_token = "<?= csrf_token(); ?>";
        var csrf_hash = "<?= csrf_hash(); ?>";
    </script>
</head>

<body>

    <?php if (session()->has('message')): ?>
        <script>
            showToastMessage("<?= session('message') ?>", "<?= session('type') ?>");
        </script>
    <?php endif; ?>

    <div id="app">
        <div class="main-wrapper">
            <?= view("vendor/header_sidebar", $data) ?>
            <?= view("vendor/pages/" . $page) ?>
            <?= view("vendor/footer") ?>
        </div>
    </div>


    <!-- Page Specific JS File -->
    <?php include("include-scripts.php") ?>

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