<?php

namespace App\Http\Requests;

use App\Http\AuthTrait\AdminUserAuthenticationTrait;
use App\Http\AuthTrait\UserAuthenticationTrait;
use Domain\Common\Paginator\Paginate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class Request extends FormRequest
{
    use UserAuthenticationTrait;
    use AdminUserAuthenticationTrait;

    /**
    * [override] バリデーション失敗時ハンドリング
    *
    * @param Validator $validator
    * @throw HttpResponseException
    * @see FormRequest::failedValidation()
    */
   protected function failedValidation(Validator $validator): void
   {
       $response['status']  = false;
       $response['message'] = $validator->errors()->toArray();
       throw new HttpResponseException(
           response()->json($response, 422)
       );
   }

    public function makePaginate(): Paginate
    {
        return Paginate::make(
            $this->integer('page', 1),
            $this->integer('per_page', 10),
        );
    }
}
