( function($) {
    var $wp_inline_edit = inlineEditPost.edit;
    inlineEditPost.edit = function( id ) {
        $wp_inline_edit.apply( this, arguments );

        var $post_id = 0;
        if ( typeof( id ) == 'object' ) {
            $post_id = parseInt( this.getId( id ) );
        }

        if ( $post_id <= 0 ) {
            return;
        }

        var $edit_row = $( '#edit-' + $post_id );
        var $repository = $( '#odwpp-project_repository-' + $post_id ).text();
        $edit_row.find( 'input[name="project_repository"]' ).val( $repository );
    };
} )( jQuery );
