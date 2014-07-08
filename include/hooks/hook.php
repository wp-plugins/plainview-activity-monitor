<?php

namespace plainview\wordpress\activity_monitor\hooks;

use \plainview\wordpress\activity_monitor\actions\log_hook;

/**
	@brief		Hook base case.
	@since		2014-04-27 15:36:41
**/
class hook
{
	use \plainview\sdk\traits\method_chaining;

	/**
		@brief		The name of the hook - the default being the class name without the namespace.
		@since		2014-07-07 22:00:50
	**/
	public $hook;

	public function __construct()
	{
		$class = get_called_class();
		$class = preg_replace( '/.*\\\/', '', $class );
		$this->hook = $class;
	}

	/**
		@brief		Return the instance of the activity monitor.
		@since		2014-05-06 22:42:55
	**/
	public function activity_monitor()
	{
		return \plainview\wordpress\activity_monitor\Plainview_Activity_Monitor::instance();
	}

	/**
		@brief		Returns the linked blog name.
		@since		2014-05-07 00:12:58
	**/
	public function blog_html()
	{
		return sprintf( '<a href="%s">%s</a>',
			get_bloginfo( 'url' ),
			esc_html( get_bloginfo( 'blogname' ) )
		);
	}

	/**
		@brief		Sets the disabled flag of the hook.
		@since		2014-05-04 19:53:12
	**/
	public function disabled( $disabled = true )
	{
		if ( ! $disabled )
		{
			unset( $this->disabled );
			return $this;
		}
		else
			return $this->set_boolean( 'disabled', $disabled );
	}

	/**
		@brief		Gets the classmap for autoloading.
		@since		2014-05-01 08:43:51
	**/
	public function get_classmap()
	{
		return get_class( $this );
	}

	/**
		@brief		Get the name of the vendor.
		@since		2014-04-27 23:45:01
	**/
	public function get_description()
	{
		return 'Default description.';
	}

	/**
		@brief		Get the name of the hook that this activity handles.
		@details	Override only if the name of the child class does not match the hook name.
		@since		2014-04-27 23:45:50
	**/
	public function get_hook()
	{
		return $this->hook;
	}

	/**
		@brief		Return an unique ID for this activity.
		@since		2014-05-01 11:25:31
	**/
	public function get_id()
	{
		$id = sprintf( '%s %s %s',
			$this->get_hook(),
			$this->get_vendor(),
			get_called_class()
		);
		return md5( $id );
	}

	/**
		@brief		How many parameters should we tell Wordpress that we want?
		@since		2014-05-01 08:33:37
	**/
	public function get_parameter_count()
	{
		// Default, as per Wordpress, is 1.
		return 1;
	}

	/**
		@brief		The priority we want to hook into.
		@since		2014-05-01 08:33:10
	**/
	public function get_priority()
	{
		// Default, as per Wordpress, is 10.
		return 10;
	}

	/**
		@brief		Get the name of the vendor.
		@since		2014-04-27 23:45:01
	**/
	public function get_vendor()
	{
		return 'Plainview';
	}

	/**
		@brief		Insert ourselves into the Wordpress hook.
		@since		2014-04-27 23:47:03
	**/
	public function hook()
	{
		if ( $this->is_disabled() )
			return;
		add_action( $this->get_hook(), [ $this, '_log' ], $this->get_priority(), $this->get_parameter_count() );
	}

	/**
		@brief		Retrieve or create the HTML collection for this hook.
		@since		2014-05-04 13:11:42
	**/
	public function html()
	{
		if ( ! isset( $this->__html ) )
			$this->__html = new \plainview\sdk\collections\html;
		return $this->__html;
	}

	/**
		@brief		Convenience method to set the HTML data and then execute the log_look action.
		@since		2014-05-04 11:59:56
	**/
	public function html_and_execute()
	{
		$args = func_get_args();
		if ( count( $args ) > 0 )
		{
			$text = call_user_func_array( 'sprintf', $args );
			if ( $text == '' )
				$text = $args[ 0 ];
		}
		else
			$text = $this->html() . '';
		$this->log_hook->data->html( $text );
		$this->html()->flush();
		$this->log_hook->execute();
	}

	/**
		@brief		Is this hook disabled?
		@since		2014-05-05 21:06:52
	**/
	public function is_disabled()
	{
		return isset( $this->disabled ) && ( $this->disabled == true );
	}

	/**
		@brief		The internal method that is called when the hook is executed (read: should be logged).
		@details	Does some housekeeping in the form of saving the parameters and prepping the log_hook action and then calling log().
		@since		2014-05-03 15:21:54
	**/
	public function _log()
	{
		if ( $this->is_disabled() )
			return;

		$this->log_hook = new log_hook;
		$this->log_hook->hook( $this->get_hook() );

		$args = func_get_args();
		$this->parameters = new \plainview\sdk\collections\collection;
		foreach( $args as $index => $arg )
			$this->parameters->set( $index + 1, $arg );

		$this->log();
	}

	/**
		@brief		Log the activity.
		@details	This overriable method handles the logging of the activity.

		The hook parameters are prepped in the ->parameters object and the log_hook action can be called with $this->log_hook->execute() action.
		@since		2014-04-28 08:25:37
	**/
	public function log()
	{
	}

	/**
		@brief		Register ourself with a hooks collection.
		@since		2014-04-27 20:55:27
	**/
	public function register_with( $hooks )
	{
		$hooks->register( $this );
	}
}
