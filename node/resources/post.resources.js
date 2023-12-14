const constants = require("../config/constants");
const LikeModel = require("../models/LikeModel");

exports.isLike = async (postId, userId) => {

  var isLike = await LikeModel.findOne({
    post_id: postId,
    created_by: userId,
    status: constants.LIKE_STATUS,
  });

  if (!isLike) {
    return false;
  }
  return true;
};
