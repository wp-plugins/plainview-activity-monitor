<?php

namespace plainview\wordpress\activity_monitor\traits\menus;

use \plainview\wordpress\activity_monitor\actions\get_activity_description;
use \plainview\wordpress\activity_monitor\actions\list_activities;
use \plainview\wordpress\activity_monitor\actions\list_distinct_values;
use \plainview\wordpress\activity_monitor\actions\remove_activities;
use \plainview\wordpress\activity_monitor\database\filters_settings;
use \plainview\sdk\collections\collection;

trait overview
{
	/**
		@brief		Display activities.
		@since		2014-05-03 20:22:31
	**/
	public function menu_activities_display( $list_activities )
	{
		$form = $this->form2();
		$r = '';
		$table = $this->table();
		$table->css_class( 'activity_monitor_activities' );

		$row = $table->head()->row();
		$table->bulk_actions()
			->form( $form )
			->add( $this->_( 'Delete' ), 'delete' )
			->cb( $row );

		if ( $form->is_posting() )
		{
			$form->post();
			$ids = $table->bulk_actions()->get_rows();
			if ( $table->bulk_actions()->pressed() )
				switch ( $table->bulk_actions()->get_action() )
				{
					case 'delete':
						$remove_activities = new remove_activities;
						foreach( $ids as $id )
							$remove_activities->ids->append( $id );
						$remove_activities->execute();
						break;
				}
		}

		$filters_settings = filters_settings::load();
		foreach( list_activities::get_keys() as $key )
			foreach( $filters_settings->get_filter( $key ) as $value )
				$list_activities->$key->append( $value );

		// Are we displaying local activities? Then remove the blog id column.
		if ( ! isset( $list_activities->global ) )
			$filters_settings->get( 'table_columns' )->forget( 'blog_id' );

		// Retrieve a count first.
		$list_activities->count = true;
		$list_activities->execute();
		$list_activities->count = false;
		$count = $list_activities->result;

		// And now retrieve the activities themselves.
		$list_activities->per_page = $this->get_site_option( 'per_page');
		$max_pages = floor( $count / $list_activities->per_page );
		$page = ( isset( $_GET[ 'paged' ] ) ? intval( $_GET[ 'paged' ] ) : 1 );
		$list_activities->page = $this->minmax($page, 1, $max_pages);

		$list_activities->execute();
		$activities = $list_activities->result;

		$page_links = paginate_links( [
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'prev_text' => '&laquo;',
			'next_text' => '&raquo;',
			'current' => $page,
			'total' => $max_pages,
		] );

		if ( $page_links )
			$page_links = '<div style="width: 50%; float: right;" class="tablenav"><div class="tablenav-pages">' . $page_links . '</div></div>';

		if ( $filters_settings->display_table_column( 'dt_created' ) )
			$row->th( 'dt_created' )->text_( 'Timestamp' );

		if ( $filters_settings->display_table_column( 'blog_id' ) )
			$row->th( 'blog_id' )->text_( 'Blog' );

		if ( $filters_settings->display_table_column( 'hook' ) )
			$row->th( 'hook' )->text_( 'Hook' );

		if ( $filters_settings->display_table_column( 'user_id' ) )
			$row->th( 'user_id' )->text_( 'User' );

		if ( $filters_settings->display_table_column( 'ip' ) )
			$row->th( 'ip' )->text_( 'IP' );

		if ( $filters_settings->display_table_column( 'description' ) )
			$row->th( 'description' )->text_( 'Description' );

		foreach( $activities as $activity )
		{
			$activity->data = (object)$activity->data;

			$row = $table->body()->row();
			$row->css_class( $activity->hook );

			$table->bulk_actions()->cb( $row, $activity->id );

			if ( $filters_settings->display_table_column( 'dt_created' ) )
				$row->td( 'dt_created' )->text( $activity->dt_created );

			if ( $filters_settings->display_table_column( 'blog_id' ) )
			{
				$blog_id = $activity->blog_id;
				$blogname = $this->cache()->blog_html( $blog_id );
				$row->td( 'blog_id' )->text( $blogname );
			}

			if ( $filters_settings->display_table_column( 'hook' ) )
				$row->td( 'hook' )->text( $activity->hook );

			if ( $filters_settings->display_table_column( 'user_id' ) )
			{
				$user_id = $activity->user_id;
				$username = $this->cache()->user_html( $user_id );
				$row->td( 'user_id' )->text( $username );
			}

			if ( $filters_settings->display_table_column( 'ip' ) )
				$row->td( 'ip' )->text( long2ip( $activity->ip ) );

			if ( $filters_settings->display_table_column( 'description' ) )
			{
				$get_activity_description = new get_activity_description;
				$get_activity_description->activity = $activity;
				$get_activity_description->execute();
				$row->td()->text( $get_activity_description->data );
			}
		}

		$r .= $page_links;
		$r .= $form->open_tag();

		$count = $filters_settings->get_active_filter_count();
		if ( $count > 0 )
			if ( $count == 1 )
				$r .= $this->p_( 'You have 1 active filter.' );
			else
				$r .= $this->p_( 'You have %s active filters.', $count );
		$r .= $table;
		$r .= $form->close_tag();
		$r .= $page_links;

		echo $r;
	}

	/**
		@brief		Allow the user to filter out activities.
		@since		2014-05-04 09:55:14
	**/
	public function menu_activity_filters()
	{
		$form = $this->form2();
		$r = $this->p_( 'The settings below will display only the activities that match the filter settings. The settings are saved per-user in the Wordpress user meta table.' );

		$filters_settings = filters_settings::load();

		$fs = $form->fieldset( 'fs_table_columns' );
		$fs->legend->label_( 'Table columns' );

		$fs->select( 'table_columns' )
			->description( 'Which columns in the activities table do you wish to see?' )
			->label( 'Visible columns' )
			->options( filters_settings::get_table_column_options() )
			->multiple()
			->value( $filters_settings->get_table_columns() )
			->autosize();

		$list_distinct_values = new list_distinct_values;
		$list_distinct_values->execute();

		// Blogs

		$fs = $form->fieldset( 'fs_blog_ids' );
		$fs->legend->label_( 'Blogs' );

		$blog_options = [];
		foreach( $list_distinct_values->as_options( 'blog_ids' ) as $blog_id )
		{
			if ( $this->is_network )
				switch_to_blog( $blog_id );
			$blog_name = get_bloginfo( 'blogname' );
			if ( ! $blog_name )
				$blog_name = $blog_id . ' (deleted)';
			else
			{
				if ( $this->is_network )
					restore_current_blog();
			}
			$blog_options[ $blog_name ] = $blog_id;
		}
		ksort( $blog_options );

		$fs->select( 'blog_ids_to_exclude' )
			->description_( 'Do not display the selected blogs in the global view.' )
			->label_( 'Hide blogs' )
			->multiple()
			->options( $blog_options )
			->size( 10 )
			->value( $filters_settings->get_filter( 'blog_ids_to_exclude' ) );

		$fs->select( 'blog_ids_to_include' )
			->description_( 'Display the selected blogs in the global view.' )
			->label_( 'Display blogs' )
			->multiple()
			->options( $blog_options )
			->size( 10 )
			->value( $filters_settings->get_filter( 'blog_ids_to_include' ) );

		// Hooks

		$fs = $form->fieldset( 'fs_hooks' );
		$fs->legend->label_( 'Hooks' );

		$fs->select( 'hooks_to_exclude' )
			->description_( 'Do not display the selected hooks.' )
			->label_( 'Hide hooks' )
			->multiple()
			->options( $list_distinct_values->as_options( 'hooks' ) )
			->size( 10 )
			->value( $filters_settings->get_filter( 'hooks_to_exclude' ) );

		$fs->select( 'hooks_to_include' )
			->description_( 'Display the selected hooks.' )
			->label_( 'Display hooks' )
			->multiple()
			->options( $list_distinct_values->as_options( 'hooks' ) )
			->size( 10 )
			->value( $filters_settings->get_filter( 'hooks_to_include' ) );

		// IPs

		$fs = $form->fieldset( 'fs_ips' );
		$fs->legend->label_( 'IP addresses' );

		$ips = [];
		foreach( $list_distinct_values->as_options( 'ips' ) as $ip )
			$ips[ long2ip( $ip ) ] = $ip;

		$fs->select( 'ips_to_exclude' )
			->description_( 'Do not display the selected IP addresses.' )
			->label_( 'Hide IPs' )
			->multiple()
			->options( $ips )
			->size( 10 )
			->value( $filters_settings->get_filter( 'ips_to_exclude' ) );

		$fs->select( 'ips_to_include' )
			->description_( 'Display the selected IP addresses.' )
			->label_( 'Display IPs' )
			->multiple()
			->options( $ips )
			->size( 10 )
			->value( $filters_settings->get_filter( 'ips_to_include' ) );

		// User IDs

		$user_ids = [];
		foreach( $list_distinct_values->as_options( 'user_ids' ) as $user_id )
		{
			if ( $user_id > 0 )
			{
				$user_info = get_userdata( $user_id );
				if ( ! $user_info )
					$user_ids[ $user_id . ' (deleted)' ] = $user_id;
				else
					$user_ids[ $user_info->data->user_login ] = $user_id;
			}
			else
			{
				$user_ids[ 'Guest' ] = 0;
			}
		}
		ksort( $user_ids );

		$fs = $form->fieldset( 'fs_user_ids' );
		$fs->legend->label_( 'Users' );

		$fs->select( 'user_ids_to_exclude' )
			->description_( 'Do not display the selected users.' )
			->label_( 'Hide users' )
			->multiple()
			->options( $user_ids )
			->size( 10 )
			->value( $filters_settings->get_filter( 'user_ids_to_exclude' ) );

		$fs->select( 'user_ids_to_include' )
			->description_( 'Display the selected users.' )
			->label_( 'Display users' )
			->multiple()
			->options( $user_ids )
			->size( 10 )
			->value( $filters_settings->get_filter( 'user_ids_to_include' ) );

		// Save!

		$form->primary_button( 'save' )
			->value_( 'Save your filter settings' );

		$form->secondary_button( 'delete' )
			->value_( 'Clear your filter settings' );

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
				if ( $form->input( 'delete' )->pressed() )
				{
					filters_settings::delete();
					$this->message_( 'Your filter settings have been cleared. Revisit the page to see that the filters are cleared.' );
				}

				if ( $form->input( 'save' )->pressed() )
				{
					foreach( list_activities::get_keys() as $key )
						$filters_settings->set_filter( $key, $form->input( $key )->get_post_value() );

					$table_columns = $form->input( 'table_columns' )->get_post_value();
					$filters_settings->set_table_columns( $table_columns );

					$filters_settings->save();
					$this->message_( 'Your filter settings have been saved.' );
				}
			}
		}

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		Show the global activity.
		@since		2014-04-27 14:15:14
	**/
	public function menu_activity_global()
	{
		$list_activities = new list_activities;
		$list_activities->global = true;
		echo $this->menu_activities_display( $list_activities );
	}

	/**
		@brief		Show the local activity.
		@since		2014-04-27 14:15:14
	**/
	public function menu_activity_local()
	{
		$list_activities = new list_activities;
		$list_activities->blog_ids_to_include->append( get_current_blog_id() );
		echo $this->menu_activities_display( $list_activities );
	}

	/**
		@brief		Various tools.
		@since		2014-05-04 10:41:51
	**/
	public function menu_activity_tools()
	{
		$r = '';

		// IP converter
		$form = $this->form2();

		$fs = $form->fieldset( 'fs_ip' );
		$fs->legend->label_( 'IP tools' );

		$fs->text( 'ip' )
			->label_( 'IP or integer' )
			->required()
			->size( 15, 15 );

		$fs->markup( 'markup_convert' )
			->markup( 'The convert button will convert the IP address or integer to its equivalent integer or IP address.' );

		$fs->secondary_button( 'convert' )
			->value( 'Convert' );

		$fs->markup( 'markup_lookup' )
			->markup( 'The lookup button will try to resolve an IP address to a host name. If dig is installed on the webserver it will also be used for the lookup.' );

		$fs->secondary_button( 'lookup' )
			->value( 'Lookup' );

		if ( $form->is_posting() )
		{
			$form->post()->use_post_value();

			$ip = $fs->input( 'ip' )->get_filtered_post_value();
			$long = $ip;
			$is_ip = ( strpos( $ip, '.' ) !== false  );
			if ( $is_ip )
				$long = ip2long( $ip );
			else
				$ip = long2ip( $ip );

			if ( $fs->input( 'convert' )->pressed() )
			{
				if ( $is_ip )
					$message = $this->p_( 'The integer value of this IP address %s is <strong>%s</strong>.', $ip, $long );
				else
					$message = $this->p_( 'The IP address of the integer %s is <strong>%s</strong>.', $long, $ip );
			}

			if ( $fs->input( 'lookup' )->pressed() )
			{
				$address = gethostbyaddr( $ip );
				$message = $this->p_( 'The IP address %s resolves to <strong>%s</strong>.', $ip, $address );

				$output = '';
				exec( 'dig -x ' . $ip, $output );
				if ( count( $output ) > 0 )
				{
					$output = array_filter( $output );
					$output = implode( "\n", $output );
					$message .= $this->p_( 'Output from dig: %s', $this->p( $output ) );
				}
			}

			$this->message( $message );
		}

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	public function menu_overview_tabs()
	{
		$this->load_language();

		$tabs = $this->tabs();

		if ( $this->is_network && is_super_admin() )
			$tabs->tab( 'activity_global' )
				->callback_this( 'menu_activity_global' )
				->heading_( 'Activity on all blogs' )
				->name_( 'Global activity' );

		$tabs->tab( 'activity_local' )
			->callback_this( 'menu_activity_local' )
			->heading_( 'Activity on %s', get_bloginfo( 'blogname' ) )
			->name_( 'Local activity' );

		$tabs->tab( 'activity_filters' )
			->callback_this( 'menu_activity_filters' )
			->name_( 'Filters' );

		$tabs->tab( 'activity_tools' )
			->callback_this( 'menu_activity_tools' )
			->name_( 'Tools' );

		// Allow other plugins to add to our tabs.
		$action = new \plainview\wordpress\activity_monitor\actions\admin_menu_tabs;
		$action->tabs = $tabs;
		$action->execute();

		echo $tabs;
	}
}