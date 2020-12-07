<?php


namespace App\Http\Controllers;

use App\Models\LeetcodeQuestion;
use App\Models\User;
use App\Models\UserSubmit;
use App\Services\UserPointService;
use App\Services\UserSubmitService;
use App\Utils\Leetcode;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

class LeetcodeController extends Controller
{
    const SYNC_LOCK_PREFIX = 'sync_lock_';

    const SYNC_LOCK_EXPIRE = 300;

    /**
     * 获取一个leetcode用户信息
     * @param Request $request
     * @param Leetcode $leetcode
     * @return mixed|string
     */
    public function user(Request $request, Leetcode $leetcode)
    {
        $input = $this->validateRequest($request, [
            'leetcode_slug' => 'required'
        ]);

        $existingUser = User::query()->where('leetcode_slug', $input['leetcode_slug'])->first();
        if ($existingUser) {
            return $this->error(Response::HTTP_CONFLICT, '用户已绑定本站，请直接登录');
        }

        $leetcodeUser = $leetcode->getUser($input['leetcode_slug']);
        if (!$leetcodeUser) {
            return $this->error(Response::HTTP_NOT_FOUND, '没找到leetcode用户，请检查');
        }

        $avatar = $leetcodeUser['userAvatar'];
        $pathStructure = explode('/', $avatar);
        $filename = 'avatar/' . end($pathStructure);
        $filePath = public_path($filename);
        if (!file_exists($filePath)) {
            if (!file_exists(public_path('avatar'))) {
                mkdir(public_path('avatar'));
            }
            $avatarData = (new Client())->get($avatar)->getBody()->getContents();
            file_put_contents($filePath, $avatarData);
        }
        $leetcodeUser['userAvatar'] = $filename;

        return $this->successWithData($leetcodeUser);
    }

    /**
     * 获取当前用户的最近提交记录
     * @param Request $request
     * @param UserSubmitService $submitService
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\CurdException
     */
    public function submissions(Request $request, UserSubmitService $submitService)
    {
        $userId = auth()->payload()->get('sub');

        $extraDataRules = [
            [
                'extra_fields' => ['question_title' => LeetcodeQuestion::FIELD_TRANSLATED_NAME, 'question_name' => LeetcodeQuestion::FIELD_QUESTION_NAME],
                'entity_class' => LeetcodeQuestion::class,
                'foreign_key' => UserSubmit::FIELD_QUESTION_ID,
                'local_key' => 'id'
            ],
            [
                'extra_fields' => 'language',
                'processor' => function (UserSubmit $submit) {
                    return $submit->getLanguage();
                }
            ],
            [
                'extra_fields' => 'result',
                'processor' => function (UserSubmit $submit) {
                    return $submit->getResult();
                }
            ]
        ];

        return $this->successWithData($submitService->retrieveList(
            ['user_id' => $userId],
            $request->input('page', 1),
            $request->input('limit', 10),
            ['submission_id' => 'desc'],
            $extraDataRules
        ));
    }

    /**
     * 同步当前用户leetcode提交数据
     * @param UserSubmitService $submitService
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncSubmissions(UserSubmitService $submitService)
    {
        /** @var User $user */
        $user = auth()->user();
        $key = self::SYNC_LOCK_PREFIX . $user->getKey();
        if (!Redis::setnx($key, 1)) {
            return $this->error(Response::HTTP_BAD_REQUEST, '每5分钟只能同步一次');
        }
        Redis::expire($key, self::SYNC_LOCK_EXPIRE);
        $submitService->syncUserRecentSubmissions($user);
        (new UserPointService())->calcUserPoint($user);

        return $this->success();
    }

    protected function makeSyncLockKey($userId)
    {
        return self::SYNC_LOCK_PREFIX . $userId;
    }
}
