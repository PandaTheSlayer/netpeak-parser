<?php

namespace App\Modules\Parser;
use GuzzleHttp;
use Symfony\Component\DomCrawler\Crawler;


class ParserClass
{
    public function getLinks($catalog)
    {
        $client = new GuzzleHttp\Client();
        $crawler = new Crawler(null, $catalog);

        $result = $client->get($catalog, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36'
            ]
        ]);

        $crawler->addHtmlContent($result->getBody(), 'UTF-8');
        $crawler = $crawler->filter('div.g-i-tile-i-title > a');

        foreach ($crawler as $node){
            var_dump($node->getAttributeNode('href')->value);
        }


    }
}