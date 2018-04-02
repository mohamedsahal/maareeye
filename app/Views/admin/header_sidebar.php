<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
        </ul>
    </form>
    <?php


    $first_name = $user->first_name; ?>
    <ul class="navbar-nav navbar-right">
        <?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? "<div><span class='badge badge-info'>Demo Mode</span></div>" : ""  ?>

        <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user"><?= strtoupper($current_lang) ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <?php foreach ($languages_locale as $language) { ?>
                    <span onclick="set_locale('<?= $language['code'] ?>')" class="dropdown-item has-icon text-left <?= ($language['code'] == $current_lang) ? "text-primary" : "" ?>">
                        <?= strtoupper($language['code']) . " - "  . ucwords($language['language']) ?>
                    </span>
                <?php } ?>


            </div>
        </li>
        <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user"><?= labels('hello', 'Hello') ?> 👋,<?= $first_name; ?></a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-title"></div>
                <a href="<?= base_url('admin/profile');  ?>" class="dropdown-item has-icon">
                    <i class="far fa-user"></i> <?= labels('profile', 'Profile') ?>
                </a>
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
            <a href="<?= base_url("admin/home") ?>"> <img src="<?php echo base_url($logo); ?>" class="sidebar_logo w-max-90 h-max-60px" alt=""></a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm mb-3">
            <a href="#"><img src="<?php echo base_url($half_logo); ?>" class="h-50" alt=""></a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header"><?= labels('dashboard', 'Dashboard') ?></li>
            <li class="nav-item">
                <a href="<?= base_url('admin/home');  ?>" class="nav-link active"><i class="bi bi-house-door text-warning"></i><span><?= labels('dashboard', 'Dashboard') ?></span></a>
            </li>

            <li class="nav-item dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="bi bi-grid-fill text-primary"></i><span><?= labels('products', 'Products') ?></span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="<?= base_url('admin/units'); ?>"><?= labels('units', 'Units') ?></a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/categories'); ?>"><?= labels('categories', 'Categories') ?></a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/tax'); ?>"><?= labels('tax', 'Tax') ?></a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('admin/packages');  ?>" class="nav-link"><i class="bi bi-stack text-success"></i><span><?= labels('packages', 'Packages') ?></span></a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('admin/subscriptions');  ?>" class="nav-link"><i class="bi bi-bag-plus text-warning"></i><span><?= labels('subscriptions', 'Subscriptions') ?></span></a>
            </li>

            <li class="nav-item">
                <a href="<?= base_url('admin/vendors');  ?>" class="nav-link"><i class="bi bi-people-fill text-danger"></i></i><span><?= labels('vendors', 'Vendors') ?></span></a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('admin/transactions');  ?>" class="nav-link"><i class="bi bi-bag-plus text-warning"></i><span><?= labels('transactions', 'Transactions') ?></span></a>
            </li>
            
            <li class="nav-item dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="bi bi-gear-fill text-primary"></i><span><?= labels('settings', 'Settings') ?></span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="<?= base_url('admin/settings/general'); ?>"><?= labels('general', 'General') ?></a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/settings/payment_gateway'); ?>"><?= labels('payment_gateway', 'Payment Gateway') ?></a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/settings/email'); ?>"><?= labels('smtp_email', 'SMTP (EMAIL)') ?></a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/languages') ?>"> <?= labels('languages', "Languages") ?></a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/database'); ?>"><?= labels('database_backup', 'Database Backup') ?></a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/settings/about_us'); ?>"><?= labels('about_us', 'About Us') ?></a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/settings/privacy_policy'); ?>"><?= labels('privacy_policy', 'Privacy Policy') ?></a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/settings/terms_and_conditions'); ?>"><?= labels('terms_and_conditions', 'Terms & Conditions') ?></a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/settings/refund_policy'); ?>"><?= labels('refund_policy', 'Refund Policy') ?></a></li>
                </ul>
            </li>
        </ul>
    </aside>
</div>