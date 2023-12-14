const constants = require("../config/constants")
var mongoose = require("mongoose");
var GalleryScheme = mongoose.Schema(
  {
    post_id: {
        type: mongoose.Schema.Types.ObjectId,
        required: true,
        ref: 'Posts'
        
    },

    img_url: {
      type: String,
      required: true,
    },

    status: {
      type: Number,
      enum: [1, 2], 
      default: 1

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

module.exports = mongoose.model(constants.GALLERY_COLLECTION_NAME, GalleryScheme);
