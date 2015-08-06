<?php

namespace plainview\wordpress\activity_monitor\traits\menus;

trait base
{
	use admin;
	use plugin_pack;
	use overview;

	/**
		@brief		Constructor
		@since		2014-05-03 09:05:24
	**/
	public function _admin_menu_construct()
	{
		$this->add_action( 'admin_menu' );
	}

	public function admin_menu()
	{
		if ( $this->is_network )
		{
			if ( ! is_super_admin() )
				return;
		}
		else
			if ( ! $this->user_has_roles( 'administrator' ) )
				return;

		$this->load_language();

		add_menu_page(
			$this->_( 'Activity monitor' ),
			$this->_( 'Activity monitor' ),
			'manage_options',
			'plainview_activity_monitor',
			[ &$this, 'menu_tabs' ]
		);

		$this->add_submenu_page(
			'plainview_activity_monitor',
			$this->_( 'Plugin pack info' ),
			$this->_( 'Plugin pack information' ),
			'manage_options',
			'plainview_activity_plugin_pack_info',
			[ &$this, 'menu_plugin_pack_info' ]
		);

		$action = new \plainview\wordpress\activity_monitor\actions\admin_menu;
		$action->execute();

		$this->add_submenu_pages();
	}
}