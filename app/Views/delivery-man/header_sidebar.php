
<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li class="dropdown"><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
        </ul>

        <div class="dropdown-menu business">
            <?php
            foreach ($businesses as $business) {
                $business_id = isset($business[0]['id']) ? $business[0]['id'] : "";
                echo "<a class='dropdown-item has-icon ' href='" . base_url('delivery_boy/home/switch_businesses') . "/" . $business_id . "'> <div class='icon-box'><img class='img-fluid-business' src='" . base_url($business[0]['icon']) . "' > " . $business[0]['name'] . "</div></a>";
            } ?>
        </div>

        <ul class="navbar-nav navbar-right">
            <li class="dropdown d-inline">
                <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                    <div class="d-sm-none d-lg-inline-block"></div><?= isset($_SESSION['business_name']) ? $_SESSION['business_name'] : "Select business first!" ?>
                </a>

                <div class="dropdown-menu dropdown-menu-left business-icon">
                    <div class="dropdown-title"><?= labels('businesses', "Businesses") ?></div>
                    <?php
                    foreach ($businesses as $business) {
                        $business_id = isset($business[0]['id']) ? $business[0]['id'] : "";
                        echo "<a class='align-items-center d-flex dropdown-item has-icon' href='" . base_url('delivery_boy/home/switch_businesses') . "/" . $business_id . "'><div class='align-items-center d-flex icon-box justify-content-center'><img class='img-fluid-business' src='" . base_url($business[0]['icon']) . "' ></div><p class='ml-2 mb-0'>" . $business[0]['name'] . "</p></a>";
                    } ?>
                </div>
            </li>
        </ul>

    </form>
    <?php


    $first_name = $user->first_name; ?>
    <ul class="navbar-nav navbar-right">
        <div><span class='badge badge-danger'><?= $version ?></span></div>
        <?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? "<div><span class='badge badge-info'>Demo Mode</span></div>" : ""  ?>

        <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <?= strtoupper($current_lang) ?>
            </a>
            <div class="dropdown-menu dropdown-menu-left">
                <?php foreach ($languages_locale as $language) { ?>
                    <span onclick="set_locale('<?= $language['code'] ?>')" class="dropdown-item has-icon text-left <?= ($language['code'] == $current_lang) ? "text-primary" : "" ?>">
                        <?= strtoupper($language['code']) . " - "  . ucwords($language['language']) ?>
                    </span>
                <?php } ?>


            </div>
        </li>
        <li class="dropdown"><a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <?= labels('hello', 'Hello') ?> 👋,<?= $first_name; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-left">
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('auth/logout') ?>" class="dropdown-item has-icon text-danger">
                    <i class="fas fa-sign-out-alt"></i> <?= labels('logout', 'Logout') ?>
                </a>
            </div>
        </li>
    </ul>
</nav>
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand mb-3">
            <a href="#"> <img src="<?php echo base_url($logo); ?>" class="sidebar_logo w-max-90 h-max-60px" alt=""></a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm mb-3">
            <a href="#"><img src="<?php echo base_url($half_logo); ?>" class="h-50" alt=""></a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header"><?= labels('dashboard', 'Dashboard') ?></li>
            <li class="nav-item">
                <a href="<?= base_url('delivery_boy/home');  ?>" class="nav-link "><i class="bi bi-house-door text-warning"></i><span><?= labels('dashboard', 'Dashboard') ?></span></a>
            </li>
            <li class="menu-header"><?= labels('business', 'Business') ?></li>
            <?php if ($customer_permission == "1") { ?>
                <li class="nav-item">
                    <a href="<?= base_url("delivery_boy/customers") ?>" class="nav-link"><i class="bi bi-people-fill text-primary"></i><span><?= labels('customers', 'Customers') ?></span></a>
                </li>
            <?php } ?>
            <li class="nav-item dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="bi bi-cart3 text-info"></i><span><?= labels('orders', 'Orders') ?></span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="<?= base_url("delivery_boy/orders") ?>"><?= labels('orders', 'Orders') ?></a></li>
                    <?php if ($orders_permission == "1") { ?>
                        <li><a class="nav-link" href="<?= base_url("delivery_boy/orders/create") ?>"><?= labels('create_order', 'Create Order') ?></a></li>
                    <?php } ?>

                </ul>
            </li>
            <?php if ($transaction_permission == "1") { ?>
                <li class="nav-item">
                    <a href="<?= base_url("delivery_boy/transactions") ?>" class="nav-link"><i class="bi bi-currency-exchange text-success"></i><span><?= labels('transactions', 'Transactions') ?></span></a>
                </li>
            <?php } ?>

        </ul>
    </aside>
</div>