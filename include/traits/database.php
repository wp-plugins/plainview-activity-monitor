<?php

namespace plainview\wordpress\activity_monitor\traits;

use \plainview\wordpress\activity_monitor\actions\prune_activities;
use \plainview\wordpress\activity_monitor\database\activity;

/**
	@brief		SQL-related callbacks and what not.
	@since		2014-05-03 09:04:32
**/
trait database
{
	/**
		@brief		Constructor this these related traits.
		@since		2014-05-03 09:06:33
	**/
	public function _database_construct()
	{
		$this->add_action( 'plainview_activity_monitor_list_activities' );
		$this->add_action( 'plainview_activity_monitor_list_distinct_values' );
		$this->add_action( 'plainview_activity_monitor_log_hook', 20 );
		$this->add_action( 'plainview_activity_monitor_prune_activities' );
		$this->add_action( 'plainview_activity_monitor_remove_activities' );
	}

	/**
		@brief		Returns the complete, prefixed name of a database table.
		@since		2014-05-03 14:59:02
	**/
	public static function get_table_name( $table = 'activities' )
	{
		$class = static::instance();
		$r = $class->wpdb->base_prefix;

		switch( $table )
		{
			case 'activities':
			default:
				$r .= 'pv_am_activities';
		}

		return $r;
	}

	/**
		@brief		Return a collection of activities.
		@since		2014-05-03 19:33:02
	**/
	public function plainview_activity_monitor_list_activities( $action )
	{
		$order = '';
		$limit = '';

		if ( $action->count )
			$select = 'COUNT(*) as rows';
		else
		{
			if ( $action->per_page > 0 )
				$limit = sprintf( 'LIMIT %s,%s', ( $action->page - 1 ) * $action->per_page, $action->per_page );

			if ( ! $action->ascending )
				$order = 'ORDER BY id DESC';

			$select = '*';
		}

		$where = [];

		foreach( [
				'blog_id' => 'blog_id',
				'hook' => 'hook',
				'ip' => 'ip',
				'user_id' => 'user_id',
			] as $type => $column )
		{
			$property = $type . 's_to_exclude';
			if ( $action->$property->count() > 0 )
				$where[] = sprintf( "`%s` NOT IN ('%s')", $column, implode( "','", $action->$property->to_array() ) );
			$property = $type . 's_to_include';
			if ( $action->$property->count() > 0 )
				$where[] = sprintf( "`%s` IN ('%s')", $column, implode( "','", $action->$property->to_array() ) );
		}

		if ( $action->from_date !== null )
			$where[] = sprintf( "`dt_created` >= '%s'", $action->from_date );

		if ( $action->until_date !== null )
			$where[] = sprintf( "`dt_created` <= '%s'", $action->until_date );

		if ( count( $where ) > 0 )
			$where = sprintf( 'WHERE %s', implode( 'AND', $where ) );
		else
			$where = '';

		$query = sprintf(
			'SELECT %s FROM %s
			%s
			%s
			%s',
			$select,
			static::get_table_name( 'activities' ),
			$where,
			$order,
			$limit
		);

		$this->debug( 'Running query: %s', $query );
		$results = $this->query( $query );

		if ( $action->count )
			$action->result = $results[ 0 ][ 'rows' ];
		else
		{
			$action->result = activity::sqls( $results );
			// Convert the data array to an object.
			foreach( $action->result as $index => $result )
				$action->result[ $index ]->data = (object)$action->result[ $index ]->data;
		}
	}

	/**
		@brief		Fill the action with collections of the distinct values in the DB.
		@since		2014-05-04 10:04:14
	**/
	public function plainview_activity_monitor_list_distinct_values( $action )
	{
		$query = sprintf( "SELECT DISTINCT blog_id FROM `%s` ORDER BY blog_id", static::get_table_name( 'activities' ) );
		$this->debug( 'Running query: %s', $query );
		$results = $this->query( $query );
		foreach( $results as $result )
			$action->blog_ids->append( $result[ 'blog_id' ] );

		$query = sprintf( "SELECT DISTINCT hook FROM `%s` ORDER BY hook", static::get_table_name( 'activities' ) );
		$this->debug( 'Running query: %s', $query );
		$results = $this->query( $query );
		foreach( $results as $result )
			$action->hooks->append( $result[ 'hook' ] );

		$query = sprintf( "SELECT DISTINCT ip FROM `%s` ORDER BY ip", static::get_table_name( 'activities' ) );
		$this->debug( 'Running query: %s', $query );
		$results = $this->query( $query );
		foreach( $results as $result )
			$action->ips->append( $result[ 'ip' ] );

		$query = sprintf( "SELECT DISTINCT user_id FROM `%s` ORDER BY user_id", static::get_table_name( 'activities' ) );
		$this->debug( 'Running query: %s', $query );
		$results = $this->query( $query );
		foreach( $results as $result )
			$action->user_ids->append( $result[ 'user_id' ] );
	}

	/**
		@brief		Adds this hook to the log.
		@since		2014-05-03 18:48:57
	**/
	public function plainview_activity_monitor_log_hook( $action )
	{
		$action->create_activity()->db_update();

		// We need a 5% chance of pruning.
		if ( rand( 1, 100 ) > 5 )
			return;

		// And now we prune.
		$prune_activities = new prune_activities;
		$prune_activities->count = $this->get_site_option( 'activities_in_database' );
		$prune_activities->execute();
	}

	/**
		@brief		Keep only a certain amount of activities in the database.
		@since		2014-07-08 12:08:40
	**/
	public function plainview_activity_monitor_prune_activities( $action )
	{
		$query = sprintf( "SELECT ( `id` ) FROM `%s` ORDER BY `id` DESC LIMIT %s,1",
			static::get_table_name( 'activities' ),
			$action->count
		);
		$this->debug( 'Running query: %s', $query );
		$results = $this->query( $query );
		$id = reset( $results );
		$id = $id[ 'id' ];
		$query = sprintf( "DELETE FROM `%s` WHERE `id` <= '%s'",
			static::get_table_name( 'activities' ),
			$id
		);
		$this->debug( 'Running query: %s', $query );
		$results = $this->query( $query );
	}

	/**
		@brief		Deletes activities from the DB.
		@since		2014-05-04 01:07:47
	**/
	public function plainview_activity_monitor_remove_activities( $action )
	{
		$query = sprintf( 'DELETE FROM %s WHERE id in (%s)', static::get_table_name( 'activities' ), implode( ',', $action->ids->to_array() ) );
		$this->debug( 'Running query: %s', $query );
		$this->query( $query );
	}
}
