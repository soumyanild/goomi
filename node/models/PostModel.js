const constants = require("../config/constants");
var mongoose = require("mongoose");
var PostScheme = mongoose.Schema(
  {
    title: {
      type: String,
      default: null,
      required: false,
    },
    description: {
      type: String,
      required: false,
    },
    feature_img: {
      type: String,
      default: null,
      required: false,
    },
    status: {
      type: Number,
      required: true,
      default: 1,
      enum: [0, 1, 2], // Allowed values: 0, 1, 2
    },
    total_likes: {
      type: Number,
      required: false,
      default: 0,
    },
    total_comments: {
      type: Number,
      required: false,
      default: 0,
  
    },
    created_by: {
      type: Number,
      required: true,
    },
  },
  {
    timestamps: true,
    toJSON: { virtuals: true },
  }
);

module.exports = mongoose.model(constants.POST_COLLECTION_NAME, PostScheme);
