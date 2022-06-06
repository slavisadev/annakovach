<div class="sidebar">
    <div class="widget search-form" id="thesis-search-widget-2">
        <p class="widget_title">SEARCH BLOG</p>
        <form class="search_form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <p>
                <input class="input_text" type="text" id="s" name="s" value=""
                       onfocus="if (this.value === '') {this.value = '';}"
                       onblur="if (this.value === '') {this.value = '';}">
                <input type="submit" id="searchsubmit" value="Search">
            </p>
        </form>
    </div>
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</div>