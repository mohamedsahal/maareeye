<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(true);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->get('admin/home/', 'admin\Home::index');
$routes->get('admin/home/fetch_sales', 'admin\Home::fetch_sales');
$routes->get('admin/home/fetch_data', 'admin\Home::fetch_data');

$routes->get('lang/(:any)', 'Language::index/$1');
$routes->get('admin/languages/', "admin\Languages::index");

$routes->post('admin/languages/create', "admin\Languages::create");
$routes->post('admin/languages/set_labels', "admin\Languages::set_labels");
$routes->get('admin/languages/change/(:any)', "admin\Languages::change/$1");

$routes->get('admin/Cron_job/', 'admin\Cron_job::renew_service');
$routes->get('admin/Cron_job/test', 'admin\Cron_job::test');

$routes->get('admin/units/', 'admin\Units::index');
$routes->post('admin/units/save_unit', 'admin\Units::save_unit');
$routes->get('admin/units/unit_table', 'admin\Units::unit_table');
$routes->get('admin/units/edit_unit/(:any)', 'admin\Units::edit_unit/$1');
$routes->post('admin/units/delete-unit', 'admin\Units::delete');
$routes->post('admin/units/get-unit', 'admin\Units::get_unit');

$routes->get('admin/tax/', 'admin\Tax::index');
$routes->post('admin/tax/save_tax', 'admin\Tax::save_tax');
$routes->get('admin/tax/tax_table', 'admin\Tax::tax_table');
$routes->get('admin/tax/edit_tax/(:any)', 'admin\Tax::edit_tax/$1');

$routes->post('vendor/tax/get_taxs', 'vendor\Products::get_taxs');
$routes->get('vendor/tax', 'vendor\Tax::index');

$routes->get('admin/categories/', 'admin\Categories::index');
$routes->post('admin/categories/save_categories', 'admin\Categories::save_categories');
$routes->get('admin/categories/edit_category/(:any)', 'admin\Categories::edit_category/$1');
$routes->get('admin/categories/category_table', 'admin\Categories::category_table');
$routes->post('admin/categories/delete-category', 'admin\Categories::delete');
$routes->post('admin/categories/get-category', 'admin\Categories::get_category');

$routes->get('admin/packages/', 'admin\Packages::index');
$routes->get('admin/packages/create', 'admin\Packages::create');
$routes->post('admin/packages/insert_package', 'admin\Packages::insert_package');
$routes->post('admin/packages/update_package', 'admin\Packages::update_package');
$routes->post('admin/packages/remove_tenure/(:any)', 'admin\Packages::remove_tenure/$1');
$routes->get('admin/packages/edit_package/(:any)', 'admin\Packages::edit_package/$1');
$routes->post('admin/packages/delete_plan', 'admin\Packages::delete_plan');

$routes->get('admin/migrate/', 'admin\Migrate::index');
$routes->get('admin/migrate/rollback/(:any)', 'admin\Migrate::rollback/$1');
$routes->get('admin/migrate/is_dir_empty/(:any)', 'admin\Migrate::is_dir_empty/$1');

$routes->get('admin/run-seeder', 'admin\Seed::runSeeder');


$routes->get('admin/seed/warehouse', 'admin\SeedWarehouse::seedWarehouseData');

$routes->get('vendor/warehouse', 'vendor\Warehouse::index', ['filter' => 'checkRoles']);
$routes->post('vendor/warehouse/save', 'vendor\Warehouse::save', ['filter' => 'checkRoles']);
$routes->get('vendor/warehouse/warehouse-table', 'vendor\Warehouse::WarehouseTable');
$routes->post('vendor/warehouse/get-warehouse/(:num)', 'vendor\Warehouse::getWarehouse/$1');
$routes->post('vendor/warehouse/get-all-warehouse', 'vendor\Warehouse::getAllWarehouse');
$routes->get('vendor/warehouse/update_default_warehouse', 'vendor\Warehouse::update_default_warehouse');

$routes->post('vendor/product/save_transfer', 'vendor\Products::save_transfer');

$routes->get('admin/subscriptions/', 'admin\Subscriptions::index');
$routes->post('admin/subscriptions/add_subscription', 'admin\Subscriptions::add_subscription');
$routes->get('admin/subscriptions/subscription_table', 'admin\Subscriptions::subscription_table');
$routes->get('admin/subscriptions/tenures/(:any)', 'admin\Subscriptions::tenures/$1');

$routes->get('admin/vendors/', 'admin\Vendors::index');
$routes->get('admin/vendors/vendor_table', 'admin\Vendors::vendor_table');
$routes->get('admin/vendors/create', 'admin\Vendors::create');
$routes->post('admin/vendors/change-vendor-status', 'admin\Vendors::changeVendorStatus');

$routes->get('admin/transactions/', 'admin\Transactions::index');
$routes->get('admin/transactions/transactions_table', 'admin\Transactions::transactions_table');

$routes->get('admin/profile', 'admin\Profile::index');
$routes->post('admin/profile/update', 'admin\Profile::update');
$routes->get('admin/settings/general', 'admin\Settings::general');
$routes->post('admin/settings/save_settings', 'admin\Settings::save_settings');
$routes->get('admin/settings/about_us', 'admin\Settings::about_us');
$routes->get('admin/settings/payment_gateway', 'admin\Settings::payment_gateway');
$routes->get('admin/settings/refund_policy', 'admin\Settings::refund_policy');
$routes->get('admin/settings/terms_and_conditions', 'admin\Settings::terms_and_conditions');
$routes->get('admin/settings/privacy_policy', 'admin\Settings::privacy_policy');
$routes->get('admin/updater', 'admin\Updater::index');
$routes->post('admin/updater/upload_update_file', 'admin\Updater::upload_update_file');
$routes->get('admin/settings/email', 'admin\Settings::email');
$routes->get('admin/database', 'admin\Database::index');
$routes->get('admin/database/backup', 'admin\Database::backup');
$routes->post('admin/database/backup_database', 'admin\Database::backup_database');
$routes->post('admin/database/delete', 'admin\Database::delete');
$routes->post('admin/database/mail_database', 'admin\Database::mail_database');
$routes->post('admin/database/download', 'admin\Database::download');

$routes->get('/', 'Home::index');
$routes->get('/home', 'Home::index');
$routes->get('/contact', 'Contact::index');
$routes->get('/faqs', 'Faqs::index');
$routes->get('/features', 'Features::index');
$routes->get('/pricing', 'Pricing::index');

$routes->get('/forgot_password_back', 'Forgot_password_back::index');

$routes->get('/login_back', 'Login_back::index');

$routes->get('/about', 'About::index');
$routes->get('/privacy_policy', 'Privacy_policy::index');
$routes->get('/refundpolicy', 'RefundPolicy::index');
$routes->get('/terms_and_conditions', 'Terms_and_conditions::index');
// vendor routes 

// $routes->group('vendor', ['filter' => 'checkpermissions'], function($routes) {


$routes->add('vendor/home', 'vendor\Home::index');

$routes->add('vendor/test', 'vendor\Home::test', ['filter' => 'checkpermissions:module=categories,action=can_create']);


$routes->get('vendor/languages/change/(:any)', "vendor\Languages::change/$1", ['filter' => 'checkpermissions:module=languages,action=read']);

$routes->get('vendor/home/fetch_warehouse_sales', 'vendor\Home::fetch_warehouse_sales');
$routes->get('vendor/home/fetch_sales', 'vendor\Home::fetch_sales', ['filter' => 'checkpermissions:module=home,action=read']);
$routes->get('vendor/home/fetch_data', 'vendor\Home::fetch_data', ['filter' => 'checkpermissions:module=home,action=read']);
$routes->get('vendor/home/fetch_purchases', 'vendor\Home::fetch_purchases', ['filter' => 'checkpermissions:module=home,action=read']);
$routes->get('vendor/home/switch_businesses/(:any)', 'vendor\Home::switch_businesses/$1', ['filter' => 'checkpermissions:module=home,action=read']);

$routes->get('vendor/customers', 'vendor\Customers::index', ['filter' => 'checkpermissions:module=customers,action=can_read']);
$routes->post('vendor/customers/save_status', 'vendor\Customers::save_status', ['filter' => 'checkpermissions:module=customers,action=can_create']);
$routes->get('vendor/customers/customers_table', 'vendor\Customers::customers_table', ['filter' => 'checkpermissions:module=customers,action=can_read']);

$routes->get('vendor/delivery_boys', 'vendor\Delivery_Boys::index');
$routes->post('vendor/delivery_boys/save', 'vendor\Delivery_Boys::save');
$routes->get('vendor/delivery_boys/count/(:any)', 'vendor\Delivery_Boys::count/$1');
$routes->get('vendor/delivery_boys/delivery_boys_table', 'vendor\Delivery_Boys::delivery_boys_table');





$routes->get('vendor/orders', 'vendor\Orders::index', ['filter' => 'checkpermissions:module=pos,action=can_create']);

$routes->get('vendor/orders/orders', 'vendor\Orders::orders', ['filter' => 'checkpermissions:module=orders,action=can_read']);

$routes->get('vendor/orders/orders_table', 'vendor\Orders::orders_table', ['filter' => 'checkpermissions:module=orders,action=can_read']);

$routes->get('vendor/orders/view_orders/(:any)', 'vendor\Orders::view_orders/$1', ['filter' => 'checkpermissions:module=orders,action=can_read']);

$routes->post('vendor/orders/save_order', 'vendor\Orders::save_order', ['filter' => 'checkpermissions:module=orders,action=can_create']);

$routes->get('vendor/orders/orders_table', 'vendor\Orders::orders_table', ['filter' => 'checkpermissions:module=orders,action=can_read']);

$routes->post('vendor/orders/create_status', 'vendor\Orders::create_status', ['filter' => 'checkpermissions:module=orders,action=can_create']);

$routes->get('vendor/orders/set_delivery_boy', 'vendor\Orders::set_delivery_boy', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->add('vendor/orders/update_order_status', 'vendor\Orders::update_order_status', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->get('vendor/orders/customer_balance', 'vendor\Orders::customer_balance', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->get('vendor/orders/get_users', 'vendor\Orders::get_users', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->post('vendor/orders/register', 'vendor\Orders::register', ['filter' => 'checkpermissions:module=orders,action=can_create']);

$routes->post('vendor/orders/save', 'vendor\Orders::save');  // check in methode  // done

$routes->post('vendor/orders/save_sales_orders', 'vendor\Orders::save_sales_orders', ['filter' => 'checkpermissions:module=orders,action=can_create']);
$routes->get('vendor/orders/sales_orders', 'vendor\Orders::sales_orders', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->get('vendor/orders/payment_reminder', 'vendor\Orders::payment_reminder', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->get('vendor/orders/payment_reminder_table', 'vendor\Orders::payment_reminder_table', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->get('vendor/orders/send_reminder', 'vendor\Orders::send_reminder', ['filter' => 'checkpermissions:module=orders,action=can_create']);
$routes->get('vendor/status', 'vendor\Orders::get_status');
$routes->get('vendor/orders/status_table', 'vendor\Orders::status_table');






$routes->get('vendor/customers_subscription', 'vendor\Customers_Subscription::index', ['filter' => 'checkpermissions:module=subscription,action=can_read']);
$routes->get('vendor/customers_subscription/customers_subscription_table', 'vendor\Customers_Subscription::customers_subscription_table', ['filter' => 'checkpermissions:module=subscription,action=can_read']);
$routes->get('vendor/customers_subscription/customers_services_table', 'vendor\Customers_Subscription::customers_services_table', ['filter' => 'checkpermissions:module=subscription,action=can_read']);
$routes->get('vendor/customers_subscription/recursive_services_table', 'vendor\Customers_Subscription::recursive_services_table', ['filter' => 'checkpermissions:module=subscription,action=can_read']);
$routes->get('vendor/customers_subscription/customers_list_of_services_table', 'vendor\Customers_Subscription::customers_list_of_services_table', ['filter' => 'checkpermissions:module=subscription,action=can_read']);
$routes->get('vendor/customers_subscription/remove_subscription/(:any)', 'vendor\Customers_Subscription::remove_subscription/$1', ['filter' => 'checkpermissions:module=subscription,action=can_create']);

$routes->get('vendor/transactions', 'vendor\Transactions::index', ['filter' => 'checkpermissions:module=transactions,action=can_read']);
$routes->get('vendor/transactions/transactions_table', 'vendor\Transactions::transactions_table', ['filter' => 'checkpermissions:module=transactions,action=can_read']);
$routes->get('vendor/transactions/customers_table', 'vendor\Transactions::customers_table', ['filter' => 'checkpermissions:module=transactions,action=can_read']);
$routes->get('vendor/transactions/customer_transaction_table/(:any)/(:any)', 'vendor\Transactions::customer_transaction_table/$1/$2', ['filter' => 'checkpermissions:module=transactions,action=can_read']);

$routes->get('vendor/transactions/purchase/', 'vendor\Transactions::purchase');
$routes->get('vendor/transactions/purchase_transactions_table/', 'vendor\Transactions::purchase_transactions_table');
$routes->get('vendor/transactions/purchase_transaction_table/(:any)', 'vendor\Transactions::purchase_transaction_table/$1', ['filter' => 'checkpermissions:module=transactions,action=can_read']);
$routes->post('vendor/transactions/save_payment', 'vendor\Transactions::save_payment', ['filter' => 'checkpermissions:module=transactions,action=can_create']);

$routes->get('vendor/products', 'vendor\Products::index', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->get('vendor/products/add_products', 'vendor\Products::Add_products', ['filter' => 'checkpermissions:module=products,action=can_create']);
$routes->get('vendor/products/get_products', 'vendor\Products::get_products', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->get('vendor/products/scanned_barcode_items', 'vendor\Products::scanned_barcode_items', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->get('vendor/products/products_table', 'vendor\Products::products_table', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->get('vendor/products/update_variant_status', 'vendor\Products::update_variant_status', ['filter' => 'checkpermissions:module=products,action=can_update']);
$routes->get('vendor/products/remove_variant', 'vendor\Products::remove_variant/$1', ['filter' => 'checkpermissions:module=products,action=can_update']);
$routes->get('vendor/products/variants_table', 'vendor\Products::variants_table/$1', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->get('vendor/products/edit_product/(:any)', 'vendor\Products::edit_product/$1', ['filter' => 'checkpermissions:module=products,action=can_update']);
$routes->post('vendor/products/save_products', 'vendor\Products::save_products');  // => check in method

$routes->get('vendor/products/stock', 'vendor\Products::stock', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->get('vendor/products/manage_stock', 'vendor\Products::manage_stock', ['filter' => 'checkpermissions:module=manage_stock,action=can_read']);
$routes->get('vendor/products/fetch_stock', 'vendor\Products::fetch_stock', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->post('vendor/products/save_adjustment', 'vendor\Products::save_adjustment', ['filter' => 'checkpermissions:module=manage_stock,action=can_create']);
$routes->get('vendor/products/table', 'vendor\Products::table', ['filter' => 'checkpermissions:module=products,action=can_read']);

$routes->get('vendor/products/stock_alert', 'vendor\Products::stock_alert', ['filter' => 'checkpermissions:module=products,action=can_read']);

$routes->post('vendor/products/send_email_stock_alert', 'vendor\Products::send_email_stock_alert', ['filter' => 'checkpermissions:module=products,action=create']);

$routes->get('vendor/generate_barcode', 'vendor\Generate_barcode::index', ['filter' => 'checkpermissions:module=generate_barcode,action=can_create']);

$routes->get('vendor/services', 'vendor\Services::index', ['filter' => 'checkpermissions:module=services,action=can_read']);
$routes->get('vendor/services/add_service', 'vendor\Services::Add_service', ['filter' => 'checkpermissions:module=services,action=can_create']);
$routes->get('vendor/services/service_table', 'vendor\Services::service_table', ['filter' => 'checkpermissions:module=services,action=can_read']);
$routes->get('vendor/services/edit_service/(:any)', 'vendor\Services::edit_service/$1', ['filter' => 'checkpermissions:module=services,action=can_update']);
$routes->post('vendor/services/save_services', 'vendor\Services::save_services'); // => check method
$routes->get('vendor/services/json', 'vendor\Services::json', ['filter' => 'checkpermissions:module=services,action=can_read']);



$routes->get('vendor/units', 'vendor\Units::index', ['filter' => 'checkpermissions:module=units,action=can_read']);
$routes->post('vendor/units/save_unit', 'vendor\Units::save_unit'); // check in method
$routes->get('vendor/units/unit_table', 'vendor\Units::unit_table', ['filter' => 'checkpermissions:module=units,action=can_read']);
$routes->get('vendor/units/edit_unit/(:any)', 'vendor\Units::edit_unit/$1', ['filter' => 'checkpermissions:module=units,action=can_update']);

$routes->get('vendor/categories', 'vendor\Categories::index', ['filter' => 'checkpermissions:module=categories,action=can_read']);
$routes->post('vendor/categories/save_categories', 'vendor\Categories::save_categories'); // => check in method
$routes->get('vendor/categories/category_table', 'vendor\Categories::category_table', ['filter' => 'checkpermissions:module=categories,action=can_read']);
$routes->get('vendor/categories/edit_category/(:any)', 'vendor\Categories::edit_category/$1', ['filter' => 'checkpermissions:module=categories,action=can_update']);







$routes->get('vendor/payments', 'vendor\Payments::index', ['filter' => 'checkpermissions:module=payments,action=read']);
$routes->post('vendor/payments/pre_payment_setup', 'vendor\Payments::pre_payment_setup', ['filter' => 'checkpermissions:module=payments,action=read']);
$routes->post('vendor/payments/post_payment', 'vendor\Payments::post_payment', ['filter' => 'checkpermissions:module=payments,action=read']);
$routes->get('vendor/payments/payment_success', 'vendor\Payments::payment_success', ['filter' => 'checkpermissions:module=payments,action=read']);
$routes->get('vendor/payments/payment_failed', 'vendor\Payments::payment_failed', ['filter' => 'checkpermissions:module=payments,action=read']);

$routes->get('vendor/profile', 'vendor\Profile::index', ['filter' => 'checkpermissions:module=profile,action=read']);
$routes->post('vendor/profile/update', 'vendor\Profile::update', ['filter' => 'checkpermissions:module=profile,action=update']);

$routes->get('vendor/invoices', 'vendor\Invoices::index', ['filter' => 'checkpermissions:module=invoices,action=read']);
$routes->get('vendor/invoices/invoice/(:any)', 'vendor\Invoices::invoice/$1', ['filter' => 'checkpermissions:module=invoices,action=read']);
$routes->get('vendor/invoices/view_invoice/(:any)', 'vendor\Invoices::view_invoice/$1', ['filter' => 'checkpermissions:module=invoices,action=read']);
$routes->get('vendor/invoices/invoice_table/(:any)', 'vendor\Invoices::invoice_table/$1', ['filter' => 'checkpermissions:module=invoices,action=read']);
$routes->get('vendor/invoices/thermal_print/(:any)', 'vendor\Invoices::thermal_print/$1', ['filter' => 'checkpermissions:module=invoices,action=read']);
$routes->post('vendor/invoices/send', 'vendor\Invoices::send', ['filter' => 'checkpermissions:module=invoices,action=create']);

$routes->get('vendor/posprinter', 'vendor\Posprinter::index', ['filter' => 'checkpermissions:module=posprinter,action=read']);

$routes->get('vendor/purchases', 'vendor\Purchases::index', ['filter' => 'checkpermissions:module=purchases,action=can_read']);
$routes->get('vendor/purchases/get_suppliers', 'vendor\Purchases::get_suppliers', ['filter' => 'checkpermissions:module=purchases,action=can_read']);

$routes->get('vendor/purchases/purchase_table', 'vendor\Purchases::purchase_table', ['filter' => 'checkpermissions:module=purchases,action=can_read']);
$routes->get('vendor/purchases/view_purchase/(:any)', 'vendor\Purchases::view_purchase/$1', ['filter' => 'checkpermissions:module=purchases,action=can_read']);

$routes->get('vendor/purchases/purchase_orders/(:any)', 'vendor\Purchases::purchase_orders/$1'); // => check in method
$routes->post('vendor/purchases/save', 'vendor\Purchases::save'); // => check in method
$routes->post('vendor/purchases/update_status_bulk', 'vendor\Purchases::update_status_bulk', ['filter' => 'checkpermissions:module=purchases,action=can_update']);
$routes->get('vendor/purchases/invoice/(:any)', 'vendor\Purchases::invoice/$1', ['filter' => 'checkpermissions:module=purchases,action=can_read']);
$routes->get('vendor/purchases/invoice_table/(:any)', 'vendor\Purchases::invoice_table/$1', ['filter' => 'checkpermissions:module=purchases,action=can_read']);
$routes->get('vendor/purchases/update_order_status', 'vendor\Purchases::update_order_status', ['filter' => 'checkpermissions:module=purchases,action=can_read']);

$routes->get('vendor/purchases/purchase_return', 'vendor\Purchases::purchase_return', ['filter' => 'checkpermissions:module=purchase_return,action=can_read']);
$routes->get('vendor/purchases/purchase_return_table', 'vendor\Purchases::purchase_return_table', ['filter' => 'checkpermissions:module=purchase_return,action=can_read']);
$routes->post('vendor/bulk_uploads/import_products', 'vendor\Bulk_Uploads::import_products', ['filter' => 'checkpermissions:module=products,action=can_create']);
$routes->post('vendor/bulk_uploads/import_categories', 'vendor\Bulk_Uploads::import_categories', ['filter' => 'checkpermissions:module=categories,action=can_create']);
$routes->post('vendor/bulk_uploads/import_stock', 'vendor\Bulk_Uploads::import_stock', ['filter' => 'checkpermissions:module=products,action=can_create']);
$routes->post('vendor/bulk_uploads/import_orders', 'vendor\Bulk_Uploads::import_orders', ['filter' => 'checkpermissions:module=orders,action=can_create']);
$routes->post('vendor/bulk_uploads/import_customers', 'vendor\Bulk_Uploads::import_customers', ['filter' => 'checkpermissions:module=customers,action=can_create']);



$routes->post('vendor/transactions/save_purchase_payment', 'vendor\Transactions::save_purchase_payment', ['filter' => 'checkpermissions:module=purchases,action=can_update']);




$routes->get('vendor/suppliers', 'vendor\Suppliers::index', ['filter' => 'checkpermissions:module=suppliers,action=can_read']);
$routes->get('vendor/suppliers/create', 'vendor\Suppliers::create', ['filter' => 'checkpermissions:module=suppliers,action=can_create']);
$routes->post('vendor/suppliers/save', 'vendor\Suppliers::save'); // => check in method;
$routes->get('vendor/suppliers/suppliers_table', 'vendor\Suppliers::suppliers_table', ['filter' => 'checkpermissions:module=suppliers,action=can_read']);
$routes->get('vendor/suppliers/edit/(:any)', 'vendor\Suppliers::edit/$1', ['filter' => 'checkpermissions:module=suppliers,action=can_update']);


$routes->get('vendor/media', 'vendor\Media::index');
$routes->get('vendor/media/media_table', 'vendor\Media::media_table');
$routes->post('vendor/media/upload', 'vendor\Media::upload'); // => check in method;
$routes->get('vendor/media/delete/(:any)', 'vendor\Media::delete/$1', );





$routes->get('vendor/webhooks', 'vendor\Webhooks::index');
$routes->get('vendor/webhooks/stripe', 'vendor\Webhooks::stripe');
$routes->get("vendor/webhooks/(:any)", "vendor\Webhooks::$1");
$routes->post("vendor/webhooks/(:any)", "vendor\Webhooks::$1");

$routes->get('vendor/bulk_uploads', 'vendor\Bulk_Uploads::index');

$routes->get('vendor/expenses', 'vendor\Expenses::index', ['filter' => 'checkpermissions:module=expenses,action=can_read']);
$routes->add('vendor/expenses/add', 'vendor\Expenses::add', ['filter' => 'checkpermissions:module=expenses,action=can_create']);
$routes->add('vendor/expenses/expenses_table', 'vendor\Expenses::expenses_table', ['filter' => 'checkpermissions:module=expenses,action=can_read']);
$routes->add('vendor/expenses/edit_expenses/(:any)', 'vendor\Expenses::edit/$1', ['filter' => 'checkpermissions:module=expenses,action=can_update']);
$routes->add('vendor/expenses/save', 'vendor\Expenses::save'); // check in method 


$routes->add('vendor/expenses_type', 'vendor\Expenses_type::index', ['filter' => 'checkpermissions:module=expenses_type,action=can_read']);
$routes->add('vendor/expenses_type/save_expenses_type', 'vendor\Expenses_type::save_expenses_type'); // => check in method
$routes->add('vendor/expenses_type/expenses_type_table', 'vendor\Expenses_type::expenses_type_table', ['filter' => 'checkpermissions:module=expenses_type,action=can_read']);
$routes->add('vendor/expenses_type/edit_expenses_type/(:any)', 'vendor\Expenses_type::edit_expenses_type/$1', ['filter' => 'checkpermissions:module=expenses_type,action=can_update']);



$routes->get('vendor/payment_reports', 'vendor\Payment_reports::index', ['filter' => 'checkpermissions:module=payment_reports,action=read']);
$routes->get('vendor/payment_reports/payment_reports_table', 'vendor\Payment_reports::payment_reports_table', ['filter' => 'checkpermissions:module=payment_reports,action=read']);


$routes->get('vendor/sales_summary', 'vendor\Sales_summary::index', ['filter' => 'checkpermissions:module=sales_summary,action=read']);
$routes->get('vendor/sales_summary/sales_summary_table', 'vendor\Sales_summary::sales_summary_table', ['filter' => 'checkpermissions:module=sales_summary,action=read']);

$routes->get('vendor/purchases_report', 'vendor\Purchases_Report::index', ['filter' => 'checkpermissions:module=purchases_report,action=can_read']);
$routes->get('vendor/purchases_report/purchases_report_table', 'vendor\Purchases_Report::purchases_report_table', ['filter' => 'checkpermissions:module=purchases_report,action=can_read']);


$routes->get('vendor/profit_loss', 'vendor\Profit_loss::index', ['filter' => 'checkpermissions:module=profit_loss,action=read']);
$routes->get('vendor/profit_loss/profit_loss_table', 'vendor\Profit_loss::profit_loss_table', ['filter' => 'checkpermissions:module=profit_loss,action=read']);

$routes->get('vendor/best_customers', 'vendor\Best_Customers::index', ['filter' => 'checkpermissions:module=best_customers,action=read']);
$routes->get('vendor/best_customers/best_customers_table', 'vendor\Best_Customers::best_customers_table', ['filter' => 'checkpermissions:module=best_customers,action=read']);

$routes->get('vendor/top_selling_products', 'vendor\Top_Selling_Products::index');
$routes->get('vendor/top_selling_products/top_selling_products_table', 'vendor\Top_Selling_Products::top_selling_products_table');

$routes->get('vendor/get_todays_expense', 'vendor\Home::todays_total_expense');
$routes->get('vendor/todays_total_sales', 'vendor\Home::todays_total_sales');
$routes->get('vendor/todays_total_payment_resived', 'vendor\Home::todays_total_payment_resived_form_orders');
$routes->get('vendor/todays_total_payment_remaining', 'vendor\Home::todays_total_payment_remaining_form_orders');
$routes->get('vendor/todays_total_purchase', 'vendor\Home::todays_total_purchase');
$routes->get('vendor/todays_total_paids', 'vendor\Home::todays_total_paids_resived_form_purchase');
$routes->get('vendor/todays_total_remaining', 'vendor\Home::todays_total_remaining_form_purchase');
$routes->get('vendor/totdays_profit', 'vendor\Home::totdays_profit');

// brands routes here :
$routes->get('vendor/brands', 'vendor\Brand::index', ['filter' => 'checkpermissions:module=brand,action=can_read']);
$routes->post('vendor/brand/add', 'vendor\Brand::Add'); // checked permission inside controler function;
$routes->get('vendor/brand/brand-table', 'vendor\Brand::table', ['filter' => 'checkpermissions:module=brand,action=can_read']);
$routes->post('vendor/brand/get-brand', 'vendor\Brand::get_brand'); // checked permission inside controler function;
$routes->post('vendor/brand/update', 'vendor\Brand::update'); // checked permission inside controler function;
$routes->post('vendor/brand/delete-brand', 'vendor\Brand::delete'); // checked permission inside controler function;

// check Roles routes here - start
$routes->group('vendor/team_members', ['filter' => 'checkRoles'], function ($routes) {
    $routes->get('', 'vendor\Team_members::index');
    $routes->get('view_team_members', 'vendor\Team_members::view_team_members');
    $routes->get('create', 'vendor\Team_members::create');
    $routes->post('save', 'vendor\Team_members::save');
    $routes->get('edit_user/(:any)', 'vendor\Team_members::edit_user/$1');
    $routes->post('update_user', 'vendor\Team_members::update_user');
});

$routes->group('vendor/businesses', ['filter' => 'checkRoles'], function ($routes) {

    $routes->get('', 'vendor\Businesses::index');
    $routes->get('business_table', 'vendor\Businesses::business_table');
    $routes->get('edit_business/(:any)', 'vendor\Businesses::edit_business/$1');
    $routes->post('save_business', 'vendor\Businesses::save_business');
    // $routes->get('edit_business', 'vendor\Businesses::edit_business');

    $routes->get('update_default_business', 'vendor\Businesses::update_default_business');
});

$routes->get('vendor/view-tax', 'vendor\Tax::index', ['filter' => 'checkRoles']);
$routes->get('vendor/tax-tale', 'vendor\Tax::taxTable', ['filter' => 'checkRoles']);
$routes->get('vendor/subscription', 'vendor\Subscription::index', ['filter' => 'checkRoles']);
$routes->get('vendor/subscription/packages', 'vendor\Subscription::packages', ['filter' => 'checkRoles']);
$routes->get('vendor/subscription/package_table', 'vendor\Subscription::package_table', ['filter' => 'checkRoles']);
$routes->get('vendor/subscription/(:any)', 'vendor\Subscription::checkout/$1', ['filter' => 'checkRoles']);
$routes->get('vendor/subscription/package_table', 'vendor\Subscription::package_table', ['filter' => 'checkRoles']);
$routes->post('vendor/subscription/free_subscription', 'vendor\Subscription::free_subscription', ['filter' => 'checkRoles']);
$routes->get('vendor/subscription_transactions', 'vendor\Subscription_Transactions::index', ['filter' => 'checkRoles']);
$routes->get('vendor/subscription_transactions/transactions_table', 'vendor\Subscription_Transactions::transactions_table', ['filter' => 'checkRoles']);

// check Roles routes here - end

// bulk upload routes start 

$routes->post('vendor/bulk_uploads/import_suppliers', 'vendor\Bulk_Uploads::import_suppliers', ['filter' => 'checkpermissions:module=suppliers,action=can_create']);
$routes->post('vendor/bulk_uploads/import_service', 'vendor\Bulk_Uploads::import_service', ['filter' => 'checkpermissions:module=services,action=can_create']);
$routes->post('vendor/bulk_uploads/import_purchase', 'vendor\Bulk_Uploads::import_purchase', ['filter' => 'checkpermissions:module=purchases,action=can_create']);




$routes->post('vendor/bulk_uploads/import_delivery_boys', 'vendor\Bulk_Uploads::import_delivery_boys', ['filter' => 'checkRoles']); // applying checkRoles because team members are not allowed to  create or update delivery boy.

// bulk upload routes end


// $routes->post('vendor/system_users/delete_user', 'vendor\System_users::delete_user');

// });
// delivery boys routes
$routes->get('delivery_boy/home', 'delivery_boy\Home::index');
$routes->get('delivery_boy/home/switch_businesses/(:any)', 'delivery_boy\Home::switch_businesses/$1');
$routes->get('delivery_boy/home/login', 'delivery_boy\Home::login');
$routes->get('delivery_boy/home/fetch_sales', 'delivery_boy\Home::fetch_sales');

$routes->get('delivery_boy/customers', 'delivery_boy\Customers::index');
$routes->get('delivery_boy/customers/customers_table', 'delivery_boy\Customers::customers_table');

$routes->get('delivery_boy/orders', 'delivery_boy\Orders::index');
$routes->get('delivery_boy/orders/create', 'delivery_boy\Orders::create');
$routes->post('delivery_boy/orders/save_order', 'delivery_boy\Orders::save_order');
$routes->post('delivery_boy/orders/update_status_bulk', 'delivery_boy\Orders::update_status_bulk');
$routes->post('delivery_boy/bulk_uploads/import_orders', 'delivery_boy\Bulk_Uploads::import_orders', ['filter' => 'checkpermissions:module=bulk_uploads,action=create']);
$routes->get('delivery_boy/orders/orders_table', 'delivery_boy\Orders::orders_table');
$routes->add('delivery_boy/orders/update_order_status', 'delivery_boy\Orders::update_order_status');


$routes->post('delivery_boy/orders/register', 'delivery_boy\Orders::register');
$routes->get('delivery_boy/orders/details/(:any)', 'delivery_boy\Orders::details/$1');

$routes->get('delivery_boy/transactions', 'delivery_boy\Transactions::index');
$routes->get('delivery_boy/transactions/transactions_table', 'delivery_boy\Transactions::transactions_table');
$routes->get('delivery_boy/transactions/customer_transaction_table/(:any)/(:any)', 'delivery_boy\Transactions::customer_transaction_table/$1/$2');
$routes->post('delivery_boy/transactions/save_payment', 'delivery_boy\Transactions::save_payment');
$routes->get('delivery_boy/transactions/customers_table', 'delivery_boy\Transactions::customers_table');


$routes->get('delivery_boy/products/get_products', 'delivery_boy\Products::get_products');
$routes->get('delivery_boy/orders/get_users', 'delivery_boy\Orders::get_users');
$routes->get('delivery_boy/orders/customer_balance', 'delivery_boy\Orders::customer_balance');
$routes->get('delivery_boy/products/get_services', 'delivery_boy\Products::get_services');


$routes->get('system_pages', 'System_pages::index');
$routes->get('system_pages/about_us', 'System_pages::about_us');
$routes->get('system_pages/terms_and_conditions', 'System_pages::terms_and_conditions');
$routes->get('system_pages/refund_policy', 'System_pages::refund_policy');
$routes->get('system_pages/privacy_policy', 'System_pages::privacy_policy');




// $routes->get('admin/system_users/delete_user/(:segment)', 'Admin\System_users::delete_user/$1');

$routes->get('/auth/logout', 'Auth::logout');
