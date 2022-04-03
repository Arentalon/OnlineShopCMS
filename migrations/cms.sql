SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `cart` (
  `cartId` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cartitems` (
  `cartItemsId` int(11) NOT NULL,
  `cartId` int(11) DEFAULT NULL,
  `productId` int(11) DEFAULT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `category` (
  `categoryId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `isActive` tinyint(1) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `lang` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `history` (
  `historyId` int(11) NOT NULL,
  `date` date NOT NULL,
  `userId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `historyproduct` (
  `historyProductId` int(11) NOT NULL,
  `historyId` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `product` (
  `productId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `price` float NOT NULL,
  `amount` int(11) NOT NULL,
  `categoryId` int(11) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amountOfBought` int(11) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `producer` varchar(255) DEFAULT NULL,
  `priceUnit` varchar(255) DEFAULT NULL,
  `lang` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `sale` (
  `id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `back_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `productId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `shop` (
  `shopId` int(11) NOT NULL,
  `attr_name` varchar(255) NOT NULL,
  `attr_value` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `shop` (`shopId`, `attr_name`, `attr_value`) VALUES
(1, 'name', 'Nazwa_sklepu');

CREATE TABLE `user` (
  `userId` int(11) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `nr` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `lang` varchar(255) NOT NULL,
  `roles` longtext NOT NULL COMMENT '(DC2Type:json)',
  `is_verified` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `user` (`userId`, `mail`, `password`, `name`, `surname`, `city`, `country`, `street`, `nr`, `code`, `lang`, `roles`, `is_verified`) VALUES
(1, 'cms@gmail.com', '$2y$13$79Km9e6/7tXQnLBF5H1jhuW1JcYmACLuihOHZPwphH/XzQGiaGjay', 'cms', 'cms', 'Poznań', 'Polska', 'Miodowa', 13, '12-123', 'pl', '[\"ROLE_ADMIN\"]', 1);

ALTER TABLE `cart`
  ADD PRIMARY KEY (`cartId`),
  ADD KEY `fk_Cart_userId` (`userId`);

ALTER TABLE `cartitems`
  ADD PRIMARY KEY (`cartItemsId`),
  ADD KEY `fk_CartItems_productId` (`productId`),
  ADD KEY `fk_CartItems_cartId` (`cartId`);

ALTER TABLE `category`
  ADD PRIMARY KEY (`categoryId`);

ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

ALTER TABLE `history`
  ADD PRIMARY KEY (`historyId`),
  ADD KEY `fk_History_userId` (`userId`);
  
ALTER TABLE `historyproduct`
  ADD PRIMARY KEY (`historyProductId`),
  ADD KEY `fk_HistoryProduct_historyId` (`historyId`);

ALTER TABLE `product`
  ADD PRIMARY KEY (`productId`),
  ADD KEY `fk_Product_categoryId` (`categoryId`);

ALTER TABLE `sale`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_E54BC00536799605` (`productId`);

ALTER TABLE `shop`
  ADD PRIMARY KEY (`shopId`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `UNIQ_8D93D6495126AC48` (`mail`);

ALTER TABLE `cart`
  MODIFY `cartId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `cartitems`
  MODIFY `cartItemsId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

ALTER TABLE `category`
  MODIFY `categoryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `history`
  MODIFY `historyId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `historyproduct`
  MODIFY `historyProductId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `product`
  MODIFY `productId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `sale`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `shop`
  MODIFY `shopId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `user`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

ALTER TABLE `cart`
  ADD CONSTRAINT `fk_Order_userId` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`);

ALTER TABLE `cartitems`
  ADD CONSTRAINT `fk_CartItems_orderId` FOREIGN KEY (`cartId`) REFERENCES `cart` (`cartId`),
  ADD CONSTRAINT `fk_CartItems_productId` FOREIGN KEY (`productId`) REFERENCES `product` (`productId`);

ALTER TABLE `history`
  ADD CONSTRAINT `fk_History_userId` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`);

ALTER TABLE `historyproduct`
  ADD CONSTRAINT `fk_HistoryProduct_historyId` FOREIGN KEY (`historyId`) REFERENCES `history` (`historyId`);

ALTER TABLE `product`
  ADD CONSTRAINT `fk_Product_categoryId` FOREIGN KEY (`categoryId`) REFERENCES `category` (`categoryId`);

ALTER TABLE `sale`
  ADD CONSTRAINT `FK_E54BC00536799605` FOREIGN KEY (`productId`) REFERENCES `product` (`productId`) ON DELETE CASCADE;
COMMIT;