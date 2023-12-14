const i18n = require("../config/i18nConfig.js");
const constants = require("../config/constants");
const axios = require("axios");
exports.auth = async (req, res, next) => {
  console.log("AuthorizationMiddleware@auth");

  if (!req.headers["authorization"]) {
    return res
      .status(404)
      .json({ status: 400, message: i18n.__("No token provided.") });
  }

  let token = req.headers["authorization"];

  let config = {
    method: "get",
    maxBodyLength: Infinity,
    url: constants.TOKEN_VALIDATE_URL,
    headers: {
      Accept: "application/json",
      Authorization: token,
    },
  };

  axios
    .request(config)
    .then((response) => {
      userData = response.data.data;
      var json = {};

      json["id"] = userData.id;
      json["full_name"] = userData.full_name;
      json["username"] = userData.username;
      json["email"] = userData.email;
      json["phone_number"] = userData.phone_number;
      json["phone_code"] = userData.phone_code;
      json["iso_code"] = userData.iso_code;
      json["profile_image"] = userData.profile_image;
      json["role"] = userData.role;
      json["status"] = userData.status;
      json["email_verification_otp"] = userData.email_verification_otp;
      json["notification"] = userData.notification;
      json["email_notification"] = userData.email_notification;
      json["email_verified_at"] = userData.email_verified_at;
      json["type"] = userData.type;
      json["gender"] = userData.gender;
      json["gender_title"] = userData.gender_title;
      json["created_at"] = userData.created_at;
      json["updated_at"] = userData.updated_at;

      req.user = json;

      next();
    })
    .catch((error) => {
      return res
        .status(404)
        .json({ status: 400, message: i18n.__("Invalid token.") });
    });
};
