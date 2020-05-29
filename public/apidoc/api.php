/**
 * @api {post} dial 拨打电话
 * @apiName dial
 * @apiGroup 接口列表
 * @apiVersion 2.0.0
 *
 * @apiParam {Number} exten 分机号
 * @apiParam {Number} phone 手机号码
 * @apiParam {Number} [user_data] 自定义json数据，会原样返回到通话记录里
 *
 * @apiSuccess {Number} code 返回状态码，0
 * @apiSuccess {String} msg  返回提示消息
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "code": 0,
 *       "msg": "请求成功"
 *     }
 *
 */

 /**
 * @api {post} hangup 挂断电话
 * @apiName hangup
 * @apiGroup 接口列表
 * @apiVersion 2.0.0
 *
 * @apiParam {Number} exten 分机号
 *
 * @apiSuccess {Number} code 返回状态码，0
 * @apiSuccess {String} msg  返回提示消息
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "code": 0,
 *       "msg": "请求成功"
 *     }
 *
 */

/**
 * @api {post} voice 语音消息
 * @apiName voice
 * @apiGroup 接口列表
 * @apiVersion 2.0.0
 *
 * @apiParam {Number} phone 手机号
 * @apiParam {String} text 内容
 * @apiParam {Number} gateway_id 网关ID
 *
 * @apiSuccess {Number} code 返回状态码，0
 * @apiSuccess {String} msg  返回提示消息
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "code": 0,
 *       "msg": "请求成功"
 *     }
 *
 */

 /**
 * @api {post} chanspy 监听
 * @apiName chanspy
 * @apiGroup 接口列表
 * @apiVersion 2.0.0
 *
 * @apiParam {Number} fromExten 发起监听分机号
 * @apiParam {Number} toExten 被监听分机号
 * @apiParam {Number} type 监听模式，1-指引（常用），2-旁听，3-三方通过
 *
 * @apiSuccess {Number} code 返回状态码，0
 * @apiSuccess {String} msg  返回提示消息
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "code": 0,
 *       "msg": "请求成功"
 *     }
 *
 */