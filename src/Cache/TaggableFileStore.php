<?php

namespace Unikent\Cache;

use Illuminate\Support\Str;
use Illuminate\Cache\FileStore;
use Illuminate\Filesystem\Filesystem;

class TaggableFileStore extends FileStore
{

	public $separator;
	protected $queue;

	/**
	 * Create a new file cache store instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem $files
	 * @param  string $directory
	 * @param  array $options
	 */
	public function __construct(Filesystem $files, $directory, $options)
	{
		$defaults = [
			'separator'=> '~#~',
			'queue' => null
		];

		$options = array_merge($defaults,$options);

		$this->separator = $options['separator'];
		$this->queue = $options['queue'];

		parent::__construct($files,$directory);
	}


	/**
	 * Get the full path for the given cache key.
	 *
	 * @param  string  $key
	 * @return string
	 */
	protected function path($key)
	{
		$isTag=false;
		$split = explode($this->separator,$key);
		if(count($split) > 1) {
			$folder = reset($split) . '/';

			if($folder==='cache_tags/'){
				$isTag=true;
			}
			$key = end($split);
		}else{
			$key = reset($split);
			$folder='';
		}
		if($isTag){
			$hash = $key;
			$parts = [];
		}else {
			$parts = array_slice(str_split($hash = sha1($key), 2), 0, 2);
		}

		return $this->directory.'/'. $folder . ( count($parts) > 0 ? implode('/', $parts).'/' : '').$hash;
	}

	/**
	 * Begin executing a new tags operation.
	 *
	 * @param  array|mixed  $names
	 * @return \Illuminate\Cache\TaggedCache
	 */
	public function tags($names)
	{
		return new TaggedFileCache($this, new FileTagSet($this, is_array($names) ? $names : func_get_args()));
	}


	public function flushOldTag($tagId){

		foreach ($this->files->directories($this->directory) as $directory) {
			if(Str::contains(basename($directory),$tagId)){
				$this->files->deleteDirectory($directory);
			}
		}

	}
}
