const i18n = require("../config/i18nConfig.js");
const constants = require("../config/constants");
const axios = require("axios");
exports.getUserData = async (req, userId) => {
  
  console.log("AuthorizationMiddleware@getData");

  if (!req.headers["authorization"]) {
    return res
      .status(404)
      .json({ status: 400, message: i18n.__("No token provided.") });
  }

  let token = req.headers["authorization"];

  let config = {
    method: "get",
    maxBodyLength: Infinity,
    url: constants.GET_USERDATA_URL+userId,
    headers: {
      Accept: "application/json",
      Authorization: token,
    },
  };

  try {
    const response = await axios.request(config);
    const userData = response.data.data;
    
    return userData;
  } catch (error) {
    return res
      .status(404)
      .json({ status: 400, message: i18n.__("Invalid token.") });
  }
};
