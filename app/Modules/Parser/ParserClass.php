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

    public function getLinks($catalog)
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

        //echo json_encode($this->parseObjects($links));
		//var_dump($this->parseObjects($links));
		$parserLog = new ParserLog();
		$parserLog->storeObjLinks($links);
    }

    public function parseObjects(array $links)
	{
		$client = $this->client;
		$fulfilled = [];
		$rejected = [];

		$requests = function ($links){
//			foreach($links as $link){
//				yield new Request('GET', $link, [
//					'delay' => 2000
//				]);
//			}
			for($i = 0; $i<5; $i++){
				yield new Request('GET', $links[$i], [
					//'delay' => 1000
				]);
			}
		};

//		$pool = new Pool($client, $requests($links), [
//			'concurrency' => 5,
//			'fulfilled' => function ($response) use (&$fulfilled){
//				array_push($fulfilled, $response->getBody());
//			},
//			'rejected' => function ($response) use (&$rejected){
//				array_push($rejected, $response->getStatus());
//			}
//		]);
//
//		$promise = $pool->promise();
//		$promise->wait();
//
//		return array($fulfilled, $rejected);

		$results = Pool::batch($client, $requests($links), ['concurrency' => 5]);

		var_dump($results);

	}
}