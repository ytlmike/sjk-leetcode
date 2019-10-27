<?php


namespace App\Utils;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

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
        $questions = $this->getQuestion();
        $translations = $this->getTranslations();
        return array_map(function ($question) use ($translations) {
            $stat = $question['stat'];
            return [
                'id' => $stat['question_id'],
                'title' => $stat['question__title'],
                'slug' => $stat['question__title_slug'],
                'difficulty' => $question['difficulty']['level'],
                'translation' => Arr::get($translations, $stat['question_id'], ''),
                'front_id' => $stat['frontend_question_id']
            ];
        }, $questions);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getQuestion()
    {
        $key = __METHOD__;
        $questions = Cache::get($key);
        if(!$questions){
            $questions =  $this->normalRequest(self::METHOD_GET, self::URL_QUESTIONS);
            $questions = $questions['stat_status_pairs'];
            Cache::put($key, $questions, 86400);
        }
        return $questions;
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
        $key = __METHOD__;
        $translations = Cache::get($key);
        if(!$translations){
            $translations = $this->graphQLRequest($operationName, $query);
            $translations = array_column($translations['data']['translations'], 'title', 'questionId');
            Cache::put($key, $translations, 86400);
        }
        return $translations;
    }

    /**
     * 获取题目标签
     * @return mixed|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTags()
    {
        $key = __METHOD__;
        $tags = Cache::get($key);
        if(!$tags){
            $tags = $this->normalRequest(self::METHOD_GET, self::URL_TAGS);
            $tags =$tags['topics'];
            Cache::put($key, $tags, 86400);
        }
        return $tags;
    }

    /**
     * 获取单个用户的提交记录
     * @param $userSlug
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserSubmissions($userSlug)
    {
        $operationName = 'recentSubmissions';
        $query = 'query recentSubmissions($userSlug: String!) {
            recentSubmissions(userSlug: $userSlug) {
                status
                lang
                question {
                    questionFrontendId
                    title
                    translatedTitle
                    titleSlug
                    __typename
                }
                submitTime
                __typename
            }
        }';
        $variables = ['userSlug' => $userSlug];
        $submissions = $this->graphQLRequest($operationName, $query, $variables);
        return array_map(function ($submission){
            return [
                'front_id' => $submission['question']['questionFrontendId'],
                'language' => $submission['lang'],
                'result' => $submission['status'],
                'time' => Carbon::createFromTimestamp($submission['submitTime'])
            ];
        }, $submissions['data']['recentSubmissions']);
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

