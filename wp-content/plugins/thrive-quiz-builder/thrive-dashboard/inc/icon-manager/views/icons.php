<h3><?php echo esc_html__( "Your Custom Icons", TVE_DASH_TRANSLATE_DOMAIN ) ?></h3>
<p><?php echo esc_html__( "These icons are available for use on your site:", TVE_DASH_TRANSLATE_DOMAIN ) ?></p>
<div class="icomoon-admin-icons">
	<?php foreach ( $this->icons as $class ) : ?>
		<span class="icomoon-icon" title="<?php echo array_key_exists( $class, $this->variations ) ? esc_attr( $this->variations[ $class ] ) : esc_attr( $class ) ?>">
            <span class="<?php echo esc_attr( $class ); ?>"></span>
        </span>
	<?php endforeach ?>
</div>
