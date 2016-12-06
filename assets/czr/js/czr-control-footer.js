//INCLUDED IN THE THEME CUSTOMIZR FMK - TO REMOVE
//
//FIX FOR SECTION CONTENT HIDDEN BY THE FOOTER
//Problem fixed : since WP4.5, the footer of the customizer includes the device switcher
//but there's aso the rating link there.
//Therefore, in sections higher than the viewport, some content might be hidden
//This is fixed on each section expanded event
(function (api, $, _) {
  //wp.customize.Section is not available before wp 4.1
  if ( 'function' == typeof api.Section ) {
    var _original_section_initialize = api.Section.prototype.initialize;
    api.Section.prototype.initialize = function( id, options ) {
        _original_section_initialize.apply( this, [id, options] );
        var section = this;

        this.expanded.callbacks.add( function( _expanded ) {
          if ( ! _expanded )
            return;

          var   container = section.container.closest( '.wp-full-overlay-sidebar-content' ),
                  content = section.container.find( '.accordion-section-content' );
            //content resizing to the container height
            _resizeContentHeight = function() {
              content.css( 'height', container.innerHeight() );
          };
          _resizeContentHeight();
          //this is set to off in the original expand callback if 'expanded' is false
          $( window ).on( 'resize.customizer-section', _.debounce( _resizeContentHeight, 110 ) );
      });
    };//add
  }
})( wp.customize , jQuery, _ );