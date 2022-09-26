-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 26 Eyl 2022, 11:55:38
-- Sunucu sürümü: 10.3.35-MariaDB
-- PHP Sürümü: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `onlinesmmbayi_fayujsiker`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` int(11) NOT NULL,
  `bank_name` varchar(225) NOT NULL,
  `bank_sube` varchar(225) NOT NULL,
  `bank_hesap` varchar(225) NOT NULL,
  `bank_iban` text NOT NULL,
  `bank_alici` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` text COLLATE utf8mb4_bin NOT NULL,
  `category_line` double NOT NULL,
  `category_type` enum('1','2') CHARACTER SET utf8 NOT NULL DEFAULT '2',
  `category_secret` enum('1','2') COLLATE utf8mb4_bin NOT NULL DEFAULT '2',
  `category_icon` text COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `clients`
--

CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL,
  `name` varchar(225) NOT NULL,
  `email` varchar(225) NOT NULL,
  `username` varchar(225) NOT NULL,
  `password` text NOT NULL,
  `telephone` varchar(225) DEFAULT NULL,
  `balance` double NOT NULL DEFAULT 0,
  `balance_type` enum('1','2') NOT NULL DEFAULT '2',
  `debit_limit` double NOT NULL,
  `spent` double NOT NULL DEFAULT 0,
  `register_date` datetime NOT NULL,
  `login_date` datetime DEFAULT NULL,
  `login_ip` varchar(225) NOT NULL,
  `apikey` text NOT NULL,
  `tel_type` enum('1','2') NOT NULL DEFAULT '1' COMMENT '2 -> ON, 1 -> OFF',
  `email_type` enum('1','2') NOT NULL DEFAULT '1' COMMENT '2 -> ON, 1 -> OFF',
  `client_type` enum('1','2') NOT NULL DEFAULT '2' COMMENT '2 -> ON, 1 -> OFF',
  `access` text NOT NULL,
  `lang` varchar(255) NOT NULL DEFAULT 'tr',
  `timezone` double NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `clients`
--

INSERT INTO `clients` (`client_id`, `name`, `email`, `username`, `password`, `telephone`, `balance`, `balance_type`, `debit_limit`, `spent`, `register_date`, `login_date`, `login_ip`, `apikey`, `tel_type`, `email_type`, `client_type`, `access`, `lang`, `timezone`) VALUES
(1, 'Admin', 'info@admin.com', 'admin', 'c30c913c6e9a10850cb187d91621effd', NULL, 0, '2', 0, 0, '2020-01-01 00:00:00', '2022-09-26 10:32:11', '', 'b90b34833e71e0f3a9f3c093117135c6', '2', '2', '2', '{\"admin_access\":\"1\",\"users\":\"1\",\"orders\":\"1\",\"subscriptions\":\"1\",\"dripfeed\":\"1\",\"services\":\"1\",\"payments\":\"1\",\"tickets\":\"1\",\"reports\":\"1\",\"general_settings\":\"1\",\"pages\":\"1\",\"payments_settings\":\"1\",\"bank_accounts\":\"1\",\"payments_bonus\":\"1\",\"alert_settings\":\"1\",\"providers\":\"1\",\"themes\":\"1\",\"language\":\"1\",\"meta\":\"1\",\"twice\":\"1\",\"proxy\":\"1\",\"kuponlar\":\"1\",\"admins\":\"1\"}', 'tr', 0),
(2, 'ahme5t ca', 'fayuj@pm.me', 'selamsiz', '0b83d63745abe5b6e2b978df562ec370', '543151351351313', 0, '2', 0, 0, '2022-09-26 07:22:26', NULL, '', '2c7d385c56e2d66bd84b0decb7e6e622', '1', '1', '2', '', 'tr', 0),
(3, 'qweqwe', 'eqweqwe@eqwewq.wwrew', 'qweqweqweqw', '0b83d63745abe5b6e2b978df562ec370', '5315135135', 0, '2', 0, 0, '2022-09-26 07:47:35', NULL, '', '87cfe06aa65ea00c0ed74707a8a826eb', '1', '1', '2', '', 'tr', 0),
(4, 'Joseph Loer', 'trexyabiqwe@gmail.com', 'Selamsiz23', 'bb9e927a42e7ecfac44512b35143a7ed', '05541475818', 0, '2', 0, 0, '2022-09-26 09:21:50', '2022-09-26 10:32:13', '', 'b2854478d82ad21b5290ff6c3a633ca8', '1', '1', '2', '', 'tr', 0),
(5, 'ahmet caner', 'qqweqw@qq.com', 'muhammetx', '0b83d63745abe5b6e2b978df562ec370', '123123213', 0, '2', 0, 0, '2022-09-26 09:22:01', '2022-09-26 09:22:10', '', '7fffbd434bff4588636d01cb280496d5', '1', '1', '2', '', 'tr', 0),
(6, 'ahmet caner', 'qweqw@qc.om', 'muhammet1', '0b83d63745abe5b6e2b978df562ec370', '123123112', 0, '2', 0, 0, '2022-09-26 10:01:56', '2022-09-26 10:02:07', '', 'a9bff1a87f72b6ab9d99321d0090c470', '1', '1', '2', '', 'tr', 0),
(7, 'jozef owners', 'jozefqwe@gmail.com', 'jozef247', '3d63e6b11afb5411163c522ece9021b8', '05554355555', 0, '2', 0, 0, '2022-09-26 10:20:32', '2022-09-26 10:20:46', '', '7e1c490170d2e1dc472e716a39fc7464', '1', '1', '2', '', 'tr', 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `clients_category`
--

CREATE TABLE `clients_category` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `clients_price`
--

CREATE TABLE `clients_price` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `clients_service`
--

CREATE TABLE `clients_service` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `client_report`
--

CREATE TABLE `client_report` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `action` text NOT NULL,
  `report_ip` varchar(225) NOT NULL,
  `report_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `client_report`
--

INSERT INTO `client_report` (`id`, `client_id`, `action`, `report_ip`, `report_date`) VALUES
(744, 1, 'Yönetici girişi yapıldı.', '31.141.39.234, 162.158.129.83', '2022-09-26 06:17:26'),
(745, 1, 'Yönetici girişi yapıldı.', '31.141.39.234, 162.158.129.83', '2022-09-26 06:17:53'),
(746, 1, 'Kullancı girişi yapıldı.', '31.141.39.234, 162.158.129.115', '2022-09-26 07:19:49'),
(747, 1, 'Kullancı girişi yapıldı.', '31.141.39.234, 162.158.129.115', '2022-09-26 07:21:29'),
(748, 2, 'Kullanıcı kaydı yapıldı.', '31.141.39.234, 162.158.129.115', '2022-09-26 07:22:26'),
(749, 1, 'Yönetici girişi yapıldı.', '31.141.39.234, 162.158.129.83', '2022-09-26 07:24:45'),
(750, 1, 'Yönetici girişi yapıldı.', '31.141.39.234, 162.158.129.115', '2022-09-26 07:45:21'),
(751, 3, 'Kullanıcı kaydı yapıldı.', '31.141.39.234, 162.158.129.115', '2022-09-26 07:47:35'),
(752, 1, 'Kullancı girişi yapıldı.', '31.141.39.234, 162.158.129.115', '2022-09-26 07:47:53'),
(753, 1, 'Kullancı girişi yapıldı.', '31.141.39.234, 162.158.129.115', '2022-09-26 07:48:33'),
(754, 1, 'Kullancı girişi yapıldı.', '31.141.39.234, 162.158.129.133', '2022-09-26 08:23:02'),
(755, 1, 'Kullancı girişi yapıldı.', '31.141.39.234, 162.158.129.115', '2022-09-26 08:25:31'),
(756, 1, 'Kullancı girişi yapıldı.', '31.141.39.234, 162.158.129.63', '2022-09-26 08:41:02'),
(757, 1, 'Kullancı girişi yapıldı.', '31.141.39.234, 162.158.129.63', '2022-09-26 08:41:20'),
(758, 1, 'Kullanıcı parolası değiştirildi', '31.141.39.234, 162.158.129.63', '2022-09-26 08:41:44'),
(759, 1, 'API Key değiştirildi', '31.141.39.234, 162.158.129.63', '2022-09-26 08:41:52'),
(760, 1, 'API Key değiştirildi', '31.141.39.234, 162.158.129.63', '2022-09-26 08:41:53'),
(761, 1, 'API Key değiştirildi', '31.141.39.234, 162.158.129.63', '2022-09-26 08:41:55'),
(762, 1, 'API Key değiştirildi', '31.141.39.234, 162.158.129.63', '2022-09-26 08:41:56'),
(763, 4, 'Kullanıcı kaydı yapıldı.', '88.244.186.202, 172.70.51.162', '2022-09-26 09:21:50'),
(764, 4, 'Kullancı girişi yapıldı.', '88.244.186.202, 172.70.51.162', '2022-09-26 09:21:55'),
(765, 5, 'Kullanıcı kaydı yapıldı.', '31.141.39.234, 162.158.129.63', '2022-09-26 09:22:01'),
(766, 5, 'Kullancı girişi yapıldı.', '31.141.39.234, 162.158.129.63', '2022-09-26 09:22:10'),
(767, 6, 'Kullanıcı kaydı yapıldı.', '31.141.39.234, 162.158.129.115', '2022-09-26 10:01:56'),
(768, 6, 'Kullancı girişi yapıldı.', '31.141.39.234, 162.158.129.115', '2022-09-26 10:02:07'),
(769, 7, 'Kullanıcı kaydı yapıldı.', '46.221.86.182, 162.158.129.133', '2022-09-26 10:20:32'),
(770, 7, 'Kullancı girişi yapıldı.', '46.221.86.182, 162.158.129.133', '2022-09-26 10:20:46'),
(771, 1, 'Yönetici girişi yapıldı.', '31.141.39.234, 162.158.129.83', '2022-09-26 10:32:11'),
(772, 4, 'Kullancı girişi yapıldı.', '88.244.186.202, 172.70.51.154', '2022-09-26 10:32:13');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kuponlar`
--

CREATE TABLE `kuponlar` (
  `id` int(11) NOT NULL,
  `kuponadi` varchar(255) NOT NULL,
  `adet` int(11) NOT NULL,
  `tutar` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kupon_kullananlar`
--

CREATE TABLE `kupon_kullananlar` (
  `id` int(11) NOT NULL,
  `uye_id` int(11) NOT NULL,
  `kuponadi` varchar(255) NOT NULL,
  `tutar` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `language_name` varchar(225) NOT NULL,
  `language_code` varchar(225) NOT NULL,
  `language_type` enum('2','1') NOT NULL DEFAULT '2',
  `default_language` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `languages`
--

INSERT INTO `languages` (`id`, `language_name`, `language_code`, `language_type`, `default_language`) VALUES
(1, 'Türkçe', 'tr', '2', '1'),
(2, 'English', 'en', '2', '0');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `api_orderid` int(11) NOT NULL DEFAULT 0,
  `order_error` text NOT NULL,
  `order_detail` text NOT NULL,
  `order_api` int(11) NOT NULL DEFAULT 0,
  `api_serviceid` int(11) NOT NULL DEFAULT 0,
  `api_charge` double NOT NULL,
  `api_currencycharge` double NOT NULL DEFAULT 1,
  `order_profit` double NOT NULL,
  `order_quantity` double NOT NULL,
  `order_extras` text NOT NULL,
  `order_charge` double NOT NULL,
  `dripfeed` enum('1','2','3') DEFAULT '1' COMMENT '2 -> ON, 1 -> OFF',
  `dripfeed_id` double NOT NULL DEFAULT 0,
  `subscriptions_id` double NOT NULL DEFAULT 0,
  `subscriptions_type` enum('1','2') NOT NULL DEFAULT '1' COMMENT '2 -> ON, 1 -> OFF',
  `dripfeed_totalcharges` double DEFAULT NULL,
  `dripfeed_runs` double DEFAULT NULL,
  `dripfeed_delivery` double NOT NULL DEFAULT 0,
  `dripfeed_interval` double DEFAULT NULL,
  `dripfeed_totalquantity` double DEFAULT NULL,
  `dripfeed_status` enum('active','completed','canceled') NOT NULL DEFAULT 'active',
  `order_url` text NOT NULL,
  `order_start` double NOT NULL DEFAULT 0,
  `order_finish` double NOT NULL DEFAULT 0,
  `order_remains` double NOT NULL DEFAULT 0,
  `order_create` datetime NOT NULL,
  `order_status` enum('pending','inprogress','completed','partial','processing','canceled') NOT NULL DEFAULT 'pending',
  `subscriptions_status` enum('active','paused','completed','canceled','expired','limit') NOT NULL DEFAULT 'active',
  `subscriptions_username` text DEFAULT NULL,
  `subscriptions_posts` double DEFAULT NULL,
  `subscriptions_delivery` double NOT NULL DEFAULT 0,
  `subscriptions_delay` double DEFAULT NULL,
  `subscriptions_min` double DEFAULT NULL,
  `subscriptions_max` double DEFAULT NULL,
  `subscriptions_expiry` date DEFAULT NULL,
  `last_check` datetime NOT NULL,
  `order_where` enum('site','api') NOT NULL DEFAULT 'site'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `pages`
--

CREATE TABLE `pages` (
  `page_id` int(11) NOT NULL,
  `page_name` varchar(225) NOT NULL,
  `page_get` varchar(225) NOT NULL,
  `page_content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `pages`
--

INSERT INTO `pages` (`page_id`, `page_name`, `page_get`, `page_content`) VALUES
(1, 'Giriş Yap', 'auth', ''),
(2, 'Yeni sipariş', 'neworder', 'Yeni Siparişler İle İlgili Duyuru Burada Yer Alacaktır.'),
(3, 'Servisler', 'services', 'Servisler İle İlgili Duyuru Burada Yer Alacaktır.'),
(4, 'Bakiye ekle', 'addfunds', 'Bakiye İle İlgili Duyuru Burada Yer Alacaktır.'),
(5, 'Destek sistemi', 'tickets', 'Destek İle İlgili Duyuru Burada Yer Alacaktır.'),
(6, 'Kullanıcı sözleşmesi', 'terms', 'Kullanıcı Sözleşmesi İle İlgili Duyuru Burada Yer Alacaktır.'),
(7, 'Sık sorulan sorular', 'faq', 'Sık Sorulanlar İle İlgili Duyuru Burada Yer Alacaktır.'),
(8, 'Çark Hizmeti', 'cark', 'Çark İle İlgili Duyuru Burada Yer Alacaktır.');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `client_balance` double NOT NULL DEFAULT 0,
  `payment_amount` double NOT NULL,
  `payment_privatecode` double DEFAULT NULL,
  `payment_method` int(11) NOT NULL,
  `payment_status` enum('1','2','3') NOT NULL DEFAULT '1',
  `payment_delivery` enum('1','2') NOT NULL DEFAULT '1',
  `payment_note` text NOT NULL,
  `payment_mode` enum('Manuel','Otomatik') NOT NULL DEFAULT 'Otomatik',
  `payment_create_date` datetime NOT NULL,
  `payment_update_date` datetime NOT NULL,
  `payment_ip` varchar(225) NOT NULL,
  `payment_extra` text NOT NULL,
  `payment_bank` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `payments_bonus`
--

CREATE TABLE `payments_bonus` (
  `bonus_id` int(11) NOT NULL,
  `bonus_method` int(11) NOT NULL,
  `bonus_from` double NOT NULL,
  `bonus_amount` double NOT NULL,
  `bonus_type` enum('1','2') NOT NULL DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `method_name` varchar(225) NOT NULL,
  `method_get` varchar(225) NOT NULL,
  `method_min` double NOT NULL,
  `method_max` double NOT NULL,
  `method_type` enum('1','2') NOT NULL DEFAULT '2' COMMENT '2 -> ON, 1 -> OFF	',
  `method_extras` text NOT NULL,
  `method_line` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `method_name`, `method_get`, `method_min`, `method_max`, `method_type`, `method_extras`, `method_line`) VALUES
(1, 'PayTR', 'paytr', 1, 0, '1', '{\"method_type\":\"2\",\"name\":\"PayTR\",\"min\":\"1\",\"max\":\"0\",\"merchant_id\":\"\",\"merchant_key\":\"\",\"merchant_salt\":\"\",\"fee\":\"0\"}', 1),
(2, 'Paywant', 'paywant', 1, 0, '1', '{\"method_type\":\"2\",\"name\":\"Paywant\",\"min\":\"1\",\"max\":\"0\",\"apiKey\":\"\",\"apiSecret\":\"\",\"fee\":\"0\",\"commissionType\":\"2\",\"payment_type\":[\"1\",\"2\",\"3\"]}', 4),
(4, 'Banka Ödemeleri', 'havale-eft', 0, 0, '2', '{\"method_type\":\"2\",\"name\":\"Havale&Eft\"}', 7),
(5, 'Shopier', 'shopier', 10, 0, '2', '{\"method_type\":\"2\",\"name\":\"Shopier\",\"min\":\"10\",\"max\":\"\",\"apiKey\":\"\",\"apiSecret\":\"\",\"website_index\":\"1\",\"processing_fee\":\"1\",\"fee\":\"4,99\"}', 5),
(6, 'Payreks - Kredi Kartı', 'payreks_cc', 1, 0, '1', '{\"method_type\":\"2\",\"name\":\"Payreks - Kredi Kart\\u0131\",\"min\":\"1\",\"max\":\"0\",\"api_key\":\"\",\"api_secret\":\"\",\"fee\":\"0\"}', 3),
(7, 'PayTR HavaleEFT', 'paytr_havale', 1, 0, '1', '{\"method_type\":\"2\",\"name\":\"Paytr Havale&EFT\",\"min\":\"1\",\"max\":\"0\",\"merchant_id\":\"\",\"merchant_key\":\"\",\"merchant_salt\":\"\",\"fee\":\"0\"}', 2),
(8, 'Payreks - Mobil Ödeme', 'payreks_mobile', 1, 0, '1', '{\"method_type\":\"2\",\"name\":\"Payreks - Mobil \\u00d6deme\",\"min\":\"1\",\"max\":\"0\",\"api_key\":\"\",\"api_secret\":\"\",\"fee\":\"0\"}', 3);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `serviceapi_alert`
--

CREATE TABLE `serviceapi_alert` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `serviceapi_alert` text NOT NULL,
  `servicealert_extra` text NOT NULL,
  `servicealert_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_api` int(11) NOT NULL DEFAULT 0,
  `api_service` int(11) NOT NULL DEFAULT 0,
  `api_servicetype` enum('1','2') NOT NULL DEFAULT '2',
  `api_detail` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `service_line` double NOT NULL,
  `service_type` enum('1','2') NOT NULL DEFAULT '2',
  `service_package` enum('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17') NOT NULL,
  `service_name` text NOT NULL,
  `service_description` text NOT NULL,
  `service_price` double NOT NULL DEFAULT 0,
  `service_min` double NOT NULL,
  `service_max` double NOT NULL,
  `service_dripfeed` enum('1','2') NOT NULL DEFAULT '1',
  `service_autotime` double NOT NULL DEFAULT 0,
  `service_autopost` double NOT NULL DEFAULT 0,
  `service_speed` enum('1','2','3','4') NOT NULL,
  `want_username` enum('1','2') NOT NULL DEFAULT '1',
  `service_secret` enum('1','2') NOT NULL DEFAULT '2',
  `price_type` enum('normal','percent','amount') NOT NULL DEFAULT 'normal',
  `price_cal` text NOT NULL,
  `instagram_second` enum('1','2') NOT NULL DEFAULT '2',
  `start_count` enum('none','instagram_follower','instagram_photo','') NOT NULL,
  `instagram_private` enum('1','2') NOT NULL,
  `name_lang` text NOT NULL,
  `description_lang` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `service_api`
--

CREATE TABLE `service_api` (
  `id` int(11) NOT NULL,
  `api_name` varchar(225) NOT NULL,
  `api_url` text NOT NULL,
  `api_key` varchar(225) NOT NULL,
  `api_type` int(11) NOT NULL,
  `api_limit` double NOT NULL DEFAULT 0,
  `api_alert` enum('1','2') NOT NULL DEFAULT '2' COMMENT '2 -> Gönder, 1 -> Gönderildi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_seo` text NOT NULL,
  `site_title` text DEFAULT NULL,
  `site_description` text DEFAULT NULL,
  `site_keywords` text DEFAULT NULL,
  `site_logo` text DEFAULT NULL,
  `site_name` text DEFAULT NULL,
  `site_currency` varchar(2555) NOT NULL DEFAULT 'try',
  `favicon` text DEFAULT NULL,
  `site_language` varchar(225) NOT NULL DEFAULT 'tr',
  `site_theme` text NOT NULL,
  `site_theme_alt` text DEFAULT NULL,
  `recaptcha` enum('1','2') NOT NULL DEFAULT '1',
  `recaptcha_key` text DEFAULT NULL,
  `recaptcha_secret` text DEFAULT NULL,
  `custom_header` text DEFAULT NULL,
  `custom_footer` text DEFAULT NULL,
  `ticket_system` enum('1','2') NOT NULL DEFAULT '2',
  `register_page` enum('1','2') NOT NULL DEFAULT '2',
  `service_speed` enum('1','2') NOT NULL,
  `service_list` enum('1','2') NOT NULL,
  `dolar_charge` double NOT NULL,
  `euro_charge` double NOT NULL,
  `smtp_user` text NOT NULL,
  `smtp_pass` text NOT NULL,
  `smtp_server` text NOT NULL,
  `smtp_port` varchar(225) NOT NULL,
  `smtp_protocol` enum('0','ssl','tls') NOT NULL,
  `alert_type` enum('1','2','3') NOT NULL,
  `alert_newbankpayment` enum('1','2') NOT NULL,
  `alert_newmanuelservice` enum('1','2') NOT NULL,
  `alert_newticket` enum('1','2') NOT NULL,
  `alert_apibalance` enum('1','2') NOT NULL,
  `alert_serviceapialert` enum('1','2') NOT NULL,
  `sms_provider` varchar(225) NOT NULL,
  `sms_title` varchar(225) NOT NULL,
  `sms_user` varchar(225) NOT NULL,
  `sms_pass` varchar(225) NOT NULL,
  `sms_validate` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1 -> OK, 0 -> NO',
  `admin_mail` varchar(225) NOT NULL,
  `admin_telephone` varchar(225) NOT NULL,
  `resetpass_page` enum('1','2') NOT NULL,
  `resetpass_sms` enum('1','2') NOT NULL,
  `resetpass_email` enum('1','2') NOT NULL,
  `site_maintenance` enum('1','2') NOT NULL DEFAULT '2',
  `servis_siralama` varchar(255) NOT NULL,
  `bronz_statu` int(11) NOT NULL,
  `silver_statu` int(11) NOT NULL,
  `gold_statu` int(11) NOT NULL,
  `bayi_statu` int(11) NOT NULL,
  `cark_balance` int(11) NOT NULL,
  `cark_amount` int(11) NOT NULL,
  `cark_amount_2` int(11) NOT NULL,
  `cark_amount_3` int(11) NOT NULL,
  `cark_amount_4` int(11) NOT NULL,
  `cark_amount_5` int(11) NOT NULL,
  `cark_amount_6` int(11) NOT NULL,
  `cark_status` enum('Y','N') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `settings`
--

INSERT INTO `settings` (`id`, `site_seo`, `site_title`, `site_description`, `site_keywords`, `site_logo`, `site_name`, `site_currency`, `favicon`, `site_language`, `site_theme`, `site_theme_alt`, `recaptcha`, `recaptcha_key`, `recaptcha_secret`, `custom_header`, `custom_footer`, `ticket_system`, `register_page`, `service_speed`, `service_list`, `dolar_charge`, `euro_charge`, `smtp_user`, `smtp_pass`, `smtp_server`, `smtp_port`, `smtp_protocol`, `alert_type`, `alert_newbankpayment`, `alert_newmanuelservice`, `alert_newticket`, `alert_apibalance`, `alert_serviceapialert`, `sms_provider`, `sms_title`, `sms_user`, `sms_pass`, `sms_validate`, `admin_mail`, `admin_telephone`, `resetpass_page`, `resetpass_sms`, `resetpass_email`, `site_maintenance`, `servis_siralama`, `bronz_statu`, `silver_statu`, `gold_statu`, `bayi_statu`, `cark_balance`, `cark_amount`, `cark_amount_2`, `cark_amount_3`, `cark_amount_4`, `cark_amount_5`, `cark_amount_6`, `cark_status`) VALUES
(1, '', '', '', '', '', 'Divinum', '₺', '', 'tr', 'smarty', 'Bootstrap', '1', '', '', '', '', '2', '2', '1', '2', 18.4309, 18.0024, '', '', '', '', '0', '3', '1', '1', '1', '1', '1', 'bizimsms', '', '', '', '1', '', '', '2', '1', '2', '2', 'asc', 200, 500, 800, 1000, 2, 150, 250, 300, 350, 400, 450, 'Y');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `themes`
--

CREATE TABLE `themes` (
  `id` int(11) NOT NULL,
  `theme_name` text NOT NULL,
  `theme_dirname` text NOT NULL,
  `theme_extras` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `themes`
--

INSERT INTO `themes` (`id`, `theme_name`, `theme_dirname`, `theme_extras`) VALUES
(1, 'Bootstrap', 'bootstrap', '{\"stylesheets\":[\"public/bootstrap/bootstrap.css\",\"public/bootstrap/style.css\",\"https:\\/\\/stackpath.bootstrapcdn.com\\/font-awesome\\/4.7.0\\/css\\/font-awesome.min.css\",\"public\\/datepicker\\/css\\/bootstrap-datepicker3.min.css\"],\"scripts\":[\"https:\\/\\/code.jquery.com\\/jquery-3.3.1.min.js\",\"public/bootstrap/script.js\",\"public/ajax.js\",\"public/bootstrap/bootstrap.js\",\"public\\/datepicker\\/js\\/bootstrap-datepicker.min.js\",\"public\\/datepicker\\/locales\\/bootstrap-datepicker.tr.min.js\"]}'),
(2, 'Business', 'business', '{\"stylesheets\":[\"public/business/bootstrap.css\",\"public/business/style.css\",\"https:\\/\\/stackpath.bootstrapcdn.com\\/font-awesome\\/4.7.0\\/css\\/font-awesome.min.css\",\"public\\/datepicker\\/css\\/bootstrap-datepicker3.min.css\"],\"scripts\":[\"https:\\/\\/code.jquery.com\\/jquery-3.3.1.min.js\",\"public/business/script.js\",\"public/ajax.js\",\"public/business/bootstrap.js\",\"public\\/datepicker\\/js\\/bootstrap-datepicker.min.js\",\"public\\/datepicker\\/locales\\/bootstrap-datepicker.tr.min.js\"]}'),
(3, 'Darker', 'darker', '{\"stylesheets\":[\"public/darker/bootstrap.css\",\"public/darker/style.css\",\"https:\\/\\/stackpath.bootstrapcdn.com\\/font-awesome\\/4.7.0\\/css\\/font-awesome.min.css\",\"public\\/datepicker\\/css\\/bootstrap-datepicker3.min.css\"],\"scripts\":[\"https:\\/\\/code.jquery.com\\/jquery-3.3.1.min.js\",\"public/darker/script.js\",\"public/ajax.js\",\"public/darker/bootstrap.js\",\"public\\/datepicker\\/js\\/bootstrap-datepicker.min.js\",\"public\\/datepicker\\/locales\\/bootstrap-datepicker.tr.min.js\"]}'),
(4, 'Smarty', 'smarty', '{\"stylesheets\":[\"public/smarty/style.css\",\"https:\\/\\/stackpath.bootstrapcdn.com\\/font-awesome\\/4.7.0\\/css\\/font-awesome.min.css\",\"public\\/datepicker\\/css\\/bootstrap-datepicker3.min.css\"],\"scripts\":[\"https:\\/\\/code.jquery.com\\/jquery-3.3.1.min.js\",\"public/smarty/script.js\",\"public/ajax.js\",\"public/smarty/bootstrap.js\",\"public\\/datepicker\\/js\\/bootstrap-datepicker.min.js\",\"public\\/datepicker\\/locales\\/bootstrap-datepicker.tr.min.js\"]}'),
(6, 'Murky', 'murky', '{\"stylesheets\":[\"public/murky/bootstrap.css\",\"public/murky/style.css\",\"https:\\/\\/stackpath.bootstrapcdn.com\\/font-awesome\\/4.7.0\\/css\\/font-awesome.min.css\",\"public\\/datepicker\\/css\\/bootstrap-datepicker3.min.css\"],\"scripts\":[\"https:\\/\\/code.jquery.com\\/jquery-3.3.1.min.js\",\"public/murky/script.js\",\"public/ajax.js\",\"public/murky/bootstrap.js\",\"public\\/datepicker\\/js\\/bootstrap-datepicker.min.js\",\"public\\/datepicker\\/locales\\/bootstrap-datepicker.tr.min.js\"]}'),
(7, 'Blitzdark', 'blitzdark', '{\"stylesheets\":[\"public/blitzdark/bootstrap.css\",\"public/blitzdark/style.css\",\"https:\\/\\/stackpath.bootstrapcdn.com\\/font-awesome\\/4.7.0\\/css\\/font-awesome.min.css\",\"public\\/datepicker\\/css\\/bootstrap-datepicker3.min.css\"],\"scripts\":[\"https:\\/\\/code.jquery.com\\/jquery-3.3.1.min.js\",\"public/blitzdark/script.js\",\"public/ajax.js\",\"public/blitzdark/bootstrap.js\",\"public\\/datepicker\\/js\\/bootstrap-datepicker.min.js\",\"public\\/datepicker\\/locales\\/bootstrap-datepicker.tr.min.js\"]}'),
(8, 'Blitzlight', 'blitzlight', '{\"stylesheets\":[\"public/blitzlight/bootstrap.css\",\"public/blitzlight/style.css\",\"https:\\/\\/stackpath.bootstrapcdn.com\\/font-awesome\\/4.7.0\\/css\\/font-awesome.min.css\",\"public\\/datepicker\\/css\\/bootstrap-datepicker3.min.css\"],\"scripts\":[\"https:\\/\\/code.jquery.com\\/jquery-3.3.1.min.js\",\"public/blitzlight/script.js\",\"public/ajax.js\",\"public/blitzlight/bootstrap.js\",\"public\\/datepicker\\/js\\/bootstrap-datepicker.min.js\",\"public\\/datepicker\\/locales\\/bootstrap-datepicker.tr.min.js\"]}');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `subject` varchar(225) NOT NULL,
  `time` datetime NOT NULL,
  `lastupdate_time` datetime NOT NULL,
  `client_new` enum('1','2') NOT NULL DEFAULT '2',
  `status` enum('pending','answered','closed') NOT NULL DEFAULT 'pending',
  `support_new` enum('1','2') NOT NULL DEFAULT '1',
  `canmessage` enum('1','2') NOT NULL DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ticket_reply`
--

CREATE TABLE `ticket_reply` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `time` datetime NOT NULL,
  `support` enum('1','2') NOT NULL DEFAULT '1',
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Tablo için indeksler `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`);

--
-- Tablo için indeksler `clients_category`
--
ALTER TABLE `clients_category`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `clients_price`
--
ALTER TABLE `clients_price`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `clients_service`
--
ALTER TABLE `clients_service`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `client_report`
--
ALTER TABLE `client_report`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `kuponlar`
--
ALTER TABLE `kuponlar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `kupon_kullananlar`
--
ALTER TABLE `kupon_kullananlar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Tablo için indeksler `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_id`);

--
-- Tablo için indeksler `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Tablo için indeksler `payments_bonus`
--
ALTER TABLE `payments_bonus`
  ADD PRIMARY KEY (`bonus_id`);

--
-- Tablo için indeksler `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `serviceapi_alert`
--
ALTER TABLE `serviceapi_alert`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Tablo için indeksler `service_api`
--
ALTER TABLE `service_api`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`);

--
-- Tablo için indeksler `ticket_reply`
--
ALTER TABLE `ticket_reply`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `clients_category`
--
ALTER TABLE `clients_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `clients_price`
--
ALTER TABLE `clients_price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `clients_service`
--
ALTER TABLE `clients_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `client_report`
--
ALTER TABLE `client_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=773;

--
-- Tablo için AUTO_INCREMENT değeri `kuponlar`
--
ALTER TABLE `kuponlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `kupon_kullananlar`
--
ALTER TABLE `kupon_kullananlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Tablo için AUTO_INCREMENT değeri `pages`
--
ALTER TABLE `pages`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Tablo için AUTO_INCREMENT değeri `payments_bonus`
--
ALTER TABLE `payments_bonus`
  MODIFY `bonus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `serviceapi_alert`
--
ALTER TABLE `serviceapi_alert`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Tablo için AUTO_INCREMENT değeri `service_api`
--
ALTER TABLE `service_api`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `themes`
--
ALTER TABLE `themes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Tablo için AUTO_INCREMENT değeri `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `ticket_reply`
--
ALTER TABLE `ticket_reply`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
