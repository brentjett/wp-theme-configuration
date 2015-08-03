<!DOCTYPE html>
<html <? language_attributes() ?>>
    <head>
    	<?php wp_head() ?>
    </head>
    <body <?php body_class() ?>>
        <main>
            <article <?php post_class() ?>>
                <h1>WP JSON Config API - Tests</h1>
                <p> This is meant as a one-page test to see if all config.json files are being read properly and their settings are then being configured as expected. More to come.</p>
                <?php the_content() ?>
            </article>
        </main>
        <?php wp_footer() ?>
    </body>
<html>
