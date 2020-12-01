<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $input = $this->validateRequest($request, [
            'name' => 'required|string',
            'password' => 'required',
            'leetcode_slug' => 'required',
            'avatar' => 'required|string',
        ]);

        $user = new User();
        $user->setSlug($input['leetcode_slug']);
        $user->setPassword($input['password']);
        $user->setName($input['name']);
        $user->setAvatar($input['avatar']);
        $user->save();

        return $this->login($request);
    }

    public function login(Request $request)
    {
        $this->validateRequest($request, [
            'leetcode_slug' => 'required',
            'password' => 'required'
        ]);

        $credentials = request(['leetcode_slug', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return $this->error(Response::HTTP_UNAUTHORIZED, '用户名或密码错误');
        }

        $response = $this->success();
        return $response->withCookie('token', $token, 86400);
    }

    public function resetPassword(Request $request)
    {
        $input = $this->validateRequest($request, [
            'leetcode_slug' => 'required',
            'password' => 'required',
            'new_password' => 'required'
        ]);

        /** @var User $user */
        $user = User::query()->where('leetcode_slug', $input['name'])->first();
        if (!$user || bcrypt($input['password']) != $user->getPassword()) {
            return $this->error(Response::HTTP_UNAUTHORIZED, '用户名或密码错误');
        }
        $user->setPassword($input['new_password'])->save();

        return $this->success();
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        /** @var User $user */
        $user = auth()->user();
        return $this->successWithData([
            'leetcode_slug' => $user->getSlug(),
            'name' => $user->getName(),
            'avatar' => $user->getAvatar()
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token = auth()->refresh();

        $response = $this->success();
        return $response->withCookie('token', $token, 600);
    }

    public function uploadAvatar(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $file=$request->file('file');
        if (!$file) {
            return $this->error(Response::HTTP_BAD_REQUEST, '请上传头像');
        }
        $fileExt=$file->getClientOriginalExtension();
        if (!in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif'])) {
            return $this->error(Response::HTTP_BAD_REQUEST, '图片格式错误');
        }
        $fileSize=$file->getSize();
        if ($fileSize > 2 * 1024 * 1024) {
            return $this->error(Response::HTTP_BAD_REQUEST, '图片最大2M');
        }
        $toPath = public_path('avatar');
        $toName = $user->getSlug() . '.' . strtolower($fileExt);
        $file->move($toPath, $toName);

        $user->setAvatar('avatar/' . $toName)->save();

        return $this->successWithData($user);
    }

    public function editUserName(Request $request)
    {
        $name = $request->input('name');
        if (empty($name) || !is_string($name)) {
            return $this->error(Response::HTTP_BAD_REQUEST, '请输入你的名字');
        }
        if (mb_strlen($name) > 32) {
            return $this->error(Response::HTTP_BAD_REQUEST, '太长啦');
        }
        /** @var User $user */
        $user = auth()->user();
        $user->setName($name)->save();

        return $this->successWithData($user);
    }
}
