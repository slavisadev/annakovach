<footer>
    <div class="container tester">
        <div class="clearfix">
			<?php
			if ( is_page( 'Cheat sheet' ) || is_page( 'Cheat sheet confirmation' ) ) { ?>
                <small><?php the_field( 'cheat_sheet_footer', 'option' ); ?></small>
				<?php
			}elseif ( is_page( 'Blog' ) || is_single() || is_search() || is_category() || is_tag() || is_author() || is_404() ) {

			} else { ?>
                <div class="clearfix" style="margin-bottom: 30px"><?php
				wp_nav_menu( array(
					'menu' => 'Main Menu'
				) ); ?></div><?php
				the_field( 'main_footer_text', 'option' );
			} ?>
        </div>
</footer>
<?php wp_footer(); ?>
	<script type="text/javascript"> adroll_adv_id = "WYZR66DPW5A3VLTJ3ONBOM"; adroll_pix_id = "HBIPMZRL5BEUBCXH325SIP"; adroll_version = "2.0";  (function(w, d, e, o, a) { w.__adroll_loaded = true; w.adroll = w.adroll || []; w.adroll.f = [ 'setProperties', 'identify', 'track' ]; var roundtripUrl = "https://s.adroll.com/j/" + adroll_adv_id + "/roundtrip.js"; for (a = 0; a < w.adroll.f.length; a++) { w.adroll[w.adroll.f[a]] = w.adroll[w.adroll.f[a]] || (function(n) { return function() { w.adroll.push([ n, arguments ]) } })(w.adroll.f[a]) }  e = d.createElement('script'); o = d.getElementsByTagName('script')[0]; e.async = 1; e.src = roundtripUrl; o.parentNode.insertBefore(e, o); })(window, document); adroll.track("pageView"); </script> 
</body>
</html>
