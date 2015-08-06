jQuery(document).ready( function( $ )
{
	$( '.pvam_overview_table' ).pvam_ajax_filters();
	$( '.pvam_overview_table' ).pvam_autorefresh_activities_table();
} );
