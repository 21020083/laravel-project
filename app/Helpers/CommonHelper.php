<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\Request;

class CommonHelper
{
    /**
     * @param Request $request
     * @param $key
     * @return string
     */
    public static function getDataFromHeaderRequest(Request $request, $key): string
    {
        return $request->headers->get($key);
    }


    public static function removeNullValue($data): array
    {
        return array_filter($data, fn($value) => !is_null($value));
    }

    /**
     * Generate random string with length
     *
     * @param int $length
     *
     * @return string
     */
    public static function generateRandomString(int $length = 10): string
    {
        try {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            }

            return $randomString;
        } catch (\Exception $e) {
            return 'SecretKey123';
        }
    }

    public static function generateOrderCode(): string
    {
        // Get the current date and time
        $dateTimeNow = now();

        // Format the date and time
        $formattedDateTime = $dateTimeNow->format('YmdHis');

        // Generate a random string
        $randomString = self::generateRandomString(5);

        // Combine the formatted date and time with the random string to create a unique order code
        return $formattedDateTime . $randomString;
    }
}
