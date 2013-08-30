<?php

require_once 'vendor/autoload.php';

$rssget_rss = new \Zelenin\RSSGet( 'https://news.google.com/news/feeds?pz=1&cf=all&ned=us&hl=en&topic=h&num=3&output=rss' );
$rssget_atom = new \Zelenin\RSSGet( 'https://github.com/zelenin/RSSGet/commits/master.atom' );

echo '<pre>';

print_r( $rssget_rss->getChannel() );
print_r( $rssget_rss->getItems() );

print_r( $rssget_atom->getChannel() );
print_r( $rssget_atom->getItems() );