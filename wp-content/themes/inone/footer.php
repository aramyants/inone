<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

</div>

<footer class="site-footer">
	<div class="site-footer__inner">
		&copy; <?php echo esc_html( (string) gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. Powered by <a href="https://aramyants.com" target="_blank">aramyants</a>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

