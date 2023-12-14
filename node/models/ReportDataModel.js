const constants = require("../config/constants")
var mongoose = require("mongoose");
var ReportDataScheme = mongoose.Schema(
  {

    reason: {
      type: String,
      required: true,

    }

  },
  {
    timestamps: true,
    //toJSON: { virtuals: true },
  }
);

module.exports = mongoose.model(constants.REPORTLIST_COLLECTION_NAME, ReportDataScheme);
