<?php

namespace App\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template;
use DaveChild\TextStatistics as TS;

class HomePageAction implements ServerMiddlewareInterface
{
    private $template;
    private $analyzer;

    public function __construct(Template\TemplateRendererInterface $template, TS\TextStatistics $analyzer)
    {
        $this->template = $template;
        $this->analyzer = $analyzer;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $data = ['articles' => [], 'category' => ''];

        // Just render page if GET request.
        if ($request->getMethod() == 'GET') {
            return new HtmlResponse($this->template->render('app::home', $data));
        }

        // TODO: Sanitize input.
        $input = $request->getParsedBody();
        $category = array_key_exists('category', $input) ? $input['category'] : '';
        $data['category'] = $category;

        // TODO: Extract business logic to it's own component.
        $articles = $this->getArticles($category);
        $articles = $this->getExtract($articles);
        $articles = $this->calculateScore($articles);
        $articles = $this->sortByScore($articles);

        $data['articles'] = $articles;

        return new HtmlResponse($this->template->render('app::home', $data));
    }

    // Fetches Wiki articles from it API by category.
    // TODO: Extract business logic to it's own component.
    private function getArticles($category)
    {
        // TODO: Handle exceptions.
        // TODO: Make the API URL configurable.
        $json = file_get_contents('https://en.wikipedia.org/w/api.php?action=query&list=categorymembers&cmtitle=Category:' . \urlencode($category) . '&cmlimit=50&format=json');
        $result = json_decode($json, true);

        if (!array_key_exists('query', $result) || !array_key_exists('categorymembers', $result['query'])) {
            return [];
        }

        return $result['query']['categorymembers'];
    }

    // Fetches the articles extract from Wiki API.
    // TODO: Extract business logic to it's own component.
    private function getExtract($articles)
    {
        $extract_articles = [];

        // Split articles to fetch beacuse Wiki API has a hard limit of 20.
        // TODO: Make this limit a constant or configurable.
        $chunks = array_chunk($articles, 20, true);

        foreach ($chunks as $chunk) {
            $pageIds = '';
            foreach ($chunk as $article) {
                $pageIds .= $article['pageid'] . '|';
            }
            $pageIds = rtrim($pageIds, '|');
    
            // TODO: Handle exceptions.
            // TODO: Make the API URL configurable.
            $url = 'https://en.wikipedia.org/w/api.php?action=query&prop=extracts&explaintext=true&exintro=true&pageids=' . \urlencode($pageIds) . '&format=json';
            $json = file_get_contents($url );

            $result = json_decode($json, true);

            $extract_articles = array_merge($extract_articles, $result['query']['pages']);
        }

        return $extract_articles;
    }

    // Calculates readability score for a list of articles.
    // TODO: Extract business logic to it's own component.
    private function calculateScore($articles)
    {
        foreach ($articles as $pageId => $article) {
            $firstParagraph = strtok($article['extract'], "\n");
            $articles[$pageId]['score'] = $this->analyzer->fleschKincaidReadingEase($firstParagraph);
        }

        return $articles;
    }

    // Sort by readability score.
    // TODO: Extract business logic to it's own component.
    private function sortByScore($articles)
    {
        usort($articles, function ($art1, $art2) {
            if ($art1['score'] == $art2['score']) {
                return 0;
            }
            return ($art1['score'] < $art2['score']) ? -1 : 1;
        });

        return $articles;
    }
}
