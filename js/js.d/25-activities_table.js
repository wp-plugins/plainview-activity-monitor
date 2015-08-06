/**
	@brief		Refresh the activities table.
	@since		2015-07-22 21:30:30
**/
function pvam_refresh_activities_table( callback )
{
	$ = jQuery;

	$current_table = $( 'table.activity_monitor_activities' );
	if ( $current_table.data( 'pvam_refreshing' ) === true )
		return;

	$current_table.data( 'pvam_refreshing', true );

	$.get( document.location )
		.done( function( data )
		{
			$data = $( data );
			var $table = $( 'table.activity_monitor_activities', $data );
			if ( $table.length < 1 )
				return;
			//$current_table.replaceWith( $table );
			$( 'tbody', $current_table ).replaceWith( $( 'tbody', $table ) );

			// We should also replace the navigation, in case the amount of pages have changed.
			var $tablenav_pages = $( '.tablenav-pages' );
			$tablenav_pages.empty();
			$( '.tablenav-pages', $data ).first().appendTo( $tablenav_pages );
		} )
		.then( function()
		{
			$current_table.data( 'pvam_refreshing', false );
			if ( callback !== undefined )
				callback();
		} );
}
