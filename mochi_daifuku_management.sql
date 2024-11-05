-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Nov 2024 pada 13.37
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mochi_daifuku_management`
--

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_sale_detail` (IN `p_sale_id` INT, IN `p_product_id` INT, IN `p_quantity` INT)   BEGIN
    DECLARE unit_price DECIMAL(10,2);
    
    -- Get product price from category
    SELECT c.price INTO unit_price
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    WHERE p.product_id = p_product_id;
    
    -- Insert sale detail
    INSERT INTO sale_details (sale_id, product_id, quantity, price_per_unit, subtotal)
    VALUES (p_sale_id, p_product_id, p_quantity, unit_price, unit_price * p_quantity);
    
    -- Update inventory
    UPDATE products 
    SET stock = stock - p_quantity
    WHERE product_id = p_product_id;
    
    -- Record inventory movement
    INSERT INTO inventory_movement (product_id, quantity, movement_type, notes)
    VALUES (p_product_id, p_quantity, 'out', 'Sale transaction');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `record_sale` (IN `p_customer_name` VARCHAR(100), IN `p_payment_method` VARCHAR(20), IN `p_notes` TEXT)   BEGIN
    DECLARE new_sale_id INT;
    
    INSERT INTO sales (customer_name, payment_method, notes)
    VALUES (p_customer_name, p_payment_method, p_notes);
    
    SET new_sale_id = LAST_INSERT_ID();
    
    SELECT new_sale_id as sale_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `price`) VALUES
(1, 'Classic', 5000.00),
(2, 'Premium', 6000.00),
(3, 'Special', 7000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `daily_reports`
--

CREATE TABLE `daily_reports` (
  `report_id` int(11) NOT NULL,
  `report_date` date DEFAULT NULL,
  `total_sales` decimal(10,2) DEFAULT NULL,
  `total_items_sold` int(11) DEFAULT NULL,
  `best_selling_product` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `daily_sales_summary`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `daily_sales_summary` (
`sale_date` date
,`product_name` varchar(100)
,`category_name` varchar(50)
,`total_quantity` decimal(32,0)
,`total_revenue` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `inventory_movement`
--

CREATE TABLE `inventory_movement` (
  `movement_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `movement_type` enum('in','out') NOT NULL,
  `movement_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `inventory_movement`
--

INSERT INTO `inventory_movement` (`movement_id`, `product_id`, `quantity`, `movement_type`, `movement_date`, `notes`) VALUES
(1, 1, 4, 'out', '2024-11-04 10:56:29', 'Sale transaction'),
(2, 2, 4, 'out', '2024-11-04 10:56:29', 'Sale transaction'),
(3, 3, 4, 'out', '2024-11-04 10:56:29', 'Sale transaction'),
(4, 6, 3, 'out', '2024-11-04 10:56:29', 'Sale transaction'),
(5, 4, 4, 'out', '2024-11-04 10:57:29', 'Sale transaction'),
(6, 5, 3, 'out', '2024-11-04 10:57:29', 'Sale transaction'),
(7, 7, 3, 'out', '2024-11-04 11:08:53', 'Sale transaction'),
(8, 9, 3, 'out', '2024-11-04 11:08:53', 'Sale transaction'),
(9, 10, 3, 'out', '2024-11-04 11:08:53', 'Sale transaction'),
(10, 1, 1, 'out', '2024-11-04 11:13:43', 'Sale transaction'),
(11, 6, 2, 'out', '2024-11-04 11:15:09', 'Sale transaction'),
(12, 3, 1, 'out', '2024-11-04 11:23:07', 'Sale transaction');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `inventory_status`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `inventory_status` (
`product_id` int(11)
,`name` varchar(100)
,`category` varchar(50)
,`stock` int(11)
,`status` enum('available','out_of_stock')
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `status` enum('pending','awaiting_payment','paid','processing','shipped','completed','cancelled') NOT NULL,
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','transfer','qris') NOT NULL,
  `payment_status` enum('pending','completed','failed','refunded') NOT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `variants` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variants`)),
  `stock` int(11) DEFAULT 0,
  `status` enum('available','out_of_stock') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `name`, `description`, `image_url`, `variants`, `stock`, `status`, `created_at`) VALUES
(1, 1, 'Choco Crunchy', 'Chocolate mochi with crunchy texture', NULL, NULL, 93, 'available', '2024-11-02 13:49:28'),
(2, 1, 'Marshmallow Creamcheese', 'Marshmallow filled mochi with cream cheese', NULL, NULL, 95, 'available', '2024-11-02 13:49:28'),
(3, 1, 'Cookies n Cream', 'Cookies and cream flavored mochi', NULL, NULL, 89, 'available', '2024-11-02 13:49:28'),
(4, 1, 'Taro', 'Traditional taro flavored mochi', NULL, NULL, 96, 'available', '2024-11-02 13:49:28'),
(5, 1, 'Matcha', 'Green tea flavored mochi', NULL, NULL, 97, 'available', '2024-11-02 13:49:28'),
(6, 1, 'Blueberry Cheese', 'Blueberry mochi with cheese filling', NULL, NULL, 90, 'available', '2024-11-02 13:49:28'),
(7, 2, 'Mango Creamcheese', 'Mango flavored mochi with cream cheese', NULL, NULL, 97, 'available', '2024-11-02 13:49:28'),
(8, 2, 'Strawberry Creamcheese', 'Strawberry mochi with cream cheese', NULL, NULL, 100, 'available', '2024-11-02 13:49:28'),
(9, 2, 'Choco Cheese', 'Chocolate mochi with cheese filling', NULL, NULL, 97, 'available', '2024-11-02 13:49:28'),
(10, 2, 'Red Velvet Cheese', 'Red velvet mochi with cheese filling', NULL, NULL, 97, 'available', '2024-11-02 13:49:28'),
(11, 3, 'Choco Strawberry', 'Premium chocolate mochi with strawberry', NULL, NULL, 100, 'available', '2024-11-02 13:49:28'),
(12, 3, 'Choco Muscat Grape', 'Chocolate mochi with muscat grape', NULL, NULL, 100, 'available', '2024-11-02 13:49:28'),
(13, 3, 'Choco Red Globe Grape', 'Chocolate mochi with red globe grape', NULL, NULL, 100, 'available', '2024-11-02 13:49:28'),
(14, 3, 'Matcha Strawberry', 'Matcha mochi with strawberry', NULL, NULL, 100, 'available', '2024-11-02 13:49:28'),
(15, 3, 'Muscat Grape Creamcheese', 'Muscat grape mochi with cream cheese', NULL, NULL, 100, 'available', '2024-11-02 13:49:28'),
(16, 3, 'Red Globe Grape Creamcheese', 'Red globe grape mochi with cream cheese', NULL, NULL, 100, 'available', '2024-11-02 13:49:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sales`
--

CREATE TABLE `sales` (
  `sale_id` int(11) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` enum('cash','transfer','qris') DEFAULT 'cash',
  `customer_name` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sales`
--

INSERT INTO `sales` (`sale_id`, `transaction_date`, `total_amount`, `payment_method`, `customer_name`, `notes`) VALUES
(1, '2024-11-04 10:54:27', NULL, 'cash', 'Ujang', NULL),
(2, '2024-11-04 10:56:29', 75000.00, 'cash', 'Ujang', NULL),
(3, '2024-11-04 10:57:29', 35000.00, 'cash', 'Ujang', NULL),
(4, '2024-11-04 11:08:53', 54000.00, 'transfer', 'usep', NULL),
(5, '2024-11-04 11:13:43', 5000.00, 'transfer', 'Kuda', NULL),
(6, '2024-11-04 11:15:08', 10000.00, 'transfer', 'Udan', NULL),
(7, '2024-11-04 11:23:07', 5000.00, 'cash', 'uj', NULL),
(8, '2024-11-04 11:51:51', 45000.00, 'cash', 'Ujang', NULL),
(9, '2024-11-04 11:59:54', 10000.00, 'cash', 'cas', NULL),
(10, '2024-11-04 12:11:14', 15000.00, 'cash', 'aaw', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sale_details`
--

CREATE TABLE `sale_details` (
  `sale_detail_id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price_per_unit` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sale_details`
--

INSERT INTO `sale_details` (`sale_detail_id`, `sale_id`, `product_id`, `quantity`, `price_per_unit`, `subtotal`) VALUES
(1, 2, 1, 4, 5000.00, 20000.00),
(2, 2, 2, 4, 5000.00, 20000.00),
(3, 2, 3, 4, 5000.00, 20000.00),
(4, 2, 6, 3, 5000.00, 15000.00),
(5, 3, 4, 4, 5000.00, 20000.00),
(6, 3, 5, 3, 5000.00, 15000.00),
(7, 4, 7, 3, 6000.00, 18000.00),
(8, 4, 9, 3, 6000.00, 18000.00),
(9, 4, 10, 3, 6000.00, 18000.00),
(10, 5, 1, 1, 5000.00, 5000.00),
(11, 6, 6, 2, 5000.00, 10000.00),
(12, 7, 3, 1, 5000.00, 5000.00),
(13, 8, 2, 1, 5000.00, 5000.00),
(14, 8, 3, 3, 5000.00, 15000.00),
(15, 8, 6, 5, 5000.00, 25000.00),
(16, 9, 1, 2, 5000.00, 10000.00),
(17, 10, 3, 3, 5000.00, 15000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `shopping_carts`
--

CREATE TABLE `shopping_carts` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin123', 'admin@gmail.com', '$2y$10$VUO.ZQ.D8tC0cMMQ0WKo.eNmu.sgCbtBP9w9Ii/d5xu9Sd7dS2vVu', 'customer', '2024-11-04 01:17:39'),
(2, 'admin234', 'admin234@gmail.com', '$2y$10$r/nrQxa6MLEk2qx5M7ZuJO4SIDkCvPJO5gQbiSRza38.sJY12DX9m', 'admin', '2024-11-04 01:31:05'),
(3, 'admin', 'admin1@gmail.com', '$2y$10$ksHz/wFOdc1kCE99SzsmKumojYW24a1fSyzvxfTTpF4XyK8jTLjrW', 'admin', '2024-11-04 02:54:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_profiles`
--

CREATE TABLE `user_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user_profiles`
--

INSERT INTO `user_profiles` (`profile_id`, `user_id`, `full_name`, `phone_number`, `address`) VALUES
(1, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur untuk view `daily_sales_summary`
--
DROP TABLE IF EXISTS `daily_sales_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `daily_sales_summary`  AS SELECT cast(`s`.`transaction_date` as date) AS `sale_date`, `p`.`name` AS `product_name`, `c`.`name` AS `category_name`, sum(`sd`.`quantity`) AS `total_quantity`, sum(`sd`.`subtotal`) AS `total_revenue` FROM (((`sales` `s` join `sale_details` `sd` on(`s`.`sale_id` = `sd`.`sale_id`)) join `products` `p` on(`sd`.`product_id` = `p`.`product_id`)) join `categories` `c` on(`p`.`category_id` = `c`.`category_id`)) GROUP BY cast(`s`.`transaction_date` as date), `p`.`product_id` ;

-- --------------------------------------------------------

--
-- Struktur untuk view `inventory_status`
--
DROP TABLE IF EXISTS `inventory_status`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `inventory_status`  AS SELECT `p`.`product_id` AS `product_id`, `p`.`name` AS `name`, `c`.`name` AS `category`, `p`.`stock` AS `stock`, `p`.`status` AS `status` FROM (`products` `p` join `categories` `c` on(`p`.`category_id` = `c`.`category_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indeks untuk tabel `daily_reports`
--
ALTER TABLE `daily_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `best_selling_product` (`best_selling_product`);

--
-- Indeks untuk tabel `inventory_movement`
--
ALTER TABLE `inventory_movement`
  ADD PRIMARY KEY (`movement_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indeks untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeks untuk tabel `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sale_id`);

--
-- Indeks untuk tabel `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`sale_detail_id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `shopping_carts`
--
ALTER TABLE `shopping_carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `daily_reports`
--
ALTER TABLE `daily_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `inventory_movement`
--
ALTER TABLE `inventory_movement`
  MODIFY `movement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `sales`
--
ALTER TABLE `sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `sale_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `shopping_carts`
--
ALTER TABLE `shopping_carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `shopping_carts` (`cart_id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Ketidakleluasaan untuk tabel `daily_reports`
--
ALTER TABLE `daily_reports`
  ADD CONSTRAINT `daily_reports_ibfk_1` FOREIGN KEY (`best_selling_product`) REFERENCES `products` (`product_id`);

--
-- Ketidakleluasaan untuk tabel `inventory_movement`
--
ALTER TABLE `inventory_movement`
  ADD CONSTRAINT `inventory_movement_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`sale_id`);

--
-- Ketidakleluasaan untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Ketidakleluasaan untuk tabel `sale_details`
--
ALTER TABLE `sale_details`
  ADD CONSTRAINT `sale_details_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`sale_id`),
  ADD CONSTRAINT `sale_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Ketidakleluasaan untuk tabel `shopping_carts`
--
ALTER TABLE `shopping_carts`
  ADD CONSTRAINT `shopping_carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
