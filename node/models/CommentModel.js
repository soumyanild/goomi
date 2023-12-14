const constants = require("../config/constants")
var mongoose = require("mongoose");
var CommentScheme = mongoose.Schema(
  {
    post_id: {
        type: mongoose.Schema.Types.ObjectId,
        required: true,
        ref: 'Posts'
        
    },

    comment: {
      type: String,
      required: true,

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

module.exports = mongoose.model(constants.COMMENT_COLLECTION_NAME, CommentScheme);
