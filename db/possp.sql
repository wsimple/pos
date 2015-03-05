-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 07-10-2014 a las 17:01:43
-- Versión del servidor: 5.6.16
-- Versión de PHP: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `possp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_app_config`
--

CREATE TABLE IF NOT EXISTS `ospos_app_config` (
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `ospos_app_config`
--

INSERT INTO `ospos_app_config` (`key`, `value`) VALUES
('address', '2112 SW 74th St, Oklahoma City, OK 73159, Estados Unidos'),
('company', 'Fast I Repair'),
('default_tax_rate', '8'),
('email', 'info@om-parts.com'),
('fax', '12356588525'),
('phone', '+1 405-601-7020'),
('return_policy', 'All Sales Final\n'),
('timezone', 'America/Caracas'),
('website', ' http://www.om-parts.com/'),
('default_tax_1_rate', '8.365'),
('default_tax_1_name', 'Sales Tax'),
('default_tax_2_rate', ''),
('default_tax_2_name', ''),
('currency_symbol', '$'),
('language', 'english'),
('print_after_sale', 'print_after_sale'),
('logo', 'logo.png'),
('alert_after_sale', '0'),
('default_service', '20.00'),
('default_item_percentage', '1.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_brand`
--

CREATE TABLE IF NOT EXISTS `ospos_brand` (
  `brand_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(50) NOT NULL,
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Volcado de datos para la tabla `ospos_brand`
--

INSERT INTO `ospos_brand` (`brand_id`, `brand_name`) VALUES
(1, 'Apple'),
(2, 'Samsung'),
(3, 'Nokia'),
(4, 'Sony Ericsson'),
(5, 'LG'),
(6, 'HTC'),
(7, 'Huawei'),
(8, 'Motorola'),
(9, 'Blackberry'),
(10, 'ZTE'),
(11, 'Microsoft'),
(12, 'Siemens');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_credits`
--

CREATE TABLE IF NOT EXISTS `ospos_credits` (
  `credit_id` int(11) NOT NULL AUTO_INCREMENT,
  `person_custo_id` int(11) DEFAULT NULL,
  `transfer_id` int(11) DEFAULT NULL,
  `person_emplo_id` int(11) DEFAULT NULL,
  `payment_amount` decimal(15,2) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT NULL,
  `payment_period` varchar(255) DEFAULT NULL,
  `day_pay` date DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`credit_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_customers`
--

CREATE TABLE IF NOT EXISTS `ospos_customers` (
  `person_id` int(10) NOT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `taxable` int(1) NOT NULL DEFAULT '1',
  `deleted` int(1) NOT NULL DEFAULT '0',
  `discounts` decimal(10,2) DEFAULT NULL,
  `type` char(1) DEFAULT '0',
  `max_amount_credit` varchar(255) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`person_id`),
  UNIQUE KEY `account_number` (`account_number`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_employees`
--

CREATE TABLE IF NOT EXISTS `ospos_employees` (
  `person_id` int(10) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_schedule` int(1) DEFAULT '1',
  `deleted` int(1) NOT NULL DEFAULT '0',
  `lastChatActivity` int(2) NOT NULL,
  `type_employees` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`person_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `ospos_employees`
--

INSERT INTO `ospos_employees` (`person_id`, `username`, `password`, `id_schedule`, `deleted`, `lastChatActivity`, `type_employees`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, 0, 0, 'administrator');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_employees_profile`
--

CREATE TABLE IF NOT EXISTS `ospos_employees_profile` (
  `profile_name` varchar(50) NOT NULL DEFAULT '',
  `module_id` varchar(255) NOT NULL DEFAULT '',
  `privileges` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`profile_name`,`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `ospos_employees_profile`
--

INSERT INTO `ospos_employees_profile` (`profile_name`, `module_id`, `privileges`) VALUES
('administrator', 'config', 'save'),
('administrator', 'customers', 'add,update,delete'),
('administrator', 'employees', 'add,update,delete'),
('administrator', 'giftcards', 'add,update,delete'),
('administrator', 'items', 'add,update,delete'),
('administrator', 'item_kits', 'add,update,delete'),
('administrator', 'locations', 'add,update,disable'),
('administrator', 'notification_alert', 'Broken Items,Low Stock,Delivery to Receive,Accounts Payable,Invoice Discounting,Pendig Orders'),
('administrator', 'reports', 'none'),
('administrator', 'sales', 'none'),
('administrator', 'services', 'add,update,delete'),
('administrator', 'stock_control', 'Receivings,Shipping,Orders'),
('administrator', 'suppliers', 'add,update,delete'),
('seller', 'reports', 'none'),
('seller', 'sales', 'none'),
('seller', 'services', 'add,update,delete'),
('stock administrator', 'items', 'add,update,delete'),
('stock administrator', 'item_kits', 'add,update,delete'),
('stock administrator', 'notification_alert', 'Low Stock,Delivery to Receive,Pendig Orders'),
('stock administrator', 'stock_control', 'Receivings,Shipping,Orders'),
('stock administrator', 'suppliers', 'add,update,delete'),
('store administrator', 'employees', 'add,update'),
('store administrator', 'giftcards', 'add,update,delete'),
('store administrator', 'notification_alert', 'Low Stock,Delivery to Receive'),
('store administrator', 'reports', 'none'),
('store administrator', 'sales', 'none'),
('store administrator', 'services', 'add,update,delete'),
('store administrator', 'stock_control', 'Receivings,Orders');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_employees_schedule`
--

CREATE TABLE IF NOT EXISTS `ospos_employees_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `login` time NOT NULL,
  `logout` time DEFAULT NULL,
  `location` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Volcado de datos para la tabla `ospos_employees_schedule`
--

INSERT INTO `ospos_employees_schedule` (`id`, `employee_id`, `date`, `login`, `logout`, `location`) VALUES
(1, 1, '2014-09-16', '09:51:54', '11:29:39', 'default'),
(2, 1, '2014-09-16', '10:05:12', '11:29:39', 'default'),
(3, 1, '2014-09-17', '08:18:41', '11:29:39', 'default'),
(4, 1, '2014-09-17', '08:37:06', '11:29:39', 'default'),
(5, 1, '2014-09-17', '09:18:39', '11:29:39', 'default'),
(6, 1, '2014-09-18', '08:29:14', '11:29:39', 'default'),
(7, 1, '2014-09-29', '15:37:13', '11:29:39', 'default'),
(8, 1, '2014-09-30', '08:48:19', '11:29:39', 'default'),
(9, 1, '2014-09-30', '15:21:35', '11:29:39', 'default'),
(10, 1, '2014-10-01', '08:46:36', '11:29:39', 'default'),
(11, 1, '2014-10-01', '10:30:03', '11:29:39', 'default'),
(12, 1, '2014-10-01', '10:46:40', '11:29:39', 'default'),
(13, 1, '2014-10-01', '14:05:24', '11:29:39', 'default'),
(14, 1, '2014-10-02', '09:20:24', '11:29:39', 'default'),
(15, 1, '2014-10-03', '09:24:14', '11:29:39', 'default'),
(16, 1, '2014-10-03', '10:53:58', '11:29:39', 'default'),
(17, 1, '2014-10-03', '11:29:44', NULL, 'default'),
(18, 1, '2014-10-06', '11:30:25', NULL, 'default'),
(19, 1, '2014-10-07', '09:36:01', NULL, 'default');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_giftcards`
--

CREATE TABLE IF NOT EXISTS `ospos_giftcards` (
  `giftcard_id` int(11) NOT NULL AUTO_INCREMENT,
  `giftcard_number` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `value` double(15,2) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`giftcard_id`),
  UNIQUE KEY `giftcard_number` (`giftcard_number`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_inventory`
--

CREATE TABLE IF NOT EXISTS `ospos_inventory` (
  `trans_id` int(11) NOT NULL AUTO_INCREMENT,
  `trans_items` int(11) NOT NULL DEFAULT '0',
  `trans_user` int(11) NOT NULL DEFAULT '0',
  `trans_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `trans_comment` text NOT NULL,
  `trans_inventory` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`trans_id`),
  KEY `ospos_inventory_ibfk_1` (`trans_items`),
  KEY `ospos_inventory_ibfk_2` (`trans_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_items`
--

CREATE TABLE IF NOT EXISTS `ospos_items` (
  `item_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `item_number` varchar(255) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `cost_price` double(15,2) NOT NULL,
  `unit_price` double(15,2) NOT NULL,
  `quantity` double(15,2) NOT NULL DEFAULT '0.00',
  `reorder_level` double(15,2) NOT NULL DEFAULT '0.00',
  `location` varchar(255) NOT NULL,
  `allow_alt_description` tinyint(1) NOT NULL,
  `is_serialized` tinyint(1) NOT NULL,
  `is_service` tinyint(1) NOT NULL,
  `is_locked` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `broken_quantity` int(15) unsigned NOT NULL DEFAULT '0',
  `model_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `item_number` (`item_number`),
  KEY `ospos_items_ibfk_1` (`supplier_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `ospos_items`
--

INSERT INTO `ospos_items` (`item_id`, `name`, `category`, `supplier_id`, `item_number`, `description`, `cost_price`, `unit_price`, `quantity`, `reorder_level`, `location`, `allow_alt_description`, `is_serialized`, `is_service`, `is_locked`, `deleted`, `broken_quantity`, `model_id`) VALUES
(-1, 'Gift Card', 'Service', NULL, NULL, '', 0.00, 0.00, 0.00, 0.00, '0', 0, 0, 1, 1, 0, 0, NULL),
(1, 'Unlock Service', 'Repair', NULL, NULL, '', 0.00, 0.00, 0.00, 0.00, '0', 0, 0, 1, 1, 0, 0, NULL),
(2, 'Service 2', 'Service', NULL, NULL, '', 0.00, 0.00, 0.00, 0.00, '0', 0, 0, 1, 1, 0, 0, NULL),
(3, 'Repair Service', 'Repair', NULL, NULL, '', 20.00, 20.00, 999.00, 0.00, '0', 0, 0, 1, 1, 0, 0, NULL),
(4, 'Service 4', 'Service', NULL, NULL, '', 0.00, 0.00, 0.00, 0.00, '0', 0, 0, 1, 1, 0, 0, NULL),
(5, 'Service 5', 'Service', NULL, NULL, '', 0.00, 0.00, 0.00, 0.00, '0', 0, 0, 1, 1, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_items_taxes`
--

CREATE TABLE IF NOT EXISTS `ospos_items_taxes` (
  `item_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `percent` double(15,3) NOT NULL,
  PRIMARY KEY (`item_id`,`name`,`percent`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Estructura de tabla para la tabla `ospos_item_kits`
--

CREATE TABLE IF NOT EXISTS `ospos_item_kits` (
  `item_kit_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`item_kit_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

--
-- Estructura de tabla para la tabla `ospos_item_kit_items`
--

CREATE TABLE IF NOT EXISTS `ospos_item_kit_items` (
  `item_kit_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` double(15,2) NOT NULL,
  PRIMARY KEY (`item_kit_id`,`item_id`,`quantity`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_migrations`
--

CREATE TABLE IF NOT EXISTS `ospos_migrations` (
  `version` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `ospos_migrations`
--

INSERT INTO `ospos_migrations` (`version`) VALUES
(1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_model`
--

CREATE TABLE IF NOT EXISTS `ospos_model` (
  `model_id` int(11) NOT NULL AUTO_INCREMENT,
  `model_name` varchar(50) NOT NULL,
  `brand_id` int(11) NOT NULL,
  PRIMARY KEY (`model_id`),
  KEY `modelphone_brand` (`brand_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Volcado de datos para la tabla `ospos_model`
--

INSERT INTO `ospos_model` (`model_id`, `model_name`, `brand_id`) VALUES
(1, 'iPhone 5s', 1),
(2, 'Galaxy s5', 2),
(4, 'iPad', 1),
(5, 'iPhone 3', 1),
(6, 'iPhone 3g', 1),
(7, 'iPhone 4', 1),
(8, 'iPhone 4s', 1),
(9, 'iPhone 5', 1),
(10, 'iPad 2', 1),
(11, 'iPad 3', 1),
(12, 'iPad mini', 1),
(13, 'iPad 4', 1),
(14, 'iPad Air', 1),
(15, 'Galaxy s2', 2),
(16, 'Galaxy s3', 2),
(18, 'Galaxy s3 mini', 2),
(19, 'Galaxy s4', 2),
(20, 'iphone 4s', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_modules`
--

CREATE TABLE IF NOT EXISTS `ospos_modules` (
  `module_id` varchar(255) NOT NULL,
  `name_lang_key` varchar(255) NOT NULL,
  `desc_lang_key` varchar(255) NOT NULL,
  `shortcut` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `options` varchar(100) DEFAULT 'none',
  PRIMARY KEY (`module_id`),
  UNIQUE KEY `desc_lang_key` (`desc_lang_key`),
  UNIQUE KEY `name_lang_key` (`name_lang_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `ospos_modules`
--

INSERT INTO `ospos_modules` (`module_id`, `name_lang_key`, `desc_lang_key`, `shortcut`, `sort`, `options`) VALUES
('config', 'module_config', 'module_config_desc', '', 100, 'save'),
('customers', 'module_customers', 'module_customers_desc', '', 10, 'add,update,delete'),
('employees', 'module_employees', 'module_employees_desc', '', 80, 'add,update,delete'),
('giftcards', 'module_giftcards', 'module_giftcards_desc', '', 90, 'add,update,delete'),
('items', 'module_items', 'module_items_desc', '', 20, 'add,update,delete'),
('item_kits', 'module_item_kits', 'module_item_kits_desc', '', 30, 'add,update,delete'),
('reports', 'module_reports', 'module_reports_desc', '', 50, 'none'),
('sales', 'module_sales', 'module_sales_desc', '', 70, 'none'),
('suppliers', 'module_suppliers', 'module_suppliers_desc', '', 40, 'add,update,delete'),
('locations', 'module_locations', 'module_locations_desc', '', 95, 'add,update,disable'),
('services', 'module_services', 'module_services_desc', '/view/-1/height:465/width:850', 1, 'add,update,delete'),
('stock_control', 'module_stock_control', 'module_stock_control_desc', '', 60, 'Receivings,Shipping,Orders'),
('notification_alert', 'module_notification_alert', 'module_notification_alert_desc', '', 0, 'Broken Items,Low Stock,Delivery to Receive,Accounts Payable,Invoice Discounting,Pendig Orders');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_observation_inventories`
--

CREATE TABLE IF NOT EXISTS `ospos_observation_inventories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_register` timestamp NULL DEFAULT NULL,
  `observation` mediumtext,
  `person_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_people`
--

CREATE TABLE IF NOT EXISTS `ospos_people` (
  `person_id` int(10) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address_1` varchar(255) DEFAULT NULL,
  `address_2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `comments` text,
  PRIMARY KEY (`person_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `ospos_people`
--

INSERT INTO `ospos_people` (`person_id`, `first_name`, `last_name`, `phone_number`, `email`, `address_1`, `address_2`, `city`, `state`, `zip`, `country`, `comments`) VALUES
(1, 'Admin', 'Root', '122525', 'info@om-parts.com', 'Address 1', '0', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_permissions`
--

CREATE TABLE IF NOT EXISTS `ospos_permissions` (
  `module_id` varchar(255) NOT NULL,
  `person_id` int(10) NOT NULL,
  `privileges` varchar(100) DEFAULT 'none',
  PRIMARY KEY (`module_id`,`person_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `ospos_permissions`
--

INSERT INTO `ospos_permissions` (`module_id`, `person_id`, `privileges`) VALUES
('employees', 1, 'add,update,delete'),
('sales', 1, 'none'),
('stock_control', 1, 'Receivings,Shipping,Orders'),
('suppliers', 1, 'add,update,delete'),
('reports', 1, 'none'),
('customers', 1, 'add,update,delete'),
('items', 1, 'add,update,delete'),
('item_kits', 1, 'add,update,delete'),
('services', 1, 'add,update,delete'),
('notification_alert', 1, 'Broken Items,Low Stock,Delivery to Receive,Accounts Payable,Invoice Discounting,Pendig Orders'),
('giftcards', 1, 'add,update,delete'),
('locations', 1, 'add,update,disable'),
('config', 1, 'save');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_receivings`
--

CREATE TABLE IF NOT EXISTS `ospos_receivings` (
  `receiving_id` int(10) NOT NULL AUTO_INCREMENT,
  `receiving_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `supplier_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `payment_type` varchar(20) DEFAULT NULL,
  `payment` double(15,0) DEFAULT NULL,
  PRIMARY KEY (`receiving_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_receivings_items`
--

CREATE TABLE IF NOT EXISTS `ospos_receivings_items` (
  `receiving_id` int(10) NOT NULL DEFAULT '0',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `line` int(3) NOT NULL,
  `description` varchar(30) DEFAULT NULL,
  `serialnumber` varchar(30) DEFAULT NULL,
  `quantity_purchased` int(10) NOT NULL DEFAULT '0',
  `item_cost_price` decimal(15,2) NOT NULL,
  `item_unit_price` double(15,2) NOT NULL,
  `discount_percent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`receiving_id`,`item_id`,`line`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Estructura de tabla para la tabla `ospos_sales`
--

CREATE TABLE IF NOT EXISTS `ospos_sales` (
  `sale_id` int(10) NOT NULL AUTO_INCREMENT,
  `sale_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `payment_type` varchar(512) DEFAULT NULL,
  `mode` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`sale_id`),
  KEY `customer_id` (`customer_id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_sales_items`
--

CREATE TABLE IF NOT EXISTS `ospos_sales_items` (
  `sale_id` int(10) NOT NULL DEFAULT '0',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `line` int(3) NOT NULL DEFAULT '0',
  `service_id` int(11) DEFAULT NULL,
  `description` varchar(30) DEFAULT NULL,
  `serialnumber` varchar(30) DEFAULT NULL,
  `quantity_purchased` double(15,2) NOT NULL DEFAULT '0.00',
  `item_cost_price` decimal(15,2) NOT NULL,
  `item_unit_price` double(15,2) NOT NULL,
  `discount_percent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sale_id`,`item_id`,`line`),
  UNIQUE KEY `service_id_u` (`service_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Estructura de tabla para la tabla `ospos_sales_items_taxes`
--

CREATE TABLE IF NOT EXISTS `ospos_sales_items_taxes` (
  `sale_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `line` int(3) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `percent` double(15,3) NOT NULL,
  PRIMARY KEY (`sale_id`,`item_id`,`line`,`name`,`percent`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Estructura de tabla para la tabla `ospos_sales_payments`
--

CREATE TABLE IF NOT EXISTS `ospos_sales_payments` (
  `sale_id` int(10) NOT NULL,
  `payment_type` varchar(40) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  PRIMARY KEY (`sale_id`,`payment_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Estructura de tabla para la tabla `ospos_sales_suspended`
--

CREATE TABLE IF NOT EXISTS `ospos_sales_suspended` (
  `sale_id` int(10) NOT NULL AUTO_INCREMENT,
  `sale_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `payment_type` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`sale_id`),
  KEY `customer_id` (`customer_id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_sales_suspended_items`
--

CREATE TABLE IF NOT EXISTS `ospos_sales_suspended_items` (
  `sale_id` int(10) NOT NULL DEFAULT '0',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `line` int(3) NOT NULL DEFAULT '0',
  `description` varchar(30) DEFAULT NULL,
  `serialnumber` varchar(30) DEFAULT NULL,
  `quantity_purchased` double(15,2) NOT NULL DEFAULT '0.00',
  `item_cost_price` decimal(15,2) NOT NULL,
  `item_unit_price` double(15,2) NOT NULL,
  `discount_percent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sale_id`,`item_id`,`line`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_sales_suspended_items_taxes`
--

CREATE TABLE IF NOT EXISTS `ospos_sales_suspended_items_taxes` (
  `sale_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `line` int(3) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `percent` double(15,3) NOT NULL,
  PRIMARY KEY (`sale_id`,`item_id`,`line`,`name`,`percent`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Estructura de tabla para la tabla `ospos_sales_suspended_payments`
--

CREATE TABLE IF NOT EXISTS `ospos_sales_suspended_payments` (
  `sale_id` int(10) NOT NULL,
  `payment_type` varchar(40) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  PRIMARY KEY (`sale_id`,`payment_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Estructura de tabla para la tabla `ospos_schedules`
--

CREATE TABLE IF NOT EXISTS `ospos_schedules` (
  `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
  `day` varchar(11) NOT NULL,
  `in` time NOT NULL,
  `out` time NOT NULL,
  `person_id` int(11) NOT NULL,
  PRIMARY KEY (`schedule_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Volcado de datos para la tabla `ospos_schedules`
--

INSERT INTO `ospos_schedules` (`schedule_id`, `day`, `in`, `out`, `person_id`) VALUES
(1, 'Saturday', '00:00:00', '23:00:00', 1),
(2, 'Friday', '08:00:00', '22:00:00', 1),
(3, 'Thursday', '00:00:00', '23:00:00', 1),
(4, 'Wednesday', '00:00:00', '21:00:00', 1),
(5, 'Tuesday', '00:00:00', '20:00:00', 1),
(6, 'Monday', '00:00:00', '21:00:00', 1),
(7, 'Sunday', '00:00:00', '23:00:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_service_items`
--

CREATE TABLE IF NOT EXISTS `ospos_service_items` (
  `service_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `unit_price` double(15,0) DEFAULT '0',
  `is_Kit` char(1) DEFAULT '0',
  PRIMARY KEY (`item_id`,`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_service_log`
--

CREATE TABLE IF NOT EXISTS `ospos_service_log` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `serial` varchar(18) DEFAULT NULL,
  `model_id` int(11) NOT NULL,
  `date_received` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_delivered` datetime DEFAULT NULL,
  `problem` mediumtext,
  `status` int(1) NOT NULL DEFAULT '1',
  `deleted` int(1) DEFAULT '0',
  `color` varchar(50) DEFAULT NULL,
  `add_pay` varchar(512) DEFAULT '0',
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_service_log_notes`
--

CREATE TABLE IF NOT EXISTS `ospos_service_log_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `service_id` int(11) NOT NULL,
  `note` mediumtext,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_sessions`
--

CREATE TABLE IF NOT EXISTS `ospos_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `ospos_sessions`
--

INSERT INTO `ospos_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
('ea0cd3c291da7d8dcbb6a336e5c4685b', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36', 1412693632, 'a:5:{s:9:"user_data";s:0:"";s:10:"dblocation";s:7:"default";s:9:"person_id";s:1:"1";s:21:"employees_working_now";a:1:{i:0;s:1:"1";}s:14:"items_location";s:7:"default";}'),
('82c36903631a39ded5b4cef7025ffb6f', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36', 1412621532, 'a:8:{s:9:"user_data";s:0:"";s:10:"dblocation";s:7:"default";s:9:"person_id";s:1:"1";s:21:"employees_working_now";a:1:{i:0;s:1:"1";}s:9:"sale_mode";s:4:"sale";s:13:"sale_discount";i:0;s:11:"sale_taxing";b:1;s:13:"sale_payments";a:0:{}}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_suppliers`
--

CREATE TABLE IF NOT EXISTS `ospos_suppliers` (
  `person_id` int(10) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `bank_info` mediumtext,
  `work_phone` varchar(20) DEFAULT NULL,
  `product_supplied` mediumtext,
  `discounts` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`person_id`),
  UNIQUE KEY `account_number` (`account_number`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ospos_transfers`
--

CREATE TABLE IF NOT EXISTS `ospos_transfers` (
  `transfer_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender` varchar(20) NOT NULL,
  `receiver` varchar(20) NOT NULL,
  `date` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `comment` text NOT NULL,
  `payment_type` varchar(512) NOT NULL,
  PRIMARY KEY (`transfer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ospos_model`
--
ALTER TABLE `ospos_model`
  ADD CONSTRAINT `modelphone_brand` FOREIGN KEY (`brand_id`) REFERENCES `ospos_brand` (`brand_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
