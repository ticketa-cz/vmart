<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "wrapper" div and all content after.
 *
 * @package Hestia
 * @since Hestia 1.0
 */
?>

<script type="text/javascript">
// menu do main wrapperu
  jQuery(function($) {
		$('.navbar').prependTo('.main');
  });

// slideshow
function cycleBackgrounds() {
    var index = 0;
 
    $imageEls = jQuery('#home .slide'); // Get the images to be cycled.
 
    setInterval(function () {
        // Get the next index.  If at end, restart to the beginning.
        index = index + 1 < $imageEls.length ? index + 1 : 0;
        
        // Show the next
        $imageEls.eq(index).addClass('show');
        
        // Hide the previous
        $imageEls.eq(index - 1).removeClass('show');
    }, 5000);
};
 
// Document Ready.
jQuery(function () {
    cycleBackgrounds();
});
</script>
			<?php do_action( 'hestia_do_footer' ); ?>
		</div>
	</div>
<?php wp_footer(); ?>
<script src="https://partners.goout.net/cz-other/vm-artcz.js"></script>
<script type="text/javascript">
	/* <![CDATA[ */
	var seznam_retargeting_id = 83229;
	/* ]]> */
</script>
<script type="text/javascript" src="//c.imedia.cz/js/retargeting.js"></script>

</body>
</html>
