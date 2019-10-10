<?php


namespace App\Utils;

use GuzzleHttp\Client;

class Leetcode
{
    const LEETCODE_DOMAIN = 'https://leetcode-cn.com';

    const URL_LOGIN = '/accounts/login';
    const URL_QUESTIONS = '/api/problems/all';
    const URL_TAGS = '/problems/api/tags/';
    const URL_GRAPHQL = '/graphql';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const BODY_FORMAT_JSON = 'json';
    const BODY_FORMAT_FORM_DATA = 'form_params';
    const DEFAULT_BODY_FORMAT = self::BODY_FORMAT_JSON;

    protected $client;
    protected $csrfToken;
    protected $sessionId;

    /**
     * 模拟登录
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function login()
    {
        $this->normalRequest(self::METHOD_POST, self::URL_LOGIN, [
            'csrfmiddlewaretoken' => $this->csrfToken,
            'login' => '15650790070',
            'password' => 'aa921103',
            'next' => '/'
        ], self::BODY_FORMAT_FORM_DATA);
    }

    /**
     * 获取题目列表
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getQuestions()
    {
        $questions =  $this->normalRequest(self::METHOD_GET, self::URL_QUESTIONS);
        $translations = $this->getTranslations();
        $questions = $questions['stat_status_pairs'];
        return array_map(function ($question) use ($translations) {
            $stat = $question['stat'];
            return [
                'id' => $stat['question_id'],
                'title' => $stat['question__title'],
                'slug' => $stat['question__title_slug'],
                'difficulty' => $question['difficulty']['level'],
                'translation' => $translations[$stat['question_id']]
            ];
        }, $questions);
    }

    /**
     * 获取题目翻译
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTranslations()
    {
        $operationName = 'getQuestionTranslation';
        $query = 'query getQuestionTranslation($lang: String) {
         translations: allAppliedQuestionTranslations(lang: $lang) {
             title
             questionId
             __typename
           }
         }';
        $translations = $this->graphQLRequest($operationName, $query);
        return array_column($translations['data']['translations'], 'title', 'question_id');
    }

    public function getTags()
    {
        $data = $this->normalRequest(self::METHOD_GET, self::URL_TAGS);
        return $data['topics'];
    }

    /**
     * 生成Token
     * @return $this
     */
    public function initToken()
    {
        $this->csrfToken = md5(round(20000)) . md5(round(20000));
        return $this;
    }

    /**
     * 发起GraphQL请求
     * @param $operationName
     * @param $query
     * @param array $variable
     * @return mixed|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function graphQLRequest($operationName, $query, $variable = [])
    {
        $payload = [
            'operationName' => $operationName,
            'query' => $query,
            'variables' => $variable
        ];
        return $this->normalRequest(self::METHOD_POST, self::URL_GRAPHQL, $payload);
    }

    /**
     * 发起Http请求
     * @param $method
     * @param $url
     * @param array $params
     * @param string $bodyFormat
     * @return mixed|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function normalRequest($method, $url, $params = [], $bodyFormat = self::DEFAULT_BODY_FORMAT)
    {
        if (!$this->client) {
            $this->client = new Client();
        }
        if (!$this->csrfToken) {
            $this->initToken();
        }
        $request = [
            'headers' => [
                'x-csrftoken' => $this->csrfToken,
                'referer' => 'https://leetcode-cn.com/',
            ],
            $bodyFormat => $params
        ];
        $response = $this->client->request($method, self::LEETCODE_DOMAIN . $url, $request);
        $content = $response->getBody()->getContents();
        $decoded = @json_decode($content, true);
        if($response->getStatusCode() !== 200 || !$decoded){
            throw new \Exception($content);
        }
        return $decoded;
    }
}

