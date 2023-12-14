var Post = require("../models/PostModel.js");
const constants = require("../config/constants");
var Gallery = require("../models/GalleryModel.js");
var Like = require("../models/LikeModel.js");
var GetUserData = require("../helpers/thirdPartyApi.helpers.js");
var UserHelper = require("../helpers/user.helpers.js");
const Response = require("../helpers/response.helpers.js");
var PostResource = require("../resources/post.resources.js");

var Comment = require("../models/CommentModel.js");
var Report = require("../models/ReportModel.js");
var ReportData = require("../models/ReportDataModel.js");
const i18n = require("../config/i18nConfig.js");
const mongoose = require("mongoose");
const ObjectId = new mongoose.Types.ObjectId();

exports.create = async (req, res) => {
  try {
    var addPost = await new Post({
      description: req.body.description,
      created_by: req.user.id,
    });
    await addPost.save();

    let gallery = req.body.gallery;
    let postId = addPost._id;
    for (const item of gallery) {
      var addGallery = await new Gallery({
        post_id: postId,
        img_url: item,
        created_by: req.user.id,
      });
      await addGallery.save();
    }

    return Response.successResponse(
      req,
      res,
      null,
      i18n.__("Post added successfully.")
    );
  } catch (err) {
    return Response.errorResponse(req, res, err.message, 400);
  }
};

exports.update = async (req, res) => {
  try {
    const postId = req.params.postId;

    const result = await Post.findOneAndUpdate(
      { _id: postId },
      { description: req.body.description },
      { new: true }
    );

    if (result) {
      return Response.successResponse(
        req,
        res,
        null,
        i18n.__("Post updated successfully.")
      );
    } else {
      return Response.errorResponse(
        req,
        res,
        i18n.__("Post not updated."),
        400
      );
    }
  } catch (err) {
    return Response.errorResponse(req, res, err.message, 400);
  }
};

exports.get_post = async (req, res) => {
  const postId = req.params.postId; // Assuming you pass the postId as a parameter in the URL
  if (!mongoose.Types.ObjectId.isValid(postId)) {
    return Response.errorResponse(req, res, i18n.__("Invalid Post ID."), 400);
  }

  try {
    const post = await Post.aggregate([
      {
        $match: {
          _id: new mongoose.Types.ObjectId(postId),
        },
      },

      {
        $lookup: {
          from: "post_galleries",
          localField: "_id",
          foreignField: "post_id",
          as: "galleryData",
        },
      },
      {
        $project: {
          title: 0,
          feature_img: 0,
          __v: 0,
          "galleryData.post_id": 0,
          "galleryData.status": 0,
          "galleryData.created_by": 0,
          "galleryData.createdAt": 0,
          "galleryData.updatedAt": 0,
          "galleryData.__v": 0,
        },
      },
    ]);

    if (!post || post.length == 0) {
      return Response.errorResponse(req, res, i18n.__("Post not found."), 400);
    }
    var userId = post[0].created_by;

    var userData = await GetUserData.getUserData(req, userId);
    let jsonData = await UserHelper.jsonData(userData[0]);

    var isLike = await Like.findOne({
      post_id: postId,
      created_by: req.user.id,
      status: constants.LIKE_STATUS,
    });
    var islike;
    if (isLike) {
      islike = true;
    } else {
      islike = false;
    }
    if (post.length > 0) {
      post[0].isLike = islike;
      post[0].created_by_data = jsonData;
    }

    return Response.successResponse(
      req,
      res,
      post[0],
      i18n.__("Post retrieved successfully.")
    );
  } catch (err) {
    return Response.errorResponse(req, res, err.message, 400);
  }
};

exports.getAll = async (req, res) => {
  try {
    var jsonData;
    const page = parseInt(req.query.page) || constants.QUERY_PAGE; // Current page (default to 1)
    const limit = parseInt(req.query.limit) || constants.QUERY_LIMIT; // Items per page (default to 10)

    const searchQuery = {}; // Initialize an empty query for now
    if (req.query.search) {
      searchQuery.description = { $regex: req.query.search, $options: "i" }; // Search by title (case-insensitive)
    }
    const postData = await Post.aggregate([
      {
        $match: searchQuery,
      },
      {
        $lookup: {
          from: "post_galleries",
          localField: "_id",
          foreignField: "post_id",
          as: "galleries",
        },
      },
      {
        $project: {
          title: 0,
          feature_img: 0,
          __v: 0,
          "galleries.post_id": 0,
          "galleries.status": 0,
          "galleries.created_by": 0,
          "galleries.createdAt": 0,
          "galleries.updatedAt": 0,
          "galleries.__v": 0,
        },
      },
      {
        $lookup: {
          from: "post_likes",
          let: { post_id: "$_id" },
          pipeline: [
            {
              $match: {
                $expr: { $eq: ["$post_id", "$$post_id"] },
                created_by: req.user.id,
              },
            },
          ],
          as: "postlikes",
        },
      },
      {
        $addFields: {
          is_like: {
            $cond: {
              if: { $gt: [{ $size: "$postlikes" }, 0] },
              then: true,
              else: false,
            },
          },
        },
      },
      {
        $lookup: {
          from: "post_reports",
          let: { post_id: "$_id" },
          pipeline: [
            {
              $match: {
                $expr: { $eq: ["$post_id", "$$post_id"] },
                created_by: req.user.id,
              },
            },
          ],
          as: "postreports",
        },
      },
      {
        $match: {
          postreports: { $size: 0 },
           // Exclude posts with postreports
        },
      },
      {
        $project: {
          postlikes: 0,
          postreports:0 // Exclude the postlikes field from the output
        }
      },
      {
        $facet: {
          metaData: [
            {
              $count: "total",
            },
          ],
          records: [
            {
              $skip: (page - 1) * limit,
            },
            {
              $limit: limit,
            },
          ],
        },
      },
      {
        $unwind: "$metaData", // Unwind to make total accessible in the next stages
      },
      {
        $project: {
          total: "$metaData.total",
          records: "$records",
        },
      },
    ]);
  
    const totalPosts = postData[0].total;
   let posts = postData[0].records
    //const totalPosts = await Post.countDocuments(searchQuery);

    if (!posts || posts.length === 0) {
      return Response.errorResponse(req, res, i18n.__("No posts found."), 404);
    }

    var userId = [];

    posts.forEach((post) => {
      userId.push(post.created_by);
    });

    const uniqueUserIds = [...new Set(userId)];

    const userIdString = uniqueUserIds.join(",");

    var userData = await GetUserData.getUserData(req, userIdString);

    for (let index = 0; index < posts.length; index++) {
      const post = posts[index];

      for (const user of userData) {
        if (user.id == post.created_by) {
          const userData = await GetUserData.getUserData(req, user.id);
          const jsonData = await UserHelper.jsonData(userData[0]);

          posts[index].created_by_data = jsonData;
        }
      }
    }

    let resObj = {
      total: totalPosts,
      current_page: page,

      per_page: limit,
      list: posts,
    };
    return Response.successResponse(
      req,
      res,
      resObj,
      i18n.__("Get Post list successfully.")
    );
  } catch (err) {
    return Response.errorResponse(req, res, err.message, 400);
  }
};

// exports.getAll = async (req, res) => {
//   try {
//     var jsonData;
//     const page = parseInt(req.query.page) || constants.QUERY_PAGE; // Current page (default to 1)
//     const limit = parseInt(req.query.limit) || constants.QUERY_LIMIT; // Items per page (default to 10)

//     const searchQuery = {}; // Initialize an empty query for now
//     if (req.query.search) {
//       searchQuery.description = { $regex: req.query.search, $options: "i" }; // Search by title (case-insensitive)
//     }
//     const posts = await Post.aggregate([
//       {
//         $match: searchQuery,
//       },
//       {
//         $lookup: {
//           from: "post_galleries",
//           localField: "_id",
//           foreignField: "post_id",
//           as: "galleries",
//         },
//       },
//       {
//         $project: {
//           title: 0,
//           feature_img: 0,
//           __v: 0,
//           "galleries.post_id": 0,
//           "galleries.status": 0,
//           "galleries.created_by": 0,
//           "galleries.createdAt": 0,
//           "galleries.updatedAt": 0,
//           "galleries.__v": 0,
//         },
//       },
//       {
//         $sort: { createdAt: -1 },
//       },
//       {
//         $skip: (page - 1) * limit,
//       },
//       {
//         $limit: limit,
//       },
//     ]);

//     const totalPosts = await Post.countDocuments(searchQuery);

//     if (!posts || posts.length === 0) {
//       return Response.errorResponse(req, res, i18n.__("No posts found."), 404);
//     }

//     var userId = [];

//     posts.forEach((post) => {
//       userId.push(post.created_by);
//     });

//     const uniqueUserIds = [...new Set(userId)];

//     const userIdString = uniqueUserIds.join(",");

//     var userData = await GetUserData.getUserData(req, userIdString);

//     for (let index = 0; index < posts.length; index++) {
//       const post = posts[index];
//       post.is_like =await PostResource.isLike(post._id,req.user.id);

//       for (const user of userData) {
//         if (user.id == post.created_by) {
//           const userData = await GetUserData.getUserData(req, user.id);
//           const jsonData = await UserHelper.jsonData(userData[0]);

//           posts[index].created_by_data = jsonData;
//         }
//       }
//     }

//     let resObj = {
//       total: totalPosts,
//       current_page: page,

//       per_page: limit,
//       list: posts,
//     };
//     return Response.successResponse(
//       req,
//       res,
//       resObj,
//       i18n.__("Get Post list successfully.")
//     );
//   } catch (err) {
//     return Response.errorResponse(req, res, err.message, 400);
//   }
// };

exports.like = async (req, res) => {
  console.log("postcontroller@like");
  try {
    var isPost = await Like.findOne({
      post_id: req.body.post_id,
      created_by: req.user.id,
    });
    var totalLike = await Like.find({
      post_id: req.body.post_id,
      status: constants.LIKE_STATUS,
    });
    var totalLikes = totalLike.length;

    if (!isPost && req.body.status == constants.LIKE_STATUS) {
      var addLike = await new Like({
        post_id: req.body.post_id,
        status: req.body.status,
        created_by: req.user.id,
      });

      await addLike.save();
      totalLikes = totalLikes + 1;
      const result = await Post.updateOne(
        { _id: req.body.post_id },
        { $set: { total_likes: totalLikes } }
      );
    } else {
      if (isPost) {
        (isPost.status = req.body.status), await isPost.save();
      }
    }
    let msg;
    let likeStatus;
    if (req.body.status == constants.LIKE_STATUS) {
      likeStatus = true;
      msg = i18n.__("Post Like successfully.");
    }
    if (req.body.status == constants.DISLIKE_STATUS) {
      likeStatus = false;
      msg = i18n.__("Post DisLike successfully.");
      if (isPost) {
        totalLikes = totalLikes - 1;
      }
      if (isPost) {
        await Like.deleteOne({ _id: isPost._id });

        const result = await Post.updateOne(
          { _id: req.body.post_id },
          { $set: { total_likes: totalLikes } }
        );
      }
    }

    let objData = {
      total_likes: totalLikes,
      is_like: likeStatus,
    };
    return Response.successResponse(req, res, objData, i18n.__(msg));
  } catch (err) {
    return Response.errorResponse(req, res, err.message, 400);
  }
};

exports.comment = async (req, res) => {
  console.log("postcontroller@comment");
  try {
    var addComment = await new Comment({
      post_id: req.body.post_id,
      comment: req.body.comment,
      created_by: req.user.id,
    });
    await addComment.save();
    var totalComment = await Comment.find({ post_id: req.body.post_id });
    var totalComments = totalComment.length;

    const result = await Post.updateOne(
      { _id: req.body.post_id },
      { $set: { total_comments: totalComments } }
    );
    return Response.successResponse(
      req,
      res,
      addComment,
      i18n.__("Post Comment successfully.")
    );
  } catch (err) {
    return Response.errorResponse(req, res, err.message, 400);
  }
};

exports.report = async (req, res) => {
  console.log("postcontroller@report");
  try {
    var addReport = await new Report({
      post_id: req.body.post_id,
      reason: req.body.reason,
      created_by: req.user.id,
    });
    await addReport.save();

    return Response.successResponse(
      req,
      res,
      null,
      i18n.__("Post has been reported.")
    );
  } catch (err) {
    return Response.errorResponse(req, res, err.message, 400);
  }
};

exports.getAllComment = async (req, res) => {
  const postId = req.params.postId; // Assuming you pass the postId as a parameter in the URL

  if (!mongoose.Types.ObjectId.isValid(postId)) {
    return Response.errorResponse(req, res, i18n.__("Invalid Post ID."), 400);
  }
  try {
    const posts = await Comment.find({ post_id: postId }).sort({
      createdAt: -1,
    });

    if (!posts || posts.length === 0) {
      return Response.errorResponse(
        req,
        res,
        i18n.__("No Comment found."),
        404
      );
    }

    var userId = [];

    posts.forEach((post) => {
      userId.push(post.created_by);
    });

    const uniqueUserIds = [...new Set(userId)];

    const userIdString = uniqueUserIds.join(",");

    var userData = await GetUserData.getUserData(req, userIdString);

    let commentList = [];
    for (let index = 0; index < posts.length; index++) {
      const post = posts[index];
      var json = {};
      json["_id"] = post.id;
      json["post_id"] = post.post_id;
      json["comment"] = post.comment;
      json["created_by"] = post.created_by;
      json["createdAt"] = post.createdAt;
      json["updatedAt"] = post.updatedAt;

      for (const user of userData) {
        if (user.id == post.created_by) {
          const userData = await GetUserData.getUserData(req, user.id);
          const jsonData = await UserHelper.jsonData(userData[0]);
          json["created_by_data"] = jsonData;
        }
      }
      commentList.push(json);
    }

    return Response.successResponse(
      req,
      res,
      commentList,
      i18n.__("Get Post Comment List successfully.")
    );
  } catch (err) {
    return Response.errorResponse(req, res, err.message, 400);
  }
};
exports.getAllReport = async (req, res) => {
  try {
    const reportData = await ReportData.find().sort({
      createdAt: -1,
    });

    if (!reportData || reportData.length === 0) {
      return Response.errorResponse(
        req,
        res,
        i18n.__("No reports data found."),
        404
      );
    }

    return Response.successResponse(
      req,
      res,
      reportData,
      i18n.__("Get reports list successfully.")
    );
  } catch (err) {
    return Response.errorResponse(req, res, err.message, 400);
  }
};

exports.delete = async (req, res) => {
  const postId = req.params.postId; // Assuming you pass the postId as a parameter in the URL
  if (!mongoose.Types.ObjectId.isValid(postId)) {
    return Response.errorResponse(req, res, i18n.__("Invalid Post ID."), 400);
  }

  try {
    await Post.deleteOne({ _id: postId });
    await Like.deleteOne({ post_id: postId });
    await Comment.deleteMany({ post_id: postId });
    await Gallery.deleteMany({ post_id: postId });

    return Response.successResponse(
      req,
      res,
      null,
      i18n.__("Post Deleted successfully.")
    );
  } catch (err) {
    return Response.errorResponse(req, res, err.message, 400);
  }
};
exports.uploadFile = async (req, res) => {
  console.log("PostController@uploadFile");

  if (req.file === undefined) {
    return Response.errorResponse(
      req,
      res,
      i18n.__("Invalid request data. Please add file to request."),
      400
    );
  }

  let data = {
    file_name: req.file.filename,
    upload_file: req.file.filename,
  };

  const Filename = data.file_name;
  const profileImage = process.env.UPLOAD_FOLDER + data.upload_file;
  return Response.successResponse(
    req,
    res,
    (data = {
      file_name: Filename,
      file_url: profileImage,
    }),
    i18n.__("Upload File successfully.")
  );
};
