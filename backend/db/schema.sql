-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2026 at 06:13 PM
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
-- Database: `ultratech_cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_content`
--

CREATE TABLE `about_content` (
  `id` int(11) NOT NULL,
  `eyebrow` varchar(200) DEFAULT 'About Company',
  `title` varchar(500) DEFAULT 'Manufacturing strength with a future-focused mindset',
  `description` text DEFAULT NULL,
  `years_badge` varchar(100) DEFAULT '30+',
  `years_label` varchar(200) DEFAULT 'Years building production intelligence',
  `image` varchar(255) DEFAULT '/assets/15.jpeg',
  `bullet_points` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`bullet_points`)),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `about_content`
--

INSERT INTO `about_content` (`id`, `eyebrow`, `title`, `description`, `years_badge`, `years_label`, `image`, `bullet_points`, `updated_at`) VALUES
(1, 'MANUFACTURING STRENGTH', 'Manufacturing Strength with a Future-Focused Mindset', 'We combine advanced engineering, automation and global expertise to build machines that power the infrastructure of tomorrow.', '30+', 'Years of Engineering Excellence', '/assets/15.jpeg', '[\"Advanced Technology\",\"Global Support\",\"Sustainable Solutions\",\"Reliable Performance\"]', '2026-06-20 06:45:55');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$UXSROmDPFfvuilZoFcTcVOnJBq4Xg1Tn1YqMuonoOAgHzMBMC4pAe', '2026-06-20 06:45:55');

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

CREATE TABLE `contact_info` (
  `id` int(11) NOT NULL,
  `phone` varchar(100) DEFAULT '+91 98765 43210',
  `phone_href` varchar(200) DEFAULT 'tel:+919876543210',
  `email` varchar(200) DEFAULT 'hello@ultra-tiles.com',
  `whatsapp` varchar(200) DEFAULT 'https://wa.me/919876543210',
  `address` text DEFAULT 'Industrial Growth Park, Pune, Maharashtra, India',
  `facebook` varchar(300) DEFAULT '#',
  `twitter` varchar(300) DEFAULT '#',
  `instagram` varchar(300) DEFAULT '#',
  `linkedin` varchar(300) DEFAULT '#',
  `youtube` varchar(300) DEFAULT '#',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_info`
--

INSERT INTO `contact_info` (`id`, `phone`, `phone_href`, `email`, `whatsapp`, `address`, `facebook`, `twitter`, `instagram`, `linkedin`, `youtube`, `updated_at`) VALUES
(1, '+91 98765 43210', 'tel:+919876543210', 'hello@ultra-tiles.com', 'https://wa.me/919876543210', 'Industrial Growth Park, Pune, Maharashtra, India', '#', '#', '#', '#', '#', '2026-06-20 06:45:55');

-- --------------------------------------------------------

--
-- Table structure for table `hero_content`
--

CREATE TABLE `hero_content` (
  `id` int(11) NOT NULL,
  `eyebrow` varchar(200) DEFAULT 'Intelligent Production Systems',
  `headline` varchar(500) DEFAULT 'Smart Machinery For Modern Construction',
  `headline_highlight` varchar(200) DEFAULT 'Modern Construction',
  `subtext` text DEFAULT NULL,
  `btn_primary_text` varchar(100) DEFAULT 'Explore Products',
  `btn_primary_href` varchar(200) DEFAULT '#products',
  `btn_secondary_text` varchar(100) DEFAULT 'Request Quote',
  `btn_secondary_href` varchar(200) DEFAULT '#contact',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hero_content`
--

INSERT INTO `hero_content` (`id`, `eyebrow`, `headline`, `headline_highlight`, `subtext`, `btn_primary_text`, `btn_primary_href`, `btn_secondary_text`, `btn_secondary_href`, `updated_at`) VALUES
(1, 'BUILT FOR PERFORMANCE. ENGINEERED FOR EXCELLENCE.', 'Smart Machinery For Modern Construction', 'Modern Construction', 'Advanced block making & material processing solutions built for performance, precision & productivity.', 'Explore Machines', '#products', 'Our Solutions', '#solutions', '2026-06-20 06:45:55');

-- --------------------------------------------------------

--
-- Table structure for table `hero_slides`
--

CREATE TABLE `hero_slides` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hero_slides`
--

INSERT INTO `hero_slides` (`id`, `image`, `sort_order`, `is_active`, `created_at`) VALUES
(8, '/assets/hero_6a37fbc514faa5.03974986.png', 7, 1, '2026-06-21 14:57:09'),
(9, '/assets/hero_6a37fbd42f3ad6.16531102.png', 8, 1, '2026-06-21 14:57:24'),
(10, '/assets/hero_6a37fbe2c12382.26691446.png', 9, 1, '2026-06-21 14:57:38'),
(11, '/assets/hero_6a37fbec3ac144.54435269.png', 10, 1, '2026-06-21 14:57:48');

-- --------------------------------------------------------

--
-- Table structure for table `hot_sales`
--

CREATE TABLE `hot_sales` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `output_label` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hot_sales`
--

INSERT INTO `hot_sales` (`id`, `name`, `image`, `output_label`, `description`, `tags`, `sort_order`, `is_active`) VALUES
(1, 'QS 1000 Supersonic Block Machine', '/assets/9.jpeg', 'Supersonic forming', 'Engineered with modern synchronized servo drive compaction and cloud diagnostic integration.', '[\"Servo vibration\",\"Smart telemetry\",\"Rapid mould change\"]', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `product_interest` varchar(200) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `name`, `email`, `phone`, `product_interest`, `message`, `is_read`, `created_at`) VALUES
(1, 'Vikram Reddy', '200101120034@cutm.ac.in', '09876543218', 'QS1000 Supersonic Block Machine', 'i have need for this machine', 1, '2026-06-15 18:38:14');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `date_text` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `title` varchar(300) NOT NULL,
  `summary` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `date_text`, `category`, `title`, `summary`, `image`, `content`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '20 May 2026', 'Innovation', 'Intelligent forming controls raise consistency across block types', 'How precision automation supports faster changeovers and reliable finished products.', NULL, NULL, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(2, '04 Apr 2026', 'Projects', 'New automated block line prepared for international delivery', 'A complete production configuration moves from assembly to commissioning support.', NULL, NULL, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(3, '16 Mar 2026', 'Exhibition', 'ULTRA Tile Machine presents efficient equipment concepts at industry expo', 'Visitors explored lower-waste production, handling automation and digital servicing.', NULL, NULL, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `title` varchar(200) NOT NULL,
  `category_filter` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `slug`, `title`, `category_filter`, `image`, `description`, `features`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'zenith-1500', 'Zenith 1500', 'BLOCK MAKING', '/assets/product_6a3b6866231578.94449210.png', 'Premium automatic block and paver forming platform with synchronized servo vibration compaction.', '[\"Servo vibration\",\"Siemens control\"]', 1, 1, '2026-06-20 06:45:55', '2026-06-24 05:17:26'),
(2, 'zenith-940', 'Zenith 940', 'BLOCK MAKING', '/assets/product_6a3b68a07bfdc8.27680268.png', 'Universal mobile/laying machine for hollow blocks, solid bricks, and curbstones.', '[\"Laying machine\",\"Fast changeovers\"]', 2, 1, '2026-06-20 06:45:55', '2026-06-24 05:18:24'),
(3, 'zenith-1200', 'Zenith 1200', 'PAVERS', '/assets/product_6a3b68d5605209.57771270.png', 'Stationary multilayer machine for pavers, blocks, and various concrete elements.', '[\"Stationary multilayer\",\"Flexible layout\"]', 3, 1, '2026-06-20 06:45:55', '2026-06-24 05:19:17'),
(4, 'zenith-quantum-1200', 'Zenith Quantum 1200', 'BLOCK MAKING', '/assets/product_6a3b68e8329714.76680770.png', 'Intelligent stationary machine optimized for multi-shift concrete block production.', '[\"Fully automated\",\"Heavy-duty frame\"]', 4, 1, '2026-06-20 06:45:55', '2026-06-24 05:19:36'),
(5, 'zenith-rhino-900', 'Zenith Rhino 900', 'CURBING', '/assets/product_6a3b690b5bc1d8.40905541.png', 'High-compaction hydraulic machine engineered for curbing and specialty elements.', '[\"Hydraulic compaction\",\"Wear-resistant molds\"]', 5, 1, '2026-06-20 06:45:55', '2026-06-24 05:20:11'),
(6, 'zenith-multi-4.0', 'Zenith Multi 4.0', 'MIXING', '/assets/product_6a3b6924de3a19.33342168.png', 'Intelligent mixing and feeding system suited for custom concrete formulas.', '[\"Variable speed\",\"Precision weighing\"]', 6, 1, '2026-06-20 06:45:55', '2026-06-24 05:20:36'),
(7, 'zenith-cubie', 'Zenith Cubie', 'MIXING', '/assets/product_6a3b69472708c8.22302010.png', 'Compact pan mixer config optimized for quick cycle times and uniform color batches.', '[\"Compact footprint\",\"High efficiency\"]', 7, 1, '2026-06-20 06:45:55', '2026-06-24 05:21:11'),
(8, 'zenith-master-1200', 'Zenith Master 1200', 'BATCHING', '/assets/product_6a3b696183de23.27339867.png', 'Full batching plant unit designed to prepare aggregates for large molding machines.', '[\"Continuous batching\",\"Siemens control modules\"]', 8, 1, '2026-06-20 06:45:55', '2026-06-24 05:21:37');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `name` varchar(200) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `slug`, `name`, `image`, `description`, `features`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'concrete-block-making-machine', 'Concrete Block Making Machine', '/assets/cat_6a3c32583788a4.51237562.png', 'Servo-driven forming technology engineered for uniform, high-density blocks and pavers.', '[\"Fast mould change\",\"Servo vibration\",\"High pressure compaction\"]', 1, 1, '2026-06-20 06:45:55', '2026-06-24 19:39:04'),
(2, 'block-production-line', 'Block Production Line', '/assets/2.jpeg', 'An integrated line combining batching, forming, curing and intelligent handling.', '[\"PLC control\",\"Modular layout\",\"Automatic operation\"]', 2, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(3, 'aac-block-production-line', 'AAC Block Production Line', '/assets/3.jpeg', 'Energy-conscious aerated concrete processing for lightweight building materials.', '[\"Precision cutting\",\"Steam curing\",\"Eco-friendly recycling\"]', 3, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(4, 'palletizing-system-cuber', 'Palletizing System / Cuber', '/assets/4.jpeg', 'Automated stacking and packaging cells that streamline finished-product logistics.', '[\"Robotic handling\",\"Stable cubing\",\"Multiple stacking styles\"]', 4, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(5, 'roof-tile-forming-machine', 'Roof Tile Forming Machine', '/assets/5.jpeg', 'Hydraulic forming platform for consistent architectural tiles and color finishes.', '[\"Hydraulic press\",\"Finish control\",\"Multi-mould versatility\"]', 5, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(6, 'concrete-batching-plant', 'Concrete Batching Plant', '/assets/6.jpeg', 'Reliable aggregate dosing and mixing systems built for continuous operations.', '[\"Accurate weighing\",\"Low waste\",\"Sturdy construction\"]', 6, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(7, 'block-moulds', 'Block Moulds', '/assets/7.jpeg', 'Wear-resistant mould solutions shaped for custom blocks, kerbs and pavers.', '[\"Heat treated\",\"Custom profiles\",\"High durability\"]', 7, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(8, 'spare-parts', 'Spare Parts', '/assets/8.jpeg', 'Critical components and service kits supporting long-term line availability.', '[\"Quick dispatch\",\"Quality checked\",\"OEM compatible\"]', 8, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(12, 'table', 'Vivrator table', '/assets/cat_6a36463b473753.61892225.png', '', '[]', 1, 1, '2026-06-20 07:50:19', '2026-06-20 07:50:19');

-- --------------------------------------------------------

--
-- Table structure for table `product_filters`
--

CREATE TABLE `product_filters` (
  `id` int(11) NOT NULL,
  `label` varchar(100) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_filters`
--

INSERT INTO `product_filters` (`id`, `label`, `sort_order`) VALUES
(1, 'ALL MACHINES', 1),
(2, 'BLOCK MAKING', 2),
(3, 'PAVERS', 3),
(4, 'CURBING', 4),
(5, 'MIXING', 5),
(6, 'BATCHING', 6);

-- --------------------------------------------------------

--
-- Table structure for table `product_subitems`
--

CREATE TABLE `product_subitems` (
  `id` int(11) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `category_slug` varchar(150) NOT NULL,
  `name` varchar(200) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specs`)),
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_subitems`
--

INSERT INTO `product_subitems` (`id`, `slug`, `category_slug`, `name`, `image`, `description`, `specs`, `features`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'qs1000', 'concrete-block-making-machine', 'QS1000 Supersonic Block Machine', '/assets/1.jpeg', 'Flagship servo vibration system for premium paving and hollow block production.', '{\"Cycle Time\":\"15-20 seconds\",\"Pallet Size\":\"1100 x 950 mm\",\"Vibration Force\":\"80 kN\",\"Total Power\":\"48 kW\"}', '[\"Servo drive compaction\",\"Smart cloud diagnostics\",\"Automatic pallet feeder\"]', 1, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(2, 'qp800', 'concrete-block-making-machine', 'QP800 Hydraulic Forming Machine', '/assets/2.jpeg', 'Flexible hydraulic machine delivering dense specialty pavers and landscape products.', '{\"Cycle Time\":\"20-25 seconds\",\"Pallet Size\":\"950 x 850 mm\",\"Vibration Force\":\"65 kN\",\"Total Power\":\"35 kW\"}', '[\"High pressure cylinders\",\"Proportional valve controls\",\"Custom block depth adjusting\"]', 2, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(3, 'qm1200', 'block-production-line', 'QM1200 Automatic Block Production Line', '/assets/3.jpeg', 'Heavy-duty platform designed for scalable plants and reliable multi-shift output.', '{\"Production Capacity\":\"120,000 blocks / day\",\"Control System\":\"Siemens PLC S7-1500\",\"Main Vibration\":\"Servo Sync\",\"Curing System\":\"Steam / Air Curing\"}', '[\"Fully integrated batching\",\"High-capacity multi-level curing loader\",\"Dual robotic cuber packaging\"]', 1, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(4, 'qm800-compact', 'block-production-line', 'QM800 Compact Production Line', '/assets/4.jpeg', 'Semi-automatic entry-level integrated line for small-to-medium regional producers.', '{\"Production Capacity\":\"60,000 blocks / day\",\"Control System\":\"Siemens PLC S7-1200\",\"Main Vibration\":\"VFD Motor Sync\",\"Curing System\":\"Natural Curing\"}', '[\"Optimized floor layout\",\"Simple raw materials conveyor\",\"Manual fork loading helper\"]', 2, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(5, 'aac-300k', 'aac-block-production-line', 'AAC 300K Annual Capacity Line', '/assets/5.jpeg', 'Industrial scale aerated autoclaved concrete production line for blocks and panels.', '{\"Annual Capacity\":\"300,000 m?\",\"Curing Autoclaves\":\"6x - 2.6m x 31.5m\",\"Cake Cutting Accuracy\":\"?1 mm\",\"Raw Materials\":\"Fly Ash / Sand, Lime, Cement\"}', '[\"Anti-sag cutting wire design\",\"Green slurry recycling system\",\"Heavy-duty cake lifting crane\"]', 1, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(6, 'aac-150k', 'aac-block-production-line', 'AAC 150K Standard Capacity Line', '/assets/6.jpeg', 'Mid-scale autoclaved aerated concrete line focusing on rapid payback and low footprint.', '{\"Annual Capacity\":\"150,000 m?\",\"Curing Autoclaves\":\"4x - 2.0m x 26.5m\",\"Cake Cutting Accuracy\":\"?1.5 mm\",\"Raw Materials\":\"Sand, Lime, Cement\"}', '[\"Compact autoclave layouts\",\"Easy-to-use batching interface\",\"Energy recovery venting system\"]', 2, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(7, 'cuber-servo', 'palletizing-system-cuber', 'High-Speed Servo Cuber Stacker', '/assets/7.jpeg', 'Four-sided clamp system engineered for rapid high-tier cubing of cured blocks.', '{\"Max Stacking Height\":\"1800 mm\",\"Cycle Capacity\":\"85-110 layers / hr\",\"Clamp Drive\":\"AC Servo Motor\",\"Clamping Range\":\"800 - 1450 mm\"}', '[\"Anti-skid layer alignment\",\"Automatic slip-sheet insertion\",\"Heavy-duty pallet magazine buffer\"]', 1, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(8, 'cuber-robotic', 'palletizing-system-cuber', 'Robotic Arm Cuber Integration', '/assets/8.jpeg', '6-axis industrial robot cell fitted with heavy pneumatic block clamping claws.', '{\"Payload Capacity\":\"500 kg\",\"Reach Radius\":\"3150 mm\",\"Control Unit\":\"Fanuc / Kuka CNC\",\"Power Consump.\":\"12 kW\"}', '[\"Custom clamping claws\",\"Flexible program selection\",\"Minimal floor space consumption\"]', 2, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(9, 'rt-600', 'roof-tile-forming-machine', 'RT600 High-Speed Tile Press', '/assets/9.jpeg', 'Hydraulic high-output press for color-slurry coated concrete roof tiles.', '{\"Cycle Time\":\"7-9 seconds\",\"Pressing Force\":\"1500 kN\",\"Tile Sizes\":\"424 x 337 mm\",\"Motor Power\":\"15 kW\"}', '[\"Rotary multi-station table\",\"Slurry spraying booth integration\",\"Automated curing frame racking\"]', 1, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(10, 'qpr600', 'roof-tile-forming-machine', 'QPR600 Terrazzo Tile Machine', '/assets/10.jpeg', 'Dedicated terrazzo press for polished floor and wall tiles with exposed aggregates.', '{\"Cycle Time\":\"12 seconds\",\"Pressing Force\":\"2000 kN\",\"Max Tile Size\":\"600 x 600 mm\",\"Aggregate Size\":\"up to 15 mm\"}', '[\"Wet-mix concrete press\",\"Aggregates distribution loader\",\"Automatic grinding/polishing path\"]', 2, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(11, 'hzs60', 'concrete-batching-plant', 'HZS60 Stationary Batching Plant', '/assets/11.jpeg', 'Skip-hoist concrete batching plant designed for precast operations and block line feeding.', '{\"Theoretical Output\":\"60 m? / hr\",\"Mixer Model\":\"JS1000 Twin-shaft\",\"Aggregate Bin Cap.\":\"4 x 15 m?\",\"Weighing Accuracy\":\"Aggregate ?2%, Cement ?1%\"}', '[\"Twin-shaft intensive mixer\",\"High-precision load cells\",\"Fully enclosed aggregate silo belts\"]', 1, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(12, 'hzs90-belt', 'concrete-batching-plant', 'HZS90 Belt-Conveyor Mixing Plant', '/assets/12.jpeg', 'High-capacity continuous aggregate batching plant with automated belt feed.', '{\"Theoretical Output\":\"90 m? / hr\",\"Mixer Model\":\"JS1500 Twin-shaft\",\"Aggregate Bin Cap.\":\"4 x 25 m?, Belt fed\",\"Weighing Accuracy\":\"Aggregate ?1.5%, Cement ?1%\"}', '[\"Continuous belt transport\",\"Moisture sensor compensation\",\"Dust extraction filtering system\"]', 2, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(13, 'mould-paver', 'block-moulds', 'Carburized Paver & Interlock Moulds', '/assets/13.jpeg', 'Custom carburized moulds engineered for interlocking pavers, grass stones, and curbs.', '{\"Hardness Level\":\"Hardness HRC 60-63\",\"Steel Grade\":\"Hardox wear-resistant\",\"Mould Clearance\":\"0.5 - 0.8 mm\",\"Lifespan\":\"120,000+ cycles\"}', '[\"Precision CNC cut cavities\",\"Interchangeable wear liners\",\"Hardened tamper head inserts\"]', 1, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(14, 'mould-hollow', 'block-moulds', 'Modular Hollow Block Moulds', '/assets/14.jpeg', 'Hollow block mould sets with quick-replaceable core bars and modular structure.', '{\"Hardness Level\":\"Hardness HRC 58-61\",\"Steel Grade\":\"Q345 / Hardened alloys\",\"Mould Core Type\":\"Tapered modular cores\",\"Lifespan\":\"100,000+ cycles\"}', '[\"Tapered cores for easy demould\",\"Bolted assembly structure\",\"High vibration resistant frame\"]', 2, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(15, 'parts-hydraulic', 'spare-parts', 'OEM Hydraulic Service Kit', '/assets/15.jpeg', 'Proportional valves, pump cartridges, seals, and cylinder repair kits.', '{\"Compatible Brands\":\"Yuken, Rexroth, Parker\",\"Max Pressure Rating\":\"315 Bar\",\"Response Speed\":\"Proportional < 15ms\",\"Certification\":\"ISO 9001 certified\"}', '[\"Includes Viton high-temp seals\",\"Fully calibrated solenoid valves\",\"Original factory test certificate\"]', 1, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(16, 'parts-electric', 'spare-parts', 'PLC & Electronics Control Upgrade Kit', '/assets/16.jpeg', 'Siemens modules, sensor suites, variable frequency drives, and junction boxes.', '{\"Compatible CPU\":\"Siemens S7 series\",\"Sensor Inputs\":\"Analog 4-20mA, Digital PNP\",\"VFD Power Support\":\"15kW - 45kW\",\"IP Protection Rating\":\"IP65 Cabinet grade\"}', '[\"Pre-loaded software framework\",\"Plug-and-play quick connect plugs\",\"Pre-calibrated transducer modules\"]', 2, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `label` varchar(150) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_large` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `label`, `title`, `image`, `is_large`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'Global Exhibition', 'Machinery Innovation Showcase', '/assets/16.jpeg', 1, 1, 1, '2026-06-20 06:45:55'),
(2, 'Customer Visit', 'Factory Acceptance Review', '/assets/11.jpeg', 0, 2, 1, '2026-06-20 06:45:55'),
(3, 'Installation', 'AAC Line Commissioning', '/assets/12.jpeg', 0, 3, 1, '2026-06-20 06:45:55'),
(4, 'Turnkey Project', 'Batching Plant Delivery', '/assets/13.jpeg', 0, 4, 1, '2026-06-20 06:45:55'),
(5, 'Service & Support', 'Global Maintenance Training', '/assets/14.jpeg', 0, 5, 1, '2026-06-20 06:45:55');

-- --------------------------------------------------------

--
-- Table structure for table `recognitions`
--

CREATE TABLE `recognitions` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `text` text DEFAULT NULL,
  `icon_name` varchar(100) DEFAULT 'ShieldCheck',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recognitions`
--

INSERT INTO `recognitions` (`id`, `title`, `text`, `icon_name`, `sort_order`, `is_active`) VALUES
(1, 'Quality Management', 'Standardized inspection practices throughout manufacturing.', 'ShieldCheck', 1, 1),
(2, 'Engineering Innovation', 'A product roadmap built around automated and efficient production.', 'BrickWall', 2, 1),
(3, 'Service Assurance', 'Structured support workflow for international project delivery.', 'Wrench', 3, 1),
(4, 'Responsible Production', 'Machinery designed to minimize waste and resource consumption.', 'Boxes', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `text` text DEFAULT NULL,
  `icon_name` varchar(100) DEFAULT 'Settings2',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `text`, `icon_name`, `sort_order`, `is_active`) VALUES
(1, 'Installation', 'On-site assembly and commissioning by specialized field teams.', 'Settings2', 1, 1),
(2, 'Training', 'Operator guidance for controls, process optimization and safety.', 'ShieldCheck', 2, 1),
(3, 'Spare Parts', 'Planned replacement support and genuine wear components.', 'Cog', 3, 1),
(4, 'After-sales Support', 'Responsive technical assistance throughout equipment life.', 'Headset', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `solutions`
--

CREATE TABLE `solutions` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `text` text DEFAULT NULL,
  `icon_name` varchar(100) DEFAULT 'Factory',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `solutions`
--

INSERT INTO `solutions` (`id`, `title`, `text`, `icon_name`, `sort_order`, `is_active`) VALUES
(1, 'Raw Material Handling', 'Storing and conveying bulk aggregates, sand, and cement.', 'Factory', 1, 1),
(2, 'Proportioning & Batching', 'High-precision aggregate and binder weight dosing.', 'Construction', 2, 1),
(3, 'Mixing & Feeding', 'Intensive homogenizing mixer feeding block machine hopper.', 'Cog', 3, 1),
(4, 'Molding & Pressing', 'Synchronized servo compression and high-frequency molding.', 'Boxes', 4, 1),
(5, 'Curing & Hardening', 'Steam curing chambers to raise structural block density.', 'Layers3', 5, 1),
(6, 'Product Handling & Packaging', 'Robotic clamp stacking, strap wrapping, and dispatch shipping.', 'PackageCheck', 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

CREATE TABLE `stats` (
  `id` int(11) NOT NULL,
  `value_text` varchar(100) NOT NULL,
  `label` varchar(150) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stats`
--

INSERT INTO `stats` (`id`, `value_text`, `label`, `sort_order`) VALUES
(1, '30+', 'Years of Excellence', 1),
(2, '120+', 'Countries Served', 2),
(3, '10,000+', 'Machines Delivered', 3),
(4, '215,000 m', 'Manufacturing Area', 4);

-- --------------------------------------------------------

--
-- Table structure for table `strengths`
--

CREATE TABLE `strengths` (
  `id` int(11) NOT NULL,
  `value_text` varchar(100) NOT NULL,
  `label` varchar(150) NOT NULL,
  `icon_name` varchar(100) DEFAULT 'Building2',
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `strengths`
--

INSERT INTO `strengths` (`id`, `value_text`, `label`, `icon_name`, `sort_order`) VALUES
(1, 'Zenith Pan Mixer', 'Consistent & High-Output Mixing', 'Cog', 1),
(2, 'Zenith Batching Plant', 'Accurate Batching Every Time', 'Construction', 2),
(3, 'Zenith Control System', 'Smart Control, Smart Production', 'Cpu', 3),
(4, 'Zenith Stacker', 'Fast, Safe & Efficient Stacking', 'PackageCheck', 4);

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `quote` text NOT NULL,
  `author` varchar(200) NOT NULL,
  `company` varchar(200) DEFAULT '',
  `stars` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `quote`, `author`, `company`, `stars`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Zenith machines have increased our production capacity by 40% and ensured unmatched reliability.', 'Rahul Sharma', 'CEO, BuildTech India', 5, 1, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(2, 'Exceptional build quality and after-sales support. Zenith is a partner we can always count on.', 'Ahmed Al-Fahad', 'Operations Director, Desert Blocks LLC', 5, 2, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55'),
(3, 'Advanced technology with robust performance. Our go-to choice for high-performance production.', 'Manuel Rivera', 'Plant Manager, SolidForm USA', 5, 3, 1, '2026-06-20 06:45:55', '2026-06-20 06:45:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_content`
--
ALTER TABLE `about_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hero_content`
--
ALTER TABLE `hero_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hero_slides`
--
ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hot_sales`
--
ALTER TABLE `hot_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `product_filters`
--
ALTER TABLE `product_filters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_subitems`
--
ALTER TABLE `product_subitems`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `product_subitems_ibfk_1` (`category_slug`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recognitions`
--
ALTER TABLE `recognitions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `solutions`
--
ALTER TABLE `solutions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stats`
--
ALTER TABLE `stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `strengths`
--
ALTER TABLE `strengths`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_content`
--
ALTER TABLE `about_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hero_content`
--
ALTER TABLE `hero_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `hot_sales`
--
ALTER TABLE `hot_sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `product_filters`
--
ALTER TABLE `product_filters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `product_subitems`
--
ALTER TABLE `product_subitems`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `recognitions`
--
ALTER TABLE `recognitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `solutions`
--
ALTER TABLE `solutions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `stats`
--
ALTER TABLE `stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `strengths`
--
ALTER TABLE `strengths`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_subitems`
--
ALTER TABLE `product_subitems`
  ADD CONSTRAINT `product_subitems_ibfk_1` FOREIGN KEY (`category_slug`) REFERENCES `product_categories` (`slug`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
