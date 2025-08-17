-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 17, 2025 at 07:48 PM
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
-- Database: `shop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `password`) VALUES
(1, 'iludangol', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `quantity` int(10) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `pid`, `name`, `price`, `quantity`, `image`) VALUES
(21, 2, 4, 'Laptop', 3, 1, 'laptop-1.webp'),
(22, 4, 1, 'Watch', 1, 1, '304a57a54bbdeedc95c63ad065ec9270.jpg_750x750.jpg_.webp');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `name`, `email`, `number`, `message`) VALUES
(2, 1, 'Sarika Dangol', 'dangolsarika29@gmail.com', '9841811504', 'Hello'),
(3, 1, 'Sarika Dangol', 'dangolilu24@gmail.com', '984777', 'Hello how are you');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `number` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `total_products` varchar(1000) NOT NULL,
  `total_price` int(100) NOT NULL,
  `placed_on` date NOT NULL DEFAULT current_timestamp(),
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `number`, `email`, `method`, `address`, `total_products`, `total_price`, `placed_on`, `payment_status`) VALUES
(1, 1, 'Sarika Dangol', '978798', 'dangolsarika29@gmail.com', 'paytm', 'flat no. Khokana, Bus Park, Khokana, Bagmati, Nepal - 95689', 'Camera (1500 x 1) - ', 1500, '2025-04-29', 'completed'),
(2, 3, 'Dipti Parajuli', '98414141', 'dipti@gmail.com', 'cash on delivery', 'flat no. Khokana, Bus Park, Khokana, Bagmati, Nepal - 95689', 'Camera (1500 x 1) - ', 1500, '2025-08-02', 'completed'),
(3, 3, 'Dipti Parajuli', '98414141', 'dipti@gmail.com', 'cash on delivery', 'flat no. Khokana, Bus Park, Khokana, Bagmati, Nepal - 95689', 'Camera (1500 x 1) - ', 1500, '2025-08-02', 'pending'),
(11, 6, 'Sarika Dangol', '98414141', 'dangolilu24@gmail.com', 'cash on delivery', 'flat no. Khokana, Bus Park, Khokana, Bagmati, Nepal - 95689', 'Watch (1 x 1) - ', 1, '2025-08-04', 'pending'),
(12, 1, 'Sarika Dangol', '98414141', 'dangolilu24@gmail.com', 'cash on delivery', 'flat no. Khokana, Bus Park, Khokana, Bagmati, Nepal - 95689', 'Tv (2 x 1) - ', 2, '2025-08-04', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `details` varchar(500) NOT NULL,
  `price` int(10) NOT NULL,
  `image_01` varchar(100) NOT NULL,
  `image_02` varchar(100) NOT NULL,
  `image_03` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `details`, `price`, `image_01`, `image_02`, `image_03`) VALUES
(1, 'Watch', 'Elegant. Durable. Precise.\r\nThis stainless steel watch features a sleek 42 mm case, scratch-resistant sapphire glass, and water resistance up to 200 m. Powered by a reliable quartz movement, it combines timeless style with everyday functionality, perfect for both formal occasions and daily wear.\r\n', 1200, '304a57a54bbdeedc95c63ad065ec9270.jpg_750x750.jpg_.webp', '275f2ef1f491e26e8da7103010804840.jpg', 'c8e59ffd2b36a0f6d418a232c6a753db.jpg'),
(3, 'Tv', 'Samsung TU8000 Series 4K UHD Smart TV\r\n\r\nKey Features:\r\n\r\nDisplay: Available in 43&#34;, 55&#34;, 65&#34;, and 75&#34; sizes with 4K UHD resolution.\r\n\r\nProcessor: Crystal Processor 4K for enhanced picture quality.\r\n\r\nOperating System: Tizen OS with access to various apps and services.\r\n\r\nAudio: 20W RMS sound output with Dolby Digital Plus support.\r\n\r\nConnectivity: Multiple HDMI and USB ports, Bluetooth 4.2, and Ethernet.\r\n\r\nAdditional Features: HDR10+, UHD Dimming, Auto Motion Plus, and Game Mod', 20000, 'tv-01.webp', 'tv-02.webp', 'tv-03.webp'),
(4, 'Laptop', 'Dell Inspiron 15 3530 (13th Gen i5-1335U)\r\n\r\nPrice in Nepal: Approximately NPR 90,700 \r\nTechLekh\r\n+1\r\n\r\nKey Specifications:\r\n\r\nProcessor: 13th Gen Intel Core i5-1335U (10 cores, 12 threads, up to 4.60 GHz)\r\n\r\nRAM: 8GB DDR4 (expandable up to 16GB)\r\n\r\nStorage: 512GB M.2 NVMe SSD\r\n\r\nDisplay: 15.6&#34; FHD (1920×1080), 120Hz, Anti-Glare\r\n\r\nGraphics: Intel UHD Graphics\r\n\r\nPorts: USB 3.2 Gen 1 Type-A, USB 3.2 Gen 1 Type-C, USB 2.0, HDMI 1.4, SD 3.0 card slot\r\n\r\nBattery: 3-cell 41 Wh Li-ion\r\n\r\nOperatin', 300000, 'laptop-1.webp', 'laptop-2.webp', 'laptop-3.webp'),
(8, 'Fridge', 'Stainless Steel French‑Door Refrigerator\r\nThis sleek stainless-steel fridge combines elegant design with practical functionality. Its spacious French‑door layout features adjustable glass shelves and humidity‑controlled crisper drawers for flexible, organized storage. Equipped with auto‑defrost technology for effortless maintenance and a digital inverter compressor for whisper‑quiet, energy‑efficient performance, it’s both stylish and highly functional', 15000, 'fridge-1.webp', 'fridge-2.webp', 'fridge-3.webp'),
(9, 'Mouse', 'Specifications:\r\n\r\nConnectivity: USB Wired\r\n\r\nSensor Type: Optical\r\n\r\nDPI (Dots Per Inch): Typically 800–1600 (varies by model)\r\n\r\nCable Length: Approximately 1.5 meters\r\n\r\nCompatibility: Windows, macOS, Linux\r\n\r\nDimensions: Varies by model (approximately 11 x 6 x 3.5 cm)', 1000, 'mouse-1.webp', 'mouse-2.webp', 'mouse-3.webp'),
(10, 'Washing Machine', 'Specification\r\nMaintenance: Auto-cleans the inner tub to prevent mold, bacteria, and detergent buildup.\r\n\r\nHygiene: Promotes cleanliness without manual scrubbing.\r\n\r\nFound In: Newer models from LG, Samsung, and Panasoni', 12000, 'washing machine-1.webp', 'washing machine-2.webp', 'washing machine-3.webp'),
(11, 'Mixer', 'Better Hotel King 1200W Mixer Grinder\r\n\r\nKey Features:\r\n\r\nPower: 1200W copper motor for heavy-duty grinding.\r\n\r\nJars: Includes 2 stainless steel jars and an aluminum socket.\r\n\r\nSpeed Control: 3-speed settings for versatile use.\r\n\r\nDesign: Compact and sturdy build with ergonomic handles.\r\n\r\nWarranty: 2-year home servicing warranty.', 1500, 'mixer-1.webp', 'mixer-2.webp', 'mixer-3.webp'),
(12, 'Camera', 'Key Camera Specifications\r\nMegapixels (MP)\r\nIndicates image resolution; higher MP allows for larger prints and detailed cropping.\r\n\r\nSensor Size\r\nLarger sensors (e.g., Full-frame) capture more light, offering better low-light performance and depth of field control.\r\n\r\nISO Range\r\nDetermines sensitivity to light; wider ISO range enables shooting in various lighting conditions.\r\n\r\nShutter Speed\r\nControls exposure duration; faster speeds freeze motion, while slower speeds capture motion blur effects', 20000, 'camera-1.webp', 'camera-2.webp', 'camera-3.webp'),
(13, 'Smart Mobile', 'Realme C21\r\nDisplay: 6.5-inch IPS LCD, HD+ (1600 x 720), 60Hz refresh rate\r\n\r\nProcessor: MediaTek Helio G35 (12nm)\r\n\r\nRAM/Storage: 3GB/4GB RAM, 32GB/64GB storage (expandable via microSD)\r\n\r\nRear Cameras:\r\n\r\n13MP (wide)\r\n\r\n2MP (macro)\r\n\r\n2MP (depth)\r\n\r\nFront Camera: 5MP\r\n\r\nBattery: 5000mAh, 10W charging\r\n\r\nOperating System: Android 10 with Realme UI 1.0\r\n\r\nAdditional Features: Rear-mounted fingerprint sensor, 3.5mm headphone jack, microUSB 2.0\r\n\r\nPrice in Nepal: NPR 16,999 for the 4GB/64GB varian', 200000, 'smartphone-1.webp', 'smartphone-2.webp', 'smartphone-3.webp');

-- --------------------------------------------------------

--
-- Table structure for table `product_ratings`
--

CREATE TABLE `product_ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_ratings`
--

INSERT INTO `product_ratings` (`id`, `user_id`, `product_id`, `rating`, `created_at`) VALUES
(2, 1, 3, 3, '2025-06-02 16:26:08'),
(3, 1, 1, 4, '2025-07-31 17:05:13'),
(4, 2, 1, 1, '2025-08-02 04:47:39'),
(7, 3, 3, 4, '2025-08-02 05:49:44'),
(9, 2, 4, 4, '2025-08-02 06:14:26'),
(11, 4, 3, 5, '2025-08-04 05:41:05'),
(12, 4, 1, 3, '2025-08-04 08:17:11'),
(13, 4, 4, 5, '2025-08-04 08:17:18'),
(15, 3, 1, 4, '2025-08-04 09:04:45'),
(16, 3, 4, 4, '2025-08-04 09:05:30'),
(17, 2, 3, 1, '2025-08-04 09:14:58'),
(18, 6, 1, 4, '2025-08-04 09:45:37'),
(19, 1, 4, 3, '2025-08-04 10:48:03'),
(20, 1, 9, 3, '2025-08-07 14:55:53'),
(21, 4, 8, 4, '2025-08-07 14:58:14'),
(22, 7, 1, 3, '2025-08-07 15:01:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(1, 'iludangol', 'dangolsarika29@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef'),
(2, 'iludangol', 'iludangol@gmail.com', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2'),
(3, 'Dipti Parajuli', 'dipti@gmail.com', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2'),
(4, 'Gyanu', 'gyanu@gmail.com', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2'),
(6, 'harendra', 'harendra@gmail.com', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2'),
(7, 'Dilip', 'dilip@gmail.com', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(100) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `pid`, `name`, `price`, `image`) VALUES
(3, 3, 4, 'Laptop', 2500, 'laptop-1.webp'),
(9, 1, 1, 'Watch', 1, '304a57a54bbdeedc95c63ad065ec9270.jpg_750x750.jpg_.webp');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `product_ratings`
--
ALTER TABLE `product_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD CONSTRAINT `product_ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `product_ratings_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
