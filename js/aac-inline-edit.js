(function( $ ) {

  if ( inlineEditPost && inlineEditPost.edit ) {

    // We create a copy of the WP inline edit post function
    var wpInlineEdit = inlineEditPost.edit;

    // and then we overwrite the function with our own code
    inlineEditPost.edit = function( id ) {

      wpInlineEdit.apply( this, arguments );

      var $editRow, $rowData, postId = 0;
      if ( typeof( id ) === 'object' ) {
        postId = parseInt( this.getId( id ) );
      }

      $editRow = $( '#edit-' + postId );
      $rowData = $( '#inline_' + postId );

      $rowData.find( '.post_category' ).each( function() {

        var $self = $( this ),
        taxonomy,
        termId,
        termIdString = $self.text();

      if ( termIdString ) {
        termId = termIdString.split( ',' );
        taxonomy = $self.attr( 'id' ).replace( '_' + postId, '' );
        $editRow.find( 'ul.' + taxonomy + '-checklist :radio' ).val( termId );
      }

      });
      return false;
    };
  }

})( jQuery );
