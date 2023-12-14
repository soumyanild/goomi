const constants = require("../config/constants")
var mongoose = require("mongoose");
var ReportScheme = mongoose.Schema(
  {
    post_id: {
        type: mongoose.Schema.Types.ObjectId,
        required: true,
        ref: 'Posts'
        
    },

    reason: {
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
  }
);

module.exports = mongoose.model(constants.REPORT_COLLECTION_NAME, ReportScheme);
