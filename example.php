<?php

require_once( 'rssget.php' );

$rssget_rss = new \zelenin\rssget( 'https://news.google.com/news/feeds?pz=1&cf=all&ned=us&hl=en&topic=h&num=3&output=rss' );
$rssget_atom = new \zelenin\rssget( 'http://www.atomenabled.org/atom.xml' );

echo '<pre>';

print_r( $rssget_rss->channel );
print_r( $rssget_rss->items );

print_r( $rssget_atom->channel );
print_r( $rssget_atom->items );

echo '</pre>';

?>