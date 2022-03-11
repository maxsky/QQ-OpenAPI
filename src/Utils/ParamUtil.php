<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2022/3/10
 * Time: 7:02 PM
 */

namespace Tencent\QQ\Utils;

use Tencent\QQ\Exceptions\QQAPIRouteException;
use Tencent\QQ\Exceptions\QQOpenIDException;

class ParamUtil {

    /**
     * @param string|null $open_id
     *
     * @return void
     * @throws QQOpenIDException
     */
    public static function openIdValidate(?string $open_id): void {
        // 检查 OpenID 是否为空
        if (!$open_id) {
            throw new QQOpenIDException('OpenID is empty', ERROR_PARAM_OPENID_EMPTY);
        }

        // 检查 OpenID 是否合法
        if (preg_match('/^[0-9a-fA-F]{32}$/', $open_id) === false) {
            throw new QQOpenIDException('OpenID is invalid', ERROR_PARAM_INVALID);
        }
    }

    /**
     * @param string $route
     *
     * @return void
     * @throws QQAPIRouteException
     */
    public static function routeValidate(string $route): void {
        // 检查请求 API 是否由左斜杠开头
        if (stripos($route, '/') !== 0) {
            throw new QQAPIRouteException('Request API route format error', ERROR_PARAM_INVALID);
        }
    }
}
