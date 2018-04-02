-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2024 at 01:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Maareeye_saas_mulit_tax`
--

-- --------------------------------------------------------

--
-- Table structure for table `businesses`
--

CREATE TABLE `businesses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `icon` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `address` varchar(2048) NOT NULL,
  `contact` varchar(64) NOT NULL,
  `tax_name` varchar(256) NOT NULL,
  `tax_value` varchar(256) NOT NULL,
  `bank_details` varchar(2048) NOT NULL,
  `default_business` tinyint(2) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `email` varchar(64) NOT NULL,
  `website` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `status` int(2) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `vendor_id`, `business_id`, `name`, `status`, `updated_at`, `created_at`) VALUES
(1, 0, 0, 0, 'general', 1, '2022-01-13 05:55:49', '2022-01-13 05:55:49');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `balance` double NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `status` tinyint(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers_transactions`
--

CREATE TABLE `customers_transactions` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `order_type` varchar(120) NOT NULL,
  `created_by` varchar(64) NOT NULL,
  `payment_type` varchar(64) NOT NULL,
  `transaction_type` varchar(64) DEFAULT NULL,
  `amount` double NOT NULL,
  `opening_balance` double NOT NULL,
  `closing_balance` double NOT NULL,
  `transaction_id` varchar(148) NOT NULL,
  `message` varchar(264) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_boys`
--

CREATE TABLE `delivery_boys` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `business_id` varchar(30) NOT NULL,
  `permissions` varchar(512) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `expenses_id` varchar(512) NOT NULL,
  `note` varchar(512) NOT NULL,
  `amount` varchar(512) NOT NULL,
  `expenses_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses_type`
--

CREATE TABLE `expenses_type` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `title` varchar(512) NOT NULL,
  `description` varchar(512) NOT NULL,
  `expenses_type_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrator'),
(2, 'vendors', 'Vendors'),
(3, 'delivery_boys', 'Delivery boys'),
(4, 'customers', 'Customers'),
(5, 'suppliers', 'Suppliers'),
(6, 'suppliers', 'Suppliers');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `is_rtl` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `language`, `code`, `is_rtl`, `created_at`) VALUES
(1, 'english', 'en', 0, '2022-04-26 11:23:14'),
(2, 'hindi', 'hi', 0, '2022-04-26 11:30:52');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) DEFAULT NULL,
  `time` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(2, '2022-06-16-053033', 'App\\Database\\Migrations\\QtyAlert', 'default', 'App', 1670417479, 1),
(3, '2022-06-16-053529', 'App\\Database\\Migrations\\FreePackage', 'default', 'App', 1670417479, 1),
(4, '2022-09-15-040429', 'App\\Database\\Migrations\\Supllier', 'default', 'App', 1670417479, 1),
(5, '2022-10-12-061735', 'App\\Database\\Migrations\\Expenses', 'default', 'App', 1670417479, 1),
(6, '2022-12-06-035654', 'App\\Database\\Migrations\\Categories_Table', 'default', 'App', 1670417479, 1),
(7, '2023_05_29_113905', 'App\\Database\\Migrations\\Address_Field', 'default', 'App', 1694691886, 2),
(8, '2023-09-13-030401', 'App\\Database\\Migrations\\User_permissions', 'default', 'App', 1694691886, 2),
(10, '2024-02-20-030402', 'App\\Database\\Migrations\\Customers_Transactions', 'default', 'App', 1708424221, 3),
(11, '2024-09-14-074730', 'App\\Database\\Migrations\\CreateTeamMembersTable', 'default', 'App', 1727150573, 4),
(12, '2024-09-19-072742', 'App\\Database\\Migrations\\AddOrderNoToOrdersTable', 'default', 'App', 1727150573, 4),
(13, '2024-10-08-060804', 'App\\Database\\Migrations\\ChangeTaxIdInProductTable', 'default', 'App', 1728559746, 5),
(14, '2024-10-08-064636', 'App\\Database\\Migrations\\AddTaxDetailsToOrderTable', 'default', 'App', 1728559746, 5),
(15, '2024-10-09-053001', 'App\\Database\\Migrations\\ModifyTaxIdInPurchaseTable', 'default', 'App', 1728559746, 5),
(16, '2024-10-09-075131', 'App\\Database\\Migrations\\ChangeTaxIdToTaxIdsToServiceTable', 'default', 'App', 1728559746, 5),
(17, '2024-10-09-075239', 'App\\Database\\Migrations\\AddTaxDetailsToOrderServiceTable', 'default', 'App', 1728559746, 5);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_no` varchar(255) DEFAULT NULL,
  `business_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `total` float NOT NULL,
  `delivery_charges` float NOT NULL,
  `discount` float NOT NULL,
  `final_total` float NOT NULL,
  `payment_status` varchar(128) NOT NULL,
  `amount_paid` double NOT NULL,
  `order_type` varchar(512) DEFAULT NULL,
  `message` varchar(64) NOT NULL,
  `payment_method` varchar(128) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders_items`
--

CREATE TABLE `orders_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` float NOT NULL,
  `price` float NOT NULL,
  `tax_name` varchar(128) NOT NULL,
  `tax_percentage` double NOT NULL,
  `is_tax_included` tinyint(2) NOT NULL,
  `tax_details` varchar(255) DEFAULT NULL,
  `sub_total` float NOT NULL,
  `status` varchar(512) NOT NULL,
  `delivery_boy` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders_services`
--

CREATE TABLE `orders_services` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_name` text NOT NULL,
  `price` double NOT NULL,
  `quantity` double NOT NULL,
  `unit_name` varchar(128) NOT NULL,
  `unit_id` tinyint(11) NOT NULL,
  `sub_total` double NOT NULL,
  `tax_name` varchar(128) NOT NULL,
  `tax_percentage` double NOT NULL,
  `is_tax_included` tinyint(2) NOT NULL,
  `tax_details` varchar(255) DEFAULT NULL,
  `is_recursive` tinyint(2) NOT NULL,
  `recurring_days` int(128) NOT NULL,
  `starts_on` datetime NOT NULL,
  `ends_on` datetime NOT NULL,
  `delivery_boy` int(11) DEFAULT NULL,
  `status` varchar(512) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `no_of_businesses` int(11) NOT NULL,
  `no_of_delivery_boys` int(11) NOT NULL,
  `no_of_products` int(11) NOT NULL,
  `no_of_customers` int(11) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(256) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages_tenures`
--

CREATE TABLE `packages_tenures` (
  `id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `tenure` varchar(64) NOT NULL,
  `months` varchar(32) NOT NULL,
  `price` double DEFAULT NULL,
  `discounted_price` double DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `tax_ids` varchar(255) DEFAULT NULL,
  `name` varchar(1024) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(128) DEFAULT NULL,
  `type` varchar(16) NOT NULL COMMENT 'simple | variable',
  `stock_management` tinyint(2) NOT NULL COMMENT '0 - disabled | 1 - product level | 2 - variable level',
  `stock` double NOT NULL,
  `qty_alert` varchar(16) DEFAULT NULL,
  `unit_id` int(11) NOT NULL,
  `is_tax_included` tinyint(2) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products_variants`
--

CREATE TABLE `products_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_name` varchar(512) DEFAULT NULL,
  `sale_price` double NOT NULL,
  `purchase_price` double NOT NULL,
  `stock` double DEFAULT NULL,
  `qty_alert` varchar(16) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `status` tinyint(2) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `order_no` varchar(64) NOT NULL,
  `purchase_date` date NOT NULL,
  `tax_ids` varchar(255) DEFAULT NULL,
  `status` int(12) NOT NULL,
  `delivery_charges` float NOT NULL,
  `order_type` varchar(256) NOT NULL,
  `total` float NOT NULL,
  `payment_method` varchar(128) NOT NULL,
  `payment_status` varchar(128) NOT NULL,
  `amount_paid` double NOT NULL,
  `message` varchar(1024) NOT NULL,
  `discount` float NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases_items`
--

CREATE TABLE `purchases_items` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `quantity` float NOT NULL,
  `price` float NOT NULL,
  `discount` float NOT NULL,
  `status` tinyint(2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `tax_ids` varchar(255) DEFAULT NULL,
  `unit_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `description` varchar(2048) NOT NULL,
  `image` varchar(512) NOT NULL,
  `price` double NOT NULL,
  `cost_price` double NOT NULL,
  `is_tax_included` tinyint(2) NOT NULL,
  `is_recursive` tinyint(2) NOT NULL,
  `recurring_days` int(128) NOT NULL,
  `recurring_price` double NOT NULL,
  `status` tinyint(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `variable` varchar(128) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `variable`, `value`) VALUES
(2, 'general', '{\"title\":\"Maareeye\",\"support_email\":\"admin@admin.com\",\"currency_symbol\":\"$\",\"select_time_zone\":\"-11:00\",\"phone\":\"0907366124\",\"primary_color\":\"#1c0252\",\"secondary_color\":\"#ff5f00\",\"primary_shadow\":\"#c5cae9\",\"logo\":\".\\/public\\/uploads\\/logo.png\",\"half_logo\":\".\\/public\\/uploads\\/favicon-128.png\",\"favicon\":\"\\/public\\/uploads\\/favicon-128_1.png\",\"copyright_details\":\"<span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span class=\\\"general-setting\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\">\\u00a9 Copyright 2022\\u00a0<\\/span><span style=\\\"font-weight: bolder; color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\">Maareeye.<\\/span><span style=\\\"color: rgb(0, 62, 100); font-family: Ubuntu, sans-serif; text-align: center;\\\"> All Rights Reserved<\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span><\\/span>\"}'),
(36, 'about_us', '{\"about_us\":\"<h4><span style=\\\"color: #000000;\\\">Maareeye - A platform for transforming your conventional business into digital.<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Inventory, Accounting, Invoicing Software. <\\/span><span style=\\\"color: #000000;\\\">Maareeye provides features for strong order management, it helps you manage everything from product management to order management to track every transaction. Maareeye offers a system of multiple roles for users. <\\/span><span style=\\\"color: #000000;\\\">With Maareeye businessmen can easily manage inventory and subscriptions with the help of its prominent features.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Grow your business rapidly as it will reduce your paperwork. <\\/span><span style=\\\"color: #000000;\\\">Straightforward solution for companies that include subscription services as well as products, and stock management, now easily managed at this platform.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Inventory :&nbsp;<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Track Complete inventory with Maareeye such as product type, stock, units, variants, and all other details by listing them. it makes sure that all the items being ordered in the store remain available when required.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Accounting:<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">With Maareeye record all the transactions of orders whether its fully paid or partially paid. Further maximizing sales its suitable for growing businesses that need to keep their accounting in check. By recording the expenses, Moreover, it will help save time creating business reports.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Invoicing Software:<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\"><strong>Maareeye<\\/strong> is an excellent addition to your business as it helps you automate your billing requirements, including GST return filing, inventory management, invoicing, and billing.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">We are talking about improving the life of a segment that is the largest in our nation, i.e &lsquo;Small Business Sector&rsquo; the heartbeat of our economy. One major aspect holding down the small and medium enterprise (SME) sector is that they hardly have any access to proper technology. Easing this situation will go a long way in nurturing and sustaining SMEs. To let India emerge as one of the brightest economic spots in the coming years, businesses should focus on ways to make cash rather than getting stuck up in counting cash.It basically helps them do business accounting easier with the modern digital way!<\\/span><\\/p>\\r\\n<p>&nbsp;<\\/p>\"}'),
(37, 'refund_policy', '{\"refund_policy\":\"<h4>The following terms are applicable for any products that You purchased with Us.<\\/h4>\\r\\n<p>First of all, we thank you and appreciate your service or product purchase with us on our Website Maareeye.taskhub.company. Please read this policy carefully as it will give you important information and guidelines about your rights and obligations as our customer, with respect to any purchase or service we provide to you.<\\/p>\\r\\n<p>At Maareeye, we take pride in the services delivered by us and guarantee your satisfaction with our services and support. We constantly improve and strive to deliver the best accounting, financial or secretarial services through the internet. We make every effort to provide the service to you as per the specifications and timelines mentioned against each service or product purchased by you from Maareeye, however, if, due for any reason, we are unable to provide to you the service or product you purchased from us, please contact us immediately and we will correct the situation, provide a refund or offer credit that can be used for future Maareeye orders.<\\/p>\\r\\n<h4>You shall be entitled to a refund which shall be subject to the following situations:<\\/h4>\\r\\n<p>The Refund shall be only considered in the event there is a clear, visible deficiency with the service or product purchased from Maareeye. No refund shall be issued if Maareeye processed the registration\\/application as per the government guidelines and registration is pending on part of a government department or officials. If any government fee, duty, challan, or any other sum is paid in the course of processing your registration application. We will refund the full payment less the government fee paid. (Don&rsquo;t worry no government fee shall be deducted until Government challan or any other payment proof is provided to you)<\\/p>\\r\\n<p>In the event a customer has paid for a service and then requests for a refund only because there was a change of mind, the refund shall not be considered as there is no fault, defect, or onus on Maareeye. Refund requests shall not be entertained after the work has been shared with you in the event of a change of mind. However, we shall give you the option of using the amount paid for by you, for an alternative service in Maareeye amounting to the same value and the said amount could be applied in part or whole towards the said new service; and If the request for a refund has been raised 30 (thirty) days after the purchase of a service or product has been completed and the same has been intimated and indicated via email or through any form of communication stating that the work has been completed, then, such refund request shall be deemed invalid and shall not be considered.<\\/p>\\r\\n<p>If the request for the refund has been approved by Maareeye, the same shall be processed and intimated to you via email. This refund process could take a minimum of 15 (fifteen) business days to process and shall be credited to your bank account accordingly. We shall handle the refund process with care and ensure that the money spent by you is returned to you at the earliest.<\\/p>\\r\\n<h4>Fees for Services<\\/h4>\\r\\n<p>When the payment of fee is made to Maareeye, the fees paid in advance is retained by Maareeye in a client account. Maareeye will earn the fees upon working on a client&rsquo;s matter. During an engagement, Maareeye earns fee at different rates and different times depending on the completion of various milestones (e.g. providing client portal access, assigning relationship manager, obtaining DIN, Filing of forms, etc.,). Refund cannot be provided for the earned fee because resources and man-hours spent on delivering the service are non-returnable in nature. Further, we can&rsquo;t refund or credit any money paid to government entities, such as filing fees or taxes, or to other third parties with a role in processing your order. Under any circumstance, Maareeye shall be liable to refund only up to the fee paid by the client.<\\/p>\\r\\n<h4>Change of Service<\\/h4>\\r\\n<p>If you want to change the service you ordered for a different one, you must request this change of service within 30 days of purchase. The purchase price of the original service, less any earned fee and money paid to government entities, such as filing fees or taxes, or to other third parties with a role in processing your order, will be credited to your Maareeye account. You can use the balance credit for any other Maareeye service.<\\/p>\\r\\n<h4>Standard Pricing<\\/h4>\\r\\n<p>Maareeye has a standard pricing policy wherein no additional service fee is requested under any circumstance. However, the standard pricing policy is not applicable for an increase in the total fee paid by the client to Maareeye due to an increase in the government fee or fee incurred by the client for completion of legal documentation or re-filing of forms with the government due to rejection or resubmission. Maareeye is not responsible or liable for any other cost incurred by the client related to the completion of the service.<\\/p>\\r\\n<h4>Factors outside our Control<\\/h4>\\r\\n<p>We cannot guarantee the results or outcome of your particular procedure. For instance, the government may reject a trademark application for legal reasons beyond the scope of Maareeye service. In some cases, a government backlog or problems with the government platforms (e.g. MCA website, Income Tax website, FSSAI website) can lead to long delays before your process is complete. Similarly, Maareeye does not guarantee the results or outcomes of the services rendered by our Associates on a Nearest Expert platform, who are not employed by Maareeye. Problems like these are beyond our control and are not covered by this guarantee or eligible for a refund. Hence, the delay in processing your file by the Government cannot be a reason for the refund.<\\/p>\\r\\n<h4>Force Majeure<\\/h4>\\r\\n<p>Maareeye shall not be considered in breach of its Satisfaction Guarantee policy or default under any terms of service, and shall not be liable to the Client for any cessation, interruption, or delay in the performance of its obligations by reason of earthquake, flood, fire, storm, lightning, drought, landslide, hurricane, cyclone, typhoon, tornado, natural disaster, act of God or the public enemy, epidemic, famine or plague, action of a court or public authority, change in law, explosion, war, terrorism, armed conflict, labor strike, lockout, boycott or similar event beyond our reasonable control, whether foreseen or unforeseen (each a &ldquo;Force Majeure Event&rdquo;).<\\/p>\\r\\n<h4>Cancellation Fee<\\/h4>\\r\\n<p>Since we&rsquo;re incurring costs and dedicating time, manpower, technology resources, and effort to your service or document preparation, our guarantee only covers satisfaction issues caused by Maareeye &ndash; not changes to your situation or your state of mind. In case you require us to hold the processing of service, we will hold the fee paid on your account until you are ready to commence the service.<\\/p>\\r\\n<p>Before processing any refund, we reserve the right to make the best effort to complete the service. In case, you are not satisfied with the service, a cancellation fee of 20% + earned fee + fee paid to the government would be applicable. In case of a change of service, the cancellation fee would not be applicable.<\\/p>\"}'),
(42, 'payment_gateway', '{\"razorpay_payment_mode\":\"Test\",\"razorpay_secret_key\":\"you secret key for razorpay\",\"razorpay_api_key\":\"you API key for razorpay\",\"razorpay_status\":\"1\",\"stripe_payment_mode\":\"Test\",\"stripe_currency_symbol\":\"INR\",\"stripe_publishable_key\":\"you Stripe Publishable key for razorpay\",\"stripe_secret_key\":\"you secret key for razorpay\",\"stripe_webhook_secret_key\":\"Stripe Webhook Secret Key\",\"stripe_status\":\"1\",\"flutterwave_payment_mode\":\"Test\",\"flutterwave_currency_symbol\":\"NGN\",\"flutterwave_public_key\":\"you  Public Key for Flutterwave \",\"flutterwave_secret_key\":\"you secret key for Flutterwave\",\"flutterwave_encryption_key\":\"you Flutterwave Encryption key key for Flutterwave \",\"flutterwave_status\":\"1\"}'),
(43, 'email', '{\"email\":\"your@mail.com\",\"password\":\"password\",\"smtp_host\":\"host\",\"smtp_port\":\"465\",\"mail_content_type\":\"html\",\"smtp_encryption\":\"ssl\"}'),
(44, 'terms_and_conditions', '{\"terms_and_conditions\":\"<p><span style=\\\"color: #000000;\\\">Welcome to <strong>Maareeye!<\\/strong><\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">These terms and conditions outline the rules and regulations for the use of Maareeye\'s Website, located at https:\\/\\/Maareeye.taskhub.company\\/.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">By accessing this website we assume you accept these terms and conditions. Do not continue to use Maareeye if you do not agree to take all of the terms and conditions stated on this page.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and all Agreements: \\\"Client\\\", \\\"You\\\" and \\\"Your\\\" refers to you, the person log on this website and compliant to the Company&rsquo;s terms and conditions. \\\"The Company\\\", \\\"Ourselves\\\", \\\"We\\\", \\\"Our\\\" and \\\"Us\\\", refers to our Company. \\\"Party\\\", \\\"Parties\\\", or \\\"Us\\\", refers to both the Client and ourselves. All terms refer to the offer, acceptance and consideration of payment necessary to undertake the process of our assistance to the Client in the most appropriate manner for the express purpose of meeting the Client&rsquo;s needs in respect of provision of the Company&rsquo;s stated services, in accordance with and subject to, prevailing law of India. Any use of the above terminology or other words in the singular, plural, capitalization and\\/or he\\/she or they, are taken as interchangeable and therefore as referring to same.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Cookies<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">We employ the use of cookies. By accessing Maareeye , you agreed to use cookies in agreement with the Maareeye\'s Privacy Policy.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Most interactive websites use cookies to let us retrieve the user&rsquo;s details for each visit. Cookies are used by our website to enable the functionality of certain areas to make it easier for people visiting our website. Some of our affiliate\\/advertising partners may also use cookies.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">License<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Unless otherwise stated, Maareeye and\\/or its licensors own the intellectual property rights for all material on Maareeye . All intellectual property rights are reserved. You may access this from Maareeye for your own personal use subjected to restrictions set in these terms and conditions.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">You must not:<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Republish material from Maareeye<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Sell, rent or sub-license material from Maareeye<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Reproduce, duplicate or copy material from Maareeye<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Redistribute content from Maareeye<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">This Agreement shall begin on the date hereof.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Parts of this website offer an opportunity for users to post and exchange opinions and information in certain areas of the website. Maareeye does not filter, edit, publish or review Comments prior to their presence on the website. Comments do not reflect the views and opinions of Maareeye,its agents and\\/or affiliates. Comments reflect the views and opinions of the person who post their views and opinions. To the extent permitted by applicable laws, Maareeye shall not be liable for the Comments or for any liability, damages or expenses caused and\\/or suffered as a result of any use of and\\/or posting of and\\/or appearance of the Comments on this website.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Maareeye reserves the right to monitor all Comments and to remove any Comments which can be considered inappropriate, offensive or causes breach of these Terms and Conditions.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">You warrant and represent that:<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">You are entitled to post the Comments on our website and have all necessary licenses and consents to do so;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">The Comments do not invade any intellectual property right, including without limitation copyright, patent or trademark of any third party;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">The Comments do not contain any defamatory, libelous, offensive, indecent or otherwise unlawful material which is an invasion of privacy<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">The Comments will not be used to solicit or promote business or custom or present commercial activities or unlawful activity.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">You hereby grant Maareeye a non-exclusive license to use, reproduce, edit and authorize others to use, reproduce and edit any of your Comments in any and all forms, formats or media.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Hyperlinking to our Content<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">The following organizations may link to our Website without prior written approval:<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Government agencies;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Search engines;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">News organizations;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Online directory distributors may link to our Website in the same manner as they hyperlink to the Websites of other listed businesses; and<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">System wide Accredited Businesses except soliciting non-profit organizations, charity shopping malls, and charity fundraising groups which may not hyperlink to our Web site.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">These organizations may link to our home page, to publications or to other Website information so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products and\\/or services; and (c) fits within the context of the linking party&rsquo;s site.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">We may consider and approve other link requests from the following types of organizations:<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">commonly-known consumer and\\/or business information sources;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">dot.com community sites;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">associations or other groups representing charities;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">online directory distributors;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">internet portals;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">accounting, law and consulting firms; and<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">educational institutions and trade associations.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">We will approve link requests from these organizations if we decide that: (a) the link would not make us look unfavorably to ourselves or to our accredited businesses; (b) the organization does not have any negative records with us; (c) the benefit to us from the visibility of the hyperlink compensates the absence of Maareeye; and (d) the link is in the context of general resource information.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">These organizations may link to our home page so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products or services; and (c) fits within the context of the linking party&rsquo;s site.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">If you are one of the organizations listed in paragraph 2 above and are interested in linking to our website, you must inform us by sending an e-mail to Maareeye.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Approved organizations may hyperlink to our Website as follows:<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">By use of our corporate name; or<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">By use of the uniform resource locator being linked to; or<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">By use of any other description of our Website being linked to that makes sense within the context and format of content on the linking party&rsquo;s site.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">No use of Maareeye\'s logo or other artwork will be allowed for linking absent a trademark license agreement.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">iFrames<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Without prior approval and written permission, you may not create frames around our Webpages that alter in any way the visual presentation or appearance of our Website.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Content Liability<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">We shall not be hold responsible for any content that appears on your Website. You agree to protect and defend us against all claims that is rising on your Website. No link(s) should appear on any Website that may be interpreted as libelous, obscene or criminal, or which infringes, otherwise violates, or advocates the infringement or other violation of, any third party rights.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Your Privacy<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Please read Privacy Policy<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Reservation of Rights<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">We reserve the right to request that you remove all links or any particular link to our Website. You approve to immediately remove all links to our Website upon request. We also reserve the right to amen these terms and conditions and it&rsquo;s linking policy at any time. By continuously linking to our Website, you agree to be bound to and follow these linking terms and conditions.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Removal of links from our website<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">If you find any link on our Website that is offensive for any reason, you are free to contact and inform us any moment. We will consider requests to remove links but we are not obligated to or so or to respond to you directly.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">We do not ensure that the information on this website is correct, we do not warrant its completeness or accuracy; nor do we promise to ensure that the website remains available or that the material on the website is kept up to date.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Disclaimer<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">To the maximum extent permitted by applicable law, we exclude all representations, warranties and conditions relating to our website and the use of this website. Nothing in this disclaimer will:<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">limit or exclude our or your liability for death or personal injury;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">limit or exclude our or your liability for fraud or fraudulent misrepresentation;<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">limit any of our or your liabilities in any way that is not permitted under applicable law; or<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">exclude any of our or your liabilities that may not be excluded under applicable law.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">The limitations and prohibitions of liability set in this Section and elsewhere in this disclaimer: (a) are subject to the preceding paragraph; and (b) govern all liabilities arising under the disclaimer, including liabilities arising in contract, in tort and for breach of statutory duty.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">As long as the website and the information and services on the website are provided free of charge, we will not be liable for any loss or damage of any nature.<\\/span><\\/p>\"}'),
(45, 'privacy_policy', '{\"privacy_policy\":\"<p><span style=\\\"color: #000000;\\\">At Maareeye , accessible from http:\\/\\/Maareeye.taskhub.company\\/, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by Maareeye and how we use it. <\\/span><span style=\\\"color: #000000;\\\">If you have additional questions or require more information about our Privacy Policy, do not hesitate to contact us.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">This Privacy Policy applies only to our online activities and is valid for visitors to our website with regards to the information that they shared and\\/or collect in Maareeye . This policy is not applicable to any information collected offline or via channels other than this website.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Consent<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">By using our website, you hereby consent to our Privacy Policy and agree to its terms.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Information we collect<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">The personal information that you are asked to provide, and the reasons why you are asked to provide it, will be made clear to you at the point we ask you to provide your personal information. <\\/span><span style=\\\"color: #000000;\\\">If you contact us directly, we may receive additional information about you such as your name, email address, phone number, the contents of the message and\\/or attachments you may send us, and any other information you may choose to provide. <\\/span><span style=\\\"color: #000000;\\\">When you register for an Account, we may ask for your contact information, including items such as name, company name, address, email address, and telephone number.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">How we use your information<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">We use the information we collect in various ways, including to:<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">1. Provide, operate, and maintain our website<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">2. Improve, personalize, and expand our website<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">3. Understand and analyze how you use our website<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">4. Develop new products, services, features, and functionality<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">5. Communicate with you, either directly or through one of our partners, including for customer service, to provide you with updates and other information relating to the website, and for marketing and promotional purposes<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">6. Send you emails<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">7. Find and prevent fraud<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Log Files<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Maareeye follows a standard procedure of using log files. These files log visitors when they visit websites. All hosting companies do this and a part of hosting services\' analytics. The information collected by log files include internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring\\/exit pages, and possibly the number of clicks. These are not linked to any information that is personally identifiable. The purpose of the information is for analyzing trends, administering the site, tracking users\' movement on the website, and gathering demographic information.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Advertising Partners Privacy Policies<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">You may consult this list to find the Privacy Policy for each of the advertising partners of Maareeye .<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Third-party ad servers or ad networks uses technologies like cookies, JavaScript, or Web Beacons that are used in their respective advertisements and links that appear on Maareeye , which are sent directly to users\' browser. They automatically receive your IP address when this occurs. These technologies are used to measure the effectiveness of their advertising campaigns and\\/or to personalize the advertising content that you see on websites that you visit.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Note that Maareeye has no access to or control over these cookies that are used by third-party advertisers.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">Third Party Privacy Policies<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">Maareeye \'s Privacy Policy does not apply to other advertisers or websites. Thus, we are advising you to consult the respective Privacy Policies of these third-party ad servers for more detailed information. It may include their practices and instructions about how to opt-out of certain options. <\\/span><span style=\\\"color: #000000;\\\">You can choose to disable cookies through your individual browser options. To know more detailed information about cookie management with specific web browsers, it can be found at the browsers\' respective websites.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">Request that a business that collects a consumer\'s personal data disclose the categories and specific pieces of personal data that a business has collected about consumers. <\\/span><span style=\\\"color: #000000;\\\">Request that a business delete any personal data about the consumer that a business has collected. <\\/span><span style=\\\"color: #000000;\\\">Request that a business that sells a consumer\'s personal data, not sell the consumer\'s personal data.<\\/span><\\/p>\\r\\n<h4><span style=\\\"color: #000000;\\\">GDPR Data Protection Rights -&nbsp;<\\/span><\\/h4>\\r\\n<p><span style=\\\"color: #000000;\\\">We would like to make sure you are fully aware of all of your data protection rights. Every user is entitled to the following:<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">1. The right to access &ndash; You have the right to request copies of your personal data. We may charge you a small fee for this service.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">2. The right to rectification &ndash; You have the right to request that we correct any information you believe is inaccurate. You also have the right to request that we complete the information you believe is incomplete.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">3. The right to erasure &ndash; You have the right to request that we erase your personal data, under certain conditions.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">4. The right to restrict processing &ndash; You have the right to request that we restrict the processing of your personal data, under certain conditions.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">5. The right to object to processing &ndash; You have the right to object to our processing of your personal data, under certain conditions.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">6. The right to data portability &ndash; You have the right to request that we transfer the data that we have collected to another organization, or directly to you, under certain conditions.<\\/span><\\/p>\\r\\n<p><span style=\\\"color: #000000;\\\">7. If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us.<\\/span><\\/p>\"}');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `status` varchar(256) NOT NULL,
  `operation` tinyint(4) NOT NULL COMMENT '0-do nothing | 1-credit | 2-debit\r\n',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `vendor_id`, `business_id`, `status`, `operation`, `created_at`, `updated_at`) VALUES
(1, 96, 2, 'ordered', 0, '2022-11-24 05:05:28', '2022-11-24 05:05:28');

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

CREATE TABLE `subscription` (
  `id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL COMMENT 'RENEWABLE SERVICES',
  `customer_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `business_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `balance` float NOT NULL,
  `billing_address` varchar(264) NOT NULL,
  `shipping_address` varchar(264) NOT NULL,
  `credit_period` int(11) NOT NULL,
  `credit_limit` float NOT NULL,
  `tax_name` varchar(64) NOT NULL,
  `tax_num` varchar(64) NOT NULL,
  `status` tinyint(2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tax`
--

CREATE TABLE `tax` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `percentage` float NOT NULL,
  `status` tinyint(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--

CREATE TABLE `team_members` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `vendor_id` int(11) UNSIGNED NOT NULL,
  `business_ids` text DEFAULT NULL,
  `permissions` text DEFAULT NULL,
  `status` int(100) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(12) NOT NULL,
  `user_id` int(12) NOT NULL,
  `amount` double NOT NULL,
  `txn_id` varchar(128) NOT NULL,
  `payment_method` varchar(64) NOT NULL,
  `status` varchar(256) NOT NULL,
  `message` varchar(264) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `symbol` varchar(64) NOT NULL,
  `conversion` double NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `vendor_id`, `parent_id`, `name`, `symbol`, `conversion`, `created_at`, `updated_at`) VALUES
(1, 0, 0, 'kilogram', 'kg', 0, '2022-01-03 07:43:05', '2022-01-12 07:38:11'),
(2, 0, 1, 'gram', 'g', 1000, '2022-01-03 07:44:03', '2022-01-12 05:14:13'),
(3, 0, 0, 'liter', 'l', 0, '2022-01-03 08:05:44', '2022-01-03 08:09:05'),
(4, 0, 3, 'milliliter', 'ml', 1000, '2022-01-03 08:09:44', '2022-01-03 08:09:44'),
(5, 0, 0, 'pack', 'pk', 0, '2022-01-03 08:20:47', '2022-01-03 08:28:03'),
(6, 0, 0, 'piece', 'pc', 0, '2022-01-03 08:28:26', '2022-01-03 08:28:26'),
(7, 15, 0, 'meter', 'mm', 0, '2022-01-03 08:29:35', '2022-01-13 04:57:47'),
(8, 15, 7, 'millimeter', 'mm', 0, '2022-01-03 08:31:01', '2022-01-13 09:51:32'),
(9, 14, 0, 'foot', 'ft', 0, '2022-01-03 11:35:49', '2022-01-12 07:48:38'),
(10, 0, 9, 'inch', 'in', 12, '2022-01-03 11:36:19', '2022-01-03 11:37:06'),
(11, 0, 0, 'square foot', 'sqft', 0, '2022-01-05 10:55:15', '2022-01-05 10:55:15'),
(12, 0, 11, 'square meter', 'sqm', 0, '2022-01-05 10:58:11', '2022-01-05 10:58:11'),
(13, 0, 0, 'bundles', 'bdl', 0, '2022-01-06 09:45:48', '2022-01-06 09:45:48'),
(32, 0, 0, 'months', 'm', 0, '2022-01-13 10:16:35', '2022-03-28 11:39:15'),
(33, 15, 0, 'hours', 'h', 3600, '2022-01-28 06:36:17', '2022-01-28 06:36:17'),
(34, 15, 0, 'general', 'g', 0, '2022-03-30 05:58:26', '2022-03-30 05:58:26');

-- --------------------------------------------------------

--
-- Table structure for table `updates`
--

CREATE TABLE `updates` (
  `id` int(11) NOT NULL,
  `version` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `updates`
--

INSERT INTO `updates` (`id`, `version`) VALUES
(1, '1.0.3'),
(3, '1.0.4'),
(4, '1.0.5'),
(5, '1.0.6'),
(6, '1.0.7'),
(7, '1.0.8'),
(8, '1.0.9'),
(9, '1.1.0'),
(11, '1.1.1'),
(21, '1.1.2');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) NOT NULL,
  `mobile` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(254) NOT NULL,
  `activation_selector` varchar(255) DEFAULT NULL,
  `activation_code` varchar(255) DEFAULT NULL,
  `forgotten_password_selector` varchar(255) DEFAULT NULL,
  `forgotten_password_code` varchar(255) DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_selector` varchar(255) DEFAULT NULL,
  `remember_code` varchar(255) DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `address` varchar(254) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `mobile`, `password`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `address`, `phone`) VALUES
(1, '127.0.0.1', 'admin@gmail.com', '9876543210', '$2y$12$CJ7.LMieiHwDDp/aDghFge5.FrNWK8r5dAYDh4GF9GHLOty55NULS', 'admin@gmail.com', NULL, '', NULL, NULL, NULL, NULL, NULL, 1268889823, 1728559877, 1, 'admin', 'admin', 'ADMIN', NULL, ''),
(96, '::1', '1234567890', '1234567890', '$2y$10$mxx14stxpiWTHyfpQ/yXFePGg/Wn6RRXGvL.1sb7Gkg4XPE9TtHE2', '123@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1669265902, 1728559906, 1, 'sid', 'bhai', NULL, NULL, NULL),
(97, '::1', '9999999999', '9999999999', '$2y$10$GyJgiTT3b59HesUbdH1NM.1i.5ovp0f0mZkzw4AZr1XzC5H6U0jvO', 'admin@example.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1669266107, NULL, 1, 'admin1', NULL, NULL, NULL, NULL),
(98, '::1', '1234569875', '1234569875', '$2y$10$FkvUgHJnxxcvhK49yuWkwOOBdVSOTi0NsQr/0FAmi9K/eRq4YZbBy', 'supplier1@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1728559612, NULL, 1, 'Supplier 1', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

CREATE TABLE `users_groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `user_id` mediumint(8) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users_groups`
--

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
(1, 1, 1),
(73, 96, 2),
(74, 97, 4),
(75, 98, 5);

-- --------------------------------------------------------

--
-- Table structure for table `users_packages`
--

CREATE TABLE `users_packages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `transaction_id` int(12) DEFAULT NULL,
  `package_name` varchar(64) NOT NULL,
  `no_of_businesses` int(11) NOT NULL,
  `no_of_delivery_boys` int(11) NOT NULL,
  `no_of_products` int(11) NOT NULL,
  `no_of_customers` int(11) NOT NULL,
  `tenure` varchar(64) NOT NULL,
  `price` double NOT NULL,
  `months` varchar(64) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_packages`
--

INSERT INTO `users_packages` (`id`, `user_id`, `package_id`, `transaction_id`, `package_name`, `no_of_businesses`, `no_of_delivery_boys`, `no_of_products`, `no_of_customers`, `tenure`, `price`, `months`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES
(2, 96, 2, NULL, 'Gold Package', 5, 10, 100, 1000, 'yearly', 0, '12', '2022-11-23 00:00:00', '2023-11-23 00:00:00', 0, '2022-11-24 04:59:01', '2024-09-24 10:21:53'),
(3, 96, 2, NULL, 'Gold Package', 5, 10, 100, 1000, 'yearly', 0, '12', '2024-10-10 00:00:00', '2025-10-10 00:00:00', 1, '2024-10-10 11:28:20', '2024-10-10 11:28:20');

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` int(11) NOT NULL,
  `permissions` text NOT NULL,
  `created_by` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `businesses`
--
ALTER TABLE `businesses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers_transactions`
--
ALTER TABLE `customers_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `vendor_id_2` (`vendor_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `delivery_boys`
--
ALTER TABLE `delivery_boys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `business_id` (`business_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses_type`
--
ALTER TABLE `expenses_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `business_id` (`business_id`);

--
-- Indexes for table `orders_items`
--
ALTER TABLE `orders_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `product_variant_id` (`product_variant_id`);

--
-- Indexes for table `orders_services`
--
ALTER TABLE `orders_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages_tenures`
--
ALTER TABLE `packages_tenures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `business_id` (`business_id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `tax_id` (`tax_ids`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `products_variants`
--
ALTER TABLE `products_variants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchases_items`
--
ALTER TABLE `purchases_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `business_id` (`business_id`);

--
-- Indexes for table `subscription`
--
ALTER TABLE `subscription`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `business_id` (`business_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tax`
--
ALTER TABLE `tax`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `updates`
--
ALTER TABLE `updates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `activation_selector` (`activation_selector`),
  ADD UNIQUE KEY `forgotten_password_selector` (`forgotten_password_selector`),
  ADD UNIQUE KEY `remember_selector` (`remember_selector`);

--
-- Indexes for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_groups_user_id_foreign` (`user_id`),
  ADD KEY `users_groups_group_id_foreign` (`group_id`);

--
-- Indexes for table `users_packages`
--
ALTER TABLE `users_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `businesses`
--
ALTER TABLE `businesses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers_transactions`
--
ALTER TABLE `customers_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_boys`
--
ALTER TABLE `delivery_boys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expenses_type`
--
ALTER TABLE `expenses_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders_items`
--
ALTER TABLE `orders_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders_services`
--
ALTER TABLE `orders_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `packages_tenures`
--
ALTER TABLE `packages_tenures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products_variants`
--
ALTER TABLE `products_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases_items`
--
ALTER TABLE `purchases_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subscription`
--
ALTER TABLE `subscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tax`
--
ALTER TABLE `tax`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `updates`
--
ALTER TABLE `updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `users_groups`
--
ALTER TABLE `users_groups`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `users_packages`
--
ALTER TABLE `users_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD CONSTRAINT `users_groups_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `users_groups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
