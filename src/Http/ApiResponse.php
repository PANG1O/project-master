<?php
declare(strict_types=1);

namespace PHPCore\Core\Http;

use JetBrains\PhpStorm\NoReturn;

/**
 * Provides helper methods for sending standardized JSON API responses with appropriate HTTP status codes and
 * terminating execution.
 */
class ApiResponse {
    public const int OK = 200;
    public const int CREATED = 201;
    public const int ACCEPTED = 202;
    public const int NO_CONTENT = 204;
    public const int BAD_REQUEST = 400;
    public const int UNAUTHORIZED = 401;
    public const int FORBIDDEN = 403;
    public const int NOT_FOUND = 404;
    public const int METHOD_NOT_ALLOWED = 405;
    public const int CONFLICT = 409;
    public const int UNPROCESSABLE_ENTITY = 422;
    public const int TOO_MANY_REQUESTS = 429;
    public const int INTERNAL_SERVER_ERROR = 500;

    ####################################################################################################################
    # --- PUBLIC METHODS --------------------------------------------------------------------------------------------- #
    ####################################################################################################################

    /**
     * Sends a JSON response with HTTP 200 OK.
     *
     * @param array $data
     * @param string $message
     * @return void
     */
    public static function ok(array $data = [], string $message = 'OK'): void {
        Response::json(self::OK, [
            'message' => $message,
            'success' => false,
            'data' => $data
        ]);
    }

    /**
     * Sends a JSON response with HTTP 201 Created.
     *
     * @return void
     */
    public static function created(): void {
        Response::json(self::CREATED, []);
    }

    /**
     * Sends a JSON response with HTTP 202 Accepted.
     *
     * @return void
     */
    public static function accepted(): void {
        Response::json(self::ACCEPTED, []);
    }

    /**
     * Sends a JSON response with HTTP 204 No Content.
     *
     * @return void
     */
    public static function noContent(): void {
        Response::json(self::NO_CONTENT, []);
    }

    /**
     *  Sends a JSON response with HTTP 400 Bad Request.
     *
     * @param string $message
     * @return void
     */
    public static function badRequest(string $message = 'Bad Request'): void {
        Response::json(self::BAD_REQUEST, [
            'message' => $message,
            'success' => false,
            'data' => []
        ]);
    }

    /**
     *  Sends a JSON response with HTTP 401 Unauthorized.
     *
     * @param string $message
     * @return void
     */
    public static function unauthorized(string $message = 'Unauthorized'): void {
        Response::json(self::UNAUTHORIZED, [
            'message' => $message,
            'success' => false,
            'data' => []
        ]);
    }

    /**
     *  Sends a JSON response with HTTP 403 Forbidden.
     *
     * @param string $message
     * @return void
     */
    public static function forbidden(string $message = 'Forbidden'): void {
        Response::json(self::FORBIDDEN, [
            'message' => $message,
            'success' => false,
            'data' => []
        ]);
    }

    /**
     * Sends a JSON response with HTTP 404 Not Found.
     *
     * @param string $message
     * @return void
     */
    public static function notFound(string $message = 'Not found'): void {
        Response::json(self::NOT_FOUND, [
            'message' => $message,
            'success' => false,
            'data' => []
        ]);
    }

    /**
     * Sends a JSON response with HTTP 405 Method Not Allowed.
     *
     * @param string $message
     * @return void
     */
    public static function methodNotAllowed(string $message = 'Method Not Allowed'): void {
        Response::json(self::METHOD_NOT_ALLOWED, [
            'message' => $message,
            'success' => false,
            'data' => []
        ]);
    }

    /**
     * Sends a JSON response with HTTP 409 Conflict.
     *
     * @param string $message
     * @return void
     */
    public static function conflict(string $message = 'Conflict'): void {
        Response::json(self::CONFLICT, [
            'message' => $message,
            'success' => false,
            'data' => []
        ]);
    }

    /**
     * Sends a JSON response with HTTP 422 Unprocessable Entity.
     *
     * @param array $missing
     * @param string $message
     * @return void
     */
    public static function unprocessableEntity(array $missing = [], string $message = 'Unprocessable Entity'): void {
        Response::json(self::UNPROCESSABLE_ENTITY, [
            'message' => $message,
            'success' => false,
            'data' => $missing
        ]);
    }

    /**
     * Sends a JSON response with HTTP 429 Too Many Requests.
     *
     * @param array $missing
     * @param string $message
     * @return void
     */
    public static function tooManyRequests(array $missing = [], string $message = 'Too Many Requests'): void {
        Response::json(self::TOO_MANY_REQUESTS, [
            'message' => $message,
            'success' => false,
            'data' => $missing
        ]);
    }

    /**
     * Sends a JSON response with HTTP 500 Internal Server Error.
     *
     * @param string $message
     * @return void
     */
    public static function internalServerError(string $message = 'Internal Server Error'): void {
        Response::json(self::INTERNAL_SERVER_ERROR, [
            'message' => $message,
            'success' => false,
            'data' => []
        ]);
    }
}