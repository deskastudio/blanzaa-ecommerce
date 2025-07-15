-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 14, 2025 at 07:19 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce_electronic`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_logs`
--

CREATE TABLE `admin_activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` bigint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `properties` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Smartphone', 'smartphone', 'Latest smartphones and mobile devices', NULL, 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(2, 'Laptop', 'laptop', 'Laptops and notebooks for work and gaming', NULL, 1, 2, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(3, 'Tablet', 'tablet', 'Tablets and iPad devices', NULL, 1, 3, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(4, 'Audio', 'audio', 'Headphones, speakers, and audio equipment', NULL, 1, 4, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(5, 'Gaming', 'gaming', 'Gaming consoles and accessories', NULL, 1, 5, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(6, 'Accessories', 'accessories', 'Phone cases, chargers, and other accessories', NULL, 1, 6, '2025-07-13 23:04:41', '2025-07-13 23:04:41');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_07_09_024348_create_categories_table', 1),
(5, '2025_07_09_024404_create_products_table', 1),
(6, '2025_07_09_024419_create_product_images_table', 1),
(7, '2025_07_09_024433_create_orders_table', 1),
(8, '2025_07_09_024449_create_order_items_table', 1),
(9, '2025_07_09_024503_create_shopping_carts_table', 1),
(10, '2025_07_09_024515_create_settings_table', 1),
(11, '2025_07_09_024529_create_admin_activity_logs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `order_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_amount` decimal(12,2) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_postal_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_province` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank_transfer',
  `payment_status` enum('pending','paid','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `status`, `total_amount`, `shipping_cost`, `tax_amount`, `discount_amount`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `shipping_city`, `shipping_postal_code`, `shipping_province`, `payment_method`, `payment_status`, `payment_proof`, `notes`, `shipped_at`, `delivered_at`, `cancelled_at`, `created_at`, `updated_at`) VALUES
(1, 7, 'ORD-20250714-0001', 'confirmed', '63010000.00', '15000.00', '0.00', '0.00', 'Mr. Mark Orn', 'gboehm@example.net', '1-530-701-4050', '560 Gerhold Forge Apt. 667\nLake Deron, TN 31360-3201', 'Hoseaberg', '28552-5442', 'Minnesota', 'bank_transfer', 'paid', 'payment-proofs/sample-1.jpg', NULL, NULL, NULL, NULL, '2025-06-26 23:04:41', '2025-07-13 23:04:41'),
(2, 6, 'ORD-20250714-0002', 'cancelled', '75808000.00', '15000.00', '0.00', '0.00', 'Dr. Okey Stehr Sr.', 'jordi.bogan@example.net', '+1-469-525-2409', '15876 Halle Key\nEast Elizabethbury, WY 38304', 'West Jaden', '03219', 'Oregon', 'bank_transfer', 'paid', NULL, 'Sample order notes for testing.', NULL, NULL, NULL, '2025-06-20 23:04:41', '2025-07-13 23:04:41'),
(3, 7, 'ORD-20250714-0003', 'pending', '94405000.00', '15000.00', '0.00', '0.00', 'Mr. Mark Orn', 'gboehm@example.net', '1-530-701-4050', '560 Gerhold Forge Apt. 667\nLake Deron, TN 31360-3201', 'Hoseaberg', '28552-5442', 'Minnesota', 'bank_transfer', 'paid', NULL, 'Sample order notes for testing.', NULL, NULL, NULL, '2025-06-16 23:04:41', '2025-07-13 23:04:41'),
(4, 3, 'ORD-20250714-0004', 'shipped', '81007000.00', '15000.00', '0.00', '0.00', 'John Customer', 'customer@electroshop.com', '081234567890', 'Jl. Test No. 123', 'Jakarta', '12345', 'DKI Jakarta', 'bank_transfer', 'pending', NULL, 'Sample order notes for testing.', NULL, NULL, NULL, '2025-07-12 23:04:41', '2025-07-13 23:04:41'),
(5, 8, 'ORD-20250714-0005', 'pending', '22512000.00', '15000.00', '0.00', '0.00', 'Nelle Blick', 'littel.donald@example.net', '954-598-9190', '54039 Bartoletti Terrace Apt. 790\nCasperville, DC 95791', 'Effieland', '77564-2929', 'Vermont', 'bank_transfer', 'paid', 'payment-proofs/sample-5.jpg', NULL, NULL, NULL, NULL, '2025-06-20 23:04:41', '2025-07-13 23:04:41'),
(6, 11, 'ORD-20250714-0006', 'processing', '217004000.00', '15000.00', '0.00', '0.00', 'Jules Oberbrunner', 'oreilly.daniella@example.com', '1-856-300-4967', '284 Kareem Lodge\nPort Anita, NV 71244', 'Boganmouth', '81633', 'Arkansas', 'bank_transfer', 'pending', NULL, 'Sample order notes for testing.', NULL, NULL, NULL, '2025-07-12 23:04:41', '2025-07-13 23:04:41'),
(7, 6, 'ORD-20250714-0007', 'delivered', '8013000.00', '15000.00', '0.00', '0.00', 'Dr. Okey Stehr Sr.', 'jordi.bogan@example.net', '+1-469-525-2409', '15876 Halle Key\nEast Elizabethbury, WY 38304', 'West Jaden', '03219', 'Oregon', 'bank_transfer', 'failed', NULL, NULL, NULL, NULL, NULL, '2025-06-19 23:04:41', '2025-07-13 23:04:41'),
(8, 4, 'ORD-20250714-0008', 'shipped', '23411000.00', '15000.00', '0.00', '0.00', 'Miss Eva Cole', 'dalton39@example.com', '480.589.3214', '2616 Jacobs Trail\nClaudineland, VT 69475-9134', 'Hesselmouth', '98866-0729', 'Iowa', 'bank_transfer', 'paid', 'payment-proofs/sample-8.jpg', NULL, NULL, NULL, NULL, '2025-07-05 23:04:41', '2025-07-13 23:04:41'),
(9, 9, 'ORD-20250714-0009', 'processing', '30508000.00', '15000.00', '0.00', '0.00', 'Reinhold Rosenbaum', 'dawson05@example.org', '(252) 301-2833', '133 Dakota Overpass Apt. 796\nPort Bria, WV 10846-6980', 'West Hanstown', '79234-7685', 'Wyoming', 'bank_transfer', 'paid', 'payment-proofs/sample-9.jpg', 'Sample order notes for testing.', NULL, NULL, NULL, '2025-07-01 23:04:41', '2025-07-13 23:04:41'),
(10, 11, 'ORD-20250714-0010', 'processing', '50206000.00', '15000.00', '0.00', '0.00', 'Jules Oberbrunner', 'oreilly.daniella@example.com', '1-856-300-4967', '284 Kareem Lodge\nPort Anita, NV 71244', 'Boganmouth', '81633', 'Arkansas', 'bank_transfer', 'failed', NULL, 'Sample order notes for testing.', NULL, NULL, NULL, '2025-07-12 23:04:41', '2025-07-13 23:04:41'),
(11, 12, 'ORD-20250714-0011', 'pending', '84010000.00', '15000.00', '0.00', '0.00', 'Mr. Dylan Shanahan', 'schowalter.robert@example.com', '+1-561-835-0162', '31838 Lois Square Suite 937\nLaurianeside, IL 40026-6459', 'Jacobsville', '78470', 'Minnesota', 'bank_transfer', 'paid', NULL, 'Sample order notes for testing.', NULL, NULL, NULL, '2025-06-18 23:04:41', '2025-07-13 23:04:41'),
(12, 6, 'ORD-20250714-0012', 'confirmed', '64509000.00', '15000.00', '0.00', '0.00', 'Dr. Okey Stehr Sr.', 'jordi.bogan@example.net', '+1-469-525-2409', '15876 Halle Key\nEast Elizabethbury, WY 38304', 'West Jaden', '03219', 'Oregon', 'bank_transfer', 'pending', 'payment-proofs/sample-12.jpg', NULL, NULL, NULL, NULL, '2025-07-06 23:04:41', '2025-07-13 23:04:42'),
(13, 12, 'ORD-20250714-0013', 'confirmed', '73007000.00', '15000.00', '0.00', '0.00', 'Mr. Dylan Shanahan', 'schowalter.robert@example.com', '+1-561-835-0162', '31838 Lois Square Suite 937\nLaurianeside, IL 40026-6459', 'Jacobsville', '78470', 'Minnesota', 'bank_transfer', 'paid', 'payment-proofs/sample-13.jpg', 'Sample order notes for testing.', NULL, NULL, NULL, '2025-07-06 23:04:42', '2025-07-13 23:04:42'),
(14, 7, 'ORD-20250714-0014', 'shipped', '88007000.00', '15000.00', '0.00', '0.00', 'Mr. Mark Orn', 'gboehm@example.net', '1-530-701-4050', '560 Gerhold Forge Apt. 667\nLake Deron, TN 31360-3201', 'Hoseaberg', '28552-5442', 'Minnesota', 'bank_transfer', 'paid', NULL, 'Sample order notes for testing.', NULL, NULL, NULL, '2025-06-15 23:04:42', '2025-07-13 23:04:42'),
(15, 8, 'ORD-20250714-0015', 'processing', '41010000.00', '15000.00', '0.00', '0.00', 'Nelle Blick', 'littel.donald@example.net', '954-598-9190', '54039 Bartoletti Terrace Apt. 790\nCasperville, DC 95791', 'Effieland', '77564-2929', 'Vermont', 'bank_transfer', 'paid', NULL, NULL, NULL, NULL, NULL, '2025-07-01 23:04:42', '2025-07-13 23:04:42'),
(16, 9, 'ORD-20250714-0016', 'processing', '14910000.00', '15000.00', '0.00', '0.00', 'Reinhold Rosenbaum', 'dawson05@example.org', '(252) 301-2833', '133 Dakota Overpass Apt. 796\nPort Bria, WV 10846-6980', 'West Hanstown', '79234-7685', 'Wyoming', 'bank_transfer', 'pending', 'payment-proofs/sample-16.jpg', NULL, NULL, NULL, NULL, '2025-07-02 23:04:42', '2025-07-13 23:04:42'),
(17, 8, 'ORD-20250714-0017', 'confirmed', '79507000.00', '15000.00', '0.00', '0.00', 'Nelle Blick', 'littel.donald@example.net', '954-598-9190', '54039 Bartoletti Terrace Apt. 790\nCasperville, DC 95791', 'Effieland', '77564-2929', 'Vermont', 'bank_transfer', 'failed', NULL, 'Sample order notes for testing.', NULL, NULL, NULL, '2025-07-02 23:04:42', '2025-07-13 23:04:42'),
(18, 8, 'ORD-20250714-0018', 'shipped', '39012000.00', '15000.00', '0.00', '0.00', 'Nelle Blick', 'littel.donald@example.net', '954-598-9190', '54039 Bartoletti Terrace Apt. 790\nCasperville, DC 95791', 'Effieland', '77564-2929', 'Vermont', 'bank_transfer', 'pending', 'payment-proofs/sample-18.jpg', 'Sample order notes for testing.', NULL, NULL, NULL, '2025-07-02 23:04:42', '2025-07-13 23:04:42'),
(19, 9, 'ORD-20250714-0019', 'confirmed', '122103000.00', '15000.00', '0.00', '0.00', 'Reinhold Rosenbaum', 'dawson05@example.org', '(252) 301-2833', '133 Dakota Overpass Apt. 796\nPort Bria, WV 10846-6980', 'West Hanstown', '79234-7685', 'Wyoming', 'bank_transfer', 'pending', NULL, NULL, NULL, NULL, NULL, '2025-06-30 23:04:42', '2025-07-13 23:04:42'),
(20, 3, 'ORD-20250714-0020', 'pending', '64011000.00', '15000.00', '0.00', '0.00', 'John Customer', 'customer@electroshop.com', '081234567890', 'Jl. Test No. 123', 'Jakarta', '12345', 'DKI Jakarta', 'bank_transfer', 'pending', 'payment-proofs/sample-20.jpg', 'Sample order notes for testing.', NULL, NULL, NULL, '2025-06-21 23:04:42', '2025-07-13 23:04:42'),
(21, 14, 'ORD-20250714-5BAD96', 'pending', '5548890.00', '0.00', '549890.00', '0.00', 'ewfweg regreg', 'deskamulyana47@gmail.com', '089767767456', 'svcsavsdgv', 'dbfdbnfd', '3523534', 'fndgn', 'bank_transfer', 'pending', 'payment-proofs/payment_proof_ORD-20250714-5BAD96_1752473477.png', '[14 Jul 2025 06:11] Payment proof uploaded by customer - awaiting admin verification', NULL, NULL, NULL, '2025-07-13 23:11:01', '2025-07-13 23:11:17');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_sku` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_sku`, `quantity`, `price`, `total`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'iPhone 15 Pro', 'IP15-PRO-128', 1, '15999000.00', '15999000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(2, 1, 20, 'Xbox Series X', 'XBOX-SERIES-X', 3, '7499000.00', '22497000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(3, 2, 1, 'iPhone 15 Pro', 'IP15-PRO-128', 1, '15999000.00', '15999000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(4, 2, 5, 'Xiaomi 14 Ultra', 'XIA-14-ULT', 3, '13999000.00', '41997000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(5, 2, 21, 'Anker PowerBank 20000mAh', 'ANKER-PB20K', 3, '899000.00', '2697000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(6, 3, 8, 'Google Pixel 8 Pro', 'GOO-PIX-8PRO', 1, '11999000.00', '11999000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(7, 3, 10, 'OnePlus 12', 'ONE-12-256', 2, '10999000.00', '21998000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(8, 3, 17, 'PlayStation 5', 'PS5-CONSOLE', 1, '7999000.00', '7999000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(9, 3, 24, 'Logitech MX Master 3S', 'LOGI-MX3S', 2, '1399000.00', '2798000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(10, 4, 8, 'Google Pixel 8 Pro', 'GOO-PIX-8PRO', 3, '11999000.00', '35997000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(11, 4, 9, 'Dell XPS 13', 'DELL-XPS-13', 1, '19999000.00', '19999000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(12, 4, 13, 'Realme GT 5 Pro', 'REA-GT5-PRO', 1, '8999000.00', '8999000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(13, 4, 22, 'Nintendo Switch OLED', 'NSW-OLED', 2, '4999000.00', '9998000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(14, 5, 20, 'Xbox Series X', 'XBOX-SERIES-X', 3, '7499000.00', '22497000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(15, 6, 1, 'iPhone 15 Pro', 'IP15-PRO-128', 2, '15999000.00', '31998000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(16, 6, 6, 'ASUS ROG Zephyrus G14', 'ASUS-ROG-G14', 1, '24999000.00', '24999000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(17, 6, 10, 'OnePlus 12', 'ONE-12-256', 2, '10999000.00', '21998000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(18, 6, 14, 'Lenovo ThinkPad X1 Carbon', 'LEN-X1-CARBON', 3, '23999000.00', '71997000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(19, 7, 16, 'AirPods Pro 2nd Gen', 'AIRPODS-PRO2', 2, '3999000.00', '7998000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(20, 8, 10, 'OnePlus 12', 'ONE-12-256', 3, '10999000.00', '32997000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(21, 8, 18, 'MagSafe Charger', 'MAGSAFE-CHAR', 2, '699000.00', '1398000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(22, 9, 11, 'iPad Air 5th Gen', 'IPAD-AIR-5', 3, '8999000.00', '26997000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(23, 9, 18, 'MagSafe Charger', 'MAGSAFE-CHAR', 3, '699000.00', '2097000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(24, 9, 19, 'Sony WH-1000XM5', 'SONY-WH1000XM5', 2, '4999000.00', '9998000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(25, 9, 21, 'Anker PowerBank 20000mAh', 'ANKER-PB20K', 2, '899000.00', '1798000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(26, 10, 11, 'iPad Air 5th Gen', 'IPAD-AIR-5', 1, '8999000.00', '8999000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(27, 10, 13, 'Realme GT 5 Pro', 'REA-GT5-PRO', 3, '8999000.00', '26997000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(28, 10, 22, 'Nintendo Switch OLED', 'NSW-OLED', 2, '4999000.00', '9998000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(29, 10, 24, 'Logitech MX Master 3S', 'LOGI-MX3S', 3, '1399000.00', '4197000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(30, 11, 12, 'HP Spectre x360', 'HP-SPEC-X360', 2, '21999000.00', '43998000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(31, 11, 13, 'Realme GT 5 Pro', 'REA-GT5-PRO', 3, '8999000.00', '26997000.00', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(32, 12, 2, 'Samsung Galaxy S24', 'SAM-S24-256', 2, '12999000.00', '25998000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(33, 12, 13, 'Realme GT 5 Pro', 'REA-GT5-PRO', 1, '8999000.00', '8999000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(34, 12, 20, 'Xbox Series X', 'XBOX-SERIES-X', 3, '7499000.00', '22497000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(35, 13, 8, 'Google Pixel 8 Pro', 'GOO-PIX-8PRO', 3, '11999000.00', '35997000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(36, 13, 11, 'iPad Air 5th Gen', 'IPAD-AIR-5', 2, '8999000.00', '17998000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(37, 13, 19, 'Sony WH-1000XM5', 'SONY-WH1000XM5', 1, '4999000.00', '4999000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(38, 14, 1, 'iPhone 15 Pro', 'IP15-PRO-128', 3, '15999000.00', '47997000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(39, 14, 7, 'Samsung Galaxy Tab S9', 'SAM-TAB-S9', 2, '9999000.00', '19998000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(40, 14, 15, 'Microsoft Surface Pro 9', 'MS-SURF-PRO9', 2, '17999000.00', '35998000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(41, 14, 17, 'PlayStation 5', 'PS5-CONSOLE', 2, '7999000.00', '15998000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(42, 15, 11, 'iPad Air 5th Gen', 'IPAD-AIR-5', 1, '8999000.00', '8999000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(43, 15, 13, 'Realme GT 5 Pro', 'REA-GT5-PRO', 1, '8999000.00', '8999000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(44, 15, 22, 'Nintendo Switch OLED', 'NSW-OLED', 1, '4999000.00', '4999000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(45, 16, 18, 'MagSafe Charger', 'MAGSAFE-CHAR', 2, '699000.00', '1398000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(46, 16, 23, 'Bose QuietComfort 45', 'BOSE-QC45', 1, '4499000.00', '4499000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(47, 17, 1, 'iPhone 15 Pro', 'IP15-PRO-128', 3, '15999000.00', '47997000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(48, 17, 14, 'Lenovo ThinkPad X1 Carbon', 'LEN-X1-CARBON', 3, '23999000.00', '71997000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(49, 17, 19, 'Sony WH-1000XM5', 'SONY-WH1000XM5', 1, '4999000.00', '4999000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(50, 17, 23, 'Bose QuietComfort 45', 'BOSE-QC45', 3, '4499000.00', '13497000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(51, 18, 2, 'Samsung Galaxy S24', 'SAM-S24-256', 2, '12999000.00', '25998000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(52, 19, 10, 'OnePlus 12', 'ONE-12-256', 2, '10999000.00', '21998000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(53, 19, 14, 'Lenovo ThinkPad X1 Carbon', 'LEN-X1-CARBON', 1, '23999000.00', '23999000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(54, 19, 18, 'MagSafe Charger', 'MAGSAFE-CHAR', 3, '699000.00', '2097000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(55, 19, 19, 'Sony WH-1000XM5', 'SONY-WH1000XM5', 3, '4999000.00', '14997000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(56, 20, 5, 'Xiaomi 14 Ultra', 'XIA-14-ULT', 3, '13999000.00', '41997000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(57, 20, 15, 'Microsoft Surface Pro 9', 'MS-SURF-PRO9', 2, '17999000.00', '35998000.00', '2025-07-13 23:04:42', '2025-07-13 23:04:42'),
(58, 21, 19, 'Sony WH-1000XM5', 'SONY-WH1000XM5', 1, '4999000.00', '4999000.00', '2025-07-13 23:11:01', '2025-07-13 23:11:01');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `compare_price` decimal(12,2) DEFAULT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `min_stock_level` int NOT NULL DEFAULT '10',
  `weight` decimal(8,2) DEFAULT NULL,
  `dimensions` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warranty` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `short_description`, `sku`, `price`, `compare_price`, `stock_quantity`, `min_stock_level`, `weight`, `dimensions`, `brand`, `warranty`, `is_active`, `is_featured`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(1, 1, 'iPhone 15 Pro', 'iphone-15-pro', 'Latest iPhone 15 Pro with A17 Pro chip', 'Premium smartphone with advanced camera system', 'IP15-PRO-128', '15999000.00', '17999000.00', 25, 10, NULL, NULL, 'Apple', '1 Year International Warranty', 1, 1, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(2, 1, 'Samsung Galaxy S24', 'samsung-galaxy-s24', 'Galaxy S24 with AI-powered photography', 'Flagship Android smartphone', 'SAM-S24-256', '12999000.00', '14999000.00', 30, 10, NULL, NULL, 'Samsung', '1 Year Local Warranty', 1, 1, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(3, 2, 'MacBook Air M3', 'macbook-air-m3', 'MacBook Air with M3 chip for ultimate performance', 'Ultra-thin laptop with all-day battery', 'MBA-M3-256', '18999000.00', '20999000.00', 15, 10, NULL, NULL, 'Apple', '1 Year International Warranty', 1, 1, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(4, 3, 'iPad Pro 12.9\"', 'ipad-pro-129', 'iPad Pro with M2 chip and Liquid Retina XDR display', 'Professional tablet for creative work', 'IPAD-PRO-129', '16999000.00', NULL, 20, 10, NULL, NULL, 'Apple', '1 Year International Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(5, 1, 'Xiaomi 14 Ultra', 'xiaomi-14-ultra', 'Xiaomi 14 Ultra with Leica cameras and Snapdragon 8 Gen 3', 'Photography-focused flagship smartphone', 'XIA-14-ULT', '13999000.00', '15999000.00', 18, 10, NULL, NULL, 'Xiaomi', '1 Year Local Warranty', 1, 1, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(6, 2, 'ASUS ROG Zephyrus G14', 'asus-rog-zephyrus-g14', 'Gaming laptop with AMD Ryzen 9 and RTX 4070', 'Compact gaming laptop with high performance', 'ASUS-ROG-G14', '24999000.00', '27999000.00', 12, 10, NULL, NULL, 'ASUS', '2 Years International Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(7, 3, 'Samsung Galaxy Tab S9', 'samsung-galaxy-tab-s9', 'Galaxy Tab S9 with S Pen and Dynamic AMOLED display', 'Premium Android tablet for productivity', 'SAM-TAB-S9', '9999000.00', '11999000.00', 22, 10, NULL, NULL, 'Samsung', '1 Year Local Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(8, 1, 'Google Pixel 8 Pro', 'google-pixel-8-pro', 'Google Pixel 8 Pro with Tensor G3 and AI features', 'Pure Android experience with advanced AI', 'GOO-PIX-8PRO', '11999000.00', '13999000.00', 16, 10, NULL, NULL, 'Google', '1 Year International Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(9, 2, 'Dell XPS 13', 'dell-xps-13', 'Dell XPS 13 with Intel Core i7 and InfinityEdge display', 'Premium ultrabook for professionals', 'DELL-XPS-13', '19999000.00', '22999000.00', 14, 10, NULL, NULL, 'Dell', '1 Year International Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(10, 1, 'OnePlus 12', 'oneplus-12', 'OnePlus 12 with Snapdragon 8 Gen 3 and 120Hz display', 'Flagship killer with premium features', 'ONE-12-256', '10999000.00', '12999000.00', 28, 10, NULL, NULL, 'OnePlus', '1 Year Local Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(11, 3, 'iPad Air 5th Gen', 'ipad-air-5th-gen', 'iPad Air with M1 chip and 10.9-inch Liquid Retina display', 'Versatile tablet for work and play', 'IPAD-AIR-5', '8999000.00', '10999000.00', 26, 10, NULL, NULL, 'Apple', '1 Year International Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(12, 2, 'HP Spectre x360', 'hp-spectre-x360', 'HP Spectre x360 2-in-1 with Intel Core i7 and OLED display', 'Convertible laptop with premium design', 'HP-SPEC-X360', '21999000.00', '24999000.00', 10, 10, NULL, NULL, 'HP', '1 Year International Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(13, 1, 'Realme GT 5 Pro', 'realme-gt-5-pro', 'Realme GT 5 Pro with Snapdragon 8 Gen 3 and 144Hz display', 'Gaming smartphone with flagship performance', 'REA-GT5-PRO', '8999000.00', '10999000.00', 35, 10, NULL, NULL, 'Realme', '1 Year Local Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(14, 2, 'Lenovo ThinkPad X1 Carbon', 'lenovo-thinkpad-x1-carbon', 'ThinkPad X1 Carbon with Intel Core i7 and military-grade durability', 'Business laptop with legendary reliability', 'LEN-X1-CARBON', '23999000.00', '26999000.00', 8, 10, NULL, NULL, 'Lenovo', '3 Years International Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(15, 3, 'Microsoft Surface Pro 9', 'microsoft-surface-pro-9', 'Surface Pro 9 with Intel Core i7 and detachable keyboard', '2-in-1 tablet that replaces your laptop', 'MS-SURF-PRO9', '17999000.00', '19999000.00', 13, 10, NULL, NULL, 'Microsoft', '1 Year International Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(16, 4, 'AirPods Pro 2nd Gen', 'airpods-pro-2nd-gen', 'AirPods Pro with Active Noise Cancellation and Spatial Audio', 'Premium wireless earbuds with ANC', 'AIRPODS-PRO2', '3999000.00', '4499000.00', 40, 10, NULL, NULL, 'Apple', '1 Year International Warranty', 1, 1, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(17, 5, 'PlayStation 5', 'playstation-5', 'PlayStation 5 console with DualSense controller', 'Next-gen gaming console', 'PS5-CONSOLE', '7999000.00', '8999000.00', 8, 10, NULL, NULL, 'Sony', '1 Year Local Warranty', 1, 1, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(18, 6, 'MagSafe Charger', 'magsafe-charger', 'Wireless MagSafe charger for iPhone 12 and later', 'Fast wireless charging for iPhone', 'MAGSAFE-CHAR', '699000.00', '899000.00', 60, 10, NULL, NULL, 'Apple', '1 Year International Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(19, 4, 'Sony WH-1000XM5', 'sony-wh-1000xm5', 'Premium noise-canceling headphones with 30-hour battery', 'Industry-leading noise cancellation', 'SONY-WH1000XM5', '4999000.00', '5999000.00', 24, 10, NULL, NULL, 'Sony', '1 Year International Warranty', 1, 1, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:11:01'),
(20, 5, 'Xbox Series X', 'xbox-series-x', 'Xbox Series X console with 1TB storage', 'Most powerful Xbox ever', 'XBOX-SERIES-X', '7499000.00', '8499000.00', 12, 10, NULL, NULL, 'Microsoft', '1 Year International Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(21, 6, 'Anker PowerBank 20000mAh', 'anker-powerbank-20000mah', 'High-capacity power bank with fast charging', 'Portable charger for all devices', 'ANKER-PB20K', '899000.00', '1199000.00', 45, 10, NULL, NULL, 'Anker', '18 Months International Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(22, 5, 'Nintendo Switch OLED', 'nintendo-switch-oled', 'Nintendo Switch with vibrant OLED screen', 'Portable gaming console with OLED', 'NSW-OLED', '4999000.00', '5499000.00', 18, 10, NULL, NULL, 'Nintendo', '1 Year International Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(23, 4, 'Bose QuietComfort 45', 'bose-quietcomfort-45', 'Comfortable noise-canceling headphones', 'All-day comfort with premium sound', 'BOSE-QC45', '4499000.00', '5199000.00', 20, 10, NULL, NULL, 'Bose', '1 Year International Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(24, 6, 'Logitech MX Master 3S', 'logitech-mx-master-3s', 'Advanced wireless mouse for productivity', 'Precision mouse for professionals', 'LOGI-MX3S', '1399000.00', '1599000.00', 30, 10, NULL, NULL, 'Logitech', '1 Year International Warranty', 1, 0, NULL, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `alt_text`, `is_primary`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'products/iphone-15-pro.jpg', 'iPhone 15 Pro', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(2, 2, 'products/samsung-galaxy-s24.jpg', 'Samsung Galaxy S24', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(3, 3, 'products/macbook-air-m3.jpg', 'MacBook Air M3', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(4, 4, 'products/ipad-pro-129.jpg', 'iPad Pro 12.9\"', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(5, 5, 'products/xiaomi-14-ultra.jpg', 'Xiaomi 14 Ultra', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(6, 6, 'products/asus-rog-g14.jpg', 'ASUS ROG Zephyrus G14', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(7, 7, 'products/samsung-tab-s9.jpg', 'Samsung Galaxy Tab S9', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(8, 8, 'products/google-pixel-8-pro.jpg', 'Google Pixel 8 Pro', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(9, 9, 'products/dell-xps-13.jpg', 'Dell XPS 13', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(10, 10, 'products/oneplus-12.jpg', 'OnePlus 12', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(11, 11, 'products/ipad-air-5.jpg', 'iPad Air 5th Gen', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(12, 12, 'products/hp-spectre-x360.jpg', 'HP Spectre x360', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(13, 13, 'products/realme-gt5-pro.jpg', 'Realme GT 5 Pro', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(14, 14, 'products/lenovo-x1-carbon.jpg', 'Lenovo ThinkPad X1 Carbon', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(15, 15, 'products/surface-pro-9.jpg', 'Microsoft Surface Pro 9', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(16, 16, 'products/airpods-pro-2.jpg', 'AirPods Pro 2nd Gen', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(17, 17, 'products/playstation-5.jpg', 'PlayStation 5', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(18, 18, 'products/magsafe-charger.jpg', 'MagSafe Charger', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(19, 19, 'products/sony-wh1000xm5.jpg', 'Sony WH-1000XM5', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(20, 20, 'products/xbox-series-x.jpg', 'Xbox Series X', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(21, 21, 'products/anker-powerbank.jpg', 'Anker PowerBank 20000mAh', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(22, 22, 'products/nintendo-switch-oled.jpg', 'Nintendo Switch OLED', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(23, 23, 'products/bose-qc45.jpg', 'Bose QuietComfort 45', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(24, 24, 'products/logitech-mx-master-3s.jpg', 'Logitech MX Master 3S', 1, 1, '2025-07-13 23:04:41', '2025-07-13 23:04:41');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'ElectroShop', 'string', 'Website name', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(2, 'site_description', 'Toko Elektronik Terpercaya', 'string', 'Website description', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(3, 'currency', 'IDR', 'string', 'Default currency', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(4, 'tax_rate', '10', 'number', 'Tax rate percentage', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(5, 'shipping_cost', '15000', 'number', 'Default shipping cost', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(6, 'contact_email', 'info@electroshop.com', 'string', 'Contact email', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(7, 'contact_phone', '+62-21-1234567', 'string', 'Contact phone', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(8, 'bank_account', 'BCA: 1234567890 a.n. ElectroShop', 'text', 'Bank account for payment', '2025-07-13 23:04:41', '2025-07-13 23:04:41');

-- --------------------------------------------------------

--
-- Table structure for table `shopping_carts`
--

CREATE TABLE `shopping_carts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('super_admin','admin','customer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `address`, `city`, `postal_code`, `province`, `avatar`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@electroshop.com', '2025-07-13 23:04:40', '$2y$12$CA89jJk0ezxU.qHOcviDj./WY4fFikVPA9xk.KZHDEIxFxynzALbO', 'super_admin', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2025-07-13 23:04:40', '2025-07-13 23:04:40'),
(2, 'Admin User', 'admin@electroshop.com', '2025-07-13 23:04:40', '$2y$12$zqiej3ehfGLn2s/i8I.ayOKJLSFG19oH8x2zTFCCMDdE7hpG1OslO', 'admin', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2025-07-13 23:04:40', '2025-07-13 23:04:40'),
(3, 'John Customer', 'customer@electroshop.com', '2025-07-13 23:04:41', '$2y$12$1L30JMaLigOrtJ9weYi9nOXIwsC8iDF4Rl3.2E4hhtB/6T8BhTWh6', 'customer', '081234567890', 'Jl. Test No. 123', 'Jakarta', '12345', 'DKI Jakarta', NULL, 1, NULL, '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(4, 'Miss Eva Cole', 'dalton39@example.com', '2025-07-13 23:04:41', '$2y$12$IMd1M7xo/u3ImYIGA8jqW.8xjw7zuCz15raj5V81YBpF979EZcpVm', 'customer', '480.589.3214', '2616 Jacobs Trail\nClaudineland, VT 69475-9134', 'Hesselmouth', '98866-0729', 'Iowa', NULL, 1, 'oQxuQZaJyj', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(5, 'Brain Predovic', 'braun.damion@example.org', '2025-07-13 23:04:41', '$2y$12$IMd1M7xo/u3ImYIGA8jqW.8xjw7zuCz15raj5V81YBpF979EZcpVm', 'customer', '(838) 210-8501', '61574 Maye Lights\nPort Darianbury, ME 69227', 'Hintzton', '57898-7202', 'Colorado', NULL, 1, 'L81avdPnze', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(6, 'Dr. Okey Stehr Sr.', 'jordi.bogan@example.net', '2025-07-13 23:04:41', '$2y$12$IMd1M7xo/u3ImYIGA8jqW.8xjw7zuCz15raj5V81YBpF979EZcpVm', 'customer', '+1-469-525-2409', '15876 Halle Key\nEast Elizabethbury, WY 38304', 'West Jaden', '03219', 'Oregon', NULL, 1, 'NlCPGNN72A', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(7, 'Mr. Mark Orn', 'gboehm@example.net', '2025-07-13 23:04:41', '$2y$12$IMd1M7xo/u3ImYIGA8jqW.8xjw7zuCz15raj5V81YBpF979EZcpVm', 'customer', '1-530-701-4050', '560 Gerhold Forge Apt. 667\nLake Deron, TN 31360-3201', 'Hoseaberg', '28552-5442', 'Minnesota', NULL, 1, 'FKsGMHYV88', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(8, 'Nelle Blick', 'littel.donald@example.net', '2025-07-13 23:04:41', '$2y$12$IMd1M7xo/u3ImYIGA8jqW.8xjw7zuCz15raj5V81YBpF979EZcpVm', 'customer', '954-598-9190', '54039 Bartoletti Terrace Apt. 790\nCasperville, DC 95791', 'Effieland', '77564-2929', 'Vermont', NULL, 1, 'q8kAdOKCgp', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(9, 'Reinhold Rosenbaum', 'dawson05@example.org', '2025-07-13 23:04:41', '$2y$12$IMd1M7xo/u3ImYIGA8jqW.8xjw7zuCz15raj5V81YBpF979EZcpVm', 'customer', '(252) 301-2833', '133 Dakota Overpass Apt. 796\nPort Bria, WV 10846-6980', 'West Hanstown', '79234-7685', 'Wyoming', NULL, 1, 'V9mwVOoDvS', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(10, 'Selina Maggio', 'wolff.braxton@example.org', '2025-07-13 23:04:41', '$2y$12$IMd1M7xo/u3ImYIGA8jqW.8xjw7zuCz15raj5V81YBpF979EZcpVm', 'customer', '+1-435-885-3337', '702 Itzel Ways Apt. 144\nNienowport, AZ 32491-3714', 'Lake Ociemouth', '75982-0476', 'Iowa', NULL, 1, '9aEypvzJXg', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(11, 'Jules Oberbrunner', 'oreilly.daniella@example.com', '2025-07-13 23:04:41', '$2y$12$IMd1M7xo/u3ImYIGA8jqW.8xjw7zuCz15raj5V81YBpF979EZcpVm', 'customer', '1-856-300-4967', '284 Kareem Lodge\nPort Anita, NV 71244', 'Boganmouth', '81633', 'Arkansas', NULL, 1, 'OHburC16Yi', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(12, 'Mr. Dylan Shanahan', 'schowalter.robert@example.com', '2025-07-13 23:04:41', '$2y$12$IMd1M7xo/u3ImYIGA8jqW.8xjw7zuCz15raj5V81YBpF979EZcpVm', 'customer', '+1-561-835-0162', '31838 Lois Square Suite 937\nLaurianeside, IL 40026-6459', 'Jacobsville', '78470', 'Minnesota', NULL, 1, '4mxk6mGf0O', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(13, 'Jerald Crona', 'leffler.jorge@example.org', '2025-07-13 23:04:41', '$2y$12$IMd1M7xo/u3ImYIGA8jqW.8xjw7zuCz15raj5V81YBpF979EZcpVm', 'customer', '+14639056603', '10095 Wisozk Creek\nVivianeton, SD 94984', 'South Jesse', '87577-2267', 'Oregon', NULL, 1, 'PyFDWlI9tw', '2025-07-13 23:04:41', '2025-07-13 23:04:41'),
(14, 'deska', 'deskamulyana47@gmail.com', NULL, '$2y$12$10BZzG4VTajp3kcT8e7rlO2Q3ueFa8lnR7lsoV.4S6EH9J1jBRaOC', 'customer', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2025-07-13 23:09:19', '2025-07-13 23:09:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
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
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_images_product_id_foreign` (`product_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `shopping_carts`
--
ALTER TABLE `shopping_carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shopping_carts_user_id_foreign` (`user_id`),
  ADD KEY `shopping_carts_product_id_foreign` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `shopping_carts`
--
ALTER TABLE `shopping_carts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  ADD CONSTRAINT `admin_activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shopping_carts`
--
ALTER TABLE `shopping_carts`
  ADD CONSTRAINT `shopping_carts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shopping_carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
