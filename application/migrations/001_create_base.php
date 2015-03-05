<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ahora se puede actualizar las tablas y sus campos directamente
 * desde el menu de configuracion.
 * para agregar un campo en una de las tables primero ubicar la seccion
 * correspondiente a la tabla para agregar un campo simplemente seguir este ej:
 * $this->dbforge->add_field("`key` varchar(255) NOT NULL ");
 *
 * Para mas informacion sobre las migraciones: https://ellislab.com/codeigniter/user-guide/libraries/migration.html
 */
class Migration_create_base extends CI_Migration {

	public function up() {
		## Create Table ospos_app_config
		$this->dbforge->add_field("`key` varchar(255) NOT NULL ");
		$this->dbforge->add_key("key",TRUE);
		$this->dbforge->add_field("`value` varchar(255) NOT NULL ");
		$this->dbforge->create_table("app_config");
		$this->db->query('ALTER TABLE  `ospos_app_config` ENGINE = MyISAM');
		## Create Table ospos_brand
		$this->dbforge->add_field("`brand_id` int(11) NOT NULL auto_increment");
		$this->dbforge->add_key("brand_id",TRUE);
		$this->dbforge->add_field("`brand_name` varchar(50) NOT NULL ");
		$this->dbforge->create_table("brand", TRUE);
		$this->db->query('ALTER TABLE  `ospos_brand` ENGINE = InnoDB');
		## Create Table ospos_customers
		$this->dbforge->add_field("`person_id` int(10) NOT NULL ");
		$this->dbforge->add_field("`account_number` varchar(255) NULL ");
		$this->dbforge->add_field("`taxable` int(1) NOT NULL DEFAULT '1' ");
		$this->dbforge->add_field("`deleted` int(1) NOT NULL ");
		$this->dbforge->add_field("`discounts` decimal(10,2) DEFAULT NULL ");
		$this->dbforge->add_field("`type` char(1) DEFAULT '0' ");
		$this->dbforge->add_field("`max_amount_credit` varchar(255) NULL ");
		$this->dbforge->add_key("person_id",TRUE);
		$this->dbforge->create_table("customers", TRUE);
		$this->db->query('ALTER TABLE  `ospos_customers` ENGINE = MyISAM');
		## Create Table ospos_employees
		$this->dbforge->add_field("`person_id` int(10) NOT NULL ");
		$this->dbforge->add_key("person_id",TRUE);
		$this->dbforge->add_field("`username` varchar(255) NOT NULL ");
		$this->dbforge->add_field("`password` varchar(255) NOT NULL ");
		$this->dbforge->add_field("`id_schedule` int(1) NULL DEFAULT '1' ");
		$this->dbforge->add_field("`deleted` int(1) NOT NULL ");
		$this->dbforge->add_field("`lastChatActivity` int(2) NOT NULL ");
		$this->dbforge->add_field("`type_employees` varchar(20) NULL ");
		$this->dbforge->create_table("employees", TRUE);
		$this->db->query('ALTER TABLE  `ospos_employees` ENGINE = MyISAM');
		## Create Table ospos_employees_profile
		$this->dbforge->add_field("`profile_name` varchar(50) NOT NULL ");
		$this->dbforge->add_key("profile_name",TRUE);
		$this->dbforge->add_field("`module_id` varchar(255) NOT NULL ");
		$this->dbforge->add_key("module_id",TRUE);
		$this->dbforge->add_field("`privileges` varchar(100) NULL ");
		$this->dbforge->create_table("employees_profile", TRUE);
		$this->db->query('ALTER TABLE  `ospos_employees_profile` ENGINE = InnoDB');
		## Create Table ospos_employees_schedule
		$this->dbforge->add_field("`id` int(11) NOT NULL auto_increment");
		$this->dbforge->add_key("id",TRUE);
		$this->dbforge->add_field("`employee_id` int(11) NOT NULL ");
		$this->dbforge->add_field("`date` date NOT NULL ");
		$this->dbforge->add_field("`login` time NOT NULL ");
		$this->dbforge->add_field("`logout` time NULL ");
		$this->dbforge->add_field("`location` varchar(20) NULL ");
		$this->dbforge->create_table("employees_schedule", TRUE);
		$this->db->query('ALTER TABLE  `ospos_employees_schedule` ENGINE = InnoDB');
		## Create Table ospos_giftcards
		$this->dbforge->add_field("`giftcard_id` int(11) NOT NULL auto_increment");
		$this->dbforge->add_key("giftcard_id",TRUE);
		$this->dbforge->add_field("`giftcard_number` varchar(25) NOT NULL ");
		$this->dbforge->add_field("`value` double(15,2) NOT NULL ");
		$this->dbforge->add_field("`deleted` int(1) NOT NULL ");
		$this->dbforge->create_table("giftcards", TRUE);
		$this->db->query('ALTER TABLE  `ospos_giftcards` ENGINE = MyISAM');
		## Create Table ospos_inventory
		$this->dbforge->add_field("`trans_id` int(11) NOT NULL auto_increment");
		$this->dbforge->add_key("trans_id",TRUE);
		$this->dbforge->add_field("`trans_items` int(11) NOT NULL ");
		$this->dbforge->add_field("`trans_user` int(11) NOT NULL ");
		$this->dbforge->add_field("`trans_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ");
		$this->dbforge->add_field("`trans_comment` text NOT NULL ");
		$this->dbforge->add_field("`trans_inventory` int(11) NOT NULL ");
		$this->dbforge->create_table("inventory", TRUE);
		$this->db->query('ALTER TABLE  `ospos_inventory` ENGINE = MyISAM');
		## Create Table ospos_item_kit_items
		$this->dbforge->add_field("`item_kit_id` int(11) NOT NULL ");
		$this->dbforge->add_key("item_kit_id",TRUE);
		$this->dbforge->add_field("`item_id` int(11) NOT NULL ");
		$this->dbforge->add_key("item_id",TRUE);
		$this->dbforge->add_field("`quantity` double(15,2) NOT NULL ");
		$this->dbforge->add_key("quantity",TRUE);
		$this->dbforge->create_table("item_kit_items", TRUE);
		$this->db->query('ALTER TABLE  `ospos_item_kit_items` ENGINE = MyISAM');
		## Create Table ospos_item_kits
		$this->dbforge->add_field("`item_kit_id` int(11) NOT NULL auto_increment");
		$this->dbforge->add_key("item_kit_id",TRUE);
		$this->dbforge->add_field("`name` varchar(255) NOT NULL ");
		$this->dbforge->add_field("`description` varchar(255) NOT NULL ");
		$this->dbforge->create_table("item_kits", TRUE);
		$this->db->query('ALTER TABLE  `ospos_item_kits` ENGINE = MyISAM');
		## Create Table ospos_items
		$this->dbforge->add_field("`item_id` int(10) NOT NULL auto_increment");
		$this->dbforge->add_key("item_id",TRUE);
		$this->dbforge->add_field("`name` varchar(255) NOT NULL ");
		$this->dbforge->add_field("`category` varchar(255) NOT NULL ");
		$this->dbforge->add_field("`supplier_id` int(11) NULL ");
		$this->dbforge->add_field("`item_number` varchar(255) NULL ");
		$this->dbforge->add_field("`description` varchar(255) NOT NULL ");
		$this->dbforge->add_field("`cost_price` double(15,2) NOT NULL ");
		$this->dbforge->add_field("`unit_price` double(15,2) NOT NULL ");
		$this->dbforge->add_field("`quantity` double(15,2) NOT NULL DEFAULT '0.00' ");
		$this->dbforge->add_field("`reorder_level` double(15,2) NOT NULL DEFAULT '0.00' ");
		$this->dbforge->add_field("`location` varchar(255) NOT NULL ");
		$this->dbforge->add_field("`allow_alt_description` tinyint(1) NOT NULL ");
		$this->dbforge->add_field("`is_serialized` tinyint(1) NOT NULL ");
		$this->dbforge->add_field("`is_service` tinyint(1) NOT NULL ");
		$this->dbforge->add_field("`is_locked` tinyint(1) NOT NULL ");
		$this->dbforge->add_field("`deleted` tinyint(1) NOT NULL ");
		$this->dbforge->add_field("`broken_quantity` int(15) unsigned NOT NULL ");
		$this->dbforge->add_field("`model_id` int(11) NULL ");
		$this->dbforge->create_table("items", TRUE);
		$this->db->query('ALTER TABLE  `ospos_items` ENGINE = MyISAM');
		## Create Table ospos_items_taxes
		$this->dbforge->add_field("`item_id` int(10) NOT NULL ");
		$this->dbforge->add_key("item_id",TRUE);
		$this->dbforge->add_field("`name` varchar(255) NOT NULL ");
		$this->dbforge->add_key("name",TRUE);
		$this->dbforge->add_field("`percent` double(15,3) NOT NULL ");
		$this->dbforge->add_key("percent",TRUE);
		$this->dbforge->create_table("items_taxes", TRUE);
		$this->db->query('ALTER TABLE  `ospos_items_taxes` ENGINE = MyISAM');
		## Create Table ospos_model
		$this->dbforge->add_field("`model_id` int(11) NOT NULL auto_increment");
		$this->dbforge->add_key("model_id",TRUE);
		$this->dbforge->add_field("`model_name` varchar(50) NOT NULL ");
		$this->dbforge->add_field("`brand_id` int(11) NOT NULL ");
		$this->dbforge->create_table("model", TRUE);
		$this->db->query('ALTER TABLE  `ospos_model` ENGINE = InnoDB');
		## Create Table ospos_modules
		$this->dbforge->add_field("`module_id` varchar(255) NOT NULL ");
		$this->dbforge->add_key("module_id",TRUE);
		$this->dbforge->add_field("`name_lang_key` varchar(255) NOT NULL ");
		$this->dbforge->add_field("`desc_lang_key` varchar(255) NOT NULL ");
		$this->dbforge->add_field("`shortcut` varchar(255) NOT NULL ");
		$this->dbforge->add_field("`sort` int(10) NOT NULL ");
		$this->dbforge->add_field("`options` varchar(100) NULL DEFAULT 'none' ");
		$this->dbforge->create_table("modules", TRUE);
		$this->db->query('ALTER TABLE  `ospos_modules` ENGINE = MyISAM');
		## Create Table ospos_observation_inventories
		$this->dbforge->add_field("`id` int(11) NOT NULL auto_increment");
		$this->dbforge->add_key("id",TRUE);
		$this->dbforge->add_field("`date_register` timestamp NULL ");
		$this->dbforge->add_field("`observation` mediumtext NULL ");
		$this->dbforge->add_field("`person_id` int(10) NULL ");
		$this->dbforge->create_table("observation_inventories", TRUE);
		$this->db->query('ALTER TABLE  `ospos_observation_inventories` ENGINE = InnoDB');
		## Create Table ospos_people
		$this->dbforge->add_field("`person_id` int(10) NOT NULL auto_increment");
		$this->dbforge->add_key("person_id",TRUE);
		$this->dbforge->add_field("`first_name` varchar(255) NOT NULL ");
		$this->dbforge->add_field("`last_name` varchar(255) NOT NULL ");
		$this->dbforge->add_field("`phone_number` varchar(255) NULL ");
		$this->dbforge->add_field("`email` varchar(255) NULL ");
		$this->dbforge->add_field("`address_1` varchar(255) NULL ");
		$this->dbforge->add_field("`address_2` varchar(255) NULL ");
		$this->dbforge->add_field("`city` varchar(255) NULL ");
		$this->dbforge->add_field("`state` varchar(255) NULL ");
		$this->dbforge->add_field("`zip` varchar(255) NULL ");
		$this->dbforge->add_field("`country` varchar(255) NULL ");
		$this->dbforge->add_field("`comments` text NULL ");
		$this->dbforge->create_table("people", TRUE);
		$this->db->query('ALTER TABLE  `ospos_people` ENGINE = MyISAM');
		## Create Table ospos_permissions
		$this->dbforge->add_field("`module_id` varchar(255) NOT NULL ");
		$this->dbforge->add_key("module_id",TRUE);
		$this->dbforge->add_field("`person_id` int(10) NOT NULL ");
		$this->dbforge->add_key("person_id",TRUE);
		$this->dbforge->add_field("`privileges` varchar(100) NULL DEFAULT 'none' ");
		$this->dbforge->create_table("permissions", TRUE);
		$this->db->query('ALTER TABLE  `ospos_permissions` ENGINE = MyISAM');
		## Create Table ospos_receivings
		$this->dbforge->add_field("`receiving_id` int(10) NOT NULL auto_increment");
		$this->dbforge->add_key("receiving_id",TRUE);
		$this->dbforge->add_field("`receiving_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ");
		$this->dbforge->add_field("`supplier_id` int(10) NULL ");
		$this->dbforge->add_field("`employee_id` int(10) NOT NULL ");
		$this->dbforge->add_field("`comment` text NOT NULL ");
		$this->dbforge->add_field("`payment_type` varchar(20) NULL ");
		$this->dbforge->add_field("`payment` double(15,0) NULL ");
		$this->dbforge->create_table("receivings", TRUE);
		$this->db->query('ALTER TABLE  `ospos_receivings` ENGINE = MyISAM');
		## Create Table ospos_receivings_items
		$this->dbforge->add_field("`receiving_id` int(10) NOT NULL ");
		$this->dbforge->add_key("receiving_id",TRUE);
		$this->dbforge->add_field("`item_id` int(10) NOT NULL ");
		$this->dbforge->add_key("item_id",TRUE);
		$this->dbforge->add_field("`line` int(3) NOT NULL ");
		$this->dbforge->add_key("line",TRUE);
		$this->dbforge->add_field("`description` varchar(30) NULL ");
		$this->dbforge->add_field("`serialnumber` varchar(30) NULL ");
		$this->dbforge->add_field("`quantity_purchased` int(10) NOT NULL ");
		$this->dbforge->add_field("`item_cost_price` decimal(15,2) NOT NULL ");
		$this->dbforge->add_field("`item_unit_price` double(15,2) NOT NULL ");
		$this->dbforge->add_field("`discount_percent` int(11) NOT NULL ");
		$this->dbforge->create_table("receivings_items", TRUE);
		$this->db->query('ALTER TABLE  `ospos_receivings_items` ENGINE = MyISAM');
		## Create Table ospos_sales
		$this->dbforge->add_field("`sale_id` int(10) NOT NULL auto_increment");
		$this->dbforge->add_key("sale_id",TRUE);
		$this->dbforge->add_field("`sale_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ");
		$this->dbforge->add_field("`customer_id` int(10) NULL ");
		$this->dbforge->add_field("`employee_id` int(10) NOT NULL ");
		$this->dbforge->add_field("`comment` text NOT NULL ");
		$this->dbforge->add_field("`payment_type` varchar(512) NULL ");
		$this->dbforge->add_field("`mode` tinyint(1) NOT NULL ");
		$this->dbforge->add_field("`status` tinyint(1) NOT NULL DEFAULT '1' ");
		$this->dbforge->create_table("sales", TRUE);
		$this->db->query('ALTER TABLE  `ospos_sales` ENGINE = MyISAM');
		## Create Table ospos_sales_items
		$this->dbforge->add_field("`sale_id` int(10) NOT NULL ");
		$this->dbforge->add_key("sale_id",TRUE);
		$this->dbforge->add_field("`item_id` int(10) NOT NULL ");
		$this->dbforge->add_key("item_id",TRUE);
		$this->dbforge->add_field("`line` int(3) NOT NULL ");
		$this->dbforge->add_key("line",TRUE);
		$this->dbforge->add_field("`service_id` int(11) NULL ");
		$this->dbforge->add_field("`description` varchar(30) NULL ");
		$this->dbforge->add_field("`serialnumber` varchar(30) NULL ");
		$this->dbforge->add_field("`quantity_purchased` double(15,2) NOT NULL DEFAULT '0.00' ");
		$this->dbforge->add_field("`item_cost_price` decimal(15,2) NOT NULL ");
		$this->dbforge->add_field("`item_unit_price` double(15,2) NOT NULL ");
		$this->dbforge->add_field("`discount_percent` int(11) NOT NULL ");
		$this->dbforge->create_table("sales_items", TRUE);
		$this->db->query('ALTER TABLE  `ospos_sales_items` ENGINE = MyISAM');
		## Create Table ospos_sales_items_taxes
		$this->dbforge->add_field("`sale_id` int(10) NOT NULL ");
		$this->dbforge->add_key("sale_id",TRUE);
		$this->dbforge->add_field("`item_id` int(10) NOT NULL ");
		$this->dbforge->add_key("item_id",TRUE);
		$this->dbforge->add_field("`line` int(3) NOT NULL ");
		$this->dbforge->add_key("line",TRUE);
		$this->dbforge->add_field("`name` varchar(255) NOT NULL ");
		$this->dbforge->add_key("name",TRUE);
		$this->dbforge->add_field("`percent` double(15,3) NOT NULL ");
		$this->dbforge->add_key("percent",TRUE);
		$this->dbforge->create_table("sales_items_taxes", TRUE);
		$this->db->query('ALTER TABLE  `ospos_sales_items_taxes` ENGINE = MyISAM');
		## Create Table ospos_sales_payments
		$this->dbforge->add_field("`sale_id` int(10) NOT NULL ");
		$this->dbforge->add_key("sale_id",TRUE);
		$this->dbforge->add_field("`payment_type` varchar(40) NOT NULL ");
		$this->dbforge->add_key("payment_type",TRUE);
		$this->dbforge->add_field("`payment_amount` decimal(15,2) NOT NULL ");
		$this->dbforge->create_table("sales_payments", TRUE);
		$this->db->query('ALTER TABLE  `ospos_sales_payments` ENGINE = MyISAM');
		## Create Table ospos_sales_suspended
		$this->dbforge->add_field("`sale_id` int(10) NOT NULL auto_increment");
		$this->dbforge->add_key("sale_id",TRUE);
		$this->dbforge->add_field("`sale_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ");
		$this->dbforge->add_field("`customer_id` int(10) NULL ");
		$this->dbforge->add_field("`employee_id` int(10) NOT NULL ");
		$this->dbforge->add_field("`comment` text NOT NULL ");
		$this->dbforge->add_field("`payment_type` varchar(512) NULL ");
		$this->dbforge->create_table("sales_suspended", TRUE);
		$this->db->query('ALTER TABLE  `ospos_sales_suspended` ENGINE = MyISAM');
		## Create Table ospos_sales_suspended_items
		$this->dbforge->add_field("`sale_id` int(10) NOT NULL ");
		$this->dbforge->add_key("sale_id",TRUE);
		$this->dbforge->add_field("`item_id` int(10) NOT NULL ");
		$this->dbforge->add_key("item_id",TRUE);
		$this->dbforge->add_field("`line` int(3) NOT NULL ");
		$this->dbforge->add_key("line",TRUE);
		$this->dbforge->add_field("`description` varchar(30) NULL ");
		$this->dbforge->add_field("`serialnumber` varchar(30) NULL ");
		$this->dbforge->add_field("`quantity_purchased` double(15,2) NOT NULL DEFAULT '0.00' ");
		$this->dbforge->add_field("`item_cost_price` decimal(15,2) NOT NULL ");
		$this->dbforge->add_field("`item_unit_price` double(15,2) NOT NULL ");
		$this->dbforge->add_field("`discount_percent` int(11) NOT NULL ");
		$this->dbforge->create_table("sales_suspended_items", TRUE);
		$this->db->query('ALTER TABLE  `ospos_sales_suspended_items` ENGINE = MyISAM');
		## Create Table ospos_sales_suspended_items_taxes
		$this->dbforge->add_field("`sale_id` int(10) NOT NULL ");
		$this->dbforge->add_key("sale_id",TRUE);
		$this->dbforge->add_field("`item_id` int(10) NOT NULL ");
		$this->dbforge->add_key("item_id",TRUE);
		$this->dbforge->add_field("`line` int(3) NOT NULL ");
		$this->dbforge->add_key("line",TRUE);
		$this->dbforge->add_field("`name` varchar(255) NOT NULL ");
		$this->dbforge->add_key("name",TRUE);
		$this->dbforge->add_field("`percent` double(15,3) NOT NULL ");
		$this->dbforge->add_key("percent",TRUE);
		$this->dbforge->create_table("sales_suspended_items_taxes", TRUE);
		$this->db->query('ALTER TABLE  `ospos_sales_suspended_items_taxes` ENGINE = MyISAM');
		## Create Table ospos_sales_suspended_payments
		$this->dbforge->add_field("`sale_id` int(10) NOT NULL ");
		$this->dbforge->add_key("sale_id",TRUE);
		$this->dbforge->add_field("`payment_type` varchar(40) NOT NULL ");
		$this->dbforge->add_key("payment_type",TRUE);
		$this->dbforge->add_field("`payment_amount` decimal(15,2) NOT NULL ");
		$this->dbforge->create_table("sales_suspended_payments", TRUE);
		$this->db->query('ALTER TABLE  `ospos_sales_suspended_payments` ENGINE = MyISAM');
		## Create Table ospos_schedules
		$this->dbforge->add_field("`schedule_id` int(11) NOT NULL auto_increment");
		$this->dbforge->add_key("schedule_id",TRUE);
		$this->dbforge->add_field("`day` varchar(11) NOT NULL ");
		$this->dbforge->add_field("`in` time NOT NULL ");
		$this->dbforge->add_field("`out` time NOT NULL ");
		$this->dbforge->add_field("`person_id` int(11) NOT NULL ");
		$this->dbforge->create_table("schedules", TRUE);
		$this->db->query('ALTER TABLE  `ospos_schedules` ENGINE = MyISAM');
		## Create Table ospos_service_items
		$this->dbforge->add_field("`service_id` int(11) NOT NULL ");
		$this->dbforge->add_key("service_id",TRUE);
		$this->dbforge->add_field("`item_id` int(11) NOT NULL ");
		$this->dbforge->add_key("item_id",TRUE);
		$this->dbforge->add_field("`unit_price` double(15,0) NULL ");
		$this->dbforge->add_field("`is_Kit` char(1) NULL ");
		$this->dbforge->create_table("service_items", TRUE);
		$this->db->query('ALTER TABLE  `ospos_service_items` ENGINE = InnoDB');
		## Create Table ospos_service_log
		$this->dbforge->add_field("`service_id` int(11) NOT NULL auto_increment");
		$this->dbforge->add_key("service_id",TRUE);
		$this->dbforge->add_field("`person_id` int(11) NOT NULL ");
		$this->dbforge->add_field("`serial` varchar(18) NULL ");
		$this->dbforge->add_field("`model_id` int(11) NOT NULL ");
		$this->dbforge->add_field("`date_received` timestamp NULL DEFAULT CURRENT_TIMESTAMP ");
		$this->dbforge->add_field("`date_delivered` datetime NULL ");
		$this->dbforge->add_field("`comments` mediumtext NULL ");
		$this->dbforge->add_field("`status` int(1) NOT NULL DEFAULT '1' ");
		$this->dbforge->add_field("`deleted` int(1) NULL ");
		$this->dbforge->create_table("service_log", TRUE);
		$this->db->query('ALTER TABLE  `ospos_service_log` ENGINE = InnoDB');
		## Create Table ospos_sessions
		$this->dbforge->add_field("`session_id` varchar(40) NOT NULL ");
		$this->dbforge->add_key("session_id",TRUE);
		$this->dbforge->add_field("`ip_address` varchar(16) NOT NULL ");
		$this->dbforge->add_field("`user_agent` varchar(120) NOT NULL ");
		$this->dbforge->add_field("`last_activity` int(10) unsigned NOT NULL ");
		$this->dbforge->add_field("`user_data` text NULL ");
		$this->dbforge->create_table("sessions", TRUE);
		$this->db->query('ALTER TABLE  `ospos_sessions` ENGINE = MyISAM');
		## Create Table ospos_suppliers
		$this->dbforge->add_field("`person_id` int(10) NOT NULL ");
		$this->dbforge->add_key("person_id",TRUE);
		$this->dbforge->add_field("`company_name` varchar(255) NOT NULL ");
		$this->dbforge->add_field("`account_number` varchar(255) NULL ");
		$this->dbforge->add_field("`deleted` int(1) NOT NULL ");
		$this->dbforge->create_table("suppliers", TRUE);
		$this->db->query('ALTER TABLE  `ospos_suppliers` ENGINE = MyISAM');
		## Create Table ospos_transfer_items
		$this->dbforge->add_field("`id` int(10) NOT NULL auto_increment");
		$this->dbforge->add_key("id",TRUE);
		$this->dbforge->add_field("`transfer_id` int(11) NOT NULL ");
		$this->dbforge->add_field("`item_id` int(10) NOT NULL ");
		$this->dbforge->add_field("`quantity_purchased` double(15,0) NOT NULL DEFAULT '1' ");
		$this->dbforge->add_field("`description` varchar(30) NULL ");
		$this->dbforge->add_field("`serialnumber` varchar(30) NULL ");
		$this->dbforge->add_field("`line` int(3) NOT NULL ");
		$this->dbforge->add_field("`item_cost_price` decimal(15,2) NOT NULL ");
		$this->dbforge->add_field("`item_unit_price` double(15,2) NOT NULL ");
		$this->dbforge->add_field("`discount_percent` int(11) NOT NULL ");
		$this->dbforge->create_table("transfer_items", TRUE);
		$this->db->query('ALTER TABLE  `ospos_transfer_items` ENGINE = InnoDB');
		## Create Table ospos_transfers
		$this->dbforge->add_field("`transfer_id` int(11) unsigned NOT NULL auto_increment");
		$this->dbforge->add_key("transfer_id",TRUE);
		$this->dbforge->add_field("`sender` varchar(20) NOT NULL ");
		$this->dbforge->add_field("`receiver` varchar(20) NOT NULL ");
		$this->dbforge->add_field("`date` date NULL ");
		$this->dbforge->add_field("`status` tinyint(1) NOT NULL DEFAULT '1' ");
		$this->dbforge->add_field("`comment` text NOT NULL ");
		$this->dbforge->add_field("`payment_type` varchar(512) NOT NULL ");
		$this->dbforge->create_table("transfers", TRUE);
		$this->db->query('ALTER TABLE  `ospos_transfers` ENGINE = InnoDB');
		## Create Table ospos_credits
		$this->dbforge->add_field("`credit_id` int(11) unsigned NOT NULL auto_increment");
		$this->dbforge->add_key("credit_id",TRUE);
		$this->dbforge->add_field("`person_custo_id` int(11) NOT NULL ");
		$this->dbforge->add_field("`transfer_id` int(11) NOT NULL ");
		$this->dbforge->add_field("`person_emplo_id` int(11) NULL ");
		$this->dbforge->add_field("`payment_amount` decimal(15,2) NOT NULL ");
		$this->dbforge->add_field("`payment_period` varchar(255) NOT NULL ");
		$this->dbforge->add_field("`pay_day` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ");
		$this->dbforge->add_field("`status` tinyint(1) NOT NULL DEFAULT '1' ");
		$this->dbforge->create_table("ospos_credits", TRUE);
		$this->db->query('ALTER TABLE  `ospos_credits` ENGINE = InnoDB');
	 }

	public function down($table=false)	{
		### Drop table ospos_app_config ##
		$this->dbforge->drop_table("app_config", TRUE);
		### Drop table ospos_brand ##
		$this->dbforge->drop_table("brand", TRUE);
		### Drop table ospos_customers ##
		$this->dbforge->drop_table("customers", TRUE);
		### Drop table ospos_employees ##
		$this->dbforge->drop_table("employees", TRUE);
		### Drop table ospos_employees_profile ##
		$this->dbforge->drop_table("employees_profile", TRUE);
		### Drop table ospos_employees_schedule ##
		$this->dbforge->drop_table("employees_schedule", TRUE);
		### Drop table ospos_giftcards ##
		$this->dbforge->drop_table("giftcards", TRUE);
		### Drop table ospos_inventory ##
		$this->dbforge->drop_table("inventory", TRUE);
		### Drop table ospos_item_kit_items ##
		$this->dbforge->drop_table("item_kit_items", TRUE);
		### Drop table ospos_item_kits ##
		$this->dbforge->drop_table("item_kits", TRUE);
		### Drop table ospos_items ##
		$this->dbforge->drop_table("items", TRUE);
		### Drop table ospos_items_taxes ##
		$this->dbforge->drop_table("items_taxes", TRUE);
		### Drop table ospos_model ##
		$this->dbforge->drop_table("model", TRUE);
		### Drop table ospos_modules ##
		$this->dbforge->drop_table("modules", TRUE);
		### Drop table ospos_observation_inventories ##
		$this->dbforge->drop_table("observation_inventories", TRUE);
		### Drop table ospos_people ##
		$this->dbforge->drop_table("people", TRUE);
		### Drop table ospos_permissions ##
		$this->dbforge->drop_table("permissions", TRUE);
		### Drop table ospos_receivings ##
		$this->dbforge->drop_table("receivings", TRUE);
		### Drop table ospos_receivings_items ##
		$this->dbforge->drop_table("receivings_items", TRUE);
		### Drop table ospos_sales ##
		$this->dbforge->drop_table("sales", TRUE);
		### Drop table ospos_sales_items ##
		$this->dbforge->drop_table("sales_items", TRUE);
		### Drop table ospos_sales_items_taxes ##
		$this->dbforge->drop_table("sales_items_taxes", TRUE);
		### Drop table ospos_sales_payments ##
		$this->dbforge->drop_table("sales_payments", TRUE);
		### Drop table ospos_sales_suspended ##
		$this->dbforge->drop_table("sales_suspended", TRUE);
		### Drop table ospos_sales_suspended_items ##
		$this->dbforge->drop_table("sales_suspended_items", TRUE);
		### Drop table ospos_sales_suspended_items_taxes ##
		$this->dbforge->drop_table("sales_suspended_items_taxes", TRUE);
		### Drop table ospos_sales_suspended_payments ##
		$this->dbforge->drop_table("sales_suspended_payments", TRUE);
		### Drop table ospos_schedules ##
		$this->dbforge->drop_table("schedules", TRUE);
		### Drop table ospos_service_items ##
		$this->dbforge->drop_table("service_items", TRUE);
		### Drop table ospos_service_log ##
		$this->dbforge->drop_table("service_log", TRUE);
		### Drop table ospos_sessions ##
		$this->dbforge->drop_table("sessions", TRUE);
		### Drop table ospos_suppliers ##
		$this->dbforge->drop_table("suppliers", TRUE);
		### Drop table ospos_transfer_items ##
		$this->dbforge->drop_table("transfer_items", TRUE);
		### Drop table ospos_transfers ##
		$this->dbforge->drop_table("transfers", TRUE);

	}
}