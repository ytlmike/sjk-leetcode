<?php

namespace App\Traits;


use App\Exceptions\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

trait ValidationTrait
{
    public function validateRequest(Request $request, $rules, $messages = [])
    {
        return $this->validate($request->input(), $rules, $messages);
    }

    public function validate($params, $rules, $messages = [])
    {
        foreach ($rules as $k => $v) {
            if (is_string($v) && !(strpos($v, 'bail') !== false)) {
                $rules[$k] = 'bail|' . $v;
            } elseif (is_array($v) && !in_array('bail', $v)) {
                $rules[$k] = array_merge(['bail'], $v);
            }
        }

        $validator = Validator::make($params, $rules, $messages);
        if ($validator->fails()) {
            throw new ValidationException(Response::HTTP_BAD_REQUEST, $validator->errors()->first());
        }
        return $params;
    }
}
