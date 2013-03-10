<?php

/**
 * PHP class for parsing of RSS-feeds and atoms. Output to array.
 * @package RSSGet
 * @author  Aleksandr Zelenin <aleksandr@zelenin.me>
 * @link    https://github.com/zelenin/rssget
 * @version 1.0.1
 * @license http://opensource.org/licenses/gpl-3.0.html GPL-3.0
 */

namespace Zelenin;

use DOMDocument;

class RSSGet extends DOMDocument
{
	const VERSION = '1.0.1';
	private $feed_url;
	private $http_code;
	private $feed_content;
	private $feed_type;
	private $channel_tag;
	private $item_tag;
	private $channel = array();
	private $items = array();

	public function __construct( $feed_url )
	{
		$curl = new Curl;
		$this->feed_url = $feed_url;
		$feed = $curl->get( $this->feed_url );
		$this->http_code = $feed['info']['http_code'];
		if ( $this->http_code != 200 ) return false;

		$this->feed_content = $feed['body'];
		$this->loadXML( $this->feed_content );
		$this->check_type();
		$this->get_channel();
		$this->get_items();
	}

	private function check_type()
	{
		if ( is_object( $this->getElementsByTagName( 'feed' )->item( 0 ) ) ) {
			$this->feed_type = 'atom';
			$this->channel_tag = 'feed';
			$this->item_tag = 'entry';
		} else {
			$this->feed_type = 'rss';
			$this->channel_tag = 'channel';
			$this->item_tag = 'item';
		}
	}

	public function get_channel()
	{
		$channel = $this->getElementsByTagName( $this->channel_tag )->item( 0 );
		$channel_elements = $channel->getElementsByTagName( '*' );
		for ( $i = 0; $i < $channel_elements->length; $i++ ) {
			if ( $channel_elements->item( $i )->nodeName == $this->item_tag ) break;
			$this->channel[$channel_elements->item( $i )->nodeName] = $this->string_normalize( $channel_elements->item( $i )->nodeValue );
			if ( $channel_elements->item( $i )->hasAttributes() ) {
				$attributes = $channel_elements->item( $i )->attributes;
				foreach ( $channel_elements->item( $i )->attributes as $attribute ) {
					$this->channel[$channel_elements->item( $i )->nodeName . '_' . $attribute->name] = $this->string_normalize( $attribute->value );
				}
			}
		}
		return $this->channel;
	}

	public function get_items()
	{
		$elements = $this->getElementsByTagName( $this->item_tag );
		for ( $i = 0; $i < $elements->length; $i++ ) {
			$item_elements = $elements->item( $i )->getElementsByTagName( '*' );
			for ( $j = 0; $j < $item_elements->length; $j++ ) {
				$this->items[$i][$item_elements->item( $j )->nodeName] = $this->string_normalize( $item_elements->item( $j )->nodeValue );
				if ( $item_elements->item( $j )->hasAttributes() ) {
					$attributes = $item_elements->item( $j )->attributes;
					foreach ( $item_elements->item( $j )->attributes as $attribute ) {
						$this->items[$i][$item_elements->item( $j )->nodeName . '_' . $attribute->name] = $this->string_normalize( $attribute->value );
					}
				}
			}
		}
		return $this->items;
	}

	private function string_normalize( $string )
	{
		$string = htmlspecialchars( $string, ENT_QUOTES, 'utf-8', false );
		// $string = str_replace(  '&amp;', '&#x26;', $string );
		// $string = str_replace(  '&lt;', '&#x3C;', $string );
		// $string = str_replace(  '&gt;', '&#x3E;', $string );
		return $string;
	}
}