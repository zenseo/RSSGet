<?php

require_once( 'class.rssget.php' );

$rssget_rss = new rssget( 'http://feeds.rssboard.org/rssboard' );
$rssget_atom = new rssget( 'http://www.atomenabled.org/atom.xml' );

echo '<pre>';

print_r( $rssget_rss->channel );
print_r( $rssget_rss->items );

print_r( $rssget_atom->channel );
print_r( $rssget_atom->items );

echo '</pre>';

?>