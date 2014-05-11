<?php

namespace plainview\wordpress\activity_monitor\traits\menus;

trait base
{
	use admin;
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
		if ( ! $this->role_at_least( 'administrator' ) )
			return;

		$this->load_language();

		add_menu_page(
			$this->_( 'Activity monitor' ),
			$this->_( 'Activity monitor' ),
			'manage_options',
			'plainview_activity_monitor',
			[ &$this, 'menu_overview_tabs' ]
		);

		$this->add_submenu_page(
			'plainview_activity_monitor',
			$this->_( 'Admin settings' ),
			$this->_( 'Admin settings' ),
			'manage_options',
			'plainview_activity_monitor_admin',
			[ &$this, 'menu_admin_tabs' ]
		);

		$action = new \plainview\wordpress\activity_monitor\actions\admin_menu;
		$action->execute();

		$this->add_submenu_pages();
	}
}