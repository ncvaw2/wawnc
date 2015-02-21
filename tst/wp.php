<?php

include $header;


require('../../ncvaw.org/wp-blog-header.php');

$posts = get_posts('numberposts=10&order=DEC&orderby=date');
foreach ($posts as $post) : setup_postdata( $post ); ?>
<?php the_date(); echo "<br />"; 
the_title();     
//the_excerpt();
the_content(); 

endforeach;
include $footer; ?>