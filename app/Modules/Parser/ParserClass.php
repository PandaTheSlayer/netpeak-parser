<?php

namespace App\Modules\Parser;

use App\ParserLog;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\DomCrawler\Crawler;


/**
 * Class ParserClass
 * @package App\Modules\Parser
 */
class ParserClass
{
    /**
     * @var Client
     */
    private $client;

    /**
     * ParserClass constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
	{
		$this->client = $client;
	}

	/**
     * Get all object links for passed pages
     * @param $catalog_links - array
     * @TODO - Tick links that are been visited
     */
    public function parseCatalogLink(array $catalog_links)
    {
        // Loop from all of url's that passed from config/parser.php
        foreach ($catalog_links as $catalog){
            // Get html-content from url
            $result = $this->client->get($catalog, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36'
                ]
            ]);

            // Create an instance of Symfony\DomCrawler for easy extracting of objects
            $crawler = new Crawler();
            $crawler->addHtmlContent($result->getBody(), 'UTF-8');
            $nodes = $crawler->filter('div.g-i-tile-i-title > a');

            // Loop from all of <a> tags and collect href attributes
            foreach ($nodes as $node){
                $links[] = $node->getAttributeNode('href')->value;
            }

            // Store all parsed links in Database
            $parserLog = new ParserLog();
            $parserLog->storeObjLinks($links);
        }
    }

    /**
     * Parse all of objects
     * @param $links - array
     * @return array
     */
    public function parseObjects(array $links)
	{
		$client = $this->client;

		// Create generator for passed links that returns GuzzleHttp\Request objects.
		$requests = function ($links){
		  foreach ($links as $link){
		      yield new Request('GET', $link, [
		        'delay' => 1000
              ]);
          }
        };

		// Send all Requests.
		$results = Pool::batch($client, $requests($links), ['concurrency' => 5]);

		// Loop for all of results to get descriptions
		foreach ($results as $result){
		    // Get description block
		    $crawler = new Crawler();
		    $crawler->addHtmlContent($result->getBody(), 'UTF-8');
		    $nodes = $crawler->filter('ul.short-chars-l.flex > li');
		    $title = $crawler->filter('h1.detail-title')->text();

		    // Get each value
		    foreach ($nodes as $node){
		        $feature = explode(':', str_replace(["\r", "\n"], '', $node->nodeValue));
		        $desr[$feature[0]] = $feature[1];
            }
            $desr['Название'] = $title;
            $resultArr[] = $desr;
        }

        return $resultArr;
	}

    /**
     * Method gets all of not visited links.
     * @return array
     */
    public function getNotVisitedLinks()
    {
        $links = \DB::table('parser_logs')->where('visited', '=', '0')->get()->pluck('url');
        return $links->toArray();
	}
}