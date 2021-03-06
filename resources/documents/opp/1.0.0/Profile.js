/**
 * @api {Get} /v1/me/profile 1. My Profile
 * @apiVersion 1.0.0
 * @apiName 1. My Profile
 * @apiGroup Profile
 *
 * @apiUse GetHeader
 *
 * @apiSuccessExample  Response (example):
 HTTP/1.1 200 Success Request
 {
    "success": true,
    "message": "",
    "data": {
        "outlet_name": "Sokahkrom",
        "owner_name": "Chhoun Sophanith",
        "area_code": null,
        "agent_code": "CZ01",
        "house_no": null,
        "street_no": "#429",
        "village": null,
        "commune": "Sangkat Terk Tla",
        "district": "Khan Sen Sok",
        "province": "Phnom Penh",
        "latitude": "11.553224",
        "longitude": "104.880950"
    }
 }
 *
 *
 * @apiUse AuthorizationInvalid
 * @apiUse AuthorizationEncryptionInvalid
 * @apiUse AuthorizationExceedLimitTime
 * @apiUse NotFound
 * @apiUse MethodNotAllowed
 * @apiUse ServerServerError
 */

 /**
 * @api {Get} /v1/me/update/password 2. Update Password
 * @apiVersion 1.0.0
 * @apiName 2. Update Password
 * @apiGroup Profile
 *
 * @apiUse PostHeader
 *
 * @apiExample {curl} Request usage:
 {
    "old_password": "123456",
    "password": "123456"
 }
 *
 * @apiSuccessExample  Response (example):
 HTTP/1.1 200 Success Request
 {
    "success": true,
    "message": "You have been update your password successfully.",
    "data": null
 }
 *
 * @apiUse InvalidCredential
 * @apiUse AuthorizationInvalid
 * @apiUse AuthorizationEncryptionInvalid
 * @apiUse AuthorizationExceedLimitTime
 * @apiUse NotFound
 * @apiUse MethodNotAllowed
 * @apiuse ErrorValidation
 * @apiUse ServerServerError
 */