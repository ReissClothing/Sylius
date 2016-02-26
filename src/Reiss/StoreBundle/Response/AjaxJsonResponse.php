<?php
/**
 * @author    Pete Ward <peter.ward@reiss.com>
 * @date      26/01/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\StoreBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Pete Ward <peter.ward@reiss.com>
 */
class AjaxJsonResponse extends JsonResponse
{
    const TYPE_SUCCESS = 'success';
    const TYPE_INFO    = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR   = 'error';

    /**
     * @param array|string $data
     * @param string       $type
     * @param string       $message
     */
    public function __construct($data, $type = self::TYPE_INFO, $message = '')
    {
        $data = [
            'success'     => $type === self::TYPE_INFO || $type === self::TYPE_SUCCESS,
            'messageType' => $type,
            'message'     => $message,
            'data'        => $data,
        ];

        parent::__construct($data);
    }

    /**
     * @param array|string $data
     * @param string       $message
     *
     * @return AjaxJsonResponse
     */
    public static function success($data, $message = '')
    {
        return new static($data, self::TYPE_SUCCESS, $message);
    }

    /**
     * @param array|string $data
     * @param string       $message
     *
     * @return AjaxJsonResponse
     */
    public static function info($data, $message = '')
    {
        return new static($data, self::TYPE_INFO, $message);
    }

    /**
     * @param string $message
     *
     * @return AjaxJsonResponse
     */
    public static function error($message)
    {
        return new static([], self::TYPE_ERROR, $message);
    }

    /**
     * @param string $message
     *
     * @return AjaxJsonResponse
     */
    public static function warning($message)
    {
        return new static([], self::TYPE_WARNING, $message);
    }
}
