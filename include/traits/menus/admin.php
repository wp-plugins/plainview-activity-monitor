<?php

namespace plainview\wordpress\activity_monitor\traits\menus;

use \plainview\wordpress\activity_monitor\actions\list_activities;
use \plainview\wordpress\activity_monitor\actions\prune_activities;

trait admin
{
	/**
		@brief		Select which hooks are to be logged.
		@since		2014-05-03 13:46:40
	**/
	public function menu_admin_hooks()
	{
		$form = $this->form2();
		$r = '';

		$action = new \plainview\wordpress\activity_monitor\actions\manifest_hooks;
		$action->execute();
		$hooks = $action->hooks;
		$action->sort();

		$logged_hooks = $this->get_site_option( 'logged_hooks', [] );

		$table = $this->table();
		$table->css_class( 'plugins' );		// We want to use the coloring.
		$row = $table->head()->row();
		$table->bulk_actions()
			->form( $form )
			->add( $this->_( 'Activate' ), 'activate' )
			->add( $this->_( 'Deactivate' ), 'deactivate' )
			->cb( $row );
		$row->th( 'hook' )->text_( 'Hook' );
		$row->th( 'vendor' )->text_( 'Vendor' );
		$row->th( 'description' )->text_( 'Description' );
		if ( $this->debugging() )
			$row->th( 'debug' )->text_( 'Debug info' );

		if ( $form->is_posting() )
		{
			$form->post();

			if ( $table->bulk_actions()->pressed() )
			{
				$ids = $table->bulk_actions()->get_rows();

				// Add all of the current hooks.
				switch ( $table->bulk_actions()->get_action() )
				{
					case 'activate':
						// Find the hook with these classmaps
						foreach( $ids as $id )
							foreach( $hooks as $hook )
							{
								if ( $hook->get_id() != $id )
									continue;
								$logged_hooks[ $hook->get_classmap() ] = $hook->get_classmap();
							}
					break;
					case 'deactivate':
						// Find the hook with these classmaps
						foreach( $ids as $id )
							foreach( $hooks as $hook )
							{
								if ( $hook->get_id() != $id )
									continue;
								unset( $logged_hooks[ $hook->get_classmap() ] );
							}
					break;
				}
				$this->update_site_option( 'logged_hooks', $logged_hooks );
			}

			$this->message_( 'The list of logged hooks has been saved.' );
		}

		foreach( $action->hooks as $hook )
		{
			$row = $table->body()->row();
			$bulk_id = $hook->get_id();
			$table->bulk_actions()->cb( $row, $bulk_id );
			$row->td( 'hook' )
				->css_class( 'plugin-title' )
				->text( $hook->get_hook() );

			$classmap = $hook->get_classmap();
			if ( isset( $logged_hooks[ $classmap ]  ) )
				$row->css_class( 'active' );
			else
				$row->css_class( 'inactive' );

			$row->td( 'vendor' )->text( $hook->get_vendor() );
			$row->td( 'description' )->text( $hook->get_description() );

			if ( $this->debugging() )
			{
				$debug = [];
				$debug[ 'Priority' ] = $hook->get_priority();
				$debug[ 'Parameter count' ] = $hook->get_parameter_count();
				$text = [];
				foreach( $debug as $key => $value )
					$text[] = sprintf( '<em>%s</em> %s', $key, $value );
				$row->td( 'debug' )->text( implode( '<br/>', $text ) );
			}
		}

		$r .= $form->open_tag();
		$r .= $table;
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		$this->debug( 'The logged_hooks setting is: %s', $logged_hooks );

		echo $r;
	}

	/**
		@brief		Display the settings tab.
		@since		2014-04-27 14:50:35
	**/
	public function menu_admin_settings()
	{
		$form = $this->form2();
		$r = '';

		$fs = $form->fieldset( 'fs_database' );
		$fs->legend->label_( 'Database' );

		// Get an activity count.
		$list_activities = new list_activities;
		$list_activities->count = true;
		$list_activities->execute();

		$fs->number( 'activities_in_database' )
			->description_( 'How many activities to store in the database. There are currently %s activities stored.', $list_activities->result )
			->label_( 'Activities in database' )
			->min( 1 )
			->required()
			->size( 5 )
			->value( $this->get_site_option( 'activities_in_database' ) );
		$fs->markup( 'mu_activities_in_database' )
			->p_( 'This value is dependent on how much activity there is on your blog and how much history you wish to keep. Updating this value cleans the database. There is also a 5% chance that the database will be cleaned up when a new activity is logged.' );

		$fs = $form->fieldset( 'fs_display' );
		$fs->legend->label_( 'Display' );

		$fs->number( 'per_page' )
			->description_( 'How many items to show per table page.' )
			->label_( 'Items per page' )
			->min( 1, 10000 )
			->required()
			->size( 5, 5 )
			->value( $this->get_site_option( 'per_page' ) );

		$this->add_debug_settings_to_form( $form );

		$form->primary_button( 'save' )
			->value_( 'Save settings' );

		if ( $form->is_posting() )
		{
			$form->post();
			$form->use_post_values();

			if ( ! $form->validates() )
			{
				foreach( $form->get_validation_errors() as $error )
					$this->error( $error );
			}
			else
			{
				$this->save_debug_settings_from_form( $form );
				$this->update_site_option( 'per_page', $form->input( 'per_page' )->get_filtered_post_value() );
				$activities_in_database = $form->input( 'activities_in_database' )->get_filtered_post_value();
				$this->update_site_option( 'activities_in_database', $activities_in_database );

				$prune_activities = new prune_activities;
				$prune_activities->count = $activities_in_database;
				$prune_activities->execute();

				$this->message_( 'The settings have been saved.' );
			}
		}

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		$object = new \ReflectionObject( new \plainview\sdk\wordpress\base );
		$r .= $this->p_( 'Plainview SDK: %s from %s',
			$this->sdk_version,
			$object->getFilename()
		);

		echo $r;
	}

	public function menu_admin_tabs()
	{
		$this->load_language();

		$tabs = $this->tabs();

		$tabs->tab( 'settings' )
			->callback_this( 'menu_admin_settings' )
			->name_( 'Settings' );

		$tabs->tab( 'hooks' )
			->callback_this( 'menu_admin_hooks' )
			->heading_( 'Logged hooks' )
			->name_( 'Hooks' );

		$tabs->tab( 'uninstall' )
			->callback_this( 'admin_uninstall' )
			->name_( 'Uninstall' );

		echo $tabs;
	}
}