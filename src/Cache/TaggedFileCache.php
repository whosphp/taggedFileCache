<?php

namespace Unikent\Cache;

use Illuminate\Cache\TaggedCache;
use Illuminate\Cache\RetrievesMultipleKeys;

class TaggedFileCache extends TaggedCache
{
	use RetrievesMultipleKeys;

	/**
	 * {@inheritdoc}
	 */
	protected function itemKey($key)
	{
		return $this->taggedItemKey($key);
	}

	/**
	 * Get a fully qualified key for a tagged item.
	 *
	 * @param  string  $key
	 * @return string
	 */
	public function taggedItemKey($key)
	{
		return $this->tags->getNamespace() . $this->store->separator . $key;
	}
}
