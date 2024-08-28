<?php

namespace App\Traits;

use App\Enums\ErrorCodeEnum;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait ApiResponse
{
    /**
     * @param $pagination
     * @param $records
     *
     * @return JsonResponse
     */
    public function sendPaginationResponse($pagination, $records): JsonResponse
    {
        $data = [
            'records' => $records,
            'limit' => $pagination->perPage(),
            'total' => $pagination->total(),
            'page' => $pagination->currentPage(),
        ];
        return $this->sendResponse($data, __('common.get_data_success'));
    }

    /**
     * @param $data
     * @param string|null $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function sendResponse($data = null, string $message = null, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $res = [
            'status' => true,
            'data' => $data,
            'message' => $message,
        ];
        return response()->json($res, $statusCode);
    }

    /**
     * @param string|null $message
     * @param string|null $errorCode
     * @param int $statusCode
     * @param array $errors
     * @return JsonResponse
     */
    public function sendError(string $message = null, string $errorCode = null, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, array $errors = []): JsonResponse
    {
        $res = [
            'status' => false,
        ];

        if ($message) {
            $res['message'] = $message;
        }

        if (!empty($errors)) {
            $res['errors'] = $errors;
        }

        if (!empty($errorCode)) {
            $res['code'] = $errorCode;
        }

        return response()->json($res, $statusCode);
    }

    protected function sendExceptionError(Exception $e, $errorCode = null): JsonResponse
    {
        Log::error(
            'Error: ',
            [
                'line' => __LINE__,
                'method' => __METHOD__,
                'error_message' => $e->getMessage(),
                'error_code' => $errorCode,
            ]
        );

        if ($e instanceof HttpException && $e?->getStatusCode() == Response::HTTP_FORBIDDEN) {
            return $this->sendError(__('common.access_denied'), $errorCode, Response::HTTP_FORBIDDEN);
        }

        return $this->sendError(__('common.server_error'), $errorCode);
    }
}
