<?php

require_once 'vendor/autoload.php';
require_once 'lib/Zelenin/RSSGet.php';

$rssget_rss = new \Zelenin\RSSGet( 'https://news.google.com/news/feeds?pz=1&cf=all&ned=us&hl=en&topic=h&num=3&output=rss' );
$rssget_atom = new \Zelenin\RSSGet( 'http://www.atomenabled.org/atom.xml' );

echo '<pre>';

print_r( $rssget_rss->get_channel() );
print_r( $rssget_rss->get_items() );

print_r( $rssget_atom->get_channel() );
print_r( $rssget_atom->get_items() );