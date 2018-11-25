<?php

namespace IsaacRankin\SocialFeed;

use SilverStripe\Dev\BuildTask;
use IsaacRankin\SocialFeed\Providers\SocialFeedProvider;
use SilverStripe\ORM\DB;


class SocialFeedCacheTask extends BuildTask {
	protected $title       = 'Social Feed Pre-Load Task';
	protected $description = 'Calls getFeed on each SocialFeedProvider and caches it. This task exists so a cronjob can be setup to update social feeds without exposing an end user to slowdown.';

	/**
	 * Gets the feed for each provider and updates the cache with it.
	 */
	public function run($request) {
		$providers = SocialFeedProvider::get();
		$providers = $providers->toArray();
		if ($providers)
		{
			foreach ($providers as $prov)
			{
				$this->log('Getting feed for #'.$prov->ID.' ('.$prov->ClassName.')');
				$feed = $prov->getFeedUncached();
				$prov->setFeedCache($feed);
				$this->log('Updated feed cache for #'.$prov->ID.' ('.$prov->ClassName.')');
			}
		}
		else
		{
			$this->log('No SocialFeedProvider exist to be updated.');
		}
	}

	public function log($message) {
		DB::alteration_message($message);
	}
}
