<?php

namespace App\Modules\Parser;

use App\ParserLog;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\DomCrawler\Crawler;


class ParserClass
{
	private $client;
	private $crawler;

	public function __construct(Client $client, Crawler $crawler)
	{
		$this->client = $client;
		$this->crawler = $crawler;
	}

	/**
     * Get all object links for 1 page
     */
    public function parseCatalogLink($catalog)
    {

        $result = $this->client->get($catalog, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36'
            ]
        ]);

        $this->crawler->addHtmlContent($result->getBody(), 'UTF-8');
        $this->crawler = $this->crawler->filter('div.g-i-tile-i-title > a');

        foreach ($this->crawler as $node){
            $links[] = $node->getAttributeNode('href')->value;
        }

		$parserLog = new ParserLog();
		$parserLog->storeObjLinks($links);
    }

    /**
     * Parse all of objects
     */
    public function parseObjects(array $links)
	{
		$client = $this->client;


		$requests = function ($links){
			for($i = 0; $i<count($links); $i++){
				yield new Request('GET', $links[$i], [
					'delay' => 1000
				]);
			}
		};

		$results = Pool::batch($client, $requests($links), ['concurrency' => 5]);

		foreach ($results as $link){
		    $crawler = new Crawler();
		    $crawler->addHtmlContent($link->getBody());
		    $nodes = $crawler->filter('ul.short-chars-l.flex')->children()->eq(3);
		    $title = $crawler->filter('h1.detail-title')->text();

		    foreach ($nodes as $node){
		        $feature = explode(':', str_replace(array("\r", "\n"), '', $node->nodeValue));
		        $descr[$feature[0]] = $feature[1];
            }


            $resultArr[] = [$title => $descr];
        }

        return $resultArr;

	}

    public function getNotVisitedLinks()
    {
        $links = \DB::table('parser_logs')->where('visited', '=', '0')->get()->pluck('url');
        return $links->toArray();
	}
}